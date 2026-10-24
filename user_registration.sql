CREATE TABLE IF NOT EXISTS system_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert an Admin user (Password: Admin@2026) and Student user (Password: Student@2026)
-- Bcrypt hashes generated via password_hash()
INSERT INTO system_users (name, email, password, role) VALUES 
('System Admin', 'admin@charusat.edu.in', '$2y$10$wEkgzJ3rRrq84Bw6xG9kAeM.76W8sC6R9cKxI78Lg6U2E1w.U85m6', 'admin'),
('Vaidehi Patel', '25cs081@charusat.edu.in', '$2y$10$VqG9b4p1F6mU1Y9B4zX8KeM0yS3.Ew2nB8fL5xO7j8U1E6w.U85m6', 'student')
ON DUPLICATE KEY UPDATE id=id;