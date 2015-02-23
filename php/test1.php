<?php

require("avssinit.php");
ini_set('display_errors',1);
error_reporting(E_ALL);

function sendtest($fromoffset=0)
{
global $file,$path,$pathprefix,$_SERVER;
  $file=str_replace("\'" , "'", $file);

  if (strstr($file,".jpg")) {
    $fn=$pathprefix.$path."/".$file;
    $sz=filesize($fn);
    header ("Content-type: image/jpeg");
    header ("Content-Length: $sz");
    readfile($fn);
    exit;
  }
  elseif (!preg_match("#\.(mp3|ogg|mpc|jpg|txt|png|gif|html)$#i",$file)){
    header("Content-Type: text/plain; charset=utf-8");
    echo "sendfile:$file cannot open unknown file<br>";
    exit;
  }

  $fn=$pathprefix.$path."/".$file;
  $sz=filesize($fn);


  if (stristr($file,".ogg"))
    header ("Content-type: audio/ogg");
  elseif (stristr($file,".mp3"))
    header ("Content-type: audio/mpeg");
  elseif (stristr($file,".mpc"))
    header ("Content-type: audio/mpc");
  elseif (stristr($file,".txt")) {
    header("Content-Type: text/plain; charset=utf-8");
  }

  //header ("Connection: close");

  $bfn=basename($fn);

  //header("icy-name: $bfn");
  //header("icy-pub:0");

  //readfile($fn);
  //exit;

  $fd = fopen($fn, "rb");
  if ( !$fd ) {
		header ("Content-type: text/plain");
	    echo 'fopen failed. reason: ', $php_errormsg;
		exit(1);
  }

  if (isset($_SERVER['HTTP_RANGE'])) {
    $seekoffset=intval(substr($_SERVER['HTTP_RANGE'],6));
    fseek($fd,$seekoffset,SEEK_SET);

	file_put_contents("/tmp/xx",$seekoffset);
	$sz0=$sz-1;

    $t=ftell($fd);
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    header("Content-Length: ".($sz-$seekoffset));
    header("Content-Range: bytes $seekoffset-$sz0/$sz");
  }
  else
    header ("Content-Length: $sz");


  while (!feof($fd)) {
    $t=ftell($fd);
    echo fread($fd, 131072);
  }
  fclose($fd);

  exit;
}


 sendtest()

?>
