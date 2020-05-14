<?php
$database_host = 'localhost';
$database_user = 'root';
$database_pass = '';
$database_name = 'sampledashboard';
$conn = mysqli_connect($database_host, $database_user, $database_pass,$database_name);
if(mysqli_connect_errno()){
    header('Location: error.php');
//        exit("Connection Error");
}
?>