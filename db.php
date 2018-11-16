<?php
function dbOpenConnection()
{
  $connection = mysqli_connect('localhost','root','','245784_030718');
  if(!$connection)
  {
     return false;
  }
  return $connection;
}
function dbCloseConnection($connection)
{
  mysqli_close($connection);
}
function dbInsertRoute($route,$connection)
{
  try{
  $from = $route->getfrom();
  $to = $route->getTo();
  $from = mysqli_real_escape_string($connection,$from);
  $to = mysqli_real_escape_string($connection,$to);
  $users = $route->getAllUsers();
  if(!empty($users))
  {
    foreach ($users as $u)
    {
      $sql = sqlInsertRoute($from, $to, $u->getUsername(), $u->getPassengers());
      if(!mysqli_query($connection, $sql))
      throw new Exception("Errore del sistema #00", 1);
      ;
    }
  }
  else
  {
    $sql = sqlInsertRoute($from, $to, null, 0);
    if(!mysqli_query($connection, $sql))
    throw new Exception("Errore del sistema #01", 1);
  }
  }
  catch(Exception $e){
    throw $e;
  }
}
function dbDeleteRoute($route,$connection)
{
  $from = $route->getfrom();
  $to = $route->getTo();
  $from = mysqli_real_escape_string($connection,$from);
  $to = mysqli_real_escape_string($connection,$to);
  $users = $route->getAllUsers();
  if($users)
  {
    foreach ($users as $u) {
      $sql = sqlDeleteFromToUsername($from, $to, $u->getUsername());
      if(!mysqli_query($connection, $sql))
      throw new Exception("Errore del sistema #10", 1);
    }
  }
  else
  {
    $sql = sqlDeleteFromTo($from, $to);
    if(!mysqli_query($connection, $sql))
    throw new Exception("Errore del sistema #11", 1);
  }
}
function sqlLogin($username)
{
  return "SELECT password from users where username='$username'";
}
function sqlUserExists($username)
{
  return "SELECT username from users where username='$username' for update";
}
function sqlSeatsPerRoute($constraint)
{
  return "SELECT source, destination from tickets group by source, destination having sum(passengers)>'$constraint' for update";
}
function sqlAllRoutesInfo()
{
  return "SELECT source, destination, username, passengers from  tickets order by source, destination " ;
}
function sqlRoutesInfo()
{
  return "SELECT source, destination, sum(passengers) as sum from  tickets group by source,destination order by source " ;
}
function sqlInsertUser($username, $hash)
{
  return "INSERT INTO users (username, password) VALUES ('$username', '$hash')";
}
function sqlInsertRoute($from, $to, $username, $passengers)
{
  return "INSERT INTO tickets (source, destination, username, passengers) VALUES ('$from','$to','$username', '$passengers')";
}
function sqlUserHasAlreadyBooked($username)
{
  return "SELECT username from tickets where username='$username' ";
}
function sqlDeleteFromToUsername($from,$to,$username)
{
  return "DELETE FROM tickets WHERE username = '$username' AND source = '$from' AND destination = '$to'";
}
function sqlDeleteFromTo($from,$to)
{
  return "DELETE FROM tickets WHERE source = '$from' AND destination = '$to'";
}
function sqlDeleteUsername($username)
{
  return "DELETE FROM tickets WHERE username = '$username'";
}

 ?>
