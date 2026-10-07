Practical 5: Registration Form with JavaScript Validation & Regex
Student Name: Fenil Finava
Student ID: 25CS011
Course Code: ITUE203 - WEB DEVELOPMENT FRAMEWORKS
Academic Year: 2026-27

Objective
Create a student registration form with HTML5 input types, comprehensive JavaScript validation using Regular Expressions, and password strength checking.

Technologies
- HTML5
- CSS3
- JavaScript ES6+

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
- Form Fields: Name, Email, Mobile, Password, Confirm Password, Course, Year, Gender, Terms.
- Real-time Validation: Instant feedback using Regex for Email and Phone.
- Password Strength: Visual meter showing password strength based on regex rules (length, cases, numbers, symbols).
- Match Validation: Checks if Confirm Password matches Password.
- Accessible: Proper labels and error messages placed near fields.

Testing
- Open register.html.
- Try submitting without data to see HTML5 validation.
- Type in the password field to see the dynamic strength meter.
- Test invalid email and phone formats.