# 🧪 StudentHub Portal - Comprehensive Test & Security Report
**Practical 15 Final Submission | Course: ITUE203 - Web Development Frameworks**  
**Developer:** Fenil Finava | **Enrollment:** 25CS011 | **Institute:** CSPIT - CHARUSAT  
**Repository:** [https://github.com/fenilfinava/StudentHub](https://github.com/fenilfinava/StudentHub)

---

## 1. Executive Summary
StudentHub is an integrated, enterprise-grade academic management web portal uniting features from Practicals 1 through 14 into a cohesive production system. This document provides the formal verification report detailing test cases, security barriers, role-based access control (RBAC), and accessibility standards.

---

## 2. Test Execution & Verification Checklist

| Test ID | Module / Feature | Test Procedure | Expected Outcome | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-01** | Gateway Router (`index.php`) | Visit root URL `http://localhost:8000/` without active session | Redirects to `studenthub/login.php` with HTTP 302 | HTTP 302 -> `studenthub/login.php` | **PASS** |
| **TC-02** | Unified Authentication | Login with valid admin credentials (`admin01` / `admin123`) | Authenticates, regenerates session ID, logs `USER_LOGIN` audit, redirects to `admin_dashboard.php` | Loaded Admin Management Console with active metrics | **PASS** |
| **TC-03** | Student Authentication | Login with student credentials (`student01` / `123456`) | Authenticates, redirects to `student_dashboard.php` with academic summary | Loaded Student Dashboard with CGPA, Timetable, Events | **PASS** |
| **TC-04** | Invalid Credentials | Submit invalid password for existing account | Displays inline error alert, records `LOGIN_FAILED` audit log with IP | Error displayed, audit logged with status `FAILED` | **PASS** |
| **TC-05** | Student Registration | Register new account via `register.php` with dual password match | Bcrypt hashes password, inserts into `users` & `students`, auto-creates profile | Account created, logged `USER_REGISTER`, redirected to login | **PASS** |
| **TC-06** | RBAC Barrier (Student to Admin) | Student user attempts direct URL navigation to `admin_dashboard.php` | Access blocked, renders standardized `403.php`, logs `ACCESS_DENIED` `SECURITY_BLOCK` | HTTP 403 Forbidden page displayed, audit log entry verified | **PASS** |
| **TC-07** | Admin Student CRUD | Admin creates, searches, filters by department, and deletes student record | Dynamic MySQL query execution, immediate UI table refresh, audit log entry | Student added, filtered by course, deleted successfully | **PASS** |
| **TC-08** | CSV Data Export | Admin clicks "Export CSV" (`?export=csv`) | Generates compliant CSV stream with MIME type `text/csv` and prompt download | Browser downloaded `StudentHub_Students_*.csv` | **PASS** |
| **TC-09** | Campus Event Management | Admin creates event with poster file upload | Validates file type (JPG/PNG/WEBP), limits size (<2MB), stores in `uploads/` | Event published, poster thumbnail displayed | **PASS** |
| **TC-10** | Event Media Deletion | Admin deletes event with associated poster | Record removed from `events`, poster unlinked from disk via PHP `unlink()` | DB record deleted, file unlinked from disk cleanly | **PASS** |
| **TC-11** | Dynamic RBAC Menu | Menu queried from `menu_items` table based on active role | Non-admin users only see permissible links; admin sees all management tools | Navigation rendered dynamically from DB | **PASS** |
| **TC-12** | Live Audit Trail Feed | Admin inspects "Security Audit Trail" tab | Displays real-time log entries with action badges, client IP, and timestamps | Complete audit trail visible in admin console | **PASS** |
| **TC-13** | Secure Session Logout | Click Logout button in navbar | Session data and cookie destroyed, `USER_LOGOUT` logged, redirected to login | Session terminated, cannot back-navigate | **PASS** |

---

## 3. Security Verification Checklist

### 3.1. Authentication & Password Security
- [x] **Bcrypt Password Hashing:** All passwords hashed using PHP `password_hash($password, PASSWORD_BCRYPT)` with standard work cost factor. Plaintext passwords are never stored.
- [x] **Session Fixation Countermeasure:** `session_regenerate_id(true)` invoked immediately upon successful login to prevent session hijacking.
- [x] **Dual Identifier Support:** Accepts either enrollment email or unique username for user convenience without leaking account enumeration details.

### 3.2. Role-Based Access Control (RBAC) & Perimeter Security
- [x] **Strict Server-Side Authorization:** Admin endpoints enforce `($_SESSION["role"] ?? "") === "admin"` before executing any business logic.
- [x] **Tamper-Evident Security Barrier:** Unauthorized student access attempts trigger an immediate `ACCESS_DENIED` audit log recording client IP, user ID, and attempted resource, followed by an HTTP 403 Forbidden page.
- [x] **Self-Deletion Protection:** Administrators cannot delete their own active user account from the user management console.

### 3.3. SQL Injection Defense
- [x] **Parameterized Prepared Statements:** All queries processing user inputs (search, insert, delete, auth) utilize MySQLi / PDO prepared statements (`$stmt->prepare()` and `$stmt->bind_param()`), neutralizing SQL injection vectors.

### 3.4. File Upload Security
- [x] **MIME-Type & Extension Whitelisting:** Event poster uploads validate against strictly allowed extensions (`jpg`, `jpeg`, `png`, `webp`, `gif`).
- [x] **File Size Capping:** Server restricts upload size to 2MB maximum.
- [x] **Randomized File Renaming:** Uploaded files are renamed using `event_{timestamp}_{random_hex}.ext` to prevent direct script execution and filename collisions.
- [x] **Garbage Cleanup on Delete:** When an event is deleted, PHP `unlink()` purges the associated image file from disk to prevent storage leaks.

### 3.5. Cross-Site Scripting (XSS) Prevention
- [x] **Output Sanitization:** All dynamic user values rendered in HTML are sanitized using `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.

---

## 4. Accessibility & UI/UX Standards (WCAG 2.1 Compliance)

- [x] **Semantic HTML5 Markup:** Core semantic landmarks (`<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`, `<meter>`, `<progress>`).
- [x] **Color Contrast Compliance:** Text and button elements adhere to WCAG AA 4.5:1 contrast ratios.
- [x] **Responsive Viewport:** Bootstrap 5 grid layout optimized across mobile (375px), tablet (768px), and desktop (1200px+).
- [x] **Form Usability:** Clear labels, descriptive placeholders, and inline validation alerts for maximum cognitive accessibility.

---

## 5. Deployment & Production Readiness

1. **Database Portability:** Root `studenthub.sql` contains the complete schema and seed data.
2. **Environment Variable Overrides:** `db.php` checks for `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` environment variables, enabling zero-code cloud deployment to Railway, Render, Docker, or cPanel.
3. **Graceful Fallbacks:** Connection fallback ensures zero downtime between local development and production environments.
