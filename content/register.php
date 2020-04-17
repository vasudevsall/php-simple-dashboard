<?php
session_start();
$invalidinput = FALSE;
$invalidemail = FALSE;
$invalidusername = FALSE;
$usernameexists = FALSE;
$invalidpass = FALSE;
$verificationSent = FALSE;
$duplicateemail = FALSE;
$invaliddate = FALSE;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $database_host = 'localhost';
    $database_user = 'root';
    $database_pass = '';
    $database_name = 'sampledashboard';
    $conn = mysqli_connect($database_host, $database_user, $database_pass,$database_name);
    if(mysqli_connect_errno()){
        header('Location: error.html');
//        exit("Connection Error");
    }

    if(!isset($_POST['username'], $_POST['password'], $_POST['firstname'], $_POST['lastname'], $_POST['birthdate'], $_POST['email'], $_POST['gender'])) {
        $invalidinput = TRUE;
//        exit("Invalid Input Error");
    }
    if(empty($_POST['username'])|| empty($_POST['password'])|| empty($_POST['firstname'])
        ||empty($_POST['lastname'])|| empty($_POST['birthdate']) || empty($_POST['email']) || empty($_POST['gender'])) {
        $invalidinput = TRUE;
//        exit("Invalid Input Error");
    }

    if($invalidinput === FALSE) {
        $datebirth = $_POST['birthdate'];
        $datevalid = date('Y-m-d', strtotime('-13 years'));

        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $invalidemail = TRUE;
//            exit("invalid email Error");
        } else {
            if($stmt = $conn->prepare('SELECT id, username FROM accounts WHERE email = ?')) {
                $stmt->bind_param('s', $_POST['email']);
                $stmt->execute();
                $stmt->store_result();
                if($stmt->num_rows > 0){
                    $duplicateemail = TRUE;
//                    exit("duplicate email");
                }
                $stmt->close();
            }
        }
        if(preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
            $invalidusername = TRUE;
//            exit("Invalid Username Error");
        } else {
            if ($stmt = $conn->prepare('SELECT id, password FROM accounts where username = ?')) {
                $stmt->bind_param('s', $_POST['username']);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $usernameexists = TRUE;
                }
                $stmt->close();
            }
        }
        if(strlen($_POST['password'])>20 || strlen($_POST['password'])<4){
            $invalidpass = TRUE;
//            exit("Invalid password Error");
        }
        if ($datebirth > $datevalid) {
            $invaliddate = TRUE;
//            exit("Date invalid");
        }

        if($invalidemail === FALSE && $invalidusername === FALSE && $invalidpass === FALSE && $duplicateemail === FALSE && $invaliddate === FALSE) {
                    if ($stmt = $conn->prepare('INSERT INTO accounts (username, password, email, activation_code, first_name, last_name, gender, birth_date)
                                   VALUES (?,?,?,?,?,?,?,?)')) {
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $uniqid = uniqid();
                        $date = date_create($_POST['birthdate']);
                        $date = date_format($date, "d-m-Y");
                        $stmt->bind_param('ssssssss', $_POST['username'], $password, $_POST['email'],
                            $uniqid, $_POST['firstname'], $_POST['lastname'], $_POST['gender'], $_POST['birthdate']);
                        $stmt->execute();
                        $from = 'noreply@sampledashboard.com';
                        $subject = 'Account Activation Required';
                        $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                        $activate_link = 'http://localhost/sampleDashboard/content/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
                        $message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
                        mail($_POST['email'], $subject, $message, $headers);
                        $verificationSent = TRUE;
                    } else {
                        header('Location: error.html');
                    }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="../css/register.css" type="text/css" rel="stylesheet">
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/598d28d35e.js" crossorigin="anonymous"></script>
    <title>Sign Up</title>
    <script>
        function checkDate() {
            console.log("hi");
            var today =new Date();
            var today1 = Date.parse(today);
            console.log("hi3",today1);
            var dOB = Date.parse(document.getElementById("birthdate").value);
            console.log("hi4",dOB);
            console.log("Hi2",today1-dOB);
            if((today1-dOB)<410240038000) {
                document.getElementById("birthdate").classList.add("red-border");
            }
            else {
                document.getElementById("birthdate").classList.remove("red-border");
            }
        }

    </script>
</head>
<body>
    <img src="../images/flame-sign-up.png" alt="sign up illustration" class="sign-up-image">
    <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>

    <div id="form-div">
        <h1>Register Here!</h1>
        <form method="post">
            <?php
            $val_to_print = "";
            if($invalidinput === TRUE) {
                $val_to_print = '<label class="font-weight-bold text-center text-danger bg-white" style = "width: 100%" >Invalid Inputs</label >';
            }
            elseif ($verificationSent === TRUE) {
                $val_to_print = '<label class="font-weight-bold text-center text-success bg-white" style = "width: 100%" >Registration Successful! 
                    A verification link has been sent to your mail.</label>';
            }
            echo $val_to_print;
            ?>
            <label for="firstname">
                <i class="fas fa-user fa-2x"></i>
            </label>
            <input type="text" name="firstname" placeholder="First Name" id="firstname" required>
            <label for="lastname">
                <i class="fas fa-user-tie fa-2x"></i>
            </label>
            <input type="text" name="lastname" placeholder="Last Name" id="lastname" required>
            <label for="username">
                <i class="fas fa-user-secret fa-2x"></i>
            </label>
            <input type="text" name="username" placeholder="Username" id="username" required
                <?php
                $val_to_echo = '';
                if($invalidusername === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Invalid Username</p';
                }
                elseif ($usernameexists === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Username already exists</p';
                }
                echo $val_to_echo;
                ?>>
            <label for="birthdate">
                <i class="fas fa-birthday-cake fa-2x"></i>
            </label>
            <input placeholder="Date of Birth (min age 13 years)" name="birthdate"
                   type="text" onchange="checkDate()" onfocus="(this.type='date')" onblur="(this.type='text')" id="birthdate" required
                <?php
                $val_to_echo = '';
                if($invaliddate === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Minimum age required is 13 years</p';
                }
                echo $val_to_echo;
                ?>>
            <label for="email">
                <i class="fas fa-at fa-2x"></i>
            </label>
            <input type="text" name="email" placeholder="Email" id="email" required
                <?php
                $val_to_echo = '';
                if($invalidemail === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Invalid Email</p';
                }
                elseif ($duplicateemail === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Email already exists</p';
                }
                echo $val_to_echo;
                ?>>
            <label for="password">
                <i class="fas fa-fingerprint fa-2x"></i>
            </label>
            <input type="password" name="password" placeholder="Password (4-20 characters long)" id="password" required
                <?php
                $val_to_echo = '';
                if($invalidpass === TRUE) {
                    $val_to_echo = 'class = "red-border"> <p class="text-danger" style="width: 100%; text-align: center">Invalid Password</p';
                }
                echo $val_to_echo;
            ?>>
            <label for="gender-div">
                <i class="fas fa-venus-mars fa-2x"></i>
            </label>
            <div id="gender-div">
                <label for="male">Male</label>
                <input type="radio" id="male" name="gender" value="M" required>
                <label for="female">Female</label>
                <input type="radio" id="female" name="gender" value="F" required>
                <label for="otherg">Other</label>
                <input type="radio" id="otherg" name="gender" value="O" required>
            </div>
            <input type="submit" value="Register">
            <a class="end-anchor" href="../index.php">Already a user? Sign in Here!</a>
        </form>
    </div>
</body>
</html>