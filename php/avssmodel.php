<?php

function searchTracksByStr($str) {
	global $dbh;
	$msg="";

	if (strlen($str) < 2) {
		$msg="Search string too small";
		return array('result'=>array(),'msg'=>$msg);
	}

	//$sql="SELECT * from track where filename GLOB :str LIMIT 1000";
	$sql="SELECT * from track where id in (SELECT docid FROM track_fts WHERE track_fts MATCH :str LIMIT 100000)";
	$stmt=db_execute($dbh,$sql, array( 'str'=>"*$str*"));
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);

	return array('result'=>$res,'msg'=>$msg);
}

function searchDirectoriesByStr($str) {
	global $dbh;

	if (strlen($str) < 2) {
		$msg="Search string too small";
		return array('result'=>array(),'msg'=>$msg);
	}
	//$sql="SELECT * from track where directory GLOB :str LIMIT 1000";
	$sql="SELECT * from track where id in (SELECT docid FROM track_fts WHERE track_fts.directory MATCH :str LIMIT 100000)";
	$stmt=db_execute($dbh,$sql, array( 'str'=>"*$str*"));
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	return array('result'=>$res,'msg'=>$msg);
}

//ordered by artist
function getAlbums($offset,$limit) {
	global $dbh;

	if (strlen($str) < 2) {
		$msg="Search string too small";
		return array('result'=>array(),'msg'=>$msg);
	}
	$sql="SELECT * from album,artist WHERE album.artistid=artist.id LIMIT $limit OFFSET $offset";
	$stmt=db_execute($dbh,$sql);
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	return array('result'=>$res,'msg'=>$msg);
}

?>
