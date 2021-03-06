<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../css/error.css" type="text/css" rel="stylesheet">
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/598d28d35e.js" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <title>Error!!</title>
</head>
<body>
    <div class="center-div">
        <p class="error-text">Oops! Something went wrong!!<br>Want to give another go?
            Try going <a class="error-anchor" href="../index.php">here</a></p>
        <img src="../images/page-not-found-4.png" class="error-img" alt="illustration">
    </div>
</body>
</html>