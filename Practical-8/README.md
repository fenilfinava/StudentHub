<div align="center">
  <h1>🎓 Practical 8</h1>
  <h3>Database Connection with PDO & Prepared Statements</h3>
</div>

---

### 👨‍🎓 Student Information
- **Name:** Fenil Finava
- **ID:** 25CS011
- **Course:** ITUE203 - WEB DEVELOPMENT FRAMEWORKS
- **Year:** 2026-27

---

### 🎯 Objective
> To create a database connection using PHP PDO, design relational 3NF tables (students, events, registrations), execute operations using Prepared Statements to prevent SQL injection vulnerabilities, and render dynamic records in PHP.

---

### 🚀 Main Features
- ✅ **Database Connection:** Established secure PHP PDO connection with try-catch error handling in `db.php`.
- ✅ **Prepared Statements:** Safely fetching and inserting records to prevent SQL injection in `test_connection.php` and `insert.php`.
- ✅ **3NF Relational Schema:** Third Normal Form database schema in `schema.sql` with Primary and Foreign key constraints.
- ✅ **Dynamic PHP Rendering:** Outputting student list and event registrations dynamically in `students.php` and `test_connection.php`.

---

### 📂 Project Structure
```text
Practical-8/
├── db.php
├── insert.php
├── schema.sql
├── students.php
└── test_connection.php
```

---

### 💻 How to Run
1. Open terminal in this directory:
   ```bash
   cd Practical-8
   ```
2. Start PHP local development server:
   ```bash
   php -S localhost:8000
   ```
3. Open the following URLs in your browser:
   - Database Connection: `http://localhost:8000/db.php`
   - Student List: `http://localhost:8000/students.php`
   - Data Retrieval & JOIN: `http://localhost:8000/test_connection.php`
   - Insert Record: `http://localhost:8000/insert.php`
