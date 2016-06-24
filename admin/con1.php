<?php 
$mysqlipre = new mysqli("localhost", "joomla-user", "youngyaa123", "joomla-db");


if (mysqli_connect_errno()) 
{
    printf("Not connect to database ", mysqli_connect_error());
    exit();
}


date_default_timezone_set('Asia/Kolkata');



?>



