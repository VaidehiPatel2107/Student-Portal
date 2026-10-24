<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    $old_session_id = session_id();

    // Query credentials from your users table
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Mitigate session fixation by regenerating session ID
            session_regenerate_id(true);
            $new_session_id = session_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['LAST_ACTIVITY'] = time();
            $_SESSION['old_session_id'] = $old_session_id;
            $_SESSION['new_session_id'] = $new_session_id;

            header("Location: dashboard.php");
            exit();
        }
    }

    header("Location: login.php?error=invalid");
    exit();
}
?>