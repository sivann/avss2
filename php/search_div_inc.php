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
		<?php
		//echo "<pre>"; print_r($res);
		?>

		<div id='files_toolbar'>

		  <div id='playall_searchresults' class='toolbar_item'>
			<div >
				<span title='Play All' class='glyphicon glyphicon-play icon-play-big'></span>
			</div>
		  </div>


		</div>
