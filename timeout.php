<?php
$timeout_duration = 60; // 60 seconds inactivity timeout for testing

if (isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?msg=timeout");
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();
?>