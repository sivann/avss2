<?php
require($head);


echo "<title>AudioPlayer :: ".basename($path)."</title>\n";
?>

</head>
<body>

<?php
if (isset($_GET['debug'])) print_r($pathtype);

if ($action=='listdir') {
	$pathdata=readdirfiles($path);
}
else
	$pathdata=getEmptypathdata();

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
						  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span id='searchontxt'>Filename</span> <span class="caret"></span></button>
						  <ul class="dropdown-menu" role="menu">
							  <li><a id='searchOnFilename' href="#">Filename</a></li>
							  <li><a id='searchOnDirectory' href="#">Directory only</a></li>
							  <li><a id='searchOnStyle' href="#">Style</a></li>
							  <li class="divider"></li>
							  <li><a href="#">Album</a></li>
						  </ul>
						</div><!-- /btn-group -->
						<form method=GET action='?action=search' id='searchfrm'>
						<?php
						if (isset($_REQUEST['searchstring']))
							$searchstring=$_REQUEST['searchstring'];
						else
							$searchstring='';
						?>
						<input name='searchstring' type="text" class="form-control input-sm" id='searchbox' value='<?=$searchstring?>'>
						<input type=hidden id='searchon' name='searchon' value='filename'>
						<input type=hidden name='action' value='search'>
						</form>
					</div><!-- /input-group -->
				</div>
			</div>


			<div class='row'>
				<div id='mode' class='col-xs-12 contentbox' > <!-- web audio player (jplayer) -->
					<div class='content_boxtitle'>
						<span>Player</span>
					</div>

					<div class="btn-group btn-group-xs" data-toggle="buttons" id=playertype >
					  <label class="btn btn-default active" id="playertypem3u" title='Call an associated M3U player (VLC,Winamp)'>
						<input type="radio" name="playertype" value='m3u'> M3U
					  </label>
					  <label class="btn btn-default" id='playertypeweb' title='Play through the embedded HTML5/Flash player'>
						<input type="radio" name="playertype" value='web'> WEB
					  </label>
					</div>

					<?php require 'jplayer_div_inc.php';  ?>
				</div>
			</div><!-- row -->


			<div class='row'>
				<div id='mode' class='col-xs-12 contentbox' > <!-- mode -->
					<div class='content_boxtitle'>
						<span>Browse by</span>
					</div>
					<A href="?action=listdir&path=<?=$path?>">File</a>
					<A href="?action=liststyles">Style</a>
					<A href="?action=listalbums">Album</a>
				</div>
			</div><!-- row -->


			<div class='row'>
				<div id='folder_images' class='col-xs-12 contentbox' > <!-- images -->
					<div class='content_boxtitle'>
						<span>Artist Photos</span>
					</div>
					<?  printfolderimages($pathdata); ?>
				</div>
			</div><!-- row -->

			<div class='row'>
				<div id='artistinfo'  class='col-xs-12 contentbox'> <!-- artistinfo -->
					<div class='content_boxtitle'>
						<span>Artist Info</span>
					</div>
					<?
					printArtistInfo($pathdata);
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
				case 'search': 
					require 'search_div_inc.php'; 
					break;
				case 'albumbrowser': 
					require 'albumbrowser_div_inc.php'; 
					break;
				default:
					echo "Not implemented: $mode";
					break;
			}
			?>
		</div><!-- main col div -->

	</div><!-- row -->

</div> <!--outer -->


<?  
$time_end = microtime_float();
$time_elapsed = round($time_end - $time_start, 3); 
echo "<!-- elapsed: $time_elapsed -->";
echo "<pre>";
print_r($_SESSION);
print_r($_COOKIE);
echo "</pre>";
?>  

<form style="display: hidden" action="/url" method="POST" id="theform">
<input type="hidden" id="var1" name="var1" value=""/>
</form>

</body>
</html>

