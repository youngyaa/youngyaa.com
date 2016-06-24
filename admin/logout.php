<?php session_start();
session_destroy();

unset($_SESSION['adminid']);

header("refresh:1;url=index.php");
 echo "<center><b><h1><font color='green'>Logout Successfully.</font></h1></b></center>";
?>