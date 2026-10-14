-- Practical 11: Student Management System (CRUD with Search, Filter, Soft Delete & CSV Export)
CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Initial Student Records
INSERT INTO students (name, email, course, gender, status) VALUES 
('Fenil Finava', '25cs011@charusat.edu.in', 'Computer Science', 'Male', 'Active'),
('John Doe', 'john@example.com', 'Computer Science', 'Male', 'Active'),
('Diya Shah', 'diya@example.com', 'Web Development', 'Female', 'Active'),
('Meet Desai', 'meet@example.com', 'Data Science', 'Male', 'Active'),
('Kavya Mehta', 'kavya@example.com', 'Web Development', 'Female', 'Active');
