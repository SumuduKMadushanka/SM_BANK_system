<?php session_start(); ?>

<?php require_once("inc/connection.php"); ?>
<?php require_once("inc/functions.php"); ?>

<?php
    // Check if user is logged and verify the user
    page_open_verification("user");
    
    // Transfer Details
    $nic = $_SESSION["nic"];
    $user_account_list = "";
    $user_email = "";
    $user_account = "";
    $beneficiary_account = "";
    $beneficiary_account_list = "";
    $beneficiary_name = "";
    $beneficiary_contact_number = "";
    $beneficiary_email = "";

    // Get user data
    $query = "SELECT email
        FROM users
        WHERE nic = '{$nic}'
        LIMIT 1;";
    $result = mysqli_query($connection, $query);
    verify_query($result, "home_user.php");
    $user = mysqli_fetch_assoc($result);
    $user_email = $user["email"];

    $query = "SELECT account_number
        FROM accounts
        WHERE nic = '{$nic}'
        AND is_deleted = 0
        ORDER BY account_number;";
    $result = mysqli_query($connection, $query);
    verify_query($result, "home_user.php");

    while ($user = mysqli_fetch_assoc($result))
        $user_account_list .= "<option value=\"{$user['account_number']}\">";
    
    // Get all accounts list
    $query = "SELECT account_number
        FROM accounts
        WHERE is_deleted = 0
        ORDER BY account_number;";
    $result = mysqli_query($connection, $query);
    verify_query($result, "home_user.php");

    while ($user = mysqli_fetch_assoc($result))
        $beneficiary_account_list .= "<option value=\"{$user['account_number']}\">";

    // Search beneficiary
    if (isset($_POST["search"])) {
        $errors = array();

        // Check for required fields
        $required_fields = array("user_account", "beneficiary_account", "nic");
        $errors = array_merge($errors, check_required_fields($required_fields));

        $user_account = mysqli_real_escape_string($connection, $_POST["user_account"]);
        $beneficiary_account = mysqli_real_escape_string($connection, $_POST["beneficiary_account"]);

        $query = "SELECT nic
            FROM accounts
            WHERE account_number = '{$user_account}';";
        $result = mysqli_query($connection, $query);
        verify_query($result, "home_user.php");
        if (mysqli_num_rows($result) != 1)
            $errors[] = "Invalid User account";
        
        else {
            $user = mysqli_fetch_assoc($result);
            if ($user["nic"] != $nic)
                $errors[] = "Invalid User account";

            else if ($user_account == $beneficiary_account)
                $errors[] = "User account and beneficiary account are same";
        }
        
        if (empty($errors)) {
            $query = "SELECT first_name, last_name, contact_number, email
                FROM accounts INNER JOIN users
                ON accounts.nic = users.nic
                WHERE accounts.account_number = '{$beneficiary_account}'
                LIMIT 1;";
            $result = mysqli_query($connection, $query);
            verify_query($result, "home_user.php");

            if (mysqli_num_rows($result) == 1){
                $user = mysqli_fetch_assoc($result);
                $beneficiary_name = $user["first_name"] . " " . $user["last_name"];
                $beneficiary_contact_number = $user["contact_number"];
                $beneficiary_email = $user["email"];
            } else
                $errors[] = "Beneficiary account NOT FOUND";
        }

    }

    // Submit transfer
    else if (isset($_POST["submit"])) {
        $errors = array();
        $transfer_done = false;

        // Check for required fields
        $required_fields = array("user_account", "beneficiary_account", "nic", "amount", "password");
        $errors = array_merge($errors, check_required_fields($required_fields));

        $password = mysqli_real_escape_string($connection, $_POST["password"]);
        $query = "SELECT hashed_password, salt
            FROM user_identity
            WHERE nic = '{$nic}'
            AND is_deleted = 0
            LIMIT 1;";
        $result = mysqli_query($connection, $query);
        verify_query($result, "home_user.php");
        $user = mysqli_fetch_assoc($result);
        $hashed_password = sha1($password . strval($user['salt']));

        if ($user['hashed_password'] != $hashed_password)
            $errors[] = "Invalid Password";

        if (empty($errors)) {
            $user_account = mysqli_real_escape_string($connection, $_POST["user_account"]);
            $beneficiary_account = mysqli_real_escape_string($connection, $_POST["beneficiary_account"]);
            $amount = floatval(mysqli_real_escape_string($connection, $_POST["amount"]));

            $query = "SELECT current_balance
                FROM accounts
                WHERE account_number = '{$user_account}'
                LIMIT 1;";
            $result = mysqli_query($connection, $query);
            verify_query($result, "home_user.php");
            $user = mysqli_fetch_assoc($result);

            if ($amount <= $user["current_balance"]) {
                $query = "SELECT current_balance
                    FROM accounts
                    WHERE account_number = '{$beneficiary_account}'
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");
                $beneficiary = mysqli_fetch_assoc($result);

                $new_user_balance = $user["current_balance"] - $amount;
                $new_beneficiary_balance = $beneficiary["current_balance"] + $amount;
                $user_amount = 0 - $amount;

                $query = "SELECT money_transfer_id
                    FROM money_transfers
                    ORDER BY money_transfer_id DESC
                    LIMIT 1;";
                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");
                if (mysqli_num_rows($result) == 1) {
                    $transfer_id = mysqli_fetch_assoc($result);
                    $money_transfer_id = $transfer_id["money_transfer_id"] + 1;
                } else
                    $money_transfer_id = 1;

                $query = "INSERT INTO money_transfers(
                    money_transfer_id, time_stamp, debit_account, credit_account, amount)
                    VALUES(
                    {$money_transfer_id}, NOW(), {$user_account}, {$beneficiary_account}, {$amount});";
                
                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");

                $query = "INSERT INTO transactions
                    (time_stamp, account_number, description, money_transfer_id, amount, balance)
                    VALUES
                    (NOW(), {$user_account}, 'Money Transfer', {$money_transfer_id}, {$user_amount}, {$new_user_balance}),
                    (NOW(), {$beneficiary_account}, 'Money Transfer', {$money_transfer_id}, {$amount}, {$new_beneficiary_balance});";
                
                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");

                $query = "UPDATE accounts SET ";
                $query .= "current_balance = '{$new_user_balance}'";
                $query .= "WHERE account_number = '{$user_account}' LIMIT 1;";

                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");

                $query = "UPDATE accounts SET ";
                $query .= "current_balance = '{$new_beneficiary_balance}'";
                $query .= "WHERE account_number = '{$beneficiary_account}' LIMIT 1;";

                $result = mysqli_query($connection, $query);
                verify_query($result, "home_user.php");
                $transfer_done = true;

            } else 
                $errors[] = "Insufficient Balance for the transaction";

        }
    }
    // Page name
    $page_name = "money_transfer";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Money Transfer (User) - SM Bank</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="icon" href="img/SM.jpg"/>
</head>
<body>
    <?php require_once("inc/header.php"); ?>
    
    <main class="clearfix">
        <?php require_once("inc/menu_bar.php"); ?>

        <div class="web_body clearfix">
            <form action="money_transfer.php" method="post">
                <?php  
                    // Check for errors
                    if (isset($errors) && !empty($errors)) {
                        display_multiple_errors($errors);
                    }
                ?>

                <?php
                    // Check if User Modified successfully
                    if (isset($transfer_done) && $transfer_done) {
                        $_POST = array();
                        echo "<p class=\"info\">Money Transfer Successfull </p>";
                    }
                ?>

                <input type="hidden" name="nic" value=<?php echo "'{$nic}'"; ?>>

                <p>
                    <label for="user_account"> Your Account: </label>
                    <input list="user_accounts" name="user_account" id="user_account" placeholder="Your Account" 
                        value=<?php echo "'{$user_account}'"; ?> required>
                        <datalist id="user_accounts">
                            <?php echo $user_account_list; ?>
                        </datalist>
                </p>

                <p>
                    <label for="user_email"> Your Email: </label>
                    <input type="email" name="user_email" id="user_email" placeholder="Your Email"
                        value=<?php echo "'{$user_email}'"; ?> disabled>
                </p>

                <p>
                    <label for="beneficiary_account"> Beneficiary Account: </label>
                    <input list="beneficiary_accounts" name="beneficiary_account" id="beneficiary_account"
                        placeholder="Beneficiary Account" value=<?php echo "'{$beneficiary_account}'"; ?> required>
                        <datalist id="beneficiary_accounts">
                            <?php echo $beneficiary_account_list; ?>
                        </datalist>
                </p>

                <p>
                    <button class="search_account" type="submit" name="search"> Search Beneficiary </button>
                        <!-- formaction="money_transfer.php"-->
                </p>

                <p>
                    <label for="beneficiary_name"> Beneficiary Name: </label>
                    <input type="text" name="beneficiary_name" id="beneficiary_name" placeholder="Beneficiary Name"
                        value=<?php echo "'{$beneficiary_name}'"; ?> disabled>
                </p>

                <p>
                    <label for="beneficiary_contact_number"> Beneficiary Contact Number: </label>
                    <input type="text" name="beneficiary_contact_number" id="beneficiary_contact_number"
                        placeholder="Beneficiary Contact Number" value=<?php echo "'{$beneficiary_contact_number}'"; ?> disabled>
                </p>

                <p>
                    <label for="beneficiary_email"> Beneficiary Email: </label>
                    <input type="email" name="beneficiary_email" id="beneficiary_email" placeholder="Beneficiary Email"
                        value=<?php echo "'{$beneficiary_email}'"; ?> disabled>
                </p>

                <p>
                    <label for="amount"> Amount: Rs. </label>
                    <input type="number" step="0.01" name="amount" id="amount" placeholder="Amount">
                </p>

                <p>
                    <label for="password"> Password: </label>
                    <input type="password" name="password" id="password" placeholder="Password">
                </p>

                <p>
                    <button type="submit" name="submit">Transfer Money</button>
                    <a class="cancel" href="home_user.php">Cancel</a> <!-- cancel -->
                </p>
            </form>
        </div> <!-- web_body -->
    </main> <!-- main_body -->
</body>
</html>

<?php mysqli_close($connection); ?>