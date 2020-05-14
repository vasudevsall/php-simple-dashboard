<?php
session_start();
$_SESSION['accountactivation'] = 0;
$database_host = 'localhost';
$database_user = 'root';
$database_pass = '';
$database_name = 'sampledashboard';

$conn = mysqli_connect($database_host, $database_user, $database_pass, $database_name);
if(mysqli_connect_errno()) {
    header('Location: error.php');
}

if(isset($_GET['email'], $_GET['code'])) {
    if($stmt = $conn->prepare('SELECT * FROM accounts WHERE email = ? AND activation_code = ?')) {
        $stmt->bind_param('ss', $_GET['email'], $_GET['code']);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            if($stmt = $conn->prepare('UPDATE accounts SET activation_code = ? WHERE email=? and activation_code=?')){
                $newcode = 'activated';
                $stmt->bind_param('sss', $newcode, $_GET['email'], $_GET['code']);
                $stmt->execute();
                $_SESSION['accountactivation'] = 1;
            }
            else {
                $_SESSION['accountactivation'] = 2;
            }
        } elseif ($stmt = $conn->prepare('SELECT * FROM accounts WHERE email=? AND activation_code = ?')){
            $activation = 'activated';
            $stmt->bind_param('ss', $_GET['email'], $activation);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $_SESSION['accountactivation'] = 2;
            }else {
                $_SESSION['accountactivation'] = 3;
            }
        }
    } else {
        header('Location: error.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="../css/loader.css" type="text/css" rel="stylesheet">
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet">
    <script>
        var choice = 0;
        setInterval(function(){
            choice = <?php echo $_SESSION['accountactivation'];?>;
            if(choice === 1) {
                document.getElementById("wait-div").classList.add("invisible");
                document.getElementById("successful-div").classList.remove("invisible");
            }
            else if(choice === 2){
                document.getElementById("wait-div").classList.add("invisible");
                document.getElementById("verified-div").classList.remove("invisible");
            }
            else if(choice === 3) {
                document.getElementById("wait-div").classList.add("invisible");
                document.getElementById("no-account-div").classList.remove("invisible");
            }
        },500);
    </script>
    <title>Activation</title>
</head>
<body>
    <div class="center-div" id="wait-div">
        <p class="wait-text">Please Wait while we verify your account...</p>
        <div class='loader loader1'>
            <div>
                <div>
                    <div>
                        <div>
                            <div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="center-div invisible" id="successful-div">
        <p class="wait-text">Account Successfully Verified!</p>
        <a href="../index.php" class="verified-anchor">Login Here</a>
        <img src="../images/flame-success.png" class="fixed-image" alt="success illustration">
        <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>
    </div>
    <div class="center-div invisible" id="verified-div">
        <p class="wait-text">Account already verified</p>
        <a href="../index.php" class="verified-anchor">Login Here</a>
        <img src="../images/flame-success.png" class="fixed-image" alt="unsuccessful illustration">
        <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>
    </div>
    <div class="center-div invisible" id="no-account-div">
        <p class="wait-text">Account doesn't exist</p>
        <a href="../content/register.php" class="verified-anchor">Register Here</a>
        <img src="../images/flame-unsubscribed.png" class="fixed-image" alt="unsuccessful illustration">
        <a href="https://icons8.com" class="copy-anchor">Image by icons8</a>
    </div>
</body>
</html>