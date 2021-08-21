<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    page_open_verification("user");
    
    // Account Details
    $nic = $_SESSION["nic"];
    $account_list = "";

    // Query for get users details
    $query = "SELECT account_number, account_type, current_balance
        FROM accounts
        WHERE nic = '{$nic}'
        AND is_deleted = 0
        ORDER BY account_number;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result, "home_user.php");

    while ($user = mysqli_fetch_assoc($result)) {
        $account_list .= "<a class=\"account_number\" href=\"view_transactions.php?account_number={$user['account_number']}\">";
        $account_list .= "<dt>{$user['account_number']}</dt>";
        $account_list .= "<dd class=\"account_data\">Current Balance: Rs. {$user['current_balance']}</dd>";
        $account_list .= "<dd class=\"account_data\">Account Type: {$user['account_type']}</dd>";
        $account_list .= "</a>";
    }

    if ($account_list == "")
        $account_list .= "<dt class=\"account_data\"> No Account Found </dt>";

    // Page name
    $page_name = "view_account";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Accounts (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="icon" href="img/SM.jpg"/>
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>
        
        <div class="web_body clearfix">
            <?php
                // Check for errors
                if (isset($_GET["err"]))
                    display_single_error($_GET["err"]);
            ?>

            <dl class="account">
                <?php echo $account_list; ?>
            </dl> <!-- account -->
        </div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>