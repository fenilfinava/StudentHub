<?php 

require_once "db.php"; 

if ($_SERVER["REQUEST_METHOD"] !== "POST") { 
    die("<div class='alert-box alert-danger'><h2>Invalid Request</h2><p>Please submit the form via POST method.</p><div class='links'><a href='register.php'>← Back to Registration</a></div></div>"); 
} 

// Get form data 
$username = trim($_POST["username"] ?? ""); 
$email = trim($_POST["email"] ?? ""); 
$password = $_POST["password"] ?? ""; 
$confirm_password = $_POST["confirm_password"] ?? ""; 

// Helper function to render error message with style.css
function showError($message) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Validation Error</title><link rel='stylesheet' href='style.css'></head><body>";
    echo "<div class='alert-box alert-danger'>";
    echo "<h2>Validation Error</h2>";
    echo "<p>" . htmlspecialchars($message) . "</p>";
    echo "<div class='links'><a href='register.php'>← Back to Form</a></div>";
    echo "</div></body></html>";
    exit;
}

// 1. Backend validation: Check empty fields
if ($username === "" || $email === "" || $password === "" || $confirm_password === "") { 
    showError("All fields are required."); 
} 

// 2. Validate username (only letters, numbers, underscore, 3 to 50 chars)
if (!preg_match("/^[a-zA-Z0-9_]{3,50}$/", $username)) { 
    showError("Username must contain only letters, numbers, and underscore."); 
} 

// 3. Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
    showError("Please enter a valid email address."); 
} 

// 4. Validate password length (at least 6 characters)
if (strlen($password) < 6) { 
    showError("Password must contain at least 6 characters."); 
} 

// 5. Confirm password match
if ($password !== $confirm_password) { 
    showError("Passwords do not match."); 
} 

// 6. Check duplicate username or email using Prepared Statements
$check = $conn->prepare( 
    "SELECT id FROM users WHERE username = ? OR email = ?" 
); 

$check->bind_param("ss", $username, $email); 
$check->execute(); 
$check->store_result(); 

if ($check->num_rows > 0) { 
    $check->close(); 
    $conn->close(); 
    showError("Username or email already exists."); 
} 

$check->close(); 

// 7. Hash password using secure password_hash()
$hashed_password = password_hash($password, PASSWORD_DEFAULT); 

// 8. Insert user into MySQL using Prepared Statement
$stmt = $conn->prepare( 
    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)" 
); 

$stmt->bind_param( 
    "sss", 
    $username, 
    $email, 
    $hashed_password 
); 

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Registration Result</title><link rel='stylesheet' href='style.css'></head><body>";

if ($stmt->execute()) { 
    echo "<div class='alert-box alert-success'>";
    echo "<h2>Registration Successful!</h2>"; 
    echo "<p>User <strong>" . htmlspecialchars($username) . "</strong> registered successfully.</p>"; 
    echo "<p style='color:#666; font-size:12px;'>Password securely hashed with <code>PASSWORD_DEFAULT</code> (bcrypt/Argon2).</p>";
    echo "<div class='links'>";
    echo "<a href='register.php'>➕ Register Another User</a> | ";
    echo "<a href='users.php'>📋 View Registered Users in MySQL</a>";
    echo "</div>";
    echo "</div>";
} else { 
    echo "<div class='alert-box alert-danger'>";
    echo "<h2>Registration Failed</h2>"; 
    echo "<p>Unable to register user in database: " . htmlspecialchars($stmt->error) . "</p>"; 
    echo "<div class='links'><a href='register.php'>← Back to Registration</a></div>";
    echo "</div>";
} 

echo "</body></html>";

$stmt->close(); 
$conn->close(); 

?>
