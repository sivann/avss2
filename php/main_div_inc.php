
		<ol class='breadcrumb' id='curdir'>
		<?php 
		$uris=path2uris($path);
		foreach ($uris as $uri) {
			echo "\t<li><a href='{$uri['uri']}'>{$uri['name']}</a></li>\n";
		}
		?>
		</ol>

		<div id='directories' >

		<?
		// print directories

		// print go back link: ..
		$lnk="$SCRIPT_NAME?action=listdir&amp;path=".cutlast($path);
	    $lnk=preg_replace("@/+@" , "/", $lnk);
		echo "\t<div class='dir_row'><a href='$lnk'>";
		echo "<img border=0 src='$icon_back'>&nbsp;..";
		echo "</a></div>\n";

		for ($i=0;$i<$nd;$i++) {
		  if ($i%2) $col="#efefef"; else $col="#fefefe";
		  if ($alldirs[$i]==".") continue;
		  if ($alldirs[$i]=="..") continue;

		  $lnk="$SCRIPT_NAME?action=listdir&amp;path=".  
			   str_replace("%2F","/",urlencode(("$path/$alldirs[$i]")));

		  $lnk=preg_replace("@/+@" , "/", $lnk);
		  echo "\t<div class='dir_row'>";
		  echo "<a href='$lnk'>";
		  echo "$icon_dir";
		  echo "<span class='glyphicon glyphicon-folder-close icon-folder'></span>";
		  echo $alldirs[$i]."</a>";
		  echo "\t</div>\n";
		}//for

		?>
		</div>



		<div id='files' >
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
			  $bpsstr="<span class='badge bitrate_vbr'>$bps VBR</span>";
			elseif ($bps<128) 
			  $bpsstr="<span class='badge bitrate_1'>$bps</span>";
			elseif ($bps==128) 
			  $bpsstr="<span class='badge bitrate_2'>$bps</span>";
			elseif ($bps==160) 
			  $bpsstr="<span class='badge bitrate_3'>$bps</span>";
			elseif ($bps==192) 
			  $bpsstr="<span class='badge bitrate_4'>$bps</span>";
			elseif ($bps>192) 
			  $bpsstr="<span class='badge bitrate_5'>$bps</span>";
		  }
		  else
			$bpsstr="";

		  echo "<div class='file_row'>";

		  //SAVE ICON
		  echo "<div class='file_save'>";
		  echo "<a href=\"?path=".urlencode($path).
			"&action=savefile".
			"&file=".urlencode($allfiles["fname"][$j])."\">".
			"$icon_save".
			"</a></td>\n";
		  echo "</div>\n";

		  //VIEW/PLAY ICON
		  echo "<div class='file_play'>";
		  if ($isaudio) {
			$href="$basem3u?path=".urlencode($path)."&action=sendm3u"."&file=".urlencode($allfiles["fname"][$j]);
			  echo "<a title='Play Track' href=\"$href\">".
			  "$icon".
			  "</a></td>\n";
		  }
		  else {
			$href="?path=".urlencode($path)."&action=sendfile"."&file=".urlencode($allfiles["fname"][$j]);
			echo "<a href=\"$href\">$icon</a></td>\n";
		  }
		  echo "</div>\n";


		  //view/play named link
		  echo "<div class='file_name'>";
		  echo "<a href=\"$href\">";
		  echo $allfiles["fname"][$j];
		  echo "</a>";
		  echo "</div>\n";


		  if ($isaudio) 
			$duration="(".$allfiles["mins"][$j].":".$allfiles["secs"][$j].") ";
		  else
			$duration="";

		  echo "<div class='file_length'>";
		  echo $duration.(int)($size/(1024*1024))."MB </td>";
		  echo "</div>\n";

		  echo "<div class='file_bitrate'>";
		  echo $bpsstr;
		  echo "</div>\n";

		  echo "</div>\n\n"; //file row

		} //for (print files)

		?>
		</div><!-- files -->




	<?
	$time_end = microtime_float();
	$time_elapsed = round($time_end - $time_start, 3);
	?>

		<div id='files_toolbar'>

		  <div class='toolbar_item'>
			<div >
			  <?  echo "<a href='$basem3u?path=".urlencode($path)."&action=playdir'><img src='$icon_playall' title='Play Dir'></a>"; ?>
			</div>
			<div >
			  <span><?  echo "<a class='smaller' href='$basem3u?path=".urlencode($path)."&action=playdir'>Play All</a>"; ?></span>
			</div>
		  </div>

		  <div class='toolbar_item'>
			<div >
			  <?  echo "<a href='$basem3u?path=".urlencode($path)."&action=savedir'><img src='$icon_savedir' title='Get Files'></a>"; ?>
			</div>
			<div >
			  <span><?  echo "<a class='smaller' href='$basem3u?path=".urlencode($path)."&action=savedir'>Get Files</a>"; ?></span>
			</div>
		  </div>


		  <div class='toolbar_item'>
			<div >
			  <?  echo "<a href='playlist.php'><img src='$icon_playlist' title='Open Playlist'></a>"; ?>
			</div>
			<div >
			  <span><?  echo "<a class='smaller' href='playlist.php'>Playlist</a>"; ?></span>
			</div>
		  </div>

		  <div class='toolbar_item'>
			<div >
			  <?  echo "<a href='php/fnfix/fnfix.php?action=listdir&amp;path=".urlencode($pathprefix.$path)."'><img src='$icon_tools' title='Fix Filenames'></a>"; ?>
			</div>
			<div >
			  <span><?  echo "<a class='smaller' href='php/fnfix/fnfix.php?action=listdir&amp;path=".urlencode($pathprefix.$path)."'>Fix</a>"; ?></span>
			</div>
		  </div>


		</div>


		<div class='album_dirs'>
	<?

	//print album dirs here
	$x=explode("/",$path); //find our depth below permanent
	//if (($x[1]=="permanent") && (count($x)==4)){
	if ($dirinfo['isartist']) {

	  $ap_r=get_subdirimages();
	  //echo "lala;"; print_r($ap_r);
	  echo "<div id='album_images'>";
	  foreach ($ap_r as $k=>$ap){
		echo $ap;
	  }
	  echo "</div>\n";
	}
	?>
		</div>


		<?  
		if ($dirinfo['isartist'] || $dirinfo['isalbum']) {
			echo "\n<div id='bio' >";
			printbio($dirinfo['datapath']); 
			echo "</div>\n";
		}
		?>
