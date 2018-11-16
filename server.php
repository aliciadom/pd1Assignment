<?php
include('db.php');
define("seats", 4);
class User
{
  private $username;
  private $passengers;
  public function __construct($username,$passengers)
  {
       $this->username = $username;
       $this->passengers = $passengers;
  }
  public function getUsername()
  {
     return $this->username;
  }
  public function getPassengers(){
    return $this->passengers;
  }
}
class Route
{
  private $from;
  private $to;
  private $users = array();
  public function __construct($from, $to, $username, $passengers)
  {
       $this->from = $from;
       $this->to = $to;
       if($username!=null)
        $this->users[] = new User($username,$passengers);
   }
  public function getfrom()
  {
       return $this->from;
   }
  public function getTo()
  {
       return $this->to;
   }
  public function getAllUsers()
  {
       return (!empty($this->users))?$this->users:null;
   }
  public function getUserByUsername($username)
  {
     foreach ($this->users as $u) {
       if(strcmp($u->getUsername(),$username)==0)
        return $username;
     }
     return null;
  }
  public function addUser($username,$passengers)
  {
       $this->users[] = new User($username,$passengers);
   }
  public function addUsers($users)
  {
    foreach ($users as $u) {
      $this->users[] = $u;
    }
  }
  public function isSameRoute($from, $to)
  {
     return (strcmp($this->from,$from)==0 && strcmp($this->to,$to)==0);
  }
  public function getAllPassengers()
  {
     $count = 0;
     foreach ($this->users as $u) {
       $count+=$u->getPassengers();
     }
     return $count;
   }
 }
function getRoutes($results)
{
   $routes = array();
   while($obj = $results->fetch_object()) {
     $new = true;
     foreach ($routes as $r) {
       if($r->isSameRoute($obj->source,$obj->destination)){
         if($obj->passengers!=0)
         $r->addUser($obj->username,$obj->passengers);
         $new = false;
       }
     }
     if($new){
       if($obj->passengers!=0)
        $routes[] = new Route($obj->source,$obj->destination,$obj->username,$obj->passengers);
       else
        $routes[] = new Route($obj->source,$obj->destination,null,0);
     }
   }
   return $routes;
 }
function getTotOfPassengersFromUsers($users)
{
  $c = 0;
  foreach ($users as $u)
    $c+=$u->getPassengers();
  return $c;
}
function debug($code,$a, $b, $c, $d)
{
  //echo '('.$code.' '.$a.' '.$b.' '.$c.' '.$d.')';
}
function newRoute($route, $from, $to, $username, $passengers, $isFirst, $isLast, $connection)
{
  $AA = $route->getFrom();
  $ZZ = $route->getTo();

  try{
  /***************************************/
  // AA < from < to < ZZ
  if((strcmp($AA,$from)==0 && strcmp($to,$ZZ)<0)
  || (strcmp($AA,$from)<0 && strcmp($to,$ZZ)==0)
  || (strcmp($AA,$from)<0 && strcmp($to,$ZZ)<0))
  {
    if(strcmp($AA,$from)==0 && strcmp($to,$ZZ)<0)
    {
      debug('#0_0',$AA,$from,$to,$ZZ);
      $t0 = new Route($from, $to, $username, $passengers);
      $t0->addUsers($route->getAllUsers());
      $x0 = new Route($to,$ZZ,null,null);
      $x0->addUsers($route->getAllUsers());
      dbInsertRoute($t0,$connection);
      dbInsertRoute($x0,$connection);
      dbDeleteRoute($route,$connection);
    }
    else if(strcmp($AA,$from)<0 && strcmp($to,$ZZ)==0)
    {
      debug('#0_1',$AA,$from,$to,$to);
      $t0 = new Route($from, $to, $username, $passengers);
      $t0->addUsers($route->getAllUsers());
      $x0 = new Route($AA,$from,null,null);
      $x0->addUsers($route->getAllUsers());
      dbInsertRoute($t0,$connection);
      dbInsertRoute($x0,$connection);
      dbDeleteRoute($route,$connection);
    }
    else
    {
      debug('#0_2',$AA,$from,$to,$ZZ);
      $t0 = new Route($from, $to, $username, $passengers);
      $t0->addUsers($route->getAllUsers());
      $x0 = new Route($AA,$from,null,null);
      $x0->addUsers($route->getAllUsers());
      $x1 = new Route($to, $ZZ, null, null);
      $x1->addUsers($route->getAllUsers());
      dbInsertRoute($t0,$connection);
      dbInsertRoute($x0,$connection);
      dbInsertRoute($x1,$connection);
      dbDeleteRoute($route,$connection);
    }
  }
  /***************************************/
  // s - AA - d - ZZ
  else if((strcmp($from,$AA)==0 && strcmp($AA,$to)<0 && strcmp($to,$ZZ)<0)
       || (strcmp($from,$AA)<0 && strcmp($AA,$to)==0 && strcmp($to,$ZZ)<0)
       || (strcmp($from,$AA)<0 && strcmp($AA,$to)<0 && strcmp($to,$ZZ)==0)
       || (strcmp($from,$AA)<0 && strcmp($AA,$to)<0 && strcmp($to,$ZZ)<0))
  {
    if(strcmp($from,$AA)==0 && strcmp($AA,$to)<0 && strcmp($to,$ZZ)<0)
    {
      debug('#1_0',$from,$AA,$to,$ZZ);
      $t0 = new Route($AA, $to, $username, $passengers);
      $t0->addUsers($route->getAllUsers());
      $x0 = new Route($to, $ZZ, null, null);
      $x0->addUsers($route->getAllUsers());
      dbInsertRoute($t0,$connection);
      dbInsertRoute($x0,$connection);
      dbDeleteRoute($route,$connection);
    }
    else if(strcmp($from,$AA)<0 && strcmp($AA,$to)==0 && strcmp($to,$ZZ)<0)
    {
      debug('#1_1',$from,$AA,$to,$ZZ);
      if($isFirst)
      {
        $t0 = new Route($from, $AA, $username, $passengers);
        dbInsertRoute($t0,$connection);
      }
    }
    else if(strcmp($from,$AA)<0 && strcmp($AA,$to)<0 && strcmp($to,$ZZ)==0)
    {
      debug('#1_2',$from,$AA,'',$ZZ);
      if($isFirst)
      {
        $t0 = new Route($from, $AA, $username, $passengers);
        dbInsertRoute($t0,$connection);
      }
      $x0 = new Route($AA, $to, $username, $passengers);
      $x0->addUsers($route->getAllUsers());
      dbDeleteRoute($route,$connection);
      dbInsertRoute($x0,$connection);
    }
    else
    {
      if($isFirst)
      {
        $t0 = new Route($from, $AA, $username, $passengers);
        dbInsertRoute($t0,$connection);
      }
      debug('#1_3',$from,$AA,$to,$ZZ);
      $x1 = new Route($AA, $to, $username, $passengers);
      $x2 = new Route($to, $ZZ, $username, $passengers);
      $x1->addUsers($route->getAllUsers());
      $x2->addUsers($route->getAllUsers());
      dbDeleteRoute($route,$connection);
      dbInsertRoute($x1,$connection);
      dbInsertRoute($x2,$connection);
    }
  }
  /***************************************/
  // AA - s - ZZ - d
  else if((strcmp($AA,$from)<0 && strcmp($from,$ZZ)<0 && strcmp($ZZ,$to)==0)
       || (strcmp($AA,$from)<0 && strcmp($from,$ZZ)==0 && strcmp($ZZ,$to)<0)
       || (strcmp($AA,$from)==0 && strcmp($from,$ZZ)<0 && strcmp($ZZ,$to)<0)
       || (strcmp($AA,$from)<0 && strcmp($from,$ZZ)<0 && strcmp($ZZ,$to)<0))
  {
    if(strcmp($AA,$from)<0 && strcmp($from,$ZZ)<0 && strcmp($ZZ,$to)==0)
    {
           debug('#2_0',$AA,$from,$ZZ,'');
           $x0 = new Route($AA, $from, null, null);
           $x0->addUsers($route->getAllUsers());
           $t0 = new Route($from, $ZZ, $username, $passengers);
           $t0->addUsers($route->getAllUsers());
           dbInsertRoute($t0,$connection);
           dbInsertRoute($x0,$connection);
           dbDeleteRoute($route,$connection);
    }
    else if(strcmp($AA,$from)<0 && strcmp($from,$ZZ)==0 && strcmp($ZZ,$to)<0)
    {
           debug('#2_1',$AA,$from,'',$ZZ);
           if($isLast)
           {
             $t0 = new Route($ZZ, $to, $username, $passengers);
             dbInsertRoute($t0,$connection);
           }
    }
    else if(strcmp($AA,$from)==0 && strcmp($from,$ZZ)<0 && strcmp($ZZ,$to)<0)
    {
           debug('#2_2',$AA,$from,'',$ZZ);
           $t0 = new Route($from, $ZZ, $username, $passengers);
           $t0->addUsers($route->getAllUsers());
           if($isLast)
           {
             $x0 = new Route($ZZ, $to, $username, $passengers);
             dbInsertRoute($x0,$connection);
           }
           dbDeleteRoute($route,$connection);
           dbInsertRoute($t0,$connection);
    }
    else
    {
           debug('#2_3',$AA,$from,$ZZ,$to);
           $t0 = new Route($from, $ZZ, $username, $passengers);
           $t0->addUsers($route->getAllUsers());
           $x0 = new Route($AA, $from, null, null);
           $x0->addUsers($route->getAllUsers());
           dbDeleteRoute($route,$connection);
           dbInsertRoute($x0,$connection);
           dbInsertRoute($t0,$connection);
    }
  }
  /***************************************/
  // s - AA - ZZ - d
  else if((strcmp($from,$AA)==0 && strcmp($ZZ,$to)<0)
       || (strcmp($from,$AA)<0 && strcmp($ZZ,$to)==0)
       || (strcmp($from,$AA)<0 && strcmp($ZZ,$to)<0))
  {
    if(strcmp($from,$AA)==0 && strcmp($ZZ,$to)<0)
    {
      debug('#3_0','',$AA,$ZZ,$to);
      $x0 = new Route($AA,$ZZ,$username,$passengers);
      $x0->addUsers($route->getAllUsers());
      dbDeleteRoute($route,$connection);
      dbInsertRoute($x0,$connection);
    }
    else if(strcmp($from,$AA)<0 && strcmp($ZZ,$to)==0)
    {
      debug('#3_1',$from,$AA,$ZZ,'');
      $x0 = new Route($AA,$ZZ,$username,$passengers);
      $x0->addUsers($route->getAllUsers());
      dbDeleteRoute($route,$connection);
      dbInsertRoute($x0,$connection);
    }
    else
    {
      debug('#3_2',$from,$AA,$ZZ,$to);
      $x0 = new Route($AA,$ZZ,$username,$passengers);
      $x0->addUsers($route->getAllUsers());
      dbDeleteRoute($route,$connection);
      dbInsertRoute($x0,$connection);
      if($isFirst)
      {
        $t0 = new Route($from,$AA,$username,$passengers);
        dbInsertRoute($t0,$connection);
      }
      if($isLast){
        $t0 = new Route($ZZ,$to,$username,$passengers);
        dbInsertRoute($t0,$connection);
      }
    }
  }
  /***************************************/
  //  s == AA - ZZ == d
  else if((strcmp($AA,$from)==0 && strcmp($to,$ZZ)==0))
  {
    debug('#4_0',$AA,$ZZ,$from,$to);
    $x0 = new Route($AA,$ZZ,$username,$passengers);
    $x0->addUsers($route->getAllUsers());
    dbDeleteRoute($route,$connection);
    dbInsertRoute($x0,$connection);
  }
  /***************************************/
  // AA - ZZ - from - to
  else if(strcmp($ZZ,$from)<0 && $isLast)
  {
    debug('#5_0',$AA,$ZZ,$from,$to);
    $t0 = new Route($ZZ,$from,null,null);
    $t1 = new Route($from,$to,$username,$passengers);
    dbInsertRoute($t0,$connection);
    dbInsertRoute($t1,$connection);
  }
  /***************************************/
  // from - to - AA - ZZ
  else if(strcmp($to,$AA)<0 && $isFirst)
  {
    debug('#6_0',$AA,$ZZ,$from,$to);
    $t0 = new Route($from,$to,$username,$passengers);
    $t1 = new Route($to,$AA,null,null);
    dbInsertRoute($t0,$connection);
    dbInsertRoute($t1,$connection);
  }
  }
  catch(Exception $e)
  {
    throw $e;
  }
}
function addNewRoute($route, $from, $to, $username, $passengers,$connection)
{
  try
  {
    $copy_route = $route;
    $lastRoute = end($copy_route);
    $firstRoute = true;
    foreach($route as $r)
    {
      if(($firstRoute) && ($lastRoute!=$r))
      {
        newRoute($r, $from, $to, $username, $passengers,true,false,$connection);
        $firstRoute = false;
      }
      else if((!$firstRoute) && ($lastRoute==$r))
      {
        newRoute($r, $from, $to, $username, $passengers,false,true,$connection);
      }
      else if(($firstRoute) && ($lastRoute==$r))
      {
        newRoute($r, $from, $to, $username, $passengers,true,true,$connection);
        $firstRoute = false;
      }
      else if((!$firstRoute) && ($lastRoute!=$r))
      {
        newRoute($r, $from, $to, $username, $passengers,false,false,$connection);
      }
    }
  }
  catch(Exception $e)
  {
    throw $e;
  }
}
function confirmRoute($from,$to,$passengers,$username,$connection,$msg)
{
  try
  {
    $sql = sqlUserHasAlreadyBooked($username);
    $query = mysqli_query($connection, $sql);
    $direction = (strcmp($from,$to)<0);
    if ($query->num_rows > 0)
    {
      $msg = $username.' hai già prenotato';
    }
    else if(!$direction)
    {
    $msg = 'direzione non consentita';
    }
    else
    {
    /*if another concurrent user modifies the table
      -after this user have read the table-
      -and-
      -before this user adds new Route in db-
      ACID fails. */
      mysqli_autocommit($connection,false);
      $sql = sqlSeatsPerRoute(seats-$passengers); //for update
      $results = mysqli_query($connection, $sql);
      $allow = isAllowed($results,$from,$to);
      if($allow && $passengers<=seats && $passengers>0)
      {
        $sql = sqlAllRoutesInfo();
        $results = mysqli_query($connection, $sql);
        $routs = getRoutes($results);
        if(empty($routs))
        {
          $sql = sqlInsertRoute($from,$to,$username,$passengers);
          mysqli_query($connection, $sql);
        }
        else
        {
          addNewRoute($routs,$from,$to,$username,$passengers,$connection);
        }
      }
      else
      {
        $msg = 'Posti non disponibili';
      }
    }
    mysqli_commit($connection);
    mysqli_autocommit($connection,true);
    return $msg;
  }
  catch(Exception $e)
  {
    mysqli_rollback($connection);
    mysqli_autocommit($connection,true);
    return $e->getMessage();
  }
}
function deleteRoute($username,$connection,$msg)
{
  try
  {
    mysqli_autocommit($connection,false);
    $sql = sqlUserHasAlreadyBooked($username);
    $query = mysqli_query($connection, $sql);
    if ($query->num_rows == 0)
    {
      $msg = $username.' non puoi cancellare';
    }
    else
    {
      $sql = sqlDeleteUsername($username);
      mysqli_query($connection, $sql);
      $msg =  'cancellazione effettuata correttamente';
    }
    $sql = sqlRoutesInfo().'for update'; //for update
    $up = mysqli_query($connection, $sql);
    $sql = sqlRoutesInfo().'DESC for update'; //for update
    $down = mysqli_query($connection, $sql);
    $num_rows = $up->num_rows;
    $i = 1;
    if($num_rows>0)
    {
      while($obj = $up->fetch_object())
      {
        if($obj->sum==0)
        dbDeleteRoute(new Route($obj->source,$obj->destination,null,null),$connection);
        else
          break;
          $i++;
      }
      if($i!=$num_rows)
      {
        while($obj = $down->fetch_object())
        {
          if($obj->sum==0)
          dbDeleteRoute(new Route($obj->source,$obj->destination,null,null),$connection);
          else
            break;
        }
      }
    }
    mysqli_commit($connection);
    mysqli_autocommit($connection,true);
    return $msg;
  }
  catch(Exception $e)
  {
    mysqli_rollback($connection);
    mysqli_autocommit($connection,true);
    return $e->getMessage();
  }
}
function getTo($routs,$username)
{
  $to = null;
  foreach ($routs as $r) {
    if($r->getUserByUsername($username)!=null)
      $to = $r->getTo();
  }
  return $to;
}
function validPassword($psw)
{
  if( preg_match("/[a-z]/", $psw) && preg_match("/[A-Z0-9]/", $psw) )
    return true;
  return false;
}
function isAllowed($results,$from,$to)
{
  if($results->num_rows===0) return true;
  while($obj = $results->fetch_object())
  {
    $s = $obj->source;
    $e = $obj->destination;
    if( (strcmp($from,$s)<=0 && strcmp($s,$to)<0)
    ||  (strcmp($from,$e)<0 && strcmp($e,$to)<=0)
    ||  (strcmp($from,$s)==0 && strcmp($e,$to)==0) )
    {
      return false;
    }
  }
  return true;
}
function redirectHttps()
{
  if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') )
  {
    return;
  }
  else
  {
    $redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$redirect);
    exit();
  }
}
function areCookiesEnabled()
{
  setcookie("245784_test_cookie", "245784_test", time() + 3600, '/');
  return (isset($_COOKIE['245784_test_cookie']) && $_COOKIE['245784_test_cookie']=='245784_test');
}
?>
