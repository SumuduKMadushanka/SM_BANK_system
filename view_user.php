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
    $user_type = "";
    $address = "";
    $birthday = "";
    $profession = "";
    $contact_number = "";
    $email = "";

    $query = "SELECT first_name, last_name, username, user_type, address, birthday, profession, contact_number, email
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
        $user_type = $user["user_type"];
        $address = $user["address"];
        $birthday = $user["birthday"];
        $profession = $user["profession"];
        $contact_number = $user["contact_number"];
        $email = $user["email"];
    }

    // Page name
    $page_name = "view_user";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Profile (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>
        
        <div class="web_body clearfix">
            <table class="view_user_table">
                <tr>
                    <td class="view_table_lable"> First Name </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $first_name; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Last Name </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $last_name; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Username </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $username; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> User Type </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $user_type; ?>  </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> NIC </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $nic; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Address </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $address; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Birthday </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $birthday; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Profession </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $profession; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Contact Number </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $contact_number; ?> </td> <!-- view_table_data -->
                </tr>

                <tr>
                    <td class="view_table_lable"> Email </td> <!-- view_table_lable -->
                    <td> : </td>
                    <td class="view_table_data"> <?php echo $email; ?> </td> <!-- view_table_data -->
                </tr>
            </table>
        </div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>