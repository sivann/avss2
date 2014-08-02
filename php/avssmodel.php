<?php

function searchTracksByStr($str) {
	global $dbh;

	$sql="SELECT * from track where filename GLOB :str LIMIT 1000";
	$stmt=db_execute($dbh,$sql, array( 'str'=>"*$str*"));
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	return $res;
}

?>
