<?php

    $dbhost = "localhost";
    $dbuser = "sm_bank_admin";
    $dbpassword = "1234";
    $dbname = "sm_bank_db";

    $connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

    if (mysqli_connect_errno()) {
        exit("Database Connection Failed" . mysqli_connect_error());
    }

?>