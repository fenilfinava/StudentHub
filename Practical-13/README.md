# Practical 13: PHP JSON API & AJAX/Fetch-Based CRUD (No Page Reload)

**Course:** ITUE203 - Web Development Frameworks  
**Department:** Computer Science & Engineering, CSPIT, CHARUSAT  
**Student:** Fenil Finava (25CS011)  
**Academic Year:** 2026-27 | Semester: 3rd  
**Due Date:** October 24, 2026  

---

## 📌 Submit-Ready Checklist
- [x] **Database Schema:** `schema.sql` (Creates `dashboard_db`, `menu_items`, `items` tables and seeds initial data)
- [x] **Database Connection:** `db.php` (Safe PDO connection with JSON 500 error reporting)
- [x] **RESTful JSON API:** `api.php` (Standardized JSON responses for RBAC menu and CRUD operations)
- [x] **Frontend View:** `index.html` (Single-Page Application dashboard with Role Switcher and Inventory CRUD form)
- [x] **JavaScript Logic:** `script.js` (Asynchronous `fetch()` calls, DOM manipulation, no page reload)
- [x] **Modern Styling:** `style.css` (Clean dashboard UI, status alerts, badges, and responsive tables)
- [x] **Lab Report Document:** `PRAC_13_ITUE203_25CS011.docx` (Structured Word report with logo headers, test report table, and 11 screenshot placeholders)

---

## 🚀 Quick Setup & Execution

### 1. Database Setup
```bash
mysql -u root < schema.sql
```

### 2. Start Local PHP Server
```bash
php -S 0.0.0.0:8000
```

### 3. Open in Browser
👉 **URL:** [http://localhost:8000/Practical-13/index.html](http://localhost:8000/Practical-13/index.html)

---

## 📡 RESTful API Reference (`api.php`)

| Operation | Action Query | HTTP Method | Request Body | Response Format |
|---|---|---|---|---|
| **Dynamic Menu (RBAC)** | `?action=get_menu&role={role}` | `GET` | *None* | `{"status":"success","data":[{"title":"...","link":"..."}]}` |
| **Read Items** | `?action=read` | `GET` | *None* | `{"status":"success","data":[{"id":1,"name":"...","price":"..."}]}` |
| **Create Item** | `?action=create` | `POST` | `{"name":"...","category":"...","price":99.99}` | `{"status":"success","message":"Item created successfully"}` |
| **Update Item** | `?action=update` | `POST` | `{"id":1,"name":"...","category":"...","price":99.99}` | `{"status":"success","message":"Item updated successfully"}` |
| **Delete Item** | `?action=delete` | `POST` | `{"id":1}` | `{"status":"success","message":"Item deleted successfully"}` |

---

## 📸 Required Screenshots for Lab Report (`PRAC_13_ITUE203_25CS011.docx`)

### Part 1: UI Screenshots
1. **Inventory List UI (Initial Fetch on Page Load)** - Inventory table showing seeded records loaded asynchronously without page reload.
2. **Add New Item (Zero Page Reload Fetch POST)** - Adding a new inventory item and displaying the green success notification.
3. **Edit Item (Form Populate & Fetch Update)** - Editing an existing item, updating fields, and verifying table change.
4. **Delete Item (Fetch DELETE/POST with Confirmation)** - Browser confirmation dialog and row removal with notification banner.
5. **Role-Based Dynamic Menu (RBAC Switching)** - Switching Active Role dropdown between `User` (2 links), `Editor` (3 links), and `Admin` (5 links).
6. **Error / Loading States** - Displaying the in-progress loading indicator and validation/network error alerts.

### Part 2: Browser Network Tab Screenshots
7. **Network Tab - GET api.php?action=read** (Status 200 OK, JSON array response)
8. **Network Tab - POST api.php?action=create** (Status 200 OK, JSON payload & success response)
9. **Network Tab - POST api.php?action=update** (Status 200 OK, JSON payload & success response)
10. **Network Tab - POST api.php?action=delete** (Status 200 OK, JSON payload `{"id":...}` & success response)
11. **Network Tab - GET api.php?action=get_menu** (Status 200 OK, RBAC role query and JSON response)
