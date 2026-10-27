# 🎓 StudentHub - Integrated Campus & Resource Management Portal
**Practical 15 Final Practical Submission | ITUE203 - Web Development Frameworks**  
**Developer:** Fenil Finava | **Enrollment Number:** 25CS011 | **Department:** Computer Science & Engineering  
**Institute:** CSPIT, CHARUSAT University  
**Repository:** [https://github.com/fenilfinava/StudentHub](https://github.com/fenilfinava/StudentHub)

---

## 🌟 Overview
**StudentHub** is a comprehensive, production-ready university web application developed through a progressive 15-stage practical curriculum. It combines frontend semantic standards, dynamic server-side PHP processing, MySQL database architectures, Role-Based Access Control (RBAC), file upload pipelines, and asynchronous CRUD capabilities into a single integrated platform.

---

## 🚀 Key Features

### 👑 Administrator Console (`studenthub/admin_dashboard.php`)
* **Dynamic Metrics Overview:** Real-time KPI counters tracking total registered users, enrolled students, campus events, and audit logs.
* **Student Records CRUD:** Full Create, Read, Update, Delete capabilities with real-time course filtering and search.
* **CSV Data Export:** One-click CSV stream generator (`?export=csv`) for student roster reporting.
* **Campus Event Management:** Form to publish technical workshops and symposiums with secure poster image upload (<2MB) and disk file cleanup (`unlink()`).
* **User & Role Administration:** View registered accounts, role levels (`Admin`, `Editor`, `Student`), and last login timestamps.
* **Live Security Audit Trail:** Centralized tamper-evident audit logger tracking all logins, CRUD actions, CSV exports, and 403 access violations.
* **Dynamic RBAC Navigation:** Dynamic directory populated directly from the `menu_items` database table.

### 👨‍🎓 Student Dashboard (`studenthub/student_dashboard.php`)
* **Academic Performance Gauges:** HTML5 `<meter>` and `<progress>` indicators for Attendance (88%), Current CGPA (9.15), and Semester Credit Progress (75%).
* **Department Timetable:** Division I weekly lecture and lab schedule (Room 506) with subject codes and faculty assignments.
* **Campus Events Feed:** Live feed of upcoming university hackathons, conferences, and club activities with thumbnail previews.
* **Student Directory & Enrolled Courses:** Clean summary of active courses (ITUE203, ITUE204, etc.) and departmental contact channels.

### 🛡️ Enterprise Security & Access Control
* **Bcrypt Password Encryption:** Passwords hashed with `PASSWORD_BCRYPT` (Cost 10).
* **Session Protection:** Session ID regeneration upon login (`session_regenerate_id(true)`) to prevent fixation attacks.
* **Perimeter RBAC Barrier:** Standardized `403.php` Forbidden response with automatic audit logging when unauthorized roles attempt administrative access.
* **SQL Injection Prevention:** 100% prepared statements (`$stmt->prepare()` / `$stmt->bind_param()`).
* **Output Sanitization:** Strict `htmlspecialchars()` escaping against Cross-Site Scripting (XSS).

---

## 📁 Repository Directory Structure

```text
StudentHub/
├── index.php                 # Smart gateway router (redirects to login or dashboard)
├── studenthub.sql            # Master MySQL database dump (5 relational tables + seed data)
├── README.md                 # Project documentation & setup instructions
├── TEST_REPORT.md            # Formal test checklist & security audit report
│
├── studenthub/               # Core Production Application Directory
│   ├── index.php             # Directory entrypoint & session router
│   ├── db.php                # Cloud-ready database connection (MySQLi/PDO with env vars)
│   ├── audit_logger.php      # Reusable security audit log handler
│   ├── login.php             # Unified login portal (Username/Email + Bcrypt verification)
│   ├── register.php          # Student self-registration & profile generator
│   ├── logout.php            # Secure session termination & audit recording
│   ├── admin_dashboard.php   # Central Admin Console with tabbed CRUD workspaces
│   ├── student_dashboard.php # Interactive Student Academic Dashboard
│   ├── 403.php               # Standardized RBAC access denied barrier
│   └── uploads/              # Event poster storage directory (with file-type validation)
│
├── Practical-15/             # Mirror academic submission directory (same clean codebase)
│   └── ...                   # Mirrors studenthub/ for academic grading structure
│
└── Practical-1/ to 14/       # Progressive milestone practicals (HTML5 -> JS -> PHP -> MySQL)
```

---

## 🗄️ Database Architecture (`studenthub.sql`)

The application runs on the `studenthub_db` relational database containing 5 integrated tables:

1. **`users`**: User authentication accounts (`id`, `username`, `email`, `password`, `role`, `last_login`, `created_at`).
2. **`students`**: Academic student profiles (`id`, `enrollment_no`, `name`, `email`, `course`, `semester`, `gender`, `status`, `created_at`).
3. **`events`**: Campus events and symposiums (`id`, `title`, `category`, `event_date`, `location`, `description`, `poster`, `created_at`).
4. **`menu_items`**: Dynamic role-based navigation links (`id`, `title`, `link`, `icon`, `min_role`, `sort_order`).
5. **`audit_logs`**: System audit log trail (`id`, `user_id`, `username`, `action`, `details`, `ip_address`, `status`, `created_at`).

---

## 💻 Local Setup & Testing

### Prerequisites
* PHP 8.0 or higher
* MySQL 8.0 or MariaDB
* Apache / Nginx or PHP Built-in Server

### Step 1: Clone Repository
```bash
git clone https://github.com/fenilfinava/StudentHub.git
cd StudentHub
```

### Step 2: Import Database
Open terminal or MySQL client and import `studenthub.sql`:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS studenthub_db;"
mysql -u root -p studenthub_db < studenthub.sql
```

### Step 3: Start Local PHP Server
```bash
php -S localhost:8000
```

### Step 4: Access the Application
Open your browser and navigate to:
```text
http://localhost:8000/
```
The gateway router will automatically direct you to the unified login screen.

---

## 🔑 Default Credentials

| Role | Username / Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin01` / `admin01@gmail.com` | `admin123` | Master Console (Full CRUD, User Mgmt, Events, CSV Export, Audit Logs) |
| **Student** | `student01` / `student01@gmail.com` | `123456` | Student Dashboard (Meters, Timetable, Events Feed, Profile) |
| **Student (Personal)** | `fenilfinava` / `25cs011@charusat.edu.in` | `123456` | Student Dashboard (Enrolled CSE Student) |

---

## ☁️ Deployment Instructions (Cloud / PaaS)

StudentHub is configured with environment variable support in `db.php`:
* `DB_HOST`: Database hostname (e.g., `mysql.railway.internal`)
* `DB_USER`: Database username
* `DB_PASS`: Database password
* `DB_NAME`: Database schema name (`studenthub_db`)
* `PORT`: Server port (defaults to standard HTTP/80 or 8000)

When deploying to platforms such as **Render**, **Railway**, **Fly.io**, or standard cPanel hosting:
1. Push this repository to GitHub.
2. Link the repository to your PaaS service.
3. Attach a MySQL database service and import `studenthub.sql`.
4. Configure the environment variables in your deployment dashboard.
5. Launch!

---

## 📜 Practical Curriculum Mapping

* **Practicals 1–6:** Semantic HTML5 landmarks, multimedia elements, structured academic timetables, forms, CSS box model, responsive layouts.
* **Practical 7:** Client-side Form Validation with JavaScript and regular expressions.
* **Practical 8:** PHP server-side scripting, sessions, dynamic content delivery, and MySQL connectivity.
* **Practical 9:** User account listing and management with server-side queries.
* **Practical 10:** Unified login, password encryption with Bcrypt, and Session management.
* **Practical 11:** Full Student CRUD with dynamic course filtering and CSV export.
* **Practical 12:** Event publishing platform with multipart image upload validation and file unlinking.
* **Practical 13:** Asynchronous Fetch API / AJAX zero-reload CRUD with dynamic role previewing.
* **Practical 14:** Centralized Admin Dashboard with Dynamic Statistics, RBAC barriers, and Audit Trail.
* **Practical 15:** Production-ready integration uniting all practicals into the deployable StudentHub platform.

---

## 👨‍🎓 Author
* **Fenil Finava**
* Enrollment: **25CS011**
* Department of Computer Science & Engineering
* Chandubhai S. Patel Institute of Technology (CSPIT)
* Charotar University of Science and Technology (CHARUSAT)
