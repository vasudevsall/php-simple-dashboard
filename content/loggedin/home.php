<?php
    $page_title = "Home";
    $page_name = "home";
    include '../include/header.php'
?>
        <p class="home-text">Welcome <?php echo $_SESSION['name']?>!<br>Check out your profile <a href="profile.php" class="home-anchor">here</a></p>
        <img src="../../images/flame-welcome.png" alt="welcome illustration" class="home-img">
    </div>
</body>
</html>