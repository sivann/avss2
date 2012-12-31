<?
//Sivann 2006-

include("avssfunc.php");

$SCRIPT_NAME=$_SERVER['SCRIPT_NAME'];
$SERVER_NAME=$_SERVER['SERVER_NAME'];

if (isset($_GET['file'])) $file=$_GET['file'];
if (isset($_GET['path'])) $path=$_GET['path'];
if (isset($_GET['playdir'])) $playdir=$_GET['playdir'];


$lsaudio="/usr/local/bin/lsaudio";
$ls="/bin/ls";

$pathprefix="/MMROOT/audio/";
$icon_generic="/avss2/images/unknown.png";
$icon_audio="/avss2/images/play.png";
$icon_save="/avss2/images/download_m.png";
$icon_image="/avss2/images/image.png";
$icon_playall="/avss2/images/play32.png";
$icon_savedir="/avss2/images/folder_down.png";
$icon_dir="/avss2/images/folder_green.png";
$icon_playlist="/avss2/images/playlist.png";
$icon_tools="/avss2/images/fix.png";

if (!isset($sess)) $sess="";
$basem3u="http://".$SERVER_NAME.$SCRIPT_NAME."/stream.m3u";
$userdir=$avssdir."/data/ipz/".$sess;

validateuser($userdir);
$time_start = microtime_float();


//check invalid path 
if (!isset($path) || empty($path)) $path="/";
else if (strstr($path,"..")) $path="/";
$path=str_replace("\'" , "'", $path);
$path=preg_replace("@/+@" , "/", $path);

if(get_magic_quotes_gpc()) {echo "Please disable magic quotes in .ini";}

if (isset( $_GET['action']))
  $action=$_GET['action'];
else
  $action="";

switch($action) {
  case "sendm3u": sendm3u(); break;
  case "sendfile": sendfile(); break;
  case "savefile": savefile(); break;
  case "savedir": savedir(); break;
  case "playdir": senddirm3u(); break;
  default:
    if (strlen($action)) 
       echo "Unknown action '$action'\n<br>";
}



gotopath($path);

readdirfiles(); //read&parse all files

makeartistphotoarray(); //fill-in javascript photo table


echo "<!--\n   (c) sivann 2003-2012 \n-->\n";
echo "<html>\n<head>\n";
echo "<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\">\n";
echo "<title>AudioPlayer :: $path</title>\n";
?>

<style type="text/css">

#box {display:block; width:100%; line-height:normal; letter-spacing:1px; 
      font-family: times new roman, serif; font-size:16px; color:#000; 
      border:1px solid #ddd; padding:5px; margin:0em auto;}

#cap {font-size:50px; color:#f60; font-weight:bold; float:left; height:34px; 
      line-height:34px; margin-top:2px; margin-right:1px;}

* {
  font-family: Arial;
  font-size: 10pt;
  }

.small {
  font-size:0.9em;
}

.smaller {
  font-size:0.8em;
}

div {
  padding:0;
  margin:0;
  /*border:1px solid red;*/
}

a {
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

</style>

</head>
<body>

<div style='width:1100px;'> <!--outer -->

<div style='float:right;width:805px; border:2px solid gray;'> <!-- main div -->

<div style='float:left;width:800px; max-height:500px;overflow:auto; border:1px solid green;'>
<?
// print top link
echo "<div style='float:left;clear:both'>";
echo "<a href=\"$SCRIPT_NAME?path=/\">Top</a>";
echo "</div>\n";

// print directories
for ($i=0;$i<$nd;$i++) {
  if ($i%2) $col="#efefef"; else $col="#fefefe";
  if ($alldirs[$i]==".") continue;
  if ($alldirs[$i]=="..") 
    $lnk="$SCRIPT_NAME?path=".cutlast($path);
  else 
    $lnk="$SCRIPT_NAME?path=".  
         str_replace("%2F","/",urlencode(("$path/$alldirs[$i]")));

  $lnk=preg_replace("@/+@" , "/", $lnk);

  echo "<div style='float:left;clear:both'>";
  echo "<a href=\"$lnk\">";
  if ($alldirs[$i]=="..") 
    echo "<img border=0 src=\"/icons/small/back.gif\">&nbsp;";
  else 
    echo "<img style='vertical-align:bottom' border=0 src='$icon_dir'>&nbsp;";

  echo $alldirs[$i]."</a>";

  echo "</div>\n";
}//for

?>
</div>

<div style='float:left;width:800px; max-height:500px;overflow:auto; border:1px solid blue;'>

<?
// print files
for ($j=0;$j<$naf;$j++) {
  if (strstr($allfiles["fname"][$j],".php") 
  //|| strstr($allfiles["fname"][$j],".txt")
  ) continue;

  if ($j%2) $col="#E1F2FF"; else $col="#fefefe";

  $size=$allfiles["size"][$j];

  $isaudio=0;

  if ($allfiles["fname"][$j][0]==".")  //.bio .info
    continue;

  if (isset ($allfiles["type"][$j]) && $allfiles["type"][$j]=="folderimage") //00photo...
    continue;

  if (isset ($allfiles["type"][$j]) && $allfiles["type"][$j]=="audio") 
    $isaudio=1; 

  if (isset($allfiles["icon"][$j])) 
    $icon=$allfiles["icon"][$j];

  if ($isaudio) {
    $bps=$allfiles["bitrate"][$j];
    $bpsinfo=$allfiles["bitrateinfo"][$j];

    if ($bpsinfo=="VBR") 
      $bpsstr="<span style='background-color:#efefef'>[$bps] VBR</span>";
    elseif ($bps<128) 
      $bpsstr="<span style='background-color:#0c0c0c;color:white'>[$bps]</span>";
    elseif ($bps==128) 
      $bpsstr="<span style='background-color:#5BBDFF'>[$bps]</span>";
    elseif ($bps==160) 
      $bpsstr="<span style='background-color:lightgreen'>[$bps]</span>";
    elseif ($bps==192) 
      $bpsstr="<span style='background-color:orange'>[$bps]</span>";
    elseif ($bps>192) 
      $bpsstr="<span style='background-color:pink'>[$bps]</span>";
  }
  else
    $bpsstr="";

  echo "<div style='float:left;clear:both;background-color:$col'>";

  //save icon
  echo "<div style='float:left;width:25px'>";
  echo "<a href=\"?path=".urlencode($path).
    "&action=savefile".
    "&file=".urlencode($allfiles["fname"][$j])."\">".
    "<img border=0 title='Save Track' src='$icon_save'></a></td>\n";
  echo "</div>\n";

  //view/play icon
  echo "<div style='float:left;width:25px'>";
  if ($isaudio) {
    echo "<a href=\"$basem3u?path=".urlencode($path).
      "&action=sendm3u".
      "&file=".urlencode($allfiles["fname"][$j]).
      "\">".
      "<img border=0 title='Play Track' src='$icon'></a></td>\n";
  }
  else {
    echo "<a href=\"?path=".urlencode($path).
      "&action=sendfile".
      "&file=".urlencode($allfiles["fname"][$j]).
      "\">".
      "<img border=0 src='$icon'></a></td>\n";
  }
  echo "</div>\n";


  //view/play named link
  echo "<div style='float:left;width:500px;'>";
  echo $allfiles["fname"][$j];
  echo "</div>\n";


  if ($isaudio) 
    $duration="(".$allfiles["mins"][$j].":".$allfiles["secs"][$j].") ";
  else
    $duration="";

  echo "<div style='float:left;width:80px'>";
  echo $duration.(int)($size/(1024*1024))."Mb </td>";
  echo "</div>\n";

  echo "<div style='float:left;width:100px'>";
  echo $bpsstr;
  echo "</div>\n";

  echo "</div>\n\n";

} //for (print files)

?>
</div>

<?
$time_end = microtime_float();
$time_elapsed = round($time_end - $time_start, 3);
?>

<div style='float:left;width:800px; max-height:50px; border:1px solid red;'>

  <div style='float:left;margin-right:10px;'>
    <div style='width:50px;clear:both;text-align:center'>
      <?  echo "<a href='$basem3u?path=".urlencode($path)."&action=playdir'><img src='$icon_playall' title='Play Dir'></a>"; ?>
    </div>
    <div style='width:50px;clear:both;text-align:center;'>
      <span><?  echo "<a class='smaller' href='$basem3u?path=".urlencode($path)."&action=playdir'>Play All</a>"; ?></span>
    </div>
  </div>

  <div style='float:left;margin-right:10px;'>
    <div style='width:50px;clear:both;text-align:center'>
      <?  echo "<a href='$basem3u?path=".urlencode($path)."&action=savedir'><img src='$icon_savedir' title='Get Files'></a>"; ?>
    </div>
    <div style='width:50px;clear:both;text-align:center;'>
      <span><?  echo "<a class='smaller' href='$basem3u?path=".urlencode($path)."&action=savedir'>Get Files</a>"; ?></span>
    </div>
  </div>


  <div style='float:left;margin-right:10px;'>
    <div style='width:50px;clear:both;text-align:center'>
      <?  echo "<a href='playlist.php'><img src='$icon_playlist' title='Open Playlist'></a>"; ?>
    </div>
    <div style='width:50px;clear:both;text-align:center;'>
      <span><?  echo "<a class='smaller' href='playlist.php'>Playlist</a>"; ?></span>
    </div>
  </div>

  <div style='float:left;margin-right:10px;'>
    <div style='width:50px;clear:both;text-align:center'>
      <?  echo "<a href='fnfix/fnfix.php?path=".urlencode($pathprefix.$path)."'><img src='$icon_tools' title='Fix Filenames'></a>"; ?>
    </div>
    <div style='width:50px;clear:both;text-align:center;'>
      <span><?  echo "<a class='smaller' href='fnfix/fnfix.php?path=".urlencode($pathprefix.$path)."'>Fix</a>"; ?></span>
    </div>
  </div>


</div>


<div style='float:left;width:800px; max-height:300px;overflow:auto; border:1px solid pink;'>
<?

//print album dirs here
$x=explode("/",$path); //find our depth below permanent
if (($x[1]=="permanent") && (count($x)==4)){
  $ap_r=get_subdirimages();
  foreach ($ap_r as $k=>$ap){
    echo $ap;
    if (!(($k+1) % 8)) echo "<br>";
  }
}
//biography
?>
</div>


<div style='float:left;width:800px; max-height:400px;overflow:auto; border:1px solid orange;'>
<?  printbio(); ?>
</div>

</div><!-- main div -->

<div style='float:left;width:250px;border:2px solid red'> <!-- narrow column -->

  <div style='float:left;width:100%; max-height:250px;overflow:auto; border:1px solid skyblue;'>
  <?  printfolderimages(); ?>
  </div>


  <div style='float:left;width:100%; max-height:850px;overflow:auto; border:1px solid lightgreen;'>
  <?
  //info column (genre, etc)
  printinfo();
  ?>
  </div>
</div><!-- narrow column -->

</div> <!--outer -->

<div style='float:left;clear:both;border:1px solid #888;'>>
<?
echo getcwd();
echo "<br>PP: $pathprefix";
echo "<br>PA: $path";
echo "<br>BN: ".basename($path."/");
?>
</div>


</body>
</html>


<?

function printfolderimages() {
  global $path,$folderimages,$photoidx,$allfiles;

  if (!count($folderimages)) return;

  $p0=$allfiles["fname"][$folderimages[0]];
  $url="?path=".urlencode($path)."&file=".urlencode($p0)."&action=sendfile";
  echo "\n<img id='photoimg' src='$url'><br>";
  for ($i=0;$i<$photoidx;$i++) {
    echo "\n<a href='javascript:showimage(\"$i\");'>$i</a>";
  }

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
  fclose($fp);

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
  global $path,$pathprefix,$SERVER_NAME,$SCRIPT_NAME,$ls;

  if (!chdir($pathprefix.$path)) {
    echo "Err:C1:<b>chdir \"$pathprefix.$path\" failed</b><br>";
    exit;
  };
  header ("Content-type: audio/mpeg-url");
  echo "#EXTM3U\n";

  $fp = popen ($ls, "r");
  while ($buffer = fgets($fp, 1024)) {
    $buffer=rtrim($buffer);
    if (!preg_match("#\.(mp3|ogg|mpc)$#i",$buffer)) continue;

    $url="?path=".rawurlencode($path)."&file=".rawurlencode($buffer)."&action=sendfile";

    echo "#EXTINF:, $buffer\n"; //format: #EXTINF - extra info - length (seconds), title
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

function printbio()
{ 
  global $bioidx,$allfiles;
  if (isset($bioidx)) {
    //readfile($allfiles["fname"][$bioidx]);
    $bio=file_get_contents($allfiles["fname"][$bioidx]);
    $bio=str_replace("{" , "<b>", $bio);
    $bio=str_replace("}" , "</b>", $bio);
    echo " <span id='cap'>$bio[0]</span>";
    echo substr($bio,1);
  }
  else echo "No Bio";
}

function printinfo()
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
      echo "<b>$x[0]:</b>";
      for ($di=0,$decade=10;$di<count($decs);$di++,$decade+=10) {
	$decade=sprintf("%02d",$decade%100);
        if ($decs[$di]) echo $decade."'s, "; 
      }
      echo "<br>";
    }//decades
    else
      echo "<b>$x[0]:</b>".$x[1]."<br>";
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
    $p=$alldirs[$i]."/00photo.jpg";
    if (file_exists($p)){
      $url="?path=".urlencode($path)."&file=".urlencode($p)."&action=sendfile";
      $dirurl="?path=".urlencode($path."/".$alldirs[$i]);
      $subdirimages[]="<a href='$dirurl'><img height=100 width=100 src='$url'></a>";
      //if (!($i % 8)) echo "<br>";
    }
  }
  return $subdirimages;

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

?>
