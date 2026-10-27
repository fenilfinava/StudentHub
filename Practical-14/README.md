# Practical 14: Centralized Admin Dashboard with RBAC, Dynamic Statistics, Audit Logging & Security

**Course:** ITUE203 - Web Development Frameworks  
**Department:** Department of Computer Science & Engineering, CSPIT - CHARUSAT  
**Student:** Fenil Finava (25CS011)  
**Academic Year:** 2026-27 | Semester: 3rd  
**Due Date:** October 26, 2026  

---

## 📌 Project Overview
Practical 14 is the master consolidation practical of **StudentHub** (Practicals 1 to 13), establishing a production-grade **Centralized Administrator Dashboard** built with modern Bootstrap 5 UI, procedural MySQLi & PDO queries, Role-Based Access Control (RBAC) security enforcement, dynamic metric counters, and an immutable security **Audit Logging System**.

### Core Architecture & Capabilities:
1. **Dynamic Metric Analytics:** Real-time SQL counting for registered user accounts (`users`), active students (`students`), campus events (`events`), and tracked audit logs (`audit_logs`).
2. **Latest Registrations Tracking:** Live tabular feed displaying recently registered student & user accounts with timestamps and role badges.
3. **Dynamic RBAC Navigation:** Dynamic navigation links rendered according to user role authorization levels (`menu_items` table).
4. **Security & Non-Admin Access Restriction:** Non-admin/student access attempts to `dashboard.php` are blocked with `HTTP 403 Forbidden` and logged in `audit_logs` as `SECURITY_BLOCK`.
5. **System Audit Logs:** Automated tracking recording administrative logins, dashboard views, queries, updates, and blocked requests with client IP address, status, and timestamps.
6. **StudentHub Suite Integration:** Seamlessly bridges all previous practicals (Profile Prac 7, Registration Prac 9, Authentication Prac 10, Student CRUD Prac 11, Event Management Prac 12, Fetch API SPA Prac 13).

---

## 📂 Submit-Ready Checklist
- [x] **`schema.sql`**: Database setup for `users`, `students`, `events`, `menu_items`, and `audit_logs` tables with seed data.
- [x] **`db.php`**: Secure MySQL database connection.
- [x] **`audit_logger.php`**: Audit logging helper recording user, action, details, client IP, and status.
- [x] **`dashboard.php`**: Main Bootstrap 5 Administrator Dashboard with dynamic counters, latest registrations table, audit logs table, and quick modules hub.
- [x] **`login.php`**: Authentication portal for Admin and Student accounts with automated audit logging.
- [x] **`logout.php`**: Session termination and logout audit logging.
- [x] **`403.php`**: HTTP 403 Forbidden page for restricted student access.
- [x] **`PRAC_14_ITUE203_25CS011.docx`**: Word lab report with Table 0 logos, student details, test report table, and 7 screenshot placeholder boxes.

---

## 🚀 Execution Instructions

### 1. Database Setup
```bash
mysql -u root < schema.sql
```

### 2. Run Local PHP Server
```bash
php -S 0.0.0.0:8000
```

### 3. Open in Browser
👉 **Login Portal:** [http://localhost:8000/Practical-14/login.php](http://localhost:8000/Practical-14/login.php)  
👉 **Admin Dashboard:** [http://localhost:8000/Practical-14/dashboard.php](http://localhost:8000/Practical-14/dashboard.php)  

* **Admin Account:** `admin01@gmail.com` / `admin123`
* **Student Account (for 403 test):** `student01@gmail.com` / `123456`

---

## 📸 Screenshots Required for Lab Report (`PRAC_14_ITUE203_25CS011.docx`)

1. **Admin Login & Dashboard Access:** Successful administrator login and redirection to Central Dashboard.
2. **Dashboard UI Dynamic Statistics:** Real-time counters showing Total Users, Enrolled Students, Campus Events, and Audit Logs.
3. **Latest Student & User Registrations:** Tabular feed showing recently registered user accounts with role badges.
4. **Live System Audit Logs Table:** Table displaying recorded admin actions (`ADMIN_LOGIN`, `VIEW_DASHBOARD`, `STUDENT_QUERY`) with IP and timestamps.
5. **Dynamic Role-Based Navigation Menu (RBAC):** Navigation menu showing accessible module pills based on role.
6. **Non-Admin Access Restriction (HTTP 403 Forbidden):** Logging in as student (`student01`) and attempting to open `dashboard.php`, triggering the 403 access blocked page.
7. **Database Audit Log Proof (`audit_logs` table in MySQL):** Terminal / database output showing recorded audit log rows including the `ACCESS_DENIED` security block.
