<?php
function destroySession()
{
  $_SESSION=array();
  if (ini_get("session.use_cookies"))
  {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600 * 24, $params["path"], $params["domain"],$params["secure"], $params["httponly"]);
  }
  session_destroy();
}
function getSession()
{
  session_start();
  $t=time();
  $diff=0;
  $new=false;
  $msg='SessionTimeOut';
  if (isset($_SESSION['245784_time']))
  {
    $t0=$_SESSION['245784_time'];
    $diff=($t-$t0);
  }
  else {
    $new=true;
    $msg='PleaseLogIn';
  }
  if ($new || ($diff > 60*2))
  {
    destroySession();
    header('Location: login.php?msg='.$msg);
    exit();
  }
  else
  {
    $_SESSION['245784_time']=time();
  }
}
function redirect($link,$msg){
  header('Location:'.$link.'?'.$msg);
  exit();
}
?>
