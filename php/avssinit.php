<?
//Sivann 2006-


require_once("avssconf.php");
require_once("avssfunc.php");
require_once("avssmodel.php");

set_include_path(get_include_path() . PATH_SEPARATOR . $avssdir);

$SCRIPT_NAME=$_SERVER['SCRIPT_NAME'];
$SERVER_NAME=$_SERVER['SERVER_NAME'];

if (isset($_GET['file'])) $file=$_GET['file'];
if (isset($_GET['path'])) $path=$_GET['path'];
if (isset($_GET['playdir'])) $playdir=$_GET['playdir'];


if (!isset($sess)) $sess="";
//$basem3u="http://".$SERVER_NAME.$SCRIPT_NAME."/stream.m3u";
$basem3u="http://mute.netmode.ece.ntua.gr/avss2/index.php/stream.m3u";
$userdir=$avssdir."/data/ipz/".$sess;

$datadir=$avssdir."/data";
$dbfile=$datadir."/avss.db";

//open db
try {
  $dbh = new PDO("sqlite:$dbfile");
  //$dbh = new PDO("sqlite:$dbfile", NULL, NULL, array(PDO::ATTR_PERSISTENT => TRUE));  
} 
catch (PDOException $e) {
  print "Open database Error!: " . $e->getMessage() . "<br>";
  die();
}

//load REGEXP function from sqlite3-pcre
//$sql=".load /usr/lib/sqlite3/pcre.so";
//db_exec($dbh,$sql); // DOES NOT WORK, cannot add sqlite extension through pdo



$time_start = microtime_float();


//check invalid path 
if (!isset($path) || empty($path)) $path="/";
else if (strstr($path,"..")) $path="/";
$path=str_replace("\'" , "'", $path);
$path=preg_replace("@/+@" , "/", $path);
$path = rtrim($path, '/');

if(get_magic_quotes_gpc()) {echo "Please disable magic quotes in .ini";}

$scriptdir=dirname( __FILE__ );

$base=$scriptdir; //dir of init.php
$sessbase="$base/sessions"; /* writeable from www user */


$servername=$_SERVER['SERVER_NAME'];
$scriptname=$_SERVER['SCRIPT_NAME'];


//for scripts including init.php from inside php/ directory:
if (basename($scriptdir)=="php") {
  $scriptdir=preg_replace("/\/php$/","",$scriptdir);
}

//wscriptdir: www relative address of base directory 
//used for cookie setting
$wscriptdir=dirname($_SERVER['SCRIPT_NAME']);
if (basename($wscriptdir)=="php") {
  $wscriptdir=preg_replace('#/php$#','',$wscriptdir);
}
if ($wscriptdir=="") $wscriptdir="/"; //installed under /

if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  $remaddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
else
  $remaddr=$_SERVER['REMOTE_ADDR'];

if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on"))
  $prot="https" ;
else
  $prot="http";

$fscriptdir="$prot://$servername".(dirname($scriptname));
$fscriptname="$prot://$servername$scriptname";
//$fuploaddirwww="$prot://$servername".dirname($scriptname)."/$uploaddirwww";


//login
$authstatus=1;

?>
