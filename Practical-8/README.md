Practical 8: Database Connection with PDO
Student Name: Fenil Finava
Student ID: 25CS011
Course Code: ITUE203 - WEB DEVELOPMENT FRAMEWORKS
Academic Year: 2026-27

Objective
Connect to a database using PHP Data Objects (PDO), fetch data using prepared statements, and display it securely.

Technologies
- PHP 8+
- SQLite (or MySQL)
- PDO

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
- Schema: 3NF database schema provided in schema.sql.
- Connection: Secure PDO instance in db.php with Try/Catch error handling.
- Queries: Uses Prepared Statements to fetch data and prevent SQL Injection.
- UI Integration: test_connection.php executes the query and renders the result dynamically in the application UI.

Testing
- Run PHP server (php -S localhost:8000 router.php).
- Navigate to Database Demo (test_connection.php).
- Verify successful database connection message and table output.