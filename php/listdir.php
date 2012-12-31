<?php
require($head);

gotopath($path);
readdirfiles(); //read&parse all files
makeartistphotoarray(); //fill-in javascript photo table

echo "<title>AudioPlayer :: ".basename($path)."</title>\n";
?>

</head>
<body>

<div id='outer'> <!--outer -->

<div id='main' >

<div id='directories' >

<div class='toplink'>
<a href="<?=$SCRIPT_NAME?>?action=listdir&amp;path=/">Top</a>
</div>

<?
// print directories
for ($i=0;$i<$nd;$i++) {
  if ($i%2) $col="#efefef"; else $col="#fefefe";
  if ($alldirs[$i]==".") continue;
  if ($alldirs[$i]=="..") 
    $lnk="$SCRIPT_NAME?action=listdir&amp;path=".cutlast($path);
  else 
    $lnk="$SCRIPT_NAME?action=listdir&amp;path=".  
         str_replace("%2F","/",urlencode(("$path/$alldirs[$i]")));

  $lnk=preg_replace("@/+@" , "/", $lnk);

  echo "<div class='dir_row'>";
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

  echo "<div class='file_row'>";

  //save icon
  echo "<div class='file_save'>";
  echo "<a href=\"?path=".urlencode($path).
    "&action=savefile".
    "&file=".urlencode($allfiles["fname"][$j])."\">".
    "<img border=0 title='Save Track' src='$icon_save'></a></td>\n";
  echo "</div>\n";

  //view/play icon
  echo "<div class='file_play'>";
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
  echo "<div class='file_name'>";
  echo $allfiles["fname"][$j];
  echo "</div>\n";


  if ($isaudio) 
    $duration="(".$allfiles["mins"][$j].":".$allfiles["secs"][$j].") ";
  else
    $duration="";

  echo "<div class='file_length'>";
  echo $duration.(int)($size/(1024*1024))."Mb </td>";
  echo "</div>\n";

  echo "<div class='file_bitrate'>";
  echo $bpsstr;
  echo "</div>\n";

  echo "</div>\n\n"; //file row

} //for (print files)

?>
</div>




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
      <?  echo "<a href='fnfix/fnfix.php?action=listdir&amp;path=".urlencode($pathprefix.$path)."'><img src='$icon_tools' title='Fix Filenames'></a>"; ?>
    </div>
    <div >
      <span><?  echo "<a class='smaller' href='fnfix/fnfix.php?action=listdir&amp;path=".urlencode($pathprefix.$path)."'>Fix</a>"; ?></span>
    </div>
  </div>


</div>


<div class='album_dirs'>
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


<div class='bio' >
<?  printbio(); ?>
</div>

</div><!-- main div -->



<div id='datacontainer'> 

  <div id='folder_images' >
  <?  printfolderimages(); ?>
  </div>


  <div id='artistinfo' >
  <?
  //info column (genre, etc)
  printinfo();
  ?>
  </div>
</div><!-- narrow column -->

</div> <!--outer -->



</body>
</html>

