<?php
include('server.php');
include('session.php');
redirectHttps();
getSession();
$msg=null;
$connection = dbOpenConnection();
$username = $_SESSION['245784_login_user'];
if($connection)
{
  if (isset($_POST['submit']))
  {
    if (empty($_POST['from']) || empty($_POST['to']) || empty($_POST['passengers']))
    {
      $msg ='riempi tutti i campi';
    }
    else
    {
      $from = $_POST['from'];
      $to = $_POST['to'];
      $passengers = $_POST['passengers'];
      $from = strtoupper($from);
      $to = strtoupper($to);
      $msg = confirmRoute($from,$to,$passengers,$username,$connection,$msg);
    }
  }
  else if (isset($_POST['cancellazione']))
  {
    $msg = deleteRoute($username,$connection,$msg);
  }
}
else
{
  $msg = "Problema tecnico, impossibile accedere, ripassa più tardi";
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript">
      function detect()
      {
        if (!navigator.cookieEnabled)
        {
          document.write("Attiva i cookies per continuare la navigazione.")
        }
      }
      function strcmp ( str1, str2 )
      {
        return ( ( str1 == str2 ) ? 0 : ( ( str1 > str2 ) ? 1 : -1 ) );
      }
      $(document).ready(function() {
      $(".btn").click(function()
      {
        if(!$('#profile #from').val()
        && !$('#profile #to').val())
        {
          $('#profile #from').val( $(this).val() );
        }
        else if ($('#profile #from').val()
             && !$('#profile #to').val())
        {
          if( strcmp($('#profile #from').val(),$(this).val())==0 )
            $('#profile #from').val('');
          else if(strcmp($('#profile #from').val(),$(this).val())>0)
          {
            $('#profile #to').val( $('#profile #from').val() );
            $('#profile #from').val( $(this).val() );
          }
          else{
              $('#profile #to').val( $(this).val() );
          }
        }
        else if (!$('#profile #from').val()
               && $('#profile #to').val())
        {
          if( strcmp($('#profile #to').val(),$(this).val())==0 )
            $('#profile #to').val('');
          else if(strcmp($('#profile #to').val(),$(this).val())<0)
          {
            $('#profile #from').val( $('#profile #to').val() );
            $('#profile #to').val( $(this).val() );
          }
          else{
              $('#profile #from').val( $(this).val() );
          }
        }
        else if ($('#profile #from').val()
              && $('#profile #to').val())
        {
          if( strcmp($('#profile #from').val(),$(this).val())>0 )
            $('#profile #from').val( $(this).val() );
          else if( strcmp($('#profile #from').val(),$(this).val())==0 )
            $('#profile #from').val('');
          else if( strcmp($('#profile #to').val(),$(this).val())==0)
              $('#profile #to').val('');
          else if( strcmp($('#profile #from').val(),$(this).val())<0)
            $('#profile #to').val( $(this).val() );
        }
        });
      });
    </script>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="master.css">
  </head>
  <body onload="detect()">
    <div id="header">
      <noscript>Attenzione: javascript disabilitato. Non si assicurano tutte le funzionalità del sito web</noscript>
      <h1>MiniBus di <?php echo seats; ?> posti</h1>
    </div>
      <div id="left-side">
        <div class="inner">
          <h3>Benvenuto <?php echo $username; ?></h3><br>
          <form id= "profile" action="profile.php" method="post">
            <label>partenza</label><br>
            <input id="from" name="from" type="text"><br>
            <label>arrivo</label><br>
            <input id="to" name="to" type="text"><br>
            <label>passeggeri</label><br>
            <input id="passengers" name="passengers" type="number" min="1"><br><br>
            <input name="submit" type="submit" value=" Conferma P "><br>
          </form><br>
          <form action="profile.php" method="post">
            <input name="cancellazione" type="submit" value="Cancella P">
          </form><br>
          <form action="logout.php" method="get">
            <input name="logout" type="submit" value="Esci">
          </form><br>
        </div>
        <?php if($msg!=null) echo '<h4>'.$msg.'</h4>'; ?>
      </div>
      <div id="right-side">
        <div class="inner">
          <?php
          if($connection)
          {
            $sql = sqlAllRoutesInfo();
            $results = mysqli_query($connection, $sql);
            if($results){
              if($results->num_rows === 0)
              {
              echo '<p style="font-size:20px;"> No prenotazioni disponibili</p>';
              }
              else
              {?>
                <table>
                  <tr>
                    <th>Partenza</th>
                    <th>Arrivo</th>
                    <th>Totale Passeggeri</th>
                    <th>Utente [Passeggeri]</th>
                  </tr><?php
                  $first = true;
                  $routs = getRoutes($results);
                  $end = getTo($routs,$username);
                  foreach($routs as $r)
                  {
                    ?><tr><?php
                    $from=$r->getFrom();
                    $to=$r->getTo();
                    $us = $r->getAllUsers();
                    $uname = $r->getUserByUsername($username);
                    if($uname!=null)
                    {
                      if($first)
                      { ?><td>
                        <input style="color:red" type="button" class= "btn" name="<?php echo $from;?>" value="<?php echo $from;?>"></input>
                      </td><?php
                    }
                    else
                    {
                      ?><td>
                        <input type="button" class= "btn" name="<?php echo $from;?>" value="<?php echo $from;?>"></input>
                      </td><?php
                    }
                    if(strcmp($to,$end)==0)
                    { ?><td>
                      <input style="color:red" type="button" class= "btn" name="<?php echo $to;?>" value="<?php echo $to;?>"></input>
                    </td><?php
                    }
                    else
                    {
                      ?>
                      <td>
                        <input type="button" class= "btn" name="<?php echo $to;?>" value="<?php echo $to;?>"></input>
                      </td>
                      <?php
                    }
                    $first = false;
                  }
                  else
                  {
                    ?>
                    <td>
                      <input type="button" class= "btn" name="<?php echo $from;?>" value="<?php echo $from;?>"></input>
                    </td>
                    <td>
                      <input type="button" class= "btn" name="<?php echo $to;?>" value="<?php echo $to;?>"></input>
                    </td>
                    <?php
                  }
                  if($us!=null)
                  {
                    ?>
                    <td>
                    <?php
                      echo '<p style="margin:10">'.getTotOfPassengersFromUsers($us).'</p>';
                    ?>
                    </td>
                    <td>
                    <?php
                    foreach($us as $u)
                    {
                      $un = $u->getUsername();
                      $ps = $u->getPassengers();
                      if(strcmp($username,$un)==0)
                      {
                        $passenger = ($ps==1)?' passeggero ':' passeggeri ';
                        echo '<p style="color:red;">'.$un.' ['.$ps.']</p>';
                      }
                      else
                      {
                        $passenger = ($ps==1)?' passeggero ':' passeggeri ';
                        echo '<p>'.$un.'  ['.$ps.']</p>';
                      }
                    }
                    ?>
                    </td>
                    <?php
                  }
                  else
                  {
                    ?><td><?php
                    echo '<p>0</p>';
                    ?></td><td></td><?php
                    }
                    ?></tr><?php
                  }
                  ?></table><?php
                }
              }
              else
              {
                echo '<h1>Problema tecnico</h1>';
              }
            }
            ?>
            </div>
          </div>
  </body>
</html>
