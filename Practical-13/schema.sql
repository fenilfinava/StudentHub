-- Practical 13: StudentHub AJAX/Fetch-Based Management & Dynamic RBAC Navigation
-- Integrated with StudentHub (Practicals 1 to 12)

CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

-- 1. Ensure students table exists (from Practical 11)
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table for Dynamic Navigation Menu with Role-Based Access Control (RBAC)
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    link VARCHAR(100) NOT NULL,
    min_role ENUM('user', 'editor', 'admin') DEFAULT 'user'
);

-- Seed StudentHub Navigation Links
DELETE FROM menu_items;
INSERT INTO menu_items (title, link, min_role) VALUES
('Student Dashboard', '../Practical-10/student_dashboard.php', 'user'),
('My Profile', '../Practical-7/contact.html', 'user'),
('Campus Events', '../Practical-12/index.php', 'user'),
('Faculty Portal', '#faculty', 'editor'),
('Student Attendance', '#attendance', 'editor'),
('Admin Console', '../Practical-10/admin_dashboard.php', 'admin'),
('Student Records (CRUD)', '#students', 'admin'),
('User Management', '../Practical-9/users.php', 'admin'),
('System Settings', '#settings', 'admin');

-- 3. Table for Campus Resources / Inventory (Lab Manual Reference)
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

DELETE FROM items;
INSERT INTO items (name, category, price) VALUES
('Laptop Pro', 'Electronics', 1200.00),
('Wireless Mouse', 'Accessories', 25.50),
('Mechanical Keyboard', 'Accessories', 85.00);
