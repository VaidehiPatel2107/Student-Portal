<?php
require_once "db.php";
$conn->query("CREATE TABLE IF NOT EXISTS system_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student'
)");

$adminPass = password_hash("Admin@2026", PASSWORD_BCRYPT);
$studentPass = password_hash("Student@2026", PASSWORD_BCRYPT);

$conn->query("REPLACE INTO system_users (id, name, email, password, role) VALUES 
(1, 'System Admin', 'admin@charusat.edu.in', '$adminPass', 'admin'),
(2, 'Vaidehi Patel', '25cs081@charusat.edu.in', '$studentPass', 'student')");

echo "Default Admin and Student accounts prepared!";
?>