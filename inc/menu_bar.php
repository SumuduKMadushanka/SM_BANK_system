<div class="menu_bar clearfix">
    <ul>
        <li>
            <?php
                if ($page_name == "home") 
                    echo "<a class=\"menu_item_selected\" href=\"home_user.php\"> Home </a>";
                else echo "<a class=\"menu_item\" href=\"home_user.php\"> Home </a>";
            ?>
        </li>

        <li>
            <?php
                if ($page_name == "profile_user") 
                    echo "<a class=\"menu_item_selected\" href=\"view_user.php\"> Profile </a>";
                else echo "<a class=\"menu_item\" href=\"view_user.php\"> View Profile </a>";
            ?>
        </li>

        <li>
            <?php
                if ($page_name == "change_password") 
                    echo "<a class=\"menu_item_selected\" href=\"change_password.php\"> Change Password </a>";
                else echo "<a class=\"menu_item\" href=\"change_password.php\"> Change Password </a>";
            ?>
        </li>

        <li>
            <?php
                if ($page_name == "view_account") 
                    echo "<a class=\"menu_item_selected\" href=\"view_accounts.php\"> View Accounts </a>";
                else echo "<a class=\"menu_item\" href=\"view_accounts.php\"> View Accounts </a>";
            ?>
        </li>
        
        <li>
            <?php
                if ($page_name == "view_transactions") 
                    echo "<a class=\"menu_item_selected\" href=\"view_transactions.php\"> View Transactions </a>";
                else echo "<a class=\"menu_item\" href=\"view_transactions.php\"> View Transactions </a>";
            ?>
        </li>

        <li>
            <?php
                if ($page_name == "money_transfer") 
                    echo "<a class=\"menu_item_selected\" href=\"money_transfer.php\"> Money Transfer </a>";
                else echo "<a class=\"menu_item\" href=\"money_transfer.php\"> Money Transfer </a>";
            ?>
        </li>
    </ul>
</div> <!-- menu_bar -->