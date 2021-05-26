<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    page_open_verification("user");
    
    // User Details
    $nic = $_SESSION["nic"];
    $first_name = "";
    $last_name = "";
    $username = "";
    $address = "";
    $birthday = "";
    $profession = "";
    $contact_number = "";
    $email = "";

    $query = "SELECT first_name, last_name, username, address, birthday, profession, contact_number, email
        FROM users INNER JOIN user_identity
        ON users.nic = user_identity.nic
        WHERE users.nic = '{$nic}'
        LIMIT 1;";
    $result = mysqli_query($connection, $query);
    verify_query($result, "home_user.php");

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $first_name = $user["first_name"];
        $last_name = $user["last_name"];
        $username = $user["username"];
        $address = $user["address"];
        $birthday = $user["birthday"];
        $profession = $user["profession"];
        $contact_number = $user["contact_number"];
        $email = $user["email"];
    }

    if (isset($_POST["submit"])) {
        $user_updated = false;
        $errors = array();

        // Check for required fields
        $required_fields = array("first_name", "username", "address", "birthday", "contact_number");
        $errors = array_merge($errors, check_required_fields($required_fields));

        // Checks max length
        $field_max_length = array(
            "first_name" => 50,
            "last_name" => 50,
            "username" => 101,
            "address" => 200,
            "birthday" => 10,
            "profession" => 100,
            "contact_number" => 10,
            "email" => 100
        );
        $errors = array_merge($errors, check_field_max_length($field_max_length));

        // Validate email
        if (!empty(trim($_POST["email"])) && !is_email($_POST["email"]))
            $errors[] = "Invalid Email";

        if (empty($errors)) {
            $first_name_new = mysqli_real_escape_string($connection, trim($_POST["first_name"]));
            $last_name_new = mysqli_real_escape_string($connection, trim($_POST["last_name"]));
            $username_new = mysqli_real_escape_string($connection, trim($_POST["username"]));
            $address_new = mysqli_real_escape_string($connection, trim($_POST["address"]));
            $birthday_new = mysqli_real_escape_string($connection, trim($_POST["birthday"]));
            $profession_new = mysqli_real_escape_string($connection, trim($_POST["profession"]));
            $contact_number_new = mysqli_real_escape_string($connection, trim($_POST["contact_number"]));
            $email_new = mysqli_real_escape_string($connection, trim($_POST["email"]));

            if ($first_name_new != $first_name ||
                $last_name_new != $last_name ||
                $username_new != $username ||
                $address_new != $address ||
                $birthday_new != $birthday ||
                $profession_new != $profession ||
                $contact_number_new != $contact_number ||
                $email_new != $email
            ) {
                $first_name = $first_name_new;
                $last_name = $last_name_new;
                $username = $username_new;
                $address = $address_new;
                $birthday = $birthday_new;
                $profession = $profession_new;
                $contact_number = $contact_number_new;
                $email = $email_new;

                // Check for existance of email
                $query = "SELECT nic
                    FROM users
                    WHERE email = '{$email}' AND nic != '{$nic}'
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");
                if (mysqli_num_rows($result) == 1)
                    $errors[] = "Email already exists";

                // Check for existance of username
                $query = "SELECT nic
                    FROM user_identity
                    WHERE username = '{$username}' AND nic != '{$nic}'
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");
                if (mysqli_num_rows($result) == 1)
                    $errors[] = "Username already exists";

                if (empty($errors)) {
                    // Update the Database
                    $query = "UPDATE users 
                        SET first_name = '{$first_name}',
                            last_name = '{$last_name}',
                            address = '{$address}',
                            birthday = '{$birthday}',
                            profession = '{$profession}',
                            contact_number = '{$contact_number}',
                            email = '{$email}'
                        WHERE nic = '{$nic}'
                        LIMIT 1;";

                    $query .= "UPDATE user_identity
                        SET username = '{$username}'
                        WHERE nic = '{$nic}'
                        LIMIT 1;";
                    $result = mysqli_multi_query($connection, $query);
                    verify_query($result, "home_user.php");

                    $_SESSION["first_name"] = $first_name;
                    $_SESSION["last_name"] = $last_name;
                    $user_updated = true;
                }

            }
        }

    }

    // Page name
    $page_name = "modify_user";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>
        
        <div class="web_body clearfix">
            <form action="modify_user.php" method="post">
                <?php
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        display_multiple_errors($errors);
                    }
                ?>

                <?php
                    // Check if User Modified successfully
                    if (isset($user_updated) && $user_updated) {
                        $_POST = array();
                        echo "<p class=\"info\">Modified User Successfull </p>";
                    }
                ?>

                <p>
                    <label class="modify_user" for="first_name"> First Name: </label>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" 
                        value=<?php echo "'{$first_name}'"; ?> required>
                </p>

                <p>
                    <label class="modify_user" for="last_name"> Last Name: </label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name" 
                        value=<?php echo "'{$last_name}'"; ?>>
                </p>

                <p>
                    <label class="modify_user" for="username"> Username: </label>
                    <input type="text" name="username" id="username" placeholder="Username" 
                        value=<?php echo "'{$username}'"; ?> required>
                </p>

                <p>
                    <label class="modify_user" for="address"> Address: </label>
                    <input type="text" name="address" id="address" placeholder="Address" 
                        value=<?php echo "'{$address}'"; ?> required>
                </p>

                <p>
                    <label class="modify_user" for="birthday"> Birthday: </label>
                    <input type="text" name="birthday" id="birthday" placeholder="Birthday" 
                        value=<?php echo "'{$birthday}'"; ?> required>
                </p>

                <p>
                    <label class="modify_user" for="profession"> Profession: </label>
                    <input type="text" name="profession" id="profession" placeholder="Profession" 
                        value=<?php echo "'{$profession}'"; ?>>
                </p>

                <p>
                    <label class="modify_user" for="contact_number"> Contact Number: </label>
                    <input type="text" name="contact_number" id="contact_number" placeholder="Contact Number" 
                        value=<?php echo "'{$contact_number}'"; ?> required>
                </p>

                <p>
                    <label class="modify_user" for="email"> Email: </label>
                    <input type="email" name="email" id="email" placeholder="Email" 
                        value=<?php echo "'{$email}'"; ?>>
                </p>

                <p>
                    <button type="submit" name="submit">Save Changes</button>
                    <a class="cancel" href="home_user.php">Cancel</a> <!-- cancel -->
                </p>
            </form>
        </div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>