<?php
/* sivann 2012 */

/* Front Controller */

ini_set('session.gc_maxlifetime', 36000);
session_start(); 

require 'php/avssinit.php';
$initok=1;

//require ("php/model.php");

if (!isset($_GET['action']))
  $_GET['action']="";
else {
  $_GET['action']=str_replace("/","",$_GET['action']);
  $_GET['action']=str_replace("%","",$_GET['action']);
  $_GET['action']=str_replace(";","",$_GET['action']);
}

if (!isset($_GET['path'])) {
	$_SESSION['path']=$_GET['path'];
}

$head="";
$req="";

$authstatus=1;

/*
$authstatus=1;
$_SESSION['loggedin']=1;
$_SESSION['username']='sivann' ;
$_SESSION['userid']=8 ;
$_SESSION['time']=1356355855 ;
*/

header('X-UA-Compatible: IE=edge,chrome=1');


switch ($_GET['action']) {

  case "sendm3u":
    if ($authstatus) {
      $req="php/sendm3u.php";
      $head="";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;

  case "sendfile":
    if ($authstatus) {
      $req="php/sendfile.php";
      $head="";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;


  case "savefile":
    if ($authstatus) {
      $req="php/savefile.php";
      $head="";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;


  case "savedir":
    if ($authstatus) {
      $req="php/savedir.php";
      $head="";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;


  case "playdir":
    if ($authstatus) {
      $req="php/playdir.php";
      $head="";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;

  case "listdir":
    if ($authstatus) {
	  $mode = 'filebrowser';
      $req="php/showmain.php";
      $head="php/head.php";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;

  case "liststyles":
    if ($authstatus) {
	  $mode = 'stylebrowser';
      $req="php/showmain.php";
      $head="php/head.php";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;


  case "listphotos":
    if ($authstatus) {
	  $mode = 'photobrowser';
      $req="php/showmain.php";
      $head="php/head.php";
    }
    else { //not logged-in
      $req="php/home.php";
      $head="php/headhome.php";
    }
    break;


  case "login":
    $title="login";
	$head="php/headhome.php";
	if (isset($_POST['username']))
		$authmsg=authuser();
    if ($authstatus) {
      header("Location: $fscriptname?action=listdir");
	  exit;
    }
    else { //not logged-in
      $req="php/service/login.php";
      $head="php/headhome.php";
    }
    break;


  case "logout":
    if (!empty($_SESSION['username']))
      setUserHist($_SESSION['userid'],"logout");
    session_destroy();
    header("Location: $fscriptname");
	exit;
    break;


  default:
    if ($authstatus) {
		  header("Location: $fscriptname?action=listdir");
		  exit;
    }
	else {
		$title="";
		$stitle="";
		$req="php/home.php";
		$head="php/headhome.php";
	}
    break;
}

require_once($req);

if (isset($_GET['debug'])) {
	echo "\n<button id='showdbgbtn' type='button'>debug info</button>";
	echo "\n<pre id='debugpre' xstyle='display:none'>";
	echo "\nauthstatus=$authstatus";
	echo "\ndberr($errorno):$errorstr \n $errorbt\n"; echo "";
	echo "\nget:\n"; print_r($_GET); echo "";
	echo "\nsession:\n"; print_r($_SESSION); echo "";
	echo "\ncookies:\n"; print_r($_COOKIE); echo "";
	echo "err:\n"; print_r($errorstr); echo "";
	echo "\n"; print_r($errorbt); echo "";
	echo "request:\n"; print_r($_REQUEST); echo "";
	//echo "challenge:\n";echo challengeofsession(session_id())."";
	echo "post:\n"; print_r($_POST); echo "";
	echo "\nsession timeout:".  ini_get(’session.gc_maxlifetime’);
	echo "\ncookie timeout:".  ini_get(’session.cookie_lifetime’);
	echo "\nSession params:";print_r(session_get_cookie_params());
	echo "\nSESSTIMEOUT=".ini_get('session.gc_maxlifetime');
	echo "\nscriptdir=$scriptdir";
	echo "\nsessbase=$sessbase";
	echo "\nfscriptname=$fscriptname";
	echo "\nwscriptdir=$wscriptdir";
	echo "\nservername=$servername";
	echo "\nmclass=".print_r($mclass); 
	echo "\ndirinfo=".print_r($dirinfo); 
	echo "pwd:".getcwd();
	echo "<br>PP: $pathprefix";
	echo "<br>PA: $path";
	echo "<br>BN: ".basename($path."/");
	echo "</pre>";
}

?>
