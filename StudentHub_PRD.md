# Product Requirements Document (PRD)
**Project Name:** StudentHub (University Student Portal)
**Institution:** Chandubhai S. Patel Institute of Technology (CSPIT) - CHARUSAT
**Version:** 1.0.0

---

## 1. Project Overview
StudentHub is a comprehensive, responsive, and dynamic web-based student portal designed for university students. It centralizes academic records, course enrollments, assignments, campus notifications, and personal profiles into a single, cohesive interface. The system is designed with a premium UI/UX, utilizing modern CSS Grid/Flexbox layouts and Vanilla JavaScript for dynamic interactions without relying on heavy frontend frameworks.

## 2. Goals & Objectives
*   **Centralized Access:** Provide students with a single dashboard to access all academic and campus-related information.
*   **Premium UI/UX:** Ensure a consistent, modern, and highly responsive design across all devices (Desktop, Tablet, Mobile) using CSS Grid and Flexbox.
*   **Dynamic Data Handling:** Utilize the Fetch API to render data dynamically from JSON files, supporting search, filter, sort, and pagination.
*   **Robust Validation:** Implement strict client-side validation using Regex and JavaScript to ensure data integrity during registration and login.
*   **Modularity:** Maintain a clean codebase with reusable CSS (global.css) and modular JS (data.js, script.js).

---

## 3. System Architecture & Tech Stack
*   **Frontend HTML:** HTML5 (Semantic Tags: `<header>`, `<nav>`, `<main>`, `<aside>`, `<section>`, `<article>`)
*   **Styling:** CSS3 (CSS Grid, Flexbox, CSS Variables, Media Queries, Custom Animations)
*   **Scripting:** Vanilla JavaScript (ES6+), DOM Manipulation, Event Listeners
*   **Data Storage:** LocalStorage (for Theme preferences), JSON files (for mock database)
*   **API / Data Fetching:** JavaScript `fetch()` API
*   **Deployment:** Local Python HTTP Server (`python3 -m http.server 8080`)

---

## 4. User Workflow & Navigation Flow

### Authentication Flow
1.  **Landing Page (`index.html`):** Public-facing page with dynamic slider, FAQ accordion, notification banners, and dark/light mode toggle.
2.  **Registration (`register.html`):** New students fill out a comprehensive form. Features real-time password strength meter and strict regex validation. Redirects to Login on success.
3.  **Login (`login.html`):** Existing students authenticate. Redirects to Dashboard on success.

### Internal Dashboard Flow (Post-Login)
All internal pages share a consistent CSS Grid layout consisting of a fixed Sidebar Navigation, Header, Main Content Area, and Footer.

*   **Dashboard (`dashboard.html`):** The central hub displaying academic summary (Attendance, CGPA, Credits) and a weekly timetable.
*   **Profile (`profile.html`):** Displays personal information, enrollment details, and forms to update contact info/password.
*   **Directory (`directory.html`):** A highly dynamic page fetching data from JSON. Features tabs for Students, Events, and FAQs. Includes Search, Filter, Sort, and Pagination logic.
*   **Assignments (`assignments.html`):** Tracks the status of semester assignments and provides a file upload form for advance submissions.
*   **Other Modules:** Courses (`courses.html`), Attendance (`attendance.html`), Results (`results.html`), Achievements (`achievements.html`), Notifications (`notifications.html`), Contact Us (`contact.html`).

---

## 5. Core Features & Specifications

### 5.1 Global Layout (`global.css`)
*   **Grid Structure:** `grid-template-areas: "nav header" "nav main" "nav aside" "nav footer";`
*   **Responsiveness:** At `< 992px`, the sidebar transforms into a top horizontal navigation bar for tablet/mobile viewing.
*   **Theming:** Consistent CSS variables (`--primary`, `--card-bg`, `--bg-color`) ensure uniform styling across all internal pages.

### 5.2 Dynamic Form Validation
*   **Name:** Alphabets only, minimum 3 characters (`/^[a-zA-Z\s]{3,}$/`).
*   **Email:** Standard email format validation.
*   **Mobile:** Exactly 10 numeric digits (`/^[0-9]{10}$/`).
*   **Password:** Minimum 8 characters, containing at least one uppercase, one lowercase, one number, and one special character.
*   **Password Strength Meter:** Dynamically calculates strength (Weak/Medium/Strong) and updates a visual progress bar.

### 5.3 Dynamic JSON Rendering (`data.js`)
*   **Fetch API:** Asynchronously loads data from `students.json`, `events.json`, and `faqs.json`.
*   **Error Handling & Loading States:** Displays a loading spinner during fetch and an error message if the fetch fails.
*   **Search:** Real-time text matching across all object properties.
*   **Filter:** Dynamic dropdowns populated based on unique categories/branches in the data.
*   **Sort:** Alphabetical sorting (A-Z, Z-A).
*   **Pagination:** Calculates total pages and limits display to 5 records per page, with dynamic Next/Previous controls.

---

## 6. Data Models (Mock JSON Database)

### `students.json`
Array of student objects containing:
*   `id` (Integer)
*   `name` (String)
*   `enrollment` (String)
*   `branch` (String)
*   `sem` (Integer)
*   `cpi` (Float)

### `events.json`
Array of event objects containing:
*   `id` (Integer)
*   `title` (String)
*   `date` (YYYY-MM-DD)
*   `location` (String)
*   `category` (String)

### `faqs.json`
Array of FAQ objects containing:
*   `id` (Integer)
*   `q` (String - Question)
*   `a` (String - Answer)

---

## 7. Future Enhancements
*   **Backend Integration:** Replace JSON files with a real database (Node.js/Express or Python/Django) and REST APIs.
*   **Authentication Session:** Implement JWT (JSON Web Tokens) for secure, persistent login sessions.
*   **Role-Based Access Control (RBAC):** Create separate dashboards for Students, Faculty, and Administrators.
