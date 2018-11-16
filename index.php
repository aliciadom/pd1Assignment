<?php
include('server.php');
redirectHttps();
$msg = null;
$connection = dbOpenConnection();
if(!$connection) $msg = "Problema tecnico, ripassa più tardi";
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
      <noscript>Attenzione: javascript disabilitato. Non si assicurano tutte le funzionalità del sito web</noscript>
      <h1>MiniBus</h1>
    </div>
      <div id="left-side">
        <div class="inner">
          <form action="login.php">
            <input type="submit" value="Accedi" /><br>
          </form>
          <form action="signin.php">
            <input type="submit" value="Registrati" /><br>
          </form>
        </div>
        <?php
        if($msg)
          echo '<h4>'.$msg.'</h4>';?>
      </div>
      <div id="right-side">
        <div class ="inner">
          <?php
          if($connection)
          {
            $sql = sqlRoutesInfo();
            $results = mysqli_query($connection, $sql);
            if ($results)
            {
              if($results->num_rows === 0)
                {
                  echo '<h1>Nessun percorso ancora</h1>';
                }
                else
                {
                  ?><table>
                      <tr>
                        <th>Partenza</th>
                        <th>Arrivo</th>
                        <th>Passeggeri</th>
                      </tr>
                      <?php
                      while($obj = $results->fetch_object())
                      {
                        ?><tr>
                          <td>
                            <?php echo $obj->source;
                            ?>
                          </td>
                          <td>
                            <?php echo $obj->destination;
                            ?>
                          </td>
                          <td>
                            <?php echo $obj->sum;
                            ?>
                          </td>
                        </tr>
                        <?php
                      }
                      ?></table><?php
                    }
                  }
                  else
                  {
                    echo '<h1>Dati attualmente non disponibili. Ripassa fra qualche minuto</h1>';
                  }
                  dbCloseConnection($connection);
                }
                  ?>
        </div>
      </div>
  </body>
</html>
