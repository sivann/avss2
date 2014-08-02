<?php
require($head);


echo "<title>AudioPlayer :: ".basename($path)."</title>\n";
?>

</head>
<body>

<?php
if (isset($_GET['debug'])) print_r($dirinfo);
?>

<div class='container' style='width:1200px'> <!--outer -->

	<div class='row'> 
		<div class='col-xs-3'>  <!-- left col -->
			<div class='row'>
				<div class="col-xs-12 contentbox"> <!-- search -->
					<div class='content_boxtitle'>
						<span>Search</span>
					</div>

					<div class="input-group">
						<div class="input-group-btn">
						  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Files <span class="caret"></span></button>
						  <ul class="dropdown-menu" role="menu">
							  <li><a href="#">Files</a></li>
							  <li><a href="#">Styles</a></li>
							  <li class="divider"></li>
							  <li><a href="#">Photos</a></li>
						  </ul>
						</div><!-- /btn-group -->
						<form id='searchfrm'>
						<input type="text" class="form-control input-sm" id='searchbox'>
						</form>
					</div><!-- /input-group -->
				</div>
			</div>

			<div class='row'>
				<div id='folder_images' class='col-xs-12 contentbox' > <!-- images -->
					<div class='content_boxtitle'>
						<span>Artist Photos</span>
					</div>
					<?  printfolderimages(); ?>
				</div>
			</div><!-- row -->

			<div class='row'>
				<div id='artistinfo'  class='col-xs-12 contentbox'> <!-- artistinfo -->
					<div class='content_boxtitle'>
						<span>Artist Info</span>
					</div>
					<?
					printArtistInfo();
					?>
				</div>
			</div><!--row-->



		</div> <!-- left col-->

		<div id='maincol' class='col-xs-9'> <!-- main col -->
			<?php 
			switch ($mode) {
				case 'filebrowser': 
					require 'filebrowser_div_inc.php'; 
					break;
				default:
					echo "Not implemented: $mode";
					break;
			}
			?>
		</div><!-- main col div -->

	</div><!-- row -->

</div> <!--outer -->


</body>
</html>

