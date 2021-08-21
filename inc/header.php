<header class="clearfix">
    <div class="bank_header">
        <div class="bank_logo">
            <a href="home_user.php"><img src="img/SM.jpg"></a>
        </div> <!-- bank_logo -->

        <div class="bank_name">
            SM Bank
        </div> <!-- bank_name -->
    </div> <!-- bank_header -->

    <div class="user_header">
        <div class="user_full_name">
            Welcome

            <?php
                $full_name = $_SESSION["first_name"] . " " . $_SESSION["last_name"];
                echo $full_name;
            ?>!
        </div> <!-- user_name -->

        <div class="last_login">
            Last Login:

            <?php echo $_SESSION["last_login"] ?>
        </div> <!-- last_login -->

        <a class="cancel" href="logout.php">Log Out</a> <!-- cancel -->
    </div> <!-- user_header -->
</header>