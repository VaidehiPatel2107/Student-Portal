<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "db.php";

// Function to enforce Admin Role (RBAC)
function checkAdminAccess() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        include "403.php";
        exit();
    }
}

// Function to Record Security Audit Logs
function logAuditAction($conn, $action) {
    $userId   = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? 'Anonymous';
    $ip       = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, username, action, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $username, $action, $ip);
    $stmt->execute();
}
?>