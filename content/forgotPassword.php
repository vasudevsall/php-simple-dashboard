<?php
session_start();
$user_exist = TRUE;
$invaliduser = FALSE;
$verificationSent = FALSE;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $database_host = 'localhost';
    $database_user = 'root';
    $database_pass = '';
    $database_name = 'sampledashboard';

    $conn = mysqli_connect($database_host, $database_user, $database_pass, $database_name);
    if(mysqli_connect_errno()) {
        header('Location: error.html');
    }

    if(!isset($_POST['username'])) {
        $invaliduser = TRUE;
    }
    if(preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
        $invaliduser = TRUE;
//            exit("Invalid Username Error");
    } else {
        if($stmt = $conn->prepare('SELECT id, email, first_name, last_name FROM accounts WHERE username=?')) {
            $stmt->bind_param('s', $_POST['username']);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $stmt->bind_result($id, $email, $firstname, $lastname);
                $stmt->fetch();
                $uniqid = uniqid();
                if($stmt1 = $conn->prepare('UPDATE accounts SET password_change = ? WHERE username=?')) {
                    $stmt1->bind_param('ss',$uniqid, $_POST['username']);
                    $stmt1->execute();
                } else {
                    header('Location: error.html');
                }
                $from = 'noreply@sampledashboard.com';
                $subject = 'Password Change Request';
                $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                $activate_link = 'http://localhost/sampleDashboard/content/recoverPassword.php?email=' . $email . '&code=' . $uniqid . '&fname=' . $firstname
                                    . '&lname=' . $lastname;
                $message = '<p>Please click the following link to reset your password: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
                mail($email, $subject, $message, $headers);
                $verificationSent = TRUE;
            } else {
                $user_exist = FALSE;
            }
        } else {
            header('Location: error.html');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../css/forgot.css" type="text/css" rel="stylesheet">
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/598d28d35e.js" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>
<body>
    <img src="../images/flame-searching.png" class="forgot-image" alt="forgot illustration">
    <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>

    <div id="form-div">
        <h1>Password Recovery!</h1>
        <?php
        $val_to_print = "";
        if ($verificationSent === TRUE) {
            $val_to_print = '<label class="font-weight-bold text-center text-success bg-white" style = "width: 100%" >Please follow the link
            mailed to you</label>';
        }
        echo $val_to_print;
        ?>
        <form method="post">
            <label for="username">
                <i class="fas fa-user-secret fa-2x"></i>
            </label>
            <input type="text" name="username" placeholder="Enter username" id="username" required
                <?php
                $val_to_echo = '';
                if($invaliduser === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Invalid Username</p';
                }
                elseif ($user_exist === FALSE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Username not found</p';
                }
                echo $val_to_echo;
                ?>>
            <input type="submit" value="Recover">
            <a class="end-anchor" href="../index.php">Remember it now? Sign in Here!</a>
            <a class="end-anchor" href="../index.php">New user? Register Here!</a>
        </form>
    </div>
</body>
</html>