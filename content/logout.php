<?php
session_start();
//echo "HI" . $_SESSION['verifiedaccount'];
//exit();
session_destroy();
header('location: ../index.php');
?>
