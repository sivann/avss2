<?php
require("avssinit.php");

//play all (m3u)
//called by: javascript playFiles()

$d=$_POST['var1'];
$d_r=json_decode($d,true);

getFiles_m3u($d_r['files'],$d_r['order']);



?>
