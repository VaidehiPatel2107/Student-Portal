-- Database: studenthub

-- 1. Users Table with Role-Based Access
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Audit Logs Table for Security Traceability
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Seed Users (passwords hashed with standard password_hash 'admin123' / 'student123')
REPLACE INTO users (id, username, password, role) VALUES
(1, 'vaidehi_admin', '$2y$10$eA09y2WfGqM0k6Nvh05DteM1s3u6t65dEfe7aZcE/8yD8aQkE5y1W', 'admin'),
(2, 'rahul_student', '$2y$10$wKkG9lU3pP5H7rS6v1V5eO7oH5mK1a9xZ7iO3mY8kL0vG5wE6e7a8', 'student');

-- 4. Initial Seed Audit Records
INSERT INTO audit_logs (user_id, username, action, ip_address) VALUES
(1, 'vaidehi_admin', 'Admin logged in and viewed analytics dashboard', '127.0.0.1'),
(1, 'vaidehi_admin', 'Exported student attendance report', '127.0.0.1');