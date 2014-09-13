<?php

function searchTracksByStr($str) {
	global $dbh;

	$sql="SELECT * from track where filename GLOB :str LIMIT 1000";
	$stmt=db_execute($dbh,$sql, array( 'str'=>"*$str*"));
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	return $res;
}

function searchDirectoriesByStr($str) {
	global $dbh;

	$sql="SELECT * from track where directory GLOB :str LIMIT 1000";
	$stmt=db_execute($dbh,$sql, array( 'str'=>"*$str*"));
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	return $res;
}

//ordered by artist
function getAlbums($offset,$limit) {
	global $dbh;

	$sql="SELECT * from album,artist WHERE album.artistid=artist.id LIMIT $limit OFFSET $offset";
	$stmt=db_execute($dbh,$sql);
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	return $res;
}

?>
