<?php

    session_start();
    
    // Clear $_SESSION array
    $_SESSION = array();

    // Clear $_COOKIE for current session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 86400, '/');
    }

    // Distroy the session
    session_destroy();
    
    // Redirect to index.php page
    exit(header("Location: index.php?logout=yes"));

?>