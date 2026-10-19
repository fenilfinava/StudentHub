<div align="center">
  <h1>🎓 Practical 12</h1>
  <h3>Event Management System with File Upload, Poster Validation, and Role-Based Access Control</h3>
</div>

---

### 👨‍🎓 Student Information
- **Name:** Fenil Finava
- **ID:** 25CS011
- **Course:** ITUE203 - WEB DEVELOPMENT FRAMEWORKS
- **Year:** 2026-27

---

### 🎯 Objective
> Implement an Event Management System in PHP and MySQL using standard procedural `mysqli` functions featuring secure file upload capabilities, multi-layer server-side poster validation (allowed extensions, file size limit, and real MIME image verification), poster thumbnail rendering in database tables, automated disk cleanup on update/delete via `unlink()`, dynamic search & category filtering, and Role-Based Access Control (RBAC) ensuring unauthorized access restriction.

---

### 🚀 Main Features
- ✅ **Admin Authentication & RBAC:** Enforces session validation to restrict event management controls to verified administrators (`role === 'admin'`). Unauthorized users are blocked with an HTTP 403 Forbidden alert.
- ✅ **Secure File Upload (`upload.php`):** Validates uploaded poster files for allowed image extensions (`.jpg`, `.jpeg`, `.png`, `.webp`), 2 MB file size limit, and real binary image authenticity using `getimagesize()`.
- ✅ **File Storage & Naming:** Generates cryptographically unique filenames using `time() . '_' . uniqid()` and saves images to the `uploads/` directory. Only the filename string is persisted in MySQL.
- ✅ **Disk File Cleanup:** Automatically removes previous poster image files from the server via `unlink()` when an event is updated with a replacement poster or deleted entirely.
- ✅ **Poster Thumbnail Display:** Displays responsive image thumbnails within the administrative event records table.
- ✅ **Dynamic Search & Filtering:** Allows real-time keyword search across event titles and locations, alongside dropdown category filtering.
- ✅ **Feedback & Confirmations:** Provides color-coded feedback banners for all CRUD operations and JavaScript confirmation popups (`confirm()`) before event removal.

---

### 📂 Project Structure
```text
Practical-12/
├── db.php
├── edit.php
├── index.php
├── login.php
├── logout.php
├── schema.sql
├── style.css
├── upload.php
├── upload_demo.php
├── uploads/
│   └── 1791537429_6ac8b1155ae37.jpeg
└── README.md
```

---

### 💻 How to Run
1. Ensure MySQL server is running.
2. Import schema into MySQL:
   ```bash
   mysql -u root < Practical-12/schema.sql
   ```
3. Start PHP development server:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Open in web browser:
   - Login Page: `http://localhost:8000/Practical-12/login.php`
   - Event Management Dashboard: `http://localhost:8000/Practical-12/index.php`
   - File Upload Test: `http://localhost:8000/Practical-12/upload_demo.php`

---

### 🔑 Test Credentials
| Role | Email | Password | Access Status |
|---|---|---|---|
| **Admin** | `admin01@gmail.com` | `admin123` | ✅ Full Dashboard Access |
| **Student** | `student01@gmail.com` | `123456` | 🚫 Unauthorized (Access Restricted) |
