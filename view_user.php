<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    verify_session_attribute("nic");
    verify_user_type($_SESSION["user_type"], "user");
    verify_session_expired();
    
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
        
        <div class="web_body clearfix">test2</div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>