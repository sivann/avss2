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

<div class='container-fluid'> <!--outer -->

	<div class='row'> 
		<div class='col-md-3'>  <!-- left col -->

			<div class='row'>
				<div class="col-md-12 contentbox"> <!-- search -->
					<div class='content_boxtitle'>
						<span>Search</span>
					</div>

					<div class="input-group">
					  <div class="input-group-btn">
						<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
						  <li><a href="#">Action</a></li>
						  <li><a href="#">Another action</a></li>
						  <li><a href="#">Something else here</a></li>
						  <li class="divider"></li>
						  <li><a href="#">Separated link</a></li>
						</ul>
					  </div><!-- /btn-group -->
					  <input type="text" class="form-control input-sm">
					</div><!-- /input-group -->
				</div>
			</div>

			<div class='row'>
				<div id='folder_images' class='col-md-12 contentbox' > <!-- images -->
					<div class='content_boxtitle'>
						<span>Artist Photos</span>
					</div>
					<?  printfolderimages(); ?>
				</div>
			</div><!-- row -->

			<div class='row'>
				<div id='artistinfo'  class='col-md-12 contentbox'> <!-- artistinfo -->
					<div class='content_boxtitle'>
						<span>Artist Info</span>
					</div>
					<?
					printArtistInfo();
					?>
				</div>
			</div><!--row-->



		</div> <!-- left col-->

		<div class='col-md-9'>
			<?php require 'main_div_inc.php'; ?>
		</div><!-- right col div -->

	</div><!-- row -->

</div> <!--outer -->


</body>
</html>

