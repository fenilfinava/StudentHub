-- Practical 14: Comprehensive StudentHub Admin Dashboard with RBAC, Dynamic Statistics, Audit Logging & Security
-- Database Schema (schema.sql)

CREATE DATABASE IF NOT EXISTS user_registration;
USE user_registration;

-- 1. Table for Registered System Users (Practical 9 & 10)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    remember_token_hash VARCHAR(255) NULL,
    remember_token_expiry DATETIME NULL
);

-- 2. Table for Enrolled Students (Practical 8, 11, 13)
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_no VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    branch VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

-- 3. Table for Campus Events (Practical 12)
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL
);

-- 4. Table for Role-Based Dynamic Navigation Menu (RBAC)
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    link VARCHAR(150) NOT NULL,
    min_role ENUM('user', 'editor', 'admin') DEFAULT 'user',
    icon VARCHAR(50) DEFAULT 'bi-link'
);

-- 5. Table for System Audit Logs (Security Tracking)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    username VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    status VARCHAR(20) DEFAULT 'SUCCESS',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Dynamic Navigation Menu Items for StudentHub
DELETE FROM menu_items;
INSERT INTO menu_items (title, link, min_role, icon) VALUES
('Student Dashboard', '../Practical-10/student_dashboard.php', 'user', 'bi-speedometer2'),
('My Profile', '../Practical-7/contact.html', 'user', 'bi-person-badge'),
('Campus Events', '../Practical-12/index.php', 'user', 'bi-calendar-event'),
('Faculty Portal', '#faculty', 'editor', 'bi-mortarboard'),
('Student Attendance', '#attendance', 'editor', 'bi-check2-square'),
('Admin Console', 'dashboard.php', 'admin', 'bi-shield-lock'),
('Student Management (CRUD)', '../Practical-11/index.php', 'admin', 'bi-people'),
('AJAX Management (SPA)', '../Practical-13/index.html', 'admin', 'bi-lightning-charge'),
('Audit Logs & Security', '#audit-logs', 'admin', 'bi-journal-text'),
('System Settings', '#settings', 'admin', 'bi-gear');

-- Ensure core demo users exist
INSERT IGNORE INTO users (username, email, password, role) VALUES
('admin01', 'admin01@gmail.com', '$2y$12$9bBdgV5MdYQdKX.PYIIKyeqM05.kDW7JN6QbZlC8XCI7ak1kr7J8G', 'admin'),
('student01', 'student01@gmail.com', '$2y$12$iNOfqPbbyjHWTuxcAKgALuhlhLYQadPvLwvKIuktRsmXvK7k94Km2', 'student'),
('fenilfinava', 'fenilfinava05@gmail.com', '$2y$12$FxCtWXeNOe..V8eypIMbH.Jbw7ZATMDW3uQDLkTSWgINrIYQ4vPeG', 'student'),
('jaynavadiya', 'jaynavadiya123@gmail.com', '$2y$12$to8lSIHQJ7c49VodUsO6WeBe/j6nDsMaT/ia8EgzeSr31fuo5bNZu', 'student'),
('kevin', 'kevin12@gmail.com', '$2y$12$Ll8MUQZ1ggfOFdbQxCB6yets/w/lY6BrQUAHfcG0XNoifgjYQZlZy', 'student');

-- Ensure demo students exist
INSERT IGNORE INTO students (enrollment_no, full_name, branch, semester, email) VALUES
('25CS011', 'Fenil Finava', 'CSE', 3, '25cs011@charusat.edu.in'),
('25CE045', 'Diya Shah', 'CE', 5, 'diya@charusat.edu.in'),
('25IT012', 'Meet Desai', 'IT', 3, 'meet@charusat.edu.in'),
('25CS089', 'Kavya Mehta', 'CSE', 7, 'kavya@charusat.edu.in'),
('25CS104', 'Rohan Patel', 'CSE', 3, 'rohan@charusat.edu.in');

-- Ensure demo events exist
INSERT IGNORE INTO events (event_name, event_date, location, category) VALUES
('Odoo Hackathon 2026', '2026-08-22', 'LDCE Ahmedabad', 'Hackathon'),
('TechFest Spoural', '2026-09-10', 'CSPIT A7 Building', 'TechFest'),
('AI & Cloud Seminar', '2026-09-15', 'Auditorium CHARUSAT', 'Seminar'),
('Robotics Exhibition', '2026-11-10', 'CSPIT Foyer', 'Workshop');

-- Seed initial representative audit logs for Admin actions
DELETE FROM audit_logs;
INSERT INTO audit_logs (user_id, username, action, details, ip_address, status, created_at) VALUES
(6, 'admin01', 'ADMIN_LOGIN', 'Administrator admin01 authenticated successfully via password verify.', '127.0.0.1', 'SUCCESS', DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(6, 'admin01', 'VIEW_DASHBOARD', 'Administrator accessed central management console.', '127.0.0.1', 'SUCCESS', DATE_SUB(NOW(), INTERVAL 20 MINUTE)),
(6, 'admin01', 'RBAC_VERIFY', 'Role authorization confirmed: role=admin permissions granted.', '127.0.0.1', 'SUCCESS', DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
(1, 'student01', 'ACCESS_DENIED', 'Unauthorized student access to /Practical-14/dashboard.php blocked with HTTP 403.', '127.0.0.1', 'SECURITY_BLOCK', DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
(6, 'admin01', 'EVENT_UPDATE', 'Updated details for event: Odoo Hackathon 2026.', '127.0.0.1', 'SUCCESS', DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(6, 'admin01', 'STUDENT_QUERY', 'Queried registered student records table.', '127.0.0.1', 'SUCCESS', NOW());
