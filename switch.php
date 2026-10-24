<?php
session_start();
$role = $_GET['role'] ?? 'admin';
if ($role === 'student') {
    $_SESSION['user_id'] = 2;
    $_SESSION['username'] = 'rahul_student';
    $_SESSION['role'] = 'student';
} else {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'vaidehi_admin';
    $_SESSION['role'] = 'admin';
}
header("Location: dashboard.php");
exit();
?>