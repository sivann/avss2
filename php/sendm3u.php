<?php
session_start();
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

 sendm3u();
?>
