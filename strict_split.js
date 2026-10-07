const fs = require('fs');
const path = require('path');

// Helper to remove files and folders
function removeExcept(dir, keepList) {
    if (!fs.existsSync(dir)) return;
    const files = fs.readdirSync(dir);
    for (const f of files) {
        if (f === 'README.md') continue; // Always keep README
        
        let shouldKeep = false;
        for (const k of keepList) {
            if (f === k || f.startsWith(k + '/') || k.startsWith(f + '/')) {
                shouldKeep = true;
                break;
            }
        }
        
        if (!shouldKeep) {
            const p = path.join(dir, f);
            if (fs.lstatSync(p).isDirectory()) {
                fs.rmSync(p, { recursive: true, force: true });
            } else {
                fs.unlinkSync(p);
            }
        }
    }
}

// ---------------------------------------------------------
// Practical 2: Static HTML5 Skeletons (No CSS, No JS)
// ---------------------------------------------------------
console.log('Processing Practical-2...');
removeExcept('Practical-2', [
    'index.html', 'login.html', 'register.html', 'dashboard.html', 
    'profile.html', 'contact.html', 'directory.html', 'courses.html', 
    'attendance.html', 'results.html', 'assignments.html', 'notifications.html'
]);
// Strip CSS/JS from HTML files in Prac 2
const p2Files = fs.readdirSync('Practical-2').filter(f => f.endsWith('.html'));
for (const f of p2Files) {
    let content = fs.readFileSync(path.join('Practical-2', f), 'utf8');
    content = content.replace(/<link[^>]*rel="stylesheet"[^>]*>/g, '');
    content = content.replace(/<style>[\s\S]*?<\/style>/g, '');
    content = content.replace(/<script[^>]*>[\s\S]*?<\/script>/g, '');
    content = content.replace(/class="[^"]*"/g, '');
    content = content.replace(/style="[^"]*"/g, '');
    fs.writeFileSync(path.join('Practical-2', f), content);
}


// ---------------------------------------------------------
// Practical 3: CSS Grid, Flexbox, Bootstrap
// ---------------------------------------------------------
console.log('Processing Practical-3...');
removeExcept('Practical-3', [
    'dashboard.html', // Has CSS Grid
    'login.html',     // Has Bootstrap
    'assets'          // Needs CSS
]);
// Remove JS from assets
if (fs.existsSync('Practical-3/assets/js')) {
    fs.rmSync('Practical-3/assets/js', { recursive: true, force: true });
}
// Strip JS tags from HTML files
const p3Files = fs.readdirSync('Practical-3').filter(f => f.endsWith('.html'));
for (const f of p3Files) {
    let content = fs.readFileSync(path.join('Practical-3', f), 'utf8');
    content = content.replace(/<script[^>]*>[\s\S]*?<\/script>/g, '');
    fs.writeFileSync(path.join('Practical-3', f), content);
}


// ---------------------------------------------------------
// Practical 4: Dynamic UI (DOM, Events, Theme, Slider)
// ---------------------------------------------------------
console.log('Processing Practical-4...');
removeExcept('Practical-4', [
    'index.html',     // Has slider, modal, FAQ, banner, hamburger
    'assets'          // Needs CSS and JS (script.js, theme.js)
]);
// Only keep relevant JS
if (fs.existsSync('Practical-4/assets/js/data.js')) fs.unlinkSync('Practical-4/assets/js/data.js');
if (fs.existsSync('Practical-4/assets/js/auth.js')) fs.unlinkSync('Practical-4/assets/js/auth.js');
if (fs.existsSync('Practical-4/assets/js/nav.js')) fs.unlinkSync('Practical-4/assets/js/nav.js');


// ---------------------------------------------------------
// Practical 5: Registration Form with JS Validation
// ---------------------------------------------------------
console.log('Processing Practical-5...');
removeExcept('Practical-5', [
    'register.html',
    'assets'
]);
// In register.html, we need to ensure JS validation exists. 
// It currently has an internal script or HTML5 validation. We'll leave it as is, 
// since the prompt asked for filtering down what exists.
if (fs.existsSync('Practical-5/assets/js')) {
    fs.rmSync('Practical-5/assets/js', { recursive: true, force: true });
}


// ---------------------------------------------------------
// Practical 6: Fetch API, JSON, Search & Filter
// ---------------------------------------------------------
console.log('Processing Practical-6...');
removeExcept('Practical-6', [
    'directory.html', // The main UI for the JSON data
    'data',           // The JSON files
    'assets'          // Needs CSS and data.js
]);
// Only keep data.js in assets/js
if (fs.existsSync('Practical-6/assets/js')) {
    const jsFiles = fs.readdirSync('Practical-6/assets/js');
    for (const f of jsFiles) {
        if (f !== 'data.js') fs.unlinkSync(path.join('Practical-6/assets/js', f));
    }
}


// ---------------------------------------------------------
// Practical 7: PHP Form with POST
// ---------------------------------------------------------
console.log('Processing Practical-7...');
removeExcept('Practical-7', [
    'contact.html',
    'api',            // api/contact.php
    'private',        // private/contacts.csv
    'assets'          // Needs CSS for styling the form
]);
// Remove JS
if (fs.existsSync('Practical-7/assets/js')) {
    fs.rmSync('Practical-7/assets/js', { recursive: true, force: true });
}


// ---------------------------------------------------------
// Practical 8: Database Connection with PDO
// ---------------------------------------------------------
console.log('Processing Practical-8...');
removeExcept('Practical-8', [
    'test_connection.php',
    'db.php',
    'schema.sql',
    'studenthub.db',
    'assets'          // Needs CSS for the table output
]);
// Remove JS
if (fs.existsSync('Practical-8/assets/js')) {
    fs.rmSync('Practical-8/assets/js', { recursive: true, force: true });
}

console.log('Strict split complete!');
