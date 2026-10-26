# Practical 13: StudentHub - AJAX/Fetch-Based Student Management & Dynamic RBAC Navigation

**Course:** ITUE203 - Web Development Frameworks  
**Department:** Computer Science & Engineering, CSPIT, CHARUSAT  
**Student:** Fenil Finava (25CS011)  
**Academic Year:** 2026-27 | Semester: 3rd  
**Due Date:** October 24, 2026  

---

## 📌 Project Overview
Practical 13 elevates the **StudentHub** semester application from traditional multi-page synchronous PHP (from Practicals 1 to 12) into a modern, asynchronous **Single-Page Application (SPA)** using a PHP RESTful JSON API and vanilla JavaScript `fetch()` API (`async/await`).

### Key Capabilities:
1. **Zero Page Reloads:** All CRUD (Create, Read, Update, Delete) student operations execute instantaneously without browser refresh (`event.preventDefault()`).
2. **Dynamic RBAC Navigation:** Dynamic navigation menu fetched via `api.php?action=get_menu` based on active user role:
   - **User (Student):** Access to Student Dashboard, My Profile, and Campus Events.
   - **Editor (Faculty):** Additional access to Faculty Portal and Student Attendance.
   - **Admin:** Full access including Admin Console, Student Records CRUD, User Management, and System Settings.
3. **StudentHub Integration:** Synchronized with the real StudentHub MySQL database (`student_db`), featuring live student records (`Fenil Finava`, `Diya Shah`, etc.).
4. **Lab Manual Spec Compliance:** Supports a dual-entity view (`students` as primary StudentHub entity and `items` from the lab manual specification).

---

## 📂 Submit-Ready Checklist
- [x] **`schema.sql`**: Configures `student_db`, `students`, `menu_items`, and `items` tables with seeded data.
- [x] **`db.php`**: Secure PDO database connection with JSON 500 error reporting.
- [x] **`api.php`**: RESTful JSON API handling parameterized queries and JSON request/response payloads.
- [x] **`index.html`**: Single-Page Application interface with StudentHub branding, dynamic RBAC navbar, and responsive CRUD cards.
- [x] **`script.js`**: Asynchronous Fetch API logic, DOM manipulation, loading states, and live alerts.
- [x] **`style.css`**: Professional StudentHub portal styling matching CSPIT branding.
- [x] **`PRAC_13_ITUE203_25CS011.docx`**: Word lab report with Table 0 logos, student details, test report table, and 11 screenshot placeholder boxes.

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
👉 **URL:** [http://localhost:8000/Practical-13/index.html](http://localhost:8000/Practical-13/index.html)

---

## 📡 RESTful API Reference (`api.php`)

| Endpoint | Method | Payload / Params | Response |
|---|---|---|---|
| `?action=get_menu&role={role}` | `GET` | *None* | `{"status":"success","role":"...","data":[...]}` |
| `?action=read&type=students` | `GET` | *None* | `{"status":"success","type":"students","data":[...]}` |
| `?action=create&type=students` | `POST` | `{"name":"...","email":"...","course":"...","gender":"..."}` | `{"status":"success","message":"..."}` |
| `?action=update&type=students` | `POST` | `{"id":1,"name":"...","email":"...","course":"...","gender":"..."}` | `{"status":"success","message":"..."}` |
| `?action=delete&type=students` | `POST` | `{"id":1}` | `{"status":"success","message":"..."}` |

---

## 📸 Screenshots Required for Lab Report (`PRAC_13_ITUE203_25CS011.docx`)

### Part 1: UI Screenshots
1. **Student List UI (Initial Fetch on Page Load):** Table showing live StudentHub student records rendered without page reload.
2. **Add New Student (Fetch POST):** Adding a student (e.g. Fenil Finava) and seeing instant green success alert and table row addition without reload.
3. **Edit Student Details (Fetch Update):** Form populating student details and updating fields with instant feedback banner.
4. **Delete Student Record (Fetch Delete):** Confirmation prompt and instantaneous removal of row from table without reload.
5. **Role-Based Dynamic Navigation (RBAC Switching):** Switching between `User (Student)`, `Editor (Faculty)`, and `Admin` in the dropdown.
6. **Error / Loading States:** Clicking "Simulate Loading State" or "Simulate Validation Error" to display the in-progress spinner and error feedback.

### Part 2: Network Tab Screenshots (Inspect -> Network -> Fetch/XHR)
7. `GET api.php?action=read` (Status 200 OK & JSON array)
8. `POST api.php?action=create` (Status 200 OK & JSON response)
9. `POST api.php?action=update` (Status 200 OK & JSON response)
10. `POST api.php?action=delete` (Status 200 OK & JSON response)
11. `GET api.php?action=get_menu` (Status 200 OK & RBAC menu JSON)
