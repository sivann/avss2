<?php
require("avssinit.php");
if (!$authstatus) { echo "not logged in"; exit; }

$searchstring=$_GET['searchstring'];
$searchon=$_GET['searchon'];

switch ($searchon) {
	case 'filename':
		$res_r=searchTracksByStr($searchstring);
		break;
	case 'directory':
		$res_r=searchDirectoriesByStr($searchstring);
		break;
	case 'style':
		$res_r=searchStylesByStr($searchstring);
		break;
	default:
		echo "Unknown search target";
}



?>

        <div id='searchresults'>
		<?php
		$nres=count($res_r['result']);
		echo "$nres results<br>";
		if (strlen($res_r['msg']))
			echo $res_r['msg']."<br>";
		showTrackResults($res_r['result']);
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
