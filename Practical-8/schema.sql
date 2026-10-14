-- schema.sql
-- Relational Database Schema in 3NF (Third Normal Form)

-- 1. Create 'students' table
CREATE TABLE IF NOT EXISTS students (
    student_id INTEGER PRIMARY KEY AUTOINCREMENT,
    enrollment_no VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    branch VARCHAR(50) NOT NULL,
    semester INTEGER NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

-- 2. Create 'events' table
CREATE TABLE IF NOT EXISTS events (
    event_id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL
);

-- 3. Create 'registrations' table (Junction table linking students and events)
CREATE TABLE IF NOT EXISTS registrations (
    registration_id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    event_id INTEGER NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE(student_id, event_id)
);

-- ==========================================
-- Insert Mock Test Data
-- ==========================================

INSERT INTO students (enrollment_no, full_name, branch, semester, email) VALUES 
('25CS011', 'Fenil Finava', 'CSE', 3, '25cs011@charusat.edu.in'),
('25CE045', 'Diya Shah', 'CE', 5, 'diya@charusat.edu.in'),
('25IT012', 'Meet Desai', 'IT', 3, 'meet@charusat.edu.in'),
('25CS089', 'Kavya Mehta', 'CSE', 7, 'kavya@charusat.edu.in');

INSERT INTO events (event_name, event_date, location, category) VALUES 
('Odoo Hackathon', '2026-08-22', 'LDCE', 'Hackathon'),
('TechFest Spoural', '2026-09-10', 'CSPIT A7', 'TechFest'),
('AI Seminar', '2026-09-15', 'Auditorium', 'Seminar');

INSERT INTO registrations (student_id, event_id) VALUES 
(1, 1), -- Fenil registered for Odoo Hackathon
(1, 2), -- Fenil registered for TechFest
(2, 3), -- Diya registered for AI Seminar
(3, 1), -- Meet registered for Odoo Hackathon
(4, 2); -- Kavya registered for TechFest
