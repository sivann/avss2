<?php
require($head);

gotopath($path);
readdirfiles(); //read&parse files and dirs
makeartistphotoarray(); //fill-in javascript photo table
$dirinfo=parsepath($path);


echo "<title>AudioPlayer :: ".basename($path)."</title>\n";
?>

</head>
<body>

<?php
if (isset($_GET['debug'])) print_r($dirinfo);
?>

<div id='outer'> <!--outer -->




	<div id='container1'> 
		<div id='search'  class='content_box'>
		<div class='content_boxtitle'>
			<span>Search</span>
		</div>
			<div class="input-group input-group-sm">
				<input class="form-control" placeholder="Search" id='searchbox'>
			</div>
		</div>

		<div id='folder_images' class='content_box' >
		<div class='content_boxtitle'>
			<span>Artist Photos</span>
		</div>
		<?  printfolderimages(); ?>
		</div>


		<div id='artistinfo'  class='content_box'>
		<div class='content_boxtitle'>
			<span>Artist Info</span>
		</div>
		<?
		printArtistInfo();
		?>
		</div>
	</div><!-- container1 -->

	<div id='main' >
		<?php require 'main_div_inc.php'; ?>
	</div><!-- main div -->


</div> <!--outer -->


</body>
</html>

