-- StudentHub Portal Unified Database Dump
-- Host: localhost    Database: studenthub
-- Generation Time: Oct 26, 2026 at 10:00 AM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `studenthub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'vaidehi_admin', '$2y$10$eA09y2WfGqM0k6Nvh05DteM1s3u6t65dEfe7aZcE/8yD8aQkE5y1W', 'admin'),
(2, 'rahul_student', '$2y$10$wKkG9lU3pP5H7rS6v1V5eO7oH5mK1a9xZ7iO3mY8kL0vG5wE6e7a8', 'student');

-- --------------------------------------------------------
-- Table structure for table `api_students`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `api_students`;
CREATE TABLE IF NOT EXISTS `api_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `course` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `api_students` (`id`, `name`, `email`, `course`, `created_at`) VALUES
(1, 'Vaidehi Patel', '25cs081@charusat.edu.in', 'CSE', '2026-10-23 12:59:51'),
(2, 'Rahul Sharma', 'rahul@gmail.com', 'CE', '2026-10-23 12:59:51'),
(3, 'Priya Shah', 'priya@gmail.com', 'IT', '2026-10-23 12:59:51');

-- --------------------------------------------------------
-- Table structure for table `events`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `event_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `events` (`id`, `event_name`, `category`, `event_date`) VALUES
(1, 'Tech Mahamanthan 2026', 'Technical Fest', '2026-09-15'),
(2, 'AWS Cloud Workshop', 'Hands-on Lab', '2026-10-05'),
(3, 'Cyber Security Hackathon', 'Competition', '2026-11-12');

-- --------------------------------------------------------
-- Table structure for table `audit_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `audit_logs` (`id`, `user_id`, `username`, `action`, `ip_address`, `timestamp`) VALUES
(1, 1, 'vaidehi_admin', 'Admin logged in and viewed analytics dashboard', '127.0.0.1', '2026-10-24 19:22:39'),
(2, 1, 'vaidehi_admin', 'Exported student attendance report', '127.0.0.1', '2026-10-24 19:22:39'),
(3, 1, 'vaidehi_admin', 'Admin viewed analytics dashboard metrics', '::1', '2026-10-24 19:23:33');

COMMIT;