# Practical 15: StudentHub Unified Campus Portal (Integrated Final Capstone)

**Course:** ITUE203 - Web Development Frameworks  
**Department:** Department of Computer Science & Engineering, CSPIT - CHARUSAT  
**Student:** Fenil Finava (25CS011)  
**Academic Year:** 2026-27 | Semester: 3rd  
**Submission Due:** October 29, 2026  

---

## 📌 Project Overview
Practical 15 is the comprehensive capstone synthesis integrating all capabilities developed across **Practicals 1 through 14** into a single, cohesive, production-ready enterprise portal: **StudentHub**.

StudentHub bridges public prospective students, active enrolled students, faculty, and administrative staff into a unified role-based ecosystem.

### Integrated Architecture:
- **Practicals 1–6 (Core Frontend & UI/UX):** Responsive HTML5 layout, CSS3 Grid/Flexbox design system, interactive dark/light theme switcher (`localStorage`), dynamic Unsplash banner carousel, and FAQ accordion.
- **Practical 7 (RESTful Contact Hub):** Dynamic student directory and multi-field feedback/contact logging with audit traceability.
- **Practicals 8–9 (Database Integration & Validation):** Automated account registration with server-side sanity validation, duplicate email detection, and direct profile binding.
- **Practical 10 (Session Authentication & Dual-Role Gateway):** Secure password hashing, session regeneration, and role-based redirect matrix (`admin` ➔ `admin_dashboard.php`, `student` ➔ `dashboard.php`).
- **Practical 11 (Student Lifecycle CRUD):** Complete administrative student record management (Add, View, Edit, Delete, Toggle Status) with modal dialogs and live search.
- **Practical 12 (Campus Event Management & Secure File Uploads):** Poster image validation (MIME type, size caps, unique hash naming) and database synchronization.
- **Practical 13 (Asynchronous Dynamic Data Rendering):** Fetch API integration for live directory search and filter without page reloads.
- **Practical 14 (Centralized RBAC & Immutable Security Audit Log):** Non-admin restriction (`HTTP 403 Forbidden`), dynamic metric dashboard analytics, CSV report export, and comprehensive action logging (`USER_LOGIN`, `USER_LOGOUT`, `VIEW_DASHBOARD`, `ACCESS_DENIED`, etc.).

---

## 📂 Repository Directory Structure
```
Practical-15/
├── index.php                 # Public Landing Portal (Header, Banner, Carousel, FAQ, Modals)
├── login.php                 # Dual-Role Authentication Gateway
├── register.php              # Enrolment & Direct Auto-Login System
├── logout.php                # Secure Session Invalidation & Audit Hook
├── 403.php                   # HTTP 403 RBAC Barrier Screen
├── dashboard.php             # Unified Student Academic Dashboard
├── student_dashboard.php     # Student Academic Dashboard alias
├── admin_dashboard.php       # Master Administrator Console (5 Tabs, CRUD, Audit, Export)
├── profile.php               # Student Profile & Credential Management
├── courses.php               # Enrolled Semester Courses & Syllabi
├── attendance.php            # Subject Attendance Tracker & Leave Applications
├── assignments.php           # Assignment Submission & Status Hub
├── results.php               # Academic GPA / Transcripts Matrix
├── achievements.php          # Badges & Co-curricular Showcase
├── notifications.php         # Campus Bulletins & Event Alerts
├── directory.php             # Live Searchable Student Directory
├── contact.php               # Department Helpdesk & Inquiry Logger
├── audit_logger.php          # Centralized Audit Trail Logger Helper
├── audit_logs.php            # Admin Audit Logs Viewer
├── db.php                    # MySQL Database Connector (mysqli)
├── studenthub.sql            # Complete Consolidated Database Dump
├── assets/
│   ├── css/global.css        # Responsive Theme Variables & Component Styles
│   └── js/
│       ├── auth.js           # Session Verification Script
│       ├── nav.js            # Dynamic Sidebar Navigator
│       ├── script.js         # Carousel, FAQ Accordion & Modals
│       └── theme.js          # Persistent Dark Mode Handler
└── uploads/                  # Upload Directory for Posters (.gitkeep)
```

---

## 🚀 Setup & Execution Instructions

### 1. Database Setup
Ensure MySQL is running on `localhost:3306`:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS studenthub_db;"
mysql -u root studenthub_db < studenthub.sql
```

### 2. Launch Built-in PHP Development Server
From the root repository directory:
```bash
php -S 0.0.0.0:8000
```

### 3. Open Portal in Browser
* **Home Landing Page:** [http://localhost:8000/studenthub/index.php](http://localhost:8000/studenthub/index.php)
* **Login Gateway:** [http://localhost:8000/studenthub/login.php](http://localhost:8000/studenthub/login.php)
* **Registration Page:** [http://localhost:8000/studenthub/register.php](http://localhost:8000/studenthub/register.php)

---

## 🔑 Default Credentials

| Role | Username / Email | Password | Landed Page |
|---|---|---|---|
| **Administrator** | `admin01` or `admin01@gmail.com` | `admin123` | `admin_dashboard.php` |
| **Student (Fenil)** | `fenilfinava` or `25cs011@charusat.edu.in` | `123456` | `dashboard.php` |
| **Student (General)** | `student01` or `student01@gmail.com` | `123456` | `dashboard.php` |

---

## 🔒 Security & RBAC Verification
1. **Student Isolation:** Log in as `fenilfinava` and navigate to `admin_dashboard.php`. System instantly halts with `HTTP 403 Forbidden` and records an `ACCESS_DENIED` security incident in `audit_logs`.
2. **Session Hijack Prevention:** `session_regenerate_id(true)` is executed upon every authentication event.
3. **SQL Injection Resistance:** Prepared statements (`mysqli_stmt_bind_param`) utilized across all database operations.
