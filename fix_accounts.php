<?php
require_once "db.php";

// Ensure role column exists in users table
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'student') NOT NULL DEFAULT 'student'");

// Generate fresh hashes
$student_pass = password_hash("Student@2026", PASSWORD_BCRYPT);
$admin_pass   = password_hash("Admin@2026", PASSWORD_BCRYPT);

// 1. Reset Student Account
$stmt1 = $conn->prepare("INSERT INTO users (username, email, password, role) 
                         VALUES ('Vaidehi', '25cs081@charusat.edu.in', ?, 'student')
                         ON DUPLICATE KEY UPDATE password = ?, role = 'student'");
$stmt1->bind_param("ss", $student_pass, $student_pass);
$stmt1->execute();

// 2. Insert/Reset Admin Account
$stmt2 = $conn->prepare("INSERT INTO users (username, email, password, role) 
                         VALUES ('Admin', 'admin@charusat.edu.in', ?, 'admin')
                         ON DUPLICATE KEY UPDATE password = ?, role = 'admin'");
$stmt2->bind_param("ss", $admin_pass, $admin_pass);
$stmt2->execute();

echo "<h2 style='color:green; font-family:sans-serif;'>Accounts updated successfully!</h2>";
echo "<p>Student -> Email: <b>25cs081@charusat.edu.in</b> | Password: <b>Student@2026</b></p>";
echo "<p>Admin -> Email: <b>admin@charusat.edu.in</b> | Password: <b>Admin@2026</b></p>";
echo "<a href='login.php'>Go to Login</a>";
?>