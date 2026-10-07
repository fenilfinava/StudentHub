Practical 6: Fetch API, JSON, Search & Filter
Student Name: Fenil Finava
Student ID: 25CS011
Course Code: ITUE203 - WEB DEVELOPMENT FRAMEWORKS
Academic Year: 2026-27

Objective
Fetch external JSON data using the Fetch API and dynamically display it with search, filtering, sorting, and pagination.

Technologies
- HTML5
- CSS3
- JavaScript ES6+
- JSON
- Fetch API

Project Structure
├── assets/
│   ├── css/ (global.css)
│   └── js/ (auth.js, nav.js, theme.js, script.js, data.js)
├── data/ (JSON databases)
├── api/ (PHP endpoints)
├── private/ (CSV storage)
├── *.html (Pages)
└── *.php (Scripts)

Main Features
- JSON Fetching: data.js fetches students.json, events.json, and faqs.json.
- Dynamic Rendering: Builds HTML tables dynamically from JSON keys.
- Search & Filter: Filter by branch/category and search across all fields.
- Sorting: Sort by name/title ascending or descending.
- Pagination: Splits data into chunks of 5 with Next/Prev buttons.
- Loading/Error: Displays a loading spinner and handles fetch errors cleanly.

Testing
- Run PHP server (php -S localhost:8000 router.php).
- Login and navigate to Directory (directory.html).
- Test switching tabs to load different JSON files.
- Test Search, Filter, Sort, and Pagination buttons.