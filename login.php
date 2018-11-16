<?php
session_start();
include('server.php');
include('session.php');
redirectHttps();
$msg = null;
if (isset($_SESSION['245784_login_user']))
{
      header('Location: profile.php');
}
if (isset($_POST['submit']))
{
  if (empty($_POST['username']) || empty($_POST['password']))
  {
      $msg = "Campi mancanti";
  }
  else
  {
    $connection = dbOpenConnection();
    if($connection)
    {
      $username=$_POST['username'];
      $password=$_POST['password'];
      $username = mysqli_real_escape_string($connection,$username);
	    $sql = sqlLogin($username);
      $results = mysqli_query($connection, $sql);
      if($results){
        $obj = $results->fetch_object();
        $md5 = md5($password);
        if (strcmp($md5,$obj->password)==0)
        {
          $_SESSION['245784_login_user']=$username;
          $_SESSION['245784_time']=time();
          header("Location: profile.php");
		  exit();
        }
        else
        {
          $msg = "Username o password sbagliati";
        }
      }
      else {
        $msg = "Problema tecnico, impossibile accedere, ripassa più tardi";
      }
      dbCloseConnection($connection);
    }
    else
    {
     $msg = "Problema tecnico, impossibile accedere, ripassa più tardi";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <script type="text/javascript">
      function detect()
      {
          if (!navigator.cookieEnabled)
          {
            document.write("Attiva i cookies per continuare la navigazione.")
          }
        }
    </script>
    <meta charset="utf-8">
    <title></title>
  <link rel="stylesheet" href="master.css">
  </head>
  <body onload="detect()">
    <div id="header">
      <div class="inner">
          <noscript>Attenzione: javascript disabilato. Non si assicurano tutte le funzionalità del sito web</noscript>
      <h1>MiniBus</h1>
      </div>
    </div>
    <div id="container">
      <div id="left-side">
        <div class="inner">
          <form action="login.php" method="post">
            <label>Email</label><br>
            <input id="name" name="username" placeholder="email" type="text"><br>
            <label>Password</label><br>
            <input id="password" name="password" placeholder="password" type="password"><br>
            <input name="submit" type="submit" value=" Conferma "><br>
          </form><br><br><br><br><br><br><br><br>
          <label>Non ancora registrato?</label><br>
          <form action="signin.php">
              <input type="submit" value="Registrati" />
          </form>
        </div>
        <?php
        if($msg)
          echo '<h4>'.$msg.'</h4>';?>
      </div>
      <div id="right-side">
        <div class ="inner">

       </div>
      </div>
    </div>
  </body>
</html>
