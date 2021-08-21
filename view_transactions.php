<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    page_open_verification("user");
    
    // Check if account_number is set
    verify_get_attribute("account_number", "view_accounts.php");
    
    // transaction details
    $account_number = mysqli_real_escape_string($connection, $_GET["account_number"]);
    $transaction_list = "";
    $account_details = "";

    // Query for get account details
    $query = "SELECT account_type, current_balance
        FROM accounts
        WHERE account_number = '{$account_number}'
        AND is_deleted = 0
        ORDER BY account_number;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result, "home_user.php");
    $user = mysqli_fetch_assoc($result);

    // Account Details
    $account_details .= "<dt>{$account_number}</dt>";
    $account_details .= "<dd class=\"account_data\">Current Balance: Rs. {$user['current_balance']}</dd>";
    $account_details .= "<dd class=\"account_data\">Account Type: {$user['account_type']}</dd>";

    // Query for get transaction details
    $query = "SELECT time_stamp, description, money_transfer_id, amount, balance
        FROM transactions
        WHERE account_number = '{$account_number}'
        ORDER BY time_stamp DESC;";
    $result = mysqli_query($connection, $query);

    // Verify query
    verify_query($result, "home_user.php");

    while ($user = mysqli_fetch_assoc($result)) {
        $transaction_list .= "<tr>";
        $transaction_list .= "<td class=\"view_transaction_data\"> {$user['time_stamp']} </td>";
        $transaction_list .= "<td class=\"view_transaction_data\"> {$user['description']} </td>";
        if ($user["money_transfer_id"] == NULL)
            $transaction_list .= "<td class=\"view_transaction_data\"> N/A </td>";

        else
            $transaction_list .= "<td class=\"view_transaction_data\"> {$user['money_transfer_id']} </td>";

        $transaction_list .= "<td class=\"view_transaction_data\"> {$user['amount']} </td>";
        $transaction_list .= "<td class=\"view_transaction_data\"> {$user['balance']} </td>";
        $transaction_list .= "</tr>";

    }

    if ($transaction_list == "")
        exit(header("Location: view_accounts.php?err=account_not_found"));

    // Page name
    $page_name = "view_transactions";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Transactions (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="icon" href="img/SM.jpg"/>
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>
        
        <div class="web_body clearfix">
            <dl class="account">
                <?php echo $account_details; ?>
            </dl> <!-- account -->

            <table class="view_transaction">
                <tr>
                    <th class="view_transaction_header"> Transaction Time </th>
                    <th class="view_transaction_header"> Description </th>
                    <th class="view_transaction_header"> Money Transfer Id </th>
                    <th class="view_transaction_header"> Amount (Rs.) </th>
                    <th class="view_transaction_header"> Balance (Rs.) </th>
                </tr>

                <?php echo $transaction_list; ?>
                
            </table>
        </div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>