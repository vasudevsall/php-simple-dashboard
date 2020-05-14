<?php
session_start();
if(!isset($_SESSION['loggedin'])) {
    header('Location: ../../index.php');
} else {

    $database_host = 'localhost';
    $database_user = 'root';
    $database_pass = '';
    $database_name = 'sampledashboard';

    $conn = mysqli_connect($database_host, $database_user, $database_pass, $database_name);
    if (mysqli_connect_errno()) {
        header('Location: ../error.php');
    } else {
        if ($stmt = $conn->prepare('SELECT email, first_name, last_name, gender, birth_date, profile_pic FROM accounts WHERE id=?')) {
            $stmt->bind_param('i',$_SESSION['id']);
            $stmt->execute();
            $stmt->bind_result($email, $fname, $lname, $gender, $birthdate, $profilepic );
            $stmt->fetch();

            $_SESSION['fname'] = $fname;
            $_SESSION['lname'] = $lname;
            $_SESSION['gender'] = $gender;
            if($profilepic === 'none'){
                $_SESSION['profilePic'] = $profilepic;
            } else {
                $_SESSION['profilePic'] = $_SESSION['id'] . "." . $profilepic;
            }
            $stmt->close();
        } else {
            header('Location: ../logout.php');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        if($page_name === "home")
            echo '<link href="../../css/home.css" type="text/css" rel="stylesheet">';
        if($page_name === "profile")
            echo '<link href="../../css/profile.css" type="text/css" rel="stylesheet">';
        if($page_name === "chat")
            echo '<link href="../../css/chat.css" type="text/css" rel="stylesheet">';
    ?>
    <link href="../../css/header.css" type="text/css" rel="stylesheet">
    <link href="../../css/bootstrap.css" type="text/css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/598d28d35e.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <title><?php echo $page_title;?></title>
</head>
<body>
    <nav>
        <div class="nav-div container-fluid">
            <a class="nav-anchors" href="../loggedin/home.php"><i class="fas fa-home fa-2x the-logo"></i>Home</a>
            <a class="nav-anchors" href="../loggedin/profile.php"><i class="fas fa-id-card fa-2x the-logo"></i>Profile</a>
            <a class="nav-anchors" href="../loggedin/chat.php"><i class="fas fa-comment-dots fa-2x the-logo"></i>Chat Room</a>
            <a id="log-out-anchor" class="nav-anchors" href="../logout.php"><i class="fas fa-sign-out-alt fa-2x the-logo"></i>Sign Out</a>
        </div>
    </nav>
    <div class="center-content">