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
> Connect to a database using PHP Data Objects (PDO), fetch data using prepared statements, and display it securely.

---

### 🚀 Main Features
- ✅ **3NF Schema: Relational tables (students, events, registrations) with PK/FK constraints in schema.sql.**
- ✅ **Secure PDO Connection: Robust db.php with PDO error exceptions and try-catch handling.**
- ✅ **Prepared Statements: SQL injection-safe queries in test_connection.php.**
- ✅ **Dynamic UI Output: Browser display showing database connection badge and relational table.**

---

### 📂 Project Structure
```text
Practical-8/
├── assets
│   └── css
│       └── global.css
├── db.php
├── schema.sql
├── screenshot_db.png
├── screenshot_output.png
├── screenshot_schema.png
├── screenshot_test_conn.png
├── studenthub.db
└── test_connection.php
```

---

### 💻 How to Run
1. Open terminal in this directory: `cd Practical-8`
2. Start PHP local server: `php -S localhost:8000`
3. Open browser: `http://localhost:8000/test_connection.php`