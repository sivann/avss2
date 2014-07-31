<?
//Sivann 2006-

require_once("avssconf.php");
require_once("avssfunc.php");

$SCRIPT_NAME=$_SERVER['SCRIPT_NAME'];
$SERVER_NAME=$_SERVER['SERVER_NAME'];

if (isset($_GET['file'])) $file=$_GET['file'];
if (isset($_GET['path'])) $path=$_GET['path'];
if (isset($_GET['playdir'])) $playdir=$_GET['playdir'];


/*
$lsaudio="/usr/local/bin/lsaudio";
$ls="/bin/ls";

$pathprefix="/MMROOT/audio/";
$icon_generic="/avss2/images/unknown.png";
$icon_audio="/avss2/images/play.png";
$icon_save="/avss2/images/download_m.png";
$icon_image="/avss2/images/image.png";
$icon_playall="/avss2/images/play32.png";
$icon_savedir="/avss2/images/folder_down.png";
$icon_dir="/avss2/images/folder_green.png";
$icon_playlist="/avss2/images/playlist.png";
$icon_tools="/avss2/images/fix.png";
*/

if (!isset($sess)) $sess="";
$basem3u="http://".$SERVER_NAME.$SCRIPT_NAME."/stream.m3u";
$userdir=$avssdir."/data/ipz/".$sess;

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

?>
