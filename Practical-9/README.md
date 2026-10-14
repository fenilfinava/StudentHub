<div align="center">
  <h1>🎓 Practical 9</h1>
  <h3>Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing</h3>
</div>

---

### 👨‍🎓 Student Information
- **Name:** Fenil Finava
- **ID:** 25CS011
- **Course:** ITUE203 - WEB DEVELOPMENT FRAMEWORKS
- **Year:** 2026-27

---

### 🎯 Objective
> Implement a secure user registration system using PHP, MySQLi, and MySQL with frontend & backend validation, duplicate email/username checking, password hashing using `password_hash()`, and secure database insertion via Prepared Statements.

---

### 🚀 Main Features
- ✅ **Frontend & Backend Validation:** Required fields, minimum length, email syntax validation (`filter_var`), and regex username checking (`^[a-zA-Z0-9_]{3,50}$`).
- ✅ **Duplicate Prevention:** Prepared statement check for duplicate username and email before insertion.
- ✅ **Secure Password Hashing:** Uses `password_hash($password, PASSWORD_DEFAULT)` to store one-way bcrypt/Argon2 hashes instead of plain text.
- ✅ **SQL Injection Prevention:** Uses MySQLi Prepared Statements (`prepare()`, `bind_param()`, `execute()`) for both duplicate lookup and insertion.
- ✅ **User Verification UI:** `users.php` page allowing direct browser inspection of the MySQL database table and hashed strings.

---

### 📂 Project Structure
```text
Practical-9/
├── db.php
├── process_registration.php
├── register.php
├── schema.sql
├── style.css
└── users.php
```

---

### 💻 How to Run
1. Start MySQL and PHP server:
   ```bash
   php -S localhost:8000
   ```
2. Open in web browser:
   - Registration Form: `http://localhost:8000/Practical-9/register.php`
   - View Database Users Table: `http://localhost:8000/Practical-9/users.php`
