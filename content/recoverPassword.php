<?php
session_start();
$account_not_found = FALSE;
$password_invalid = FALSE;
$password_mismatch = FALSE;
$_SESSION['passwordchange'] = FALSE;

$database_host = 'localhost';
$database_user = 'root';
$database_pass = '';
$database_name = 'sampledashboard';

$conn = mysqli_connect($database_host, $database_user, $database_pass, $database_name);
if(mysqli_connect_errno()) {
    header('Location: error.php');
}
if($stmt = $conn->prepare('SELECT password FROM accounts WHERE email = ? AND password_change=?')) {
    $stmt->bind_param('ss', $_GET['email'], $_GET['code']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0 && $_GET['code'] != FALSE) {
        $stmt->bind_result($oldpassword);
        $stmt->fetch();
    } else {
        $account_not_found = TRUE;
        header('Location: error.php');
    }
} else {
    header('Location: error.php');
}
if($account_not_found === FALSE && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if(strlen($_POST['password1'])>20 || strlen($_POST['password1'])<4) {
        $password_invalid = TRUE;
    } else {
        $password1 = password_hash($_POST['password1'], PASSWORD_DEFAULT);
        $password2 = password_hash($_POST['password2'], PASSWORD_DEFAULT);
        if($_POST['password1'] === $_POST['password2']) {
            if($stmt = $conn->prepare('UPDATE accounts SET password=?, password_change=? WHERE email=?')) {
                $newcode = FALSE;
                $stmt->bind_param('sss', $password1, $newcode, $_GET['email']);
                $stmt->execute();
                $_SESSION['passwordchange'] = TRUE;
                header('Location: ../index.php');
            } else {
                header('Location: error.php');
            }
        } else {
            $password_mismatch = TRUE;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="../css/forgot.css" type="text/css" rel="stylesheet">
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/598d28d35e.js" crossorigin="anonymous"></script>
    <title>Recover Password</title>
</head>
<body>
    <img src="../images/flame-8.png" alt="illustration" class="forgot-image" style="width: 500px;">
    <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>

    <div id="form-div">
        <h1>Hi <?php echo $_GET['fname']?>!</h1>
        <?php
        $val_to_print = "";
        if($account_not_found === TRUE) {
            $val_to_print = '<label class="font-weight-bold text-center text-danger bg-warning" style = "width: 100%" >Account not found.</label >';
        }
        echo $val_to_print;
        ?>
        <form method="post">
            <label for="password1">
                <i class="fas fa-fingerprint fa-2x"></i>
            </label>
            <input name="password1" type="password" id="password1" placeholder="Enter New Password" required
                <?php
                $val_to_echo = '';
                if($password_invalid === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Invalid Password</p';
                }
                echo $val_to_echo;
                ?>>
            <label for="password2">
                <i class="fas fa-mask fa-2x"></i>
            </label>
            <input name="password2" type="password" id="password2" placeholder="Retype New Password" required
                <?php
                $val_to_echo = '';
                if($password_mismatch === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Passwords Mismatch</p';
                }
                echo $val_to_echo;
                ?>>
            <input type="submit" value="Reset Password">
        </form>
    </div>
</body>
</html>