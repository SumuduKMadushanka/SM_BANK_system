<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php

    // Check if click Log IN button
    if (isset($_POST["submit"])) {
        // Errors array for put errors if exists
        $errors = array();

        // Check if username and password are correctly entered
        $required_fields = array("username", "password");
        $errors = array_merge($errors, check_required_fields($required_fields));

        // Check are there any errors
        if (empty($errors)) {
            // save username and password in variables
            $username = mysqli_real_escape_string($connection, $_POST["username"]);
            $password = mysqli_real_escape_string($connection, $_POST["password"]);

            // Query create
            $query = "SELECT username, nic, hashed_password, salt
                FROM user_identity
                WHERE username = '{$username}'
                AND is_deleted = 0
                LIMIT 1;";
            $result = mysqli_query($connection, $query);

            // Verify query
            verify_query($result);

            // Verify User
            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                $hashed_password = sha1($password . strval($user['salt']));

                // Check if username and password are correct
                if ($user['username'] == $username && $user['hashed_password'] == $hashed_password) {
                    // Save User NIC to get user details
                    $nic = $user['nic'];
                    // Get user details from users table
                    $query = "SELECT first_name, last_name, user_type
                        FROM users
                        WHERE nic = '{$nic}'
                        LIMIT 1;";
                    $result = mysqli_query($connection, $query);

                    // Verify query
                    verify_query($result);
                    if (mysqli_num_rows($result) == 1) {
                        $user_details = mysqli_fetch_assoc($result);

                        // Valid user found
                        $_SESSION["nic"] = $nic;
                        $_SESSION["first_name"] = $user_details["first_name"];
                        $_SESSION["last_name"] = $user_details["last_name"];
                        $_SESSION["user_type"] = $user_details["user_type"];
                        $_SESSION["expire"] = time() + 60;
                        
                        // Updating last Login
                        $query = "UPDATE users
                            SET last_login = NOW()
                            WHERE nic = '{$nic}'
                            LIMIT 1;";
                        $result = mysqli_query($connection, $query);

                        // Verify query
                        verify_query($result);

                        // Redirect to home pages
                        if ($user_details["user_type"] == "admin") exit(header("Location: home_admin.php"));
                        if ($user_details["user_type"] == "user") exit(header("Location: home_user.php"));
                    } else {
                        $errors[] = "User Not Found";
    
                    }

                } else {
                    $errors[] = "Invalid username / pasword";

                }
            } else {
                $errors[] = "Invalid username / pasword";

            }

        }

    } else {
        clear_session();

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <div class="login_title">
        <h1>SM BANK</h1>
    </div> <!-- login_title -->

    <div class="login">
        <form action="index.php" method="post">
            <h1>Log In</h1>

            <?php
                // Check for errors
                if (isset($errors) && !empty($errors)) {
                    clear_session();
                    echo "<p class=\"error\">Invalid Username / Password </p>";
                }
            ?>

            <?php
                // Check if Log Out user
                if (isset($_GET["logout"])) {
                    echo "<p class=\"info\">Logout Successfull </p>";
                }
            ?>

            <p>
                <label for="username">Username: </label>
                <input type="username" name="username" id="username" placeholder="Username">
            </p>

            <p>
                <label for="password">Password: </label>
                <input type="password" name="password" id="password" placeholder="Password">
            </p>

            <p>
                <button type="submit" name="submit">Log In</button>
            </p>
        </form>
    </div> <!-- login -->
</body>
</html>