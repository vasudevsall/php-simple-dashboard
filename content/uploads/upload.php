<?php
session_start();
$target_dir = "";
$target_file = $target_dir . basename($_FILES['fileToUpload']['name']);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$file_uploaded = FALSE;

$database_host = 'localhost';
$database_user = 'root';
$database_pass = '';
$database_name = 'sampledashboard';

$conn = mysqli_connect($database_host, $database_user, $database_pass, $database_name);
if(mysqli_connect_errno()) {
    header("Location: ../error.html");
}

if(isset($_POST["submit"])) {
    $check = getimagesize(($_FILES['fileToUpload']['tmp_name']));
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}
if ($_FILES['fileToUpload']['size'] > 1000000) {
    echo "File too large";
    $uploadOk = 0;
}
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
    echo "invalid File";
    $uploadOk = 0;
}

if($uploadOk == 1){
    if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $old_name = $_FILES['fileToUpload']['name'];
        $new_name = $_SESSION['id'] . '.' . $imageFileType;
        rename($old_name, $new_name);
        $_SESSION['file_uploaded'] = TRUE;
        if ($stmt = $conn->prepare('UPDATE accounts SET profile_pic = ? WHERE id=?')) {
            $stmt->bind_param('ss',$imageFileType, $_SESSION['id']);
            $stmt->execute();
//            exit("Executed");
        }
        else
            exit("Invalid Statement");
        header('Location: ../loggedin/profile.php');
    } else {
        $_SESSION['file_uploaded'] = FALSE;
    }
} else {
    $_SESSION['invalid_file'] = TRUE;
}
?>