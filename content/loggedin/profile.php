<?php
    $page_title = 'Profile';
    $page_name = 'profile';
    $fname = "Mr.";
    $lname = "Unknown";
    $birthdate = '01-01-2000';
    $gender = 'M';
    $email = 'unknown@unknown.com';
    $profilepic = '../../images/male.png';
    include '../include/header.php';
?>
        <div class="profile-opace-div">
            <div class="border-div">
                <img src="
                    <?php
                    $to_echo = '../../images/male.png';
                    if($profilepic != 'none') {
                        $to_echo = '../uploads/' . $_SESSION['id'] . '.' . $profilepic;
                    } else {
                        if($gender == 'M' || $gender=='O')
                            $to_echo = '../../images/male.png';
                        elseif ($gender == 'F')
                            $to_echo = '../../images/female.png';
                    }
                    echo $to_echo;
                    ?>
                 " class="profile-picture" alt="Profile Picture" id="profile-pic">
                <form action="../uploads/upload.php" method="post" enctype="multipart/form-data">
                    <div class="custom-file file-input-div">
                        <input name="fileToUpload" type="file" class="custom-file-input" id="customFile" onchange="fileUpload()" required>
                        <label class="custom-file-label" id="file-label" for="customFile">Update Picture</label>
                    </div>
                    <input type="submit" value="Upload" class="file-upload-button">
                </form>
                <label class="information-label">*max file size 1MB</label>
                <div class="personal-information-div">
                    <p class="head-label">Name: </p>
                    <p class="head-label">Username: </p>
                    <p class="head-label">Date Of Birth: </p>
                    <p class="head-label">Gender: </p>
                    <p class="head-label">Email: </p>
                </div>
                <div class="personal-values-div">
                    <p class="info-label"><?php echo $fname . " " . $lname?></p>
                    <p class="info-label"><?php echo $_SESSION['name']?></p>
                    <p class="info-label"><?php echo $birthdate?></p>
                    <p class="info-label">
                        <?php
                            $val_to_echo = '';
                            if($gender === 'M')
                                $val_to_echo = 'Male';
                            elseif($gender === 'F')
                                $val_to_echo = 'Female';
                            elseif($gender === 'O')
                                $val_to_echo = 'Other';
                            echo $val_to_echo;
                        ?></p>
                    <p class="info-label"><?php echo $email;?></p>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Add the following code if you want the name of the file appear on select
        function fileUpload() {
            var fileName = $('#customFile').val().split("\\").pop();
            $('#file-label').addClass("selected").html(fileName);
        }
    </script>
</body>
</html>