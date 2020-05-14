<?php
session_start();
$_SESSION['credentialsValid'] = TRUE;
$nonverifiedaccount = FALSE;
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database_host = "localhost";
    $database_user = "root";
    $database_pass = '';
    $database_name = 'sampledashboard';

    $conn = mysqli_connect($database_host, $database_user, $database_pass, $database_name);
    if (mysqli_connect_errno()) {
        exit("Error");
//        header('Location: error.php');
    }

    if (!isset($_POST['username'], $_POST['password'])) {
        exit("Error");
//        header('Location: error.php');
    }

    if ($stmt = $conn->prepare('SELECT activation_code, email FROM accounts WHERE username=?')) {
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($activation, $email);
        $stmt->fetch();
        if ($activation != 'activated' && $stmt->num_rows() > 0) {
//            exit("I am going here");
            $nonverifiedaccount = TRUE;

            $from = 'noreply@sampledashboard.com';
            $subject = 'Account Activation Required';
            $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            $activate_link = 'http://localhost/sampleDashboard/content/activate.php?email=' . $email . '&code=' . $activation;
            $message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
            mail($email, $subject, $message, $headers);
        } else {
            if ($stmt = $conn->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
                $stmt->bind_param('s', $_POST['username']);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($id, $password);
                    $stmt->fetch();
                    if (password_verify($_POST['password'], $password)) {
                        //User has logged in
                        if ($stmt1 = $conn->prepare('SET password_code = ? WHERE username = ?')) {
                            $pass_code = FALSE;
                            $stmt1->bind_param('ss', $pass_code, $_POST['username']);
                        }
                        session_regenerate_id();//This function wiil replace the current session id with a new one, and keep the current session information.
                        $_SESSION['loggedin'] = TRUE;
                        $_SESSION['name'] = $_POST['username'];
                        $_SESSION['id'] = $id;
//                echo "login successful!";
                        header('Location: content/loggedin/home.php');
                    } else {
                        $_SESSION['credentialsValid'] = FALSE;
                    }
                } else {
                    $_SESSION['credentialsValid'] = FALSE;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Welcome Back!</title>
        <meta charset="utf-8">
        <link href="./css/index.css" type="text/css" rel="stylesheet">
        <link href="./css/bootstrap.css" type="text/css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/598d28d35e.js" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Girassol&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
    </head>
    <body>
        <img src="./images/flame-sign-in.png" alt="sign in illustration" class="sign-in-image">
        <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>

        <div id="form-div">
            <h1>Welcome Back!</h1>
            <form method="post">
                <?php
                $val_to_print = "";
                if($_SESSION['credentialsValid'] === FALSE) {
                    $val_to_print = '<label class="font-weight-bold text-center text-danger bg-warning" style = "width: 100%" > Incorrect Username / Password </label >';
                }
                elseif(isset($_SESSION['passwordchange'])) {
                    $val_to_print = '<label class="font-weight-bold text-center text-success bg-white" style = "width: 100%" >Password Change successful!
                    Login with your new password.</label>';
                }
                elseif($nonverifiedaccount === TRUE){
                    $val_to_print = '<label class="font-weight-bold text-center text-danger bg-white" style = "width: 100%" >Please verify your account first.
                    A link has been emailed to you.</label>';
                }

                echo $val_to_print;
                ?>
                <label for="username">
                    <i class="fas fa-user-secret fa-2x"></i>
                </label>
                <input type="text" name="username" placeholder="Username" id="username" required class="<?php
                session_start();
                $val_to_print = "";
                if($_SESSION['credentialsValid'] === FALSE) {
                    $val_to_print = "red-border";
                }
                echo $val_to_print;
                ?>">
                <label for="password">
                    <i class="fas fa-fingerprint fa-2x"></i>
                </label>
                <input type="password" name="password" placeholder="Password" id="password" required class="<?php
                session_start();
                $val_to_print = "";
                if($_SESSION['credentialsValid'] === FALSE) {
                    $val_to_print = "red-border";
                }
                echo $val_to_print;
                ?>">
                <div class="div-right">
                    <a class="forgot-anchor" href="./content/forgotPassword.php">Forgot Password? Recover Here</a>
                </div>
                <input type="submit" value="Sign In">
                <a class="end-anchor" href="content/register.php">New User! Sign Up Here</a>
            </form>
        </div>
        <script src="./js/bootstrap.js" rel="script" type="text/javascript"></script>
    </body>
</html>