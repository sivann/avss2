<?php
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

$searchstring=$_GET['searchstring'];
$searchon=$_GET['searchon'];

switch ($searchon) {
	case 'filename':
		$res=searchTracksByStr($searchstring);
		break;
	case 'directory':
		$res=searchDirectoriesByStr($searchstring);
		break;
	case 'style':
		$res=searchStylesByStr($searchstring);
		break;
	default:
		echo "Unknown search target";
}



?>

        <div id='searchresults'>
		<?php
		showTrackResults($res);
		?>
		</div><!-- searchresults -->
