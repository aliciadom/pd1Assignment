<?php
include('session.php');
session_start();
destroySession();
header("Location: index.php");
exit();
?>
