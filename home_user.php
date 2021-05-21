<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    verify_session_attribute("nic");
    verify_user_type($_SESSION["user_type"], "user");
    verify_session_expired();

    // Current balance and loan ammount
    $current_balance = 0.0;
    $current_loan = 0.0;

    // Current balance
    $query = "SELECT current_balance
        FROM accounts
        WHERE nic = '{$_SESSION["nic"]}';";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result);
    while ($user = mysqli_fetch_assoc($result)) {
        $current_balance += $user["current_balance"];
    }
    
    // Page name
    $page_name = "home";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>
        
        <div class="web_body clearfix">
            <table class="balance_table">
                <tr>
                    <td class="balance_table_label"> Current Available Balance </td> <!-- balance_table_label -->

                    <td> : Rs. </td>

                    <td class="balance_table_data"> <?php echo number_format($current_balance, 2, '.', ''); ?> </td> <!-- balance_table_data -->
                </tr>

                <tr>
                    <td class="balance_table_label"> Current Loan Amount </td> <!-- balance_table_label -->

                    <td> : Rs. </td>

                    <td class="balance_table_data"> <?php echo number_format($current_loan, 2, '.', ''); ?> </td> <!-- balance_table_data -->
                </tr>
            </table> <!-- balance_table -->
        </div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>