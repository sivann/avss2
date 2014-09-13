<?php
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

$searchstring=$_GET['searchstring'];


$res=searchTracksByStr($searchstring);
showTrackResult($res);

?>
