<?php
    date_default_timezone_set("Asia/Colombo");

    // Check if session attribute set
    function verify_session_attribute($attribute) {
        if (!isset($_SESSION[$attribute])){
            clear_session();
            exit(header("Location: index.php"));
        }
    }

    // Checks for get attributes
    function verify_get_attribute($attribute, $redirect_to = "") {
        if (!isset($_GET[$attribute])) {
            if (empty($redirect_to)) {
                clear_session();
                exit(header("Location: index.php"));

            } else {
                $redirect_to = "Location: " . $redirect_to;
                exit(header($redirect_to));
            }
        }
    }

    // Verify user type
    function verify_user_type($user_type, $expected_type) {
        if ($user_type != $expected_type){
            clear_session();
            exit(header("Location: index.php"));
        }
    }

    // Verify session expired (10 min rule)
    function verify_session_expired() {
        if (!isset($_SESSION["expire"]) || (time() - $_SESSION["expire"]) >= 0){
            clear_session();
            exit(header("Location: index.php"));
            
        } else {
            $_SESSION["expire"] = time() + 600;
        }
    }

    function page_open_verification($user_type) {
        $attributes = array("nic", "first_name", "last_name", "user_type", "last_login", "expire");
        foreach ($attributes as $attribute) {
            verify_session_attribute($attribute);
        }
        verify_user_type($_SESSION["user_type"], $user_type);
        verify_session_expired();
    }

    // Checks required fields
    function check_required_fields($required_fields) {
        $errors = array();

        foreach ($required_fields as $field) {
            if (empty(trim($_POST[$field]))) {
                $errors[] = "{$field} is required";
            }
        }
        return $errors;
    }

    // Checks field max length
    function check_field_max_length($field_max_length) {
        $errors = array();
        
        foreach ($field_max_length as $field => $max_length) {
            if (strlen(trim($_POST[$field])) > $max_length) {
                $errors[] = "{$field} length must be less than {$max_length}";
            }
        }
        return $errors;
    }

    // Validate email
    function is_email($email) {
        $pattern = "/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i";

        return (preg_match($pattern, $email));
    }

    // Verify query
    function verify_query($result, $redirect_to = "") {
        global $connection;

        if (!$result) {
            if (empty($redirect_to)) {
                exit ("Database Query Failed. " . mysqli_error($connection));

            } else {
                $redirect_to = "Location: " . $redirect_to . "?err=query_faild";
                exit(header($redirect_to));
            }
        }
    }

    // Display errors in proper format
    function display_multiple_errors($errors) {
        echo "<p class=\"error\">";
        
        foreach ($errors as $error) {
            $error = ucfirst(str_replace("_", " ", $error));
            echo "*{$error}<br>";
        }
        echo "</p>";
    }

    // Display single errors in proper format
    function display_single_error($error) {
        echo "<p class=\"error\">";
        echo ucfirst(str_replace("_", " ", $error));
        echo "</p>";
    }

    // Clear session
    function clear_session() {
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 86400, '/');
        }

        session_destroy();
    }

?>