<div align="center">
  <h1>🎓 Practical 10</h1>
  <h3>Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token</h3>
</div>

---

### 👨‍🎓 Student Information
- **Name:** Fenil Finava
- **ID:** 25CS011
- **Course:** ITUE203 - WEB DEVELOPMENT FRAMEWORKS
- **Year:** 2026-27

---

### 🎯 Objective
> Implement a secure login/logout authentication system using PHP sessions with `password_verify()`, session ID regeneration (`session_regenerate_id(true)`), Role-Based Access Control (RBAC) separating Admin and Student dashboards, 5-minute inactivity session timeout, secure session destruction on logout, and secure cookie-based Remember-Me token authentication.

---

### 🚀 Main Features
- ✅ **Password Verification:** Verifies submitted credentials against stored bcrypt/Argon2 hashes using `password_verify()`.
- ✅ **Session Fixation Prevention:** Calls `session_regenerate_id(true)` upon successful login to invalidate old session IDs.
- ✅ **Role-Based Access Control (RBAC):** Restricts access to `admin_dashboard.php` (Admins only) and `student_dashboard.php` (Students only) with HTTP 403 response on unauthorized attempts.
- ✅ **Inactivity Session Timeout:** Automatically expires user sessions after 300 seconds (5 minutes) of inactivity and redirects to login.
- ✅ **Secure Logout:** Unsets all session variables, invalidates session cookie (`setcookie` with expired timestamp), and executes `session_destroy()`.
- ✅ **Remember-Me Functionality:** Issues a cryptographically secure random token (`random_bytes(32)`), stores its SHA-256 hash in the database, and stores the raw token in an HttpOnly cookie for auto-login.
- ✅ **Intermediate Tracking:** Automatically records and updates `last_login` timestamp in the database.

---

### 📂 Project Structure
```text
Practical-10/
├── admin_dashboard.php
├── authenticate.php
├── dashboard.php
├── db.php
├── login.php
├── logout.php
├── student_dashboard.php
├── style.css
├── timeout.php
└── user_registration.sql
```

---

### 💻 How to Run
1. Ensure MySQL is running on port 3306.
2. Start PHP development server:
   ```bash
   php -S 0.0.0.0:8000
   ```
3. Open in web browser:
   - Login Page: `http://localhost:8000/Practical-10/login.php`
   - Common Dashboard: `http://localhost:8000/Practical-10/dashboard.php`
   - Admin Dashboard: `http://localhost:8000/Practical-10/admin_dashboard.php`
   - Student Dashboard: `http://localhost:8000/Practical-10/student_dashboard.php`

---

### 🔑 Test Credentials
| Role | Email | Password |
|---|---|---|
| **Student** | `student01@gmail.com` | `123456` |
| **Admin** | `admin01@gmail.com` | `admin123` |
