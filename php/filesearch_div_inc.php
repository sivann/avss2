<?php
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

$searchstring=$_GET['searchstring'];
$searchon=$_GET['searchon'];

switch ($searchon) {
	case 'filename':
		$res=searchTracksByStr($searchstring);
		showTrackResults($res);
		break;
	case 'directory':
		$res=searchDirectoriesByStr($searchstring);
		showTrackResults($res);
		break;
	case 'style':
		$res=searchStylesByStr($searchstring);
		showStyleResults($res);
		break;
	default:
		echo "Unknown search target";
}



?>
