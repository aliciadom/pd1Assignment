<?php
session_start();
include('server.php');
include('session.php');
redirectHttps();
$msg = null;
if (isset($_SESSION['245784_login_user']))
{
      header('Location: profile.php');
      exit();
}
if (isset($_POST['submit']))
{
  if (empty($_POST['username']) || empty($_POST['password']))
  {
      $error = "Username or Password is invalid";
      $_REQUEST['error'] = $error;
      header('Location: signin.php?error=UsernameOrPasswordEmpty');
      exit();
  }
  else
  {
    $username=$_POST['username'];
    $password=$_POST['password'];
    if (filter_var($username, FILTER_VALIDATE_EMAIL) && validPassword($password))
    {
      $connection = dbOpenConnection();
      if($connection){
		    $username = mysqli_real_escape_string($connection,$username);
        mysqli_autocommit($connection,false);
        $sql = sqlUserExists($username);
        $query = mysqli_query($connection, $sql);
        if($query)
        {
          if ($query->num_rows>0)
          {
            mysqli_autocommit($connection,true);
            $msg = "Username già esistente";
          }
          else
          {
		    $hash = md5($password);
            $sql = sqlInsertUser($username, $hash);
            if (mysqli_query($connection, $sql))
            {
              mysqli_commit($connection);
              mysqli_autocommit($connection,true);
              dbCloseConnection($connection);
              header('HTTP/1.1 307 temporary redirect');
              header("location: login.php?");
              exit();
            }
            else
            {
              mysqli_autocommit($connection,true);
              $msg = "Problema tecnico, impossibile registrarsi, ripassa più tardi";
            }
          }
          dbCloseConnection($connection);
        }
        else
        {
          $msg = "Problema tecnico, impossibile registrarsi, ripassa più tardi";
        }
      }
      else
      {
        $msg = "Problema tecnico, impossibile registrarsi, ripassa più tardi";
      }
    }
    else
    {
     $msg = "Inserisci username o password valide";
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
        <noscript>Attenzione: javascript disabilitato. Non si assicurano tutte le funzionalità del sito web</noscript>
      <h1>MiniBus</h1>
      </div>
    </div>
    <div id="container">
      <div id="left-side">
        <div class="inner">
          <form action="signin.php" method="post">
            <label>Email</label><br>
            <input id="name" name="username" placeholder="email" type="text"><br>
            <label>Password</label><br>
            <input type="text" id="password" name="password" placeholder="password" onchange="myFunction()"><br>
            <script>
            function myFunction()
            {
              var regex1 = /[a-z]/;
              var regex2 = /[A-Z0-9]/;
              var x = document.getElementById("password");
              var color = "Red";
              if (!x.value.match(regex1) || !x.value.match(regex2))
              {
                x.style.border = "3px solid red";
              }
              else
              {
                x.style.border = "1px solid green";
              }
            }
            </script>
            <input name="submit" type="submit" value=" Conferma ">
          </form><br><br><br><br><br><br><br><br>
          <label>Già iscritto?</label><br>
          <form action="login.php">
            <input type="submit" value="Accedi" />
          </form><br>
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
