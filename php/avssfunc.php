<?

function parsepath($path) {
	global $pathprefix;
	$x=explode("/",$path); 

	//defaults
	$dirinfo=array(
	'isletterslist'=>0,
	'isletterlist'=>0,
	'iscollectionlist'=>0,
	'iscollection'=>0,
	'isartist'=>0,
	'isalbum'=>0,
	'datapath'=>'',
	'issoundtrack'=>0
	);


	if (is_dir($pathprefix.$path)) {
		//$after: part after /artists/

		if ($apos=strpos($path,"/artists")) {
			$after=substr($path,$apos+strlen("/artists"));
			$after=ltrim($after,"/");
			$after_r=explode("/",$after);

			if (strlen($after)==0) { // artist
				$dirinfo['isletterslist']=1;
			}
			elseif (count($after_r)==1) { // A
				$dirinfo['isletterlist']=1;
			}
			elseif (count($after_r)==2) { // A/abba
				$dirinfo['isartist']=1;
				$dirinfo['datapath']=$path;
			}
			elseif (count($after_r)==3) { // A/abba/album
				$dirinfo['isalbum']=1;
				$dirinfo['datapath']=dirname($path);
			}
		}
		elseif ($apos=strpos($path,"/collections")) {
			$after=substr($path,$apos+strlen("/collections"));
			$after=ltrim($after,"/");
			$after_r=explode("/",$after);

			if (strlen($after)==0) { // collections
				$dirinfo['iscollectionlist']=1;
			}
			elseif (count($after_r)==1) { // collectionname
				$dirinfo['iscollection']=1;
				$dirinfo['datapath']=$path;
			}
			elseif (count($after_r)==2) { // collectionname/disk1
				$dirinfo['iscollection']=1;
				$dirinfo['datapath']=dirname($path);
			}

		}
		elseif ($apos=strpos($path,"/soundtracks")) {
			$after=substr($path,$apos+strlen("/soundtracks"));
			$after=ltrim($after,"/");
			$after_r=explode("/",$after);

			if (strlen($after)==0) { // collections
				$dirinfo['iscollectionlist']=1;
			}
			elseif (count($after_r)==1) { // collectionname
				$dirinfo['iscollection']=1;
				$dirinfo['datapath']=$path;
			}
			elseif (count($after_r)==2) { // collectionname/disk1
				$dirinfo['iscollection']=1;
				$dirinfo['datapath']=dirname($path);
			}

		}

	}


	return $dirinfo;

}

function isexpired($userdir)
{
  
  if (!file_exists($userdir."/timecheck")) {
    echo "$userdir/timecheck  does not exist<br>";
    return 1;
  }
  else {
    $stat = stat($userdir."/timecheck");
    $mtime=$stat['mtime'];
    $age=time()-$mtime;
    if ($age<(120*60)) return 0;
  }
  return $age;
}




function printfolderimages() {
	global $path,$folderimages,$photoidx,$allfiles;

	if (!count($folderimages)) return;

	$p0=$allfiles["fname"][$folderimages[0]];
	$url="?path=".urlencode($path)."&file=".urlencode($p0)."&action=sendfile";

	echo "<div id='photoimgcontainer'>";
	echo "\n<img id='photoimg' class='img-rounded' src='$url'>";
	echo "</div><div>";

	if ($photoidx>1)
		for ($i=0;$i<$photoidx;$i++) {
		  echo "\n<a class='badge' href='javascript:showimage(\"$i\");'>$i</a>";
		}
	echo "</div>";

}

/////////////////////////////////////////////////////////////
function validateuser($userdir)
{
//$isexpired=isexpired($userdir);
$isexpired=0;

  if ($isexpired) {
    echo "Session expired ".($isexpired/60)." minutes ago, please login again<br>";
    exit;
  }
}//validateuser

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}


//save file
function savefile()
{
global $file,$path,$pathprefix;
  if (!preg_match("#\.(mp3|ogg|mpc|jpg|txt|png|gif|html)$#i",$file)){
    header("Content-Type: text/html; charset=utf-8");
    echo "savefile:cannot save '$file'<br>";
    exit;
  }

  $fn=$pathprefix.$path."/".$file;
  $sz=filesize($fn);

  header ("Content-Length: $sz");
  header ("Content-disposition: attachment; filename=\"".$file."\"\n");
  header ("Content-type: application/octet-stream\n\n");
  readfile($fn);
  exit;
}

//save file
function savedir()
{
global $file,$path,$pathprefix;

  $mypid=getmypid();
  $dir=$pathprefix.$path."/";
  $dirname=basename($path);

  ignore_user_abort(true); /* to be able to remove pid file if user cancels transfer */
  set_time_limit(3600);   

  //find tar size - foverh idea tou vip
  $cmdtot="tar --total --one-file-system -chf /dev/null -C \"$dir\"/.. \"$dirname\" 2>&1 | grep Total | awk '{print \$4}' ";
  
  $fp=popen("$cmdtot","r");
  $bytes=fgets($fp,1024);
  pclose($fp);

  $maxmb=200;

  if (round($bytes/(1024*1024)) > $maxmb) {
    header("Content-Type: text/html; charset=utf-8");
    echo "savedir: download restricted to $maxmb MB. You chose to download ".round($bytes/(1024*1024))." MB <br>\n";
    exit;
  }

  header ("Content-length: $bytes\n");
  header ("Content-disposition: attachment; filename=\"${dirname}.tar\"\n");
  header ("Content-type: application/octet-stream\n\n");

  //kapoia den skotwnontai me broken pipe.
  //$cmd=" echo $$ >/tmp/mp3-$username.pid;exec star chf - -C $linkdir $files";
  //$cmd=" echo $$ >/tmp/mp3-$username.pid;exec star cdf - $files";
  //passthru($cmd);

  $cmd=" echo $$ >/tmp/avss-$mypid.pid; exec tar --one-file-system -chf - -C \"$dir\"/.. \"$dirname\"";
  $fp=popen("$cmd","r");
  while (!($ab=connection_aborted()) && ($s=fread ($fp, 20480))) {
   echo $s;
  }

  //$r=pclose($fp); // buggy, menei to tar kai trexei kai to pclose perimenei
  $pid=file("/tmp/avss-$mypid.pid");
  system("/bin/kill $pid[0]");
  unlink("/tmp/avss-$mypid.pid");
  $r=pclose($fp); // buggy, menei to tar kai trexei kai to pclose perimenei

  exit;

}


//play audio, show images etc
function sendfile($fromoffset=0)
{
global $file,$path,$pathprefix,$_SERVER;
  $file=str_replace("\'" , "'", $file);

  if (strstr($file,".jpg")) {
    $fn=$pathprefix.$path."/".$file;
    $sz=filesize($fn);
    header ("Content-type: image/jpeg");
    header ("Content-Length: $sz");
    readfile($fn);
    exit;
  }
  elseif (!preg_match("#\.(mp3|ogg|mpc|jpg|txt|png|gif|html)$#i",$file)){
    header("Content-Type: text/plain; charset=utf-8");
    echo "sendfile:$file cannot open unknown file<br>";
    exit;
  }

  $fn=$pathprefix.$path."/".$file;
  $sz=filesize($fn);


  if (stristr($file,".ogg"))
    header ("Content-type: audio/ogg");
  elseif (stristr($file,".mp3"))
    header ("Content-type: audio/mpeg");
  elseif (stristr($file,".mpc"))
    header ("Content-type: audio/mpc");
  elseif (stristr($file,".txt")) {
    header("Content-Type: text/plain; charset=utf-8");
  }

  header ("Connection: close");

  $bfn=basename($fn);

  header("icy-name: $bfn");
  header("icy-pub:0");

  $fd = fopen($fn, "rb");

  if (isset($_SERVER['HTTP_RANGE'])) {
    $seekoffset=intval(substr($_SERVER['HTTP_RANGE'],6));
    fseek($fd,$seekoffset,SEEK_SET);
    $t=ftell($fd);
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    header("Content-Range: bytes $seekoffset-$sz/$sz");
    header("Content-Length: ".($sz-$seekoffset));
  }
  else
    header ("Content-Length: $sz");

  while (!feof($fd)) {
    $t=ftell($fd);
    echo fread($fd, 131072);
  }
  fclose($fd);

  exit;
}

//streaming - send urls in m3u format
function sendm3u()
{
  global $path,$file,$SERVER_NAME,$SCRIPT_NAME;

  header ("Content-type: audio/mpeg-url");
  $url="?path=".rawurlencode($path)."&file=".rawurlencode($file)."&action=sendfile";
  echo "http://".$SERVER_NAME.$SCRIPT_NAME.$url;
  echo "\n";

  exit;
}

//streaming - send all playable files in extended m3u format
function senddirm3u()
{
  global $path,$pathprefix,$SERVER_NAME,$SCRIPT_NAME,$ls,$lsaudio;

  if (!chdir($pathprefix.$path)) {
    echo "Err:C1:<b>chdir \"$pathprefix.$path\" failed</b><br>";
    exit;
  };
  header ("Content-type: audio/mpeg-url");
  echo "#EXTM3U\n";

  //$fp = popen ($ls, "r");
  $fp = popen ($lsaudio, "r");

  while ($buffer = fgets($fp, 1024)) {
    $buffer=rtrim($buffer);
	$row=explode("/",$buffer);
	if (is_numeric($row[2])) {
		$secs=$row[2]*60+$row[3];
	}
    if (!preg_match("#\.(mp3|ogg|mpc)$#i",$row[6])) continue;

    $url="?path=".rawurlencode($path)."&file=".rawurlencode($row[6])."&action=sendfile";

    echo "#EXTINF:$secs,$row[6]\n"; //format: #EXTINF - extra info - length (seconds), title
    echo "http://".$SERVER_NAME.$SCRIPT_NAME.$url;
    echo "\n\n";
  }
  pclose($fp);
  exit;
}

function gotopath($path)
{
  global $pathprefix;
  $goto=$pathprefix.$path;
  if (!chdir($goto)) {
    echo "Err:C2:<b>chdir \"$goto\" failed</b><br>";
    chdir($pathprefix);
    $path=$pathprefix;
  };
}


//read & parse
function readdirfiles()
{

	global $lsaudio,$alldirs,$nd,$allfiles,$naf;
	global $bioidx,$infoidx,$folderimages,$photoidx;
	global $icon_generic, $icon_audio, $icon_image;

	$photoidx=0;

	$fp = popen ("$lsaudio", "r");
	$nadf=0;$nd=0;$naf=0;
	while ($buffer = fgets($fp, 4096)) {
		$x=explode("/",rtrim($buffer));
		if ($x[0]=="d")  {
		    $alldirs[$nd++]=$x[6];
		}
		else {
			$allfiles["size"][$naf]=$x[1];
			$allfiles["mins"][$naf]=$x[2];
			$allfiles["secs"][$naf]=$x[3];
			$allfiles["bitrate"][$naf]=$x[4];
			$allfiles["bitrateinfo"][$naf]=$x[5];
			$allfiles["fname"][$naf]=$x[6];
			$fname=$x[6];


		if (preg_match("#\.(mp3|ogg|mpc)#i",$fname)) {
			$allfiles["type"][$naf]="audio";
			$allfiles["icon"][$naf]=$icon_audio;
		}
		elseif (strstr($fname,".bio"))  {
			$bioidx=$naf;
			$allfiles["type"][$naf]="bio";
		}
		elseif (strstr($fname,".info"))  {
			$infoidx=$naf;
			$allfiles["type"][$naf]="info";
		}
		elseif (strstr($fname,"photo.jpg")) {
			$folderimages[$photoidx++]=$naf;
			$allfiles["type"][$naf]="folderimage";
		}
		elseif (preg_match("#\.(jpg|png|gif)$#i",$fname)) {
			$allfiles["type"][$naf]="image";
			$allfiles["icon"][$naf]=$icon_image;
		}
		else  {
			$allfiles["icon"][$naf]=$icon_generic;
			$allfiles["type"][$naf]="generic";
		}
		$naf++;
		}
	}
	pclose($fp);
}


function printbio($path)
{ 
	global $bioidx,$allfiles,$pathprefix;

	if (isset($bioidx)) {
		$bio=file_get_contents($allfiles["fname"][$bioidx]);
	}
	else {
		$files = glob($pathprefix.$path.'/.*.bio', GLOB_BRACE);
		$bio=file_get_contents($files[0]);
	}
	$bio=str_replace("{" , "<b>", $bio);
	$bio=str_replace("}" , "</b>", $bio);
	echo " <span id='cap'>$bio[0]</span>";
	echo substr($bio,1);
}

function printArtistInfo()
{
  global $infoidx,$allfiles;
  if (!isset($infoidx)) return;
  $fp=fopen($allfiles["fname"][$infoidx],"r");
  while (!feof($fp)) {
    $buffer = fgets($fp, 512);
    $x=explode(":",rtrim($buffer));
    if (!isset($x[1]) || !strlen($x[1])) continue;
    $x[1]=str_replace("," , ", ", $x[1]);
    if ($x[0]=="Decades") {
        $decs=explode("@",$x[1]);
        echo "<b>$x[0]:</b> ";
        for ($di=0,$decade=10;$di<count($decs);$di++,$decade+=10) {
			$decade=sprintf("%02d",$decade%100);
            if ($decs[$di]) 
				echo $decade."'s, "; 
        }
        echo "<br>";
    }//decades
    else
        echo "<p><b>$x[0]:</b> ".$x[1]."<br></p>";
   }
  fclose($fp);
}

function makeartistphotoarray()
{
	global $folderimages,$photoidx,$path,$allfiles;

	if (!count($folderimages)) return;
	echo "\n<script>\nPictures = new Array(";
	for ($i=0;$i<$photoidx;$i++) {
		$p=$allfiles["fname"][$folderimages[$i]];
		$url="?path=".urlencode($path)."&file=$p&action=sendfile";
		if ($i>0) echo ",\n";
		echo "\"$url\"";
	}
	echo ");\n</script>\n\n";
}


//get album photos of subdirectories
function get_subdirimages()
{
	global $nd, $alldirs,$path;
	$subdirimages=array();

	for ($i=1;$i<$nd;$i++) {
		if ($alldirs[$i]=="..") 
			continue;
		$dirurl="?action=listdir&amp;path=".urlencode($path."/".$alldirs[$i]);
		$p=$alldirs[$i]."/00photo.jpg";
		if (file_exists($p)){
			$img="<img class='th_albumimg' src='?path=".urlencode($path)."&file=".urlencode($p)."&action=sendfile'>";

		}
		else {
			//$img=$alldirs[$i];
			$img="<img class='th_albumimg' src='images/noalbumart.png'>";
		}

		$block="\n<div class='th_album'>";
		$block.="<div class='th_albumimgcontainer'>$img</div>";
		$block.="<div class='th_albumtitle'>{$alldirs[$i]}</div>";
		$block.="</div>\n";

		$subdirimages[]="<a title='".$alldirs[$i]."' class='albumlink' href='$dirurl'>$block</a>";
	}
	return $subdirimages;

}

function path2uris($path) {
	$path_parts = pathinfo($path);
	$xp=explode ("/",$path);
	$uriprefix="$SCRIPT_NAME?action=listdir&amp;path=";
	$uris=array();
	$prevpath="";

	array_push($uris,array('uri'=>$uriprefix."/",'name'=>"<span class='glyphicon glyphicon-home icon-home'></span>"));
	foreach ($xp as $p) {
		if (!strlen($p)) continue;
		$prevpath.="/".$p;
		$uri=$uriprefix.urlencode($prevpath);
		array_push($uris,array('uri'=>$uri,'name'=>$p));
	}
	return $uris;

}

//remove last directory from path
function cutlast($path)
{
  $x=explode("/",$path);
  if (count($x)) {
    unset($x[count($x)-1]); //remove last path component
    $x2=implode("/",$x);
    return $x2;
  }
  else 
    return ""; // no path components

}

//normalize /.././.// in path
function pathnorm($path) {
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = $part;
		}
	}
	return implode(DIRECTORY_SEPARATOR, $absolutes);
}

//
//$stmt=db_execute($dbh,$sql, array( 'code'=>$code));
//$res=$stmt->fetch(PDO::FETCH_ASSOC);
function db_execute($dbh,$sql,$params=array())
{
  global $errorstr,$errorbt,$errorno;

  $sth = $dbh->prepare($sql);
  $error = $dbh->errorInfo();

  if(((int)$error[0]||(int)$error[1]) && isset($error[2])) {
    $errorstr= "DB Error: ($sql): <br>\n".$error[2]."<br>\nParameters:".implode(",",$params);
    $errorbt= debug_backtrace();
    $errorno=$error[1]+$error[0];
        logerr("$errorstr BACKTRACE:".$errorbt);
    return 0;
  }

  if (count($params)) {
	  $sth->execute($params);
  }
  else {
	  $sth->execute();
  }

  $error = $sth->errorInfo();
  if(((int)$error[0]||(int)$error[1]) && isset($error[2])) {
    $errorstr= "DB Error: ($sql): <br>\n".$error[2]."<br>\nParameters:".implode(",",$params);
    $errorbt= debug_backtrace();
    $errorno=$error[1]+$error[0];
        logerr("$errorstr BACKTRACE:".$errorbt);
  }

  return $sth;
}


//no prepare
function db_exec($dbh,$sql,$skipauth=0,$skiphist=0,&$wantlastid=0) {
global $authstatus,$userdata, $remaddr, $dblogsize,$errorstr,$errorbt;

  if (!$skipauth && !$authstatus) {$errstr="<big><b>Not logged in</b></big><br>";return 0;}
  if (stristr($sql,"insert ")) $skiphist=1; //for lastid function to work.

  $r=$dbh->exec($sql);
  $error = $dbh->errorInfo();
  if($error[0] && isset($error[2])) {
    $errorstr= "<br><b>db_exec:db Error: ($sql): ".$error[2]."<br></b>";
    $errorbt = debug_backtrace();
    logerr("$errorstr BACKTRACE:".$errorbt);
    return 0;
  }
  $wantlastid=$dbh->lastInsertId();

  return $r;
} //db_exec

function logerr($err) {
        global $remaddr;
        $browser=$_SERVER['HTTP_USER_AGENT'];
        $phpbt=json_encode(debug_backtrace());

        openlog("avss", LOG_PID|LOG_PERROR , LOG_LOCAL3);
        syslog(LOG_INFO, "ERROR:$err, REMOTE_ADDRESS:$remaddr, BROWSER:$browser, PHPBACKTRACE:".$phpbt);
}

function track2lnk($track) {
	global $basem3u;

	$pathd=$pathprefix."/".$track['directory'];
	$pathf=$pathprefix."/".$track['filename'];
	$patha=$pathprefix."/".$track['directory']."/".$track['filename'];

	$lnkf="$basem3u?path=".urlencode($pathd)."&action=sendm3u"."&file=".urlencode($pathf);
	$lnkd="$SCRIPT_NAME?action=listdir&amp;path=".urlencode($pathd);

	$lnk="<a class='dir_lnk' href='$lnkd'>{$track['directory']}</a>/ ".
		 "<a class='audio_lnk' href='$lnkf'>{$track['filename']}</a>";
	return $lnk;
}


function showTrackResults($tracks) {
	//echo "<table class='table table-condensed table-bordered'>";
	echo "<table >";
	foreach ($tracks as  $idx=>$track) {
		echo "\n";
		echo "<tr><td>";
		echo track2lnk($track);
		echo "</td></tr>";
	}
	echo "</table>\n";
}

?>
