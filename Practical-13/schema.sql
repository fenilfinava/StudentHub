-- Practical 13: PHP JSON API & AJAX/Fetch-Based CRUD (No Page Reload)
-- Database Schema for Role-Based Navigation & Inventory CRUD

CREATE DATABASE IF NOT EXISTS dashboard_db;
USE dashboard_db;

-- 1. Table for Dynamic Navigation Menu with Role Restrictions (RBAC)
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    link VARCHAR(100) NOT NULL,
    min_role ENUM('user', 'editor', 'admin') DEFAULT 'user'
);

-- 2. Table for CRUD Operations (Inventory Items)
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- Initial Menu Items
INSERT INTO menu_items (title, link, min_role) VALUES
('Dashboard', '#dashboard', 'user'),
('View Inventory', '#inventory', 'user'),
('Manage Content', '#content', 'editor'),
('User Management', '#users', 'admin'),
('System Settings', '#settings', 'admin');

-- Initial Inventory Data
INSERT INTO items (name, category, price) VALUES
('Laptop Pro', 'Electronics', 1200.00),
('Wireless Mouse', 'Accessories', 25.50),
('Mechanical Keyboard', 'Accessories', 85.00);
