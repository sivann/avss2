<?php
gotopath($path);
readdirfiles(); //read&parse all files
makeartistphotoarray(); //fill-in javascript photo table


echo "<!--\n   (c) sivann 2003-2012 \n-->\n";
echo "<html>\n<head>\n";
echo "<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\">\n";
echo "<title>AudioPlayer :: $path</title>\n";
?>

</head>
<body>

<div style='width:1100px;'> <!--outer -->

<div style='float:right;width:805px; border:2px solid gray;'> <!-- main div -->

<div style='float:left;width:800px; max-height:500px;overflow:auto; border:1px solid green;'>
<?
// print top link
echo "<div style='float:left;clear:both'>";
echo "<a href=\"$SCRIPT_NAME?action=listdir&amp;path=/\">Top</a>";
echo "</div>\n";

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
      <?  echo "<a href='fnfix/fnfix.php?action=listdir&amp;path=".urlencode($pathprefix.$path)."'><img src='$icon_tools' title='Fix Filenames'></a>"; ?>
    </div>
    <div style='width:50px;clear:both;text-align:center;'>
      <span><?  echo "<a class='smaller' href='fnfix/fnfix.php?action=listdir&amp;path=".urlencode($pathprefix.$path)."'>Fix</a>"; ?></span>
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

