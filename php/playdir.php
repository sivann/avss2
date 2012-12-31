<?php
session_start();
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

senddirm3u();
?>
