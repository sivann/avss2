<?php
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

$offset=0;
$limit=20;
$res=getAlbums($offset,$limit);
print_r($res);
//showPhotoResult($res);

?>
