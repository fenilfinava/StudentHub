-- Practical 12: Event Management System with File Upload & Poster Validation
CREATE DATABASE IF NOT EXISTS event_db;
USE event_db;

-- 1. Events Table
CREATE TABLE IF NOT EXISTS events (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    location   VARCHAR(150) NOT NULL,
    category   VARCHAR(50) NOT NULL,
    poster     VARCHAR(255) NULL
);

-- 2. Users Table for Role-Based Access Control (Admin vs Unauthorized)
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50) NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       VARCHAR(20) NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Admin and Student Accounts
-- Passwords: admin123 and 123456 hashed with password_hash()
INSERT INTO users (username, email, password, role) VALUES 
('admin01', 'admin01@gmail.com', '$2y$12$7IA3kezJb9sPVDVbguPzZO3xhax9vb8nbcmiY9pwQjM.x9w37ELHa', 'admin'),
('student01', 'student01@gmail.com', '$2y$12$UcLjfNM1kk.J0vW6slLyCOHc2hctoDARiw2rkCtaaicGBcc1VXGfa', 'student')
ON DUPLICATE KEY UPDATE role=VALUES(role);

-- Seed Initial Events with Existing Sample Poster
INSERT INTO events (title, event_date, location, category, poster) VALUES 
('Odoo Hackathon 2026', '2026-08-22', 'LDCE Ahmedabad', 'Hackathon', '1791537429_6ac8b1155ae37.jpeg'),
('Spoural TechFest', '2026-09-10', 'CSPIT A7 Building', 'Workshop', NULL),
('AI & Cloud Seminar', '2026-09-15', 'Auditorium CHARUSAT', 'Conference', NULL);
