<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    page_open_verification("user");
    
    // User Details
    $nic = $_SESSION["nic"];
    $username = "";

    // Select query for get user details
    $query = "SELECT username, hashed_password, salt
        FROM user_identity
        WHERE nic = '{$nic}'
        LIMIT 1;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result, "home_user.php");
    if (mysqli_num_rows($result) == 1) {
        // Save the user details
        $user = mysqli_fetch_assoc($result);
        $username = $user["username"];
    }

    // If submit change password form
    if (isset($_POST["submit"])) {
        $changed_password = false;
        $errors = array();

        // Check for required fields
        $required_fields = array("username", "old_password", "password", "confirm_password");
        $errors = array_merge($errors, check_required_fields($required_fields));

        // Check if password is correct
        $hashed_password = sha1($_POST["old_password"] . strval($user['salt']));
        if ($user['hashed_password'] != $hashed_password) {
            $errors[] = "Invalid Password"; 
        }

        // Confirms enter password twice correctly
        if ($_POST["password"] != $_POST["confirm_password"]) {
            $errors[] = "Password and Confirm Password must match";
        }

        // If not errors in form
        if (empty($errors)) {
            $password = mysqli_real_escape_string($connection, $_POST["password"]);
            $salt = $user["salt"];
            $hashed_password = sha1($password . strval($salt));

            if ($hashed_password == $user["hashed_password"]) {
                $errors[] = "Password already used";

            } else {
                $query = "UPDATE user_identity
                    SET hashed_password = '{$hashed_password}'
                    WHERE username = '{$username}'
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);
                
                // Verify query
                verify_query($result, "home_user.php");
                $changed_password = true;

            }
        }

    }

    // Page name
    $page_name = "change_password";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="icon" href="img/SM.jpg"/>
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>
        
        <div class="web_body clearfix">
            <form action="change_password.php" method="post">
                <?php
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        display_multiple_errors($errors);
                    }
                ?>

                <?php
                    // Check if User Modified successfully
                    if (isset($changed_password) && $changed_password) {
                        $_POST = array();
                        echo "<p class=\"info\"> Password Changed </p>";
                    }
                ?>

                <input type="hidden" name="username" value=<?php echo "'{$nic}'"; ?>>

                <p>
                    <label for="user_name"> Username: </label>
                    <input type="text" name="user_name" id="user_name" placeholder="Username" 
                        value=<?php echo "'{$username}'"; ?> disabled>
                </p>

                <p>
                    <label for="old_password"> Old Password: </label>
                    <input type="password" name="old_password" id="old_password" placeholder="Old Password" 
                        required>
                </p>

                <p>
                    <label for="password"> New Password: </label>
                    <input type="password" name="password" id="password" placeholder="Password" 
                        required>
                </p>

                <p class="checkbox">
                    <label for="showpassword"> Show Passwords: </label>
                    <input type="checkbox" name="showpassword" id="showpassword">
                </p>

                <p>
                    <label for="confirm_password"> Confirm Password: </label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" 
                        required>
                </p>

                <p>
                    <button type="submit" name="submit"> Changes Password </button>
                    <a class="cancel" href="home_user.php">Cancel</a> <!-- cancel -->
                </p>
            </form>
        </div> <!-- web_body -->
    </main> <!-- main_body -->

    <script src="js/jquery.js"></script>
    <script>
        $(document).ready(function() {
            $('#showpassword').click(function() {
                if ($('#showpassword').is(':checked')) {
                    $('#password').attr('type', 'text');
                    $('#old_password').attr('type', 'text');

                } else{
                    $('#password').attr('type', 'password');
                    $('#old_password').attr('type', 'password');
                    
                }
            });
        });
    </script>
</body>
</html>

<?php mysqli_close($connection); ?>