<div align="center">
  <h1>🎓 Practical 11</h1>
  <h3>Student Management System (CRUD with Search, Filter, Soft Delete & CSV Export)</h3>
</div>

---

### 👨‍🎓 Student Information
- **Name:** Fenil Finava
- **ID:** 25CS011
- **Course:** ITUE203 - WEB DEVELOPMENT FRAMEWORKS
- **Year:** 2026-27

---

### 🎯 Objective
> Implement a complete Student Management System using PHP and MySQL standard procedural `mysqli` functions with Create, Read, Update, Delete (CRUD) capabilities, dynamic Search by Name/Email, Course-wise dropdown filtering, Soft Delete (marking status as Inactive), and CSV export for spreadsheet analysis.

---

### 🚀 Main Features
- ✅ **Create (Insert):** Adds new student records with name, email, course, and gender.
- ✅ **Read & Display:** Renders student records in an HTML table with status badges (`Active` / `Inactive`).
- ✅ **Search Functionality:** Real-time search query matching names or email addresses using SQL `LIKE`.
- ✅ **Course Filtering:** Dropdown filtering allowing isolation of students by department/course (`Computer Science`, `Web Development`, `Data Science`).
- ✅ **Update (Edit):** Dedicated `edit.php` page allowing modification of student attributes and status.
- ✅ **Soft Delete:** Toggles student status to `Inactive` without destroying historical database rows.
- ✅ **Hard Delete:** Permanently deletes a student record with client-side JavaScript confirmation dialog (`confirm()`).
- ✅ **CSV Export:** Generates and downloads a formatted `.csv` spreadsheet file with a single click.
- ✅ **Feedback Alerts:** Color-coded success and error notifications for every database operation.

---

### 📂 Project Structure
```text
Practical-11/
├── db.php
├── edit.php
├── index.php
├── schema.sql
├── style.css
└── README.md
```

---

### 💻 How to Run
1. Ensure MySQL server is running.
2. Import schema into MySQL:
   ```bash
   mysql -u root < Practical-11/schema.sql
   ```
3. Start PHP development server:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Open in web browser:
   - Student Management System: `http://localhost:8000/Practical-11/index.php`
   - CSV Export: `http://localhost:8000/Practical-11/index.php?export=csv`
