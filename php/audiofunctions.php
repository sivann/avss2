<?
function validateuser()
{
  global $HTTP_ENV_VARS,$HTTP_X_FORWARDED_FOR,$REMOTE_ADDR,$path;

  if (isset($HTTP_X_FORWARDED_FOR))
    $remaddr=$HTTP_X_FORWARDED_FOR;
  else 
    $remaddr=$REMOTE_ADDR;

    if (($remaddr!="147.102.11.130") && 
    ($remaddr!="147.102.11.134") && 
    ($remaddr!="147.102.11.112") && 
    ($remaddr!="81.249.34.42") && 
    ($remaddr!="147.102.11.104") && 
    ($remaddr!="147.102.11.103")) {
    header("Content-type: text/html");
    echo "<h3>You can access only your Incoming directory $username from $remaddr ($path)</h3>";
    exit;
  }
}//validateuser

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}


?>
