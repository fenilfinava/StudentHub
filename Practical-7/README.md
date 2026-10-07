Practical 7: PHP Form with POST, Validation & File Storage
Student Name: Fenil Finava
Student ID: 25CS011
Course Code: ITUE203 - WEB DEVELOPMENT FRAMEWORKS
Academic Year: 2026-27

Objective
Create a PHP contact/registration form using POST method, validate and sanitize input, and store data securely in a CSV file.

Technologies
- HTML5
- CSS3
- PHP 8+
- CSV Storage

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
- PHP Backend: api/contact.php handles the POST request.
- Sanitization: Uses htmlspecialchars, stripslashes, and trim.
- Validation: Validates Name (regex), Email (filter_var), and empty fields.
- File Storage: Uses fputcsv and flock (LOCK_EX) to securely append to private/contacts.csv.
- Redirection: Returns to contact.html with ?success=1 or ?error=...

Testing
- Run PHP server (php -S localhost:8000 router.php).
- Login and navigate to Contact Us (contact.html).
- Submit the form with valid data.
- Verify success message and check private/contacts.csv for the new entry.