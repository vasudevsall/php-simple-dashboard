<?php
session_start();

include ('../../../config.php');

switch ($_REQUEST['action']){
    case "sendMessage":
        if($stmt = $conn->prepare("INSERT INTO messages SET user=?, message=?, name =?, pic=?, gender=?")){
            $name = $_SESSION['fname'] . " " . $_SESSION['lname'];
            $stmt->bind_param('sssss',$_SESSION['name'], $_REQUEST['message'], $name, $_SESSION['profilePic'], $_SESSION['gender']);
            $stmt->execute();
            if($stmt->errno)
                header("Location: ../../error.php");
            else
                echo 1;
        } else {
            header("Location: ../../error.php");
        }
        break;

    case "getMessages":
        $query = "SELECT * FROM(SELECT * FROM messages ORDER BY id DESC LIMIT 150) sub ORDER BY id ASC";
        if ($stmt = $conn->prepare($query)){
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id, $user, $message, $date, $name, $profilepic, $gender);

            $chat = '';
            while($stmt->fetch()){
                if($profilepic === 'none'){
                    if($gender === 'M' || $gender === 'O')
                        $imgUrl = "../../images/male.png";
                    else
                        $imgUrl = "../../images/female.png";
                } else{
                    $imgUrl = "../uploads/" . $profilepic;
                }


                if($user == $_SESSION['name']){
                    $chat .= '<div class="to-message">
                    <div class="to-message-wrapper">
                        <label class="profile-name" onclick="profileClick(\''.$user.'\')">'.$name.'</label>
                        <hr class="to-divider">
                        <p class="message-content">'.$message.'</p>
                    </div>
                    <div class="right-triangle"></div>
                    <img src='.$imgUrl.' alt="profile-picture" class="to-image" onclick="profileClick(\''.$user.'\')">
                </div>';
                } else {
                    $chat .= '<div class="from-message">
                    <img src='.$imgUrl.' alt="profile-picture" class="from-image" onclick="profileClick(\''.$user.'\')">
                    <div class="left-triangle"></div>
                    <div class="message-wrapper">
                        <label class="profile-name" onclick="profileClick(\''.$user.'\')">'.$name.'</label>
                        <hr class="divider">
                        <p class="message-content">'.$message.'</p>
                    </div>
                </div>';
                }
            }
            echo $chat;
        } else {
            header("Location: ../../error.php");
        }
        break;
}
?>