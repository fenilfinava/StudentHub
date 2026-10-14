<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$remember_me = isset($_POST["remember_me"]);

// Backend validation
if ($email === "" || $password === "") {
    header("Location: login.php?error=All fields are required.");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?error=Invalid email address.");
    exit;
}

// Find user by email using Prepared Statements
$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
if (!$stmt) {
    header("Location: login.php?error=Internal database error.");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=Invalid email or password.");
    exit;
}

$user = $result->fetch_assoc();

// Verify password using password_verify()
if (!password_verify($password, $user["password"])) {
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=Invalid email or password.");
    exit;
}

// Regenerate session ID after successful authentication (Fixation Protection)
session_regenerate_id(true);

// Store authenticated user state in session
$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];
$_SESSION["last_activity"] = time();

// Intermediate Extension: Update last_login timestamp
$upd = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
if ($upd) {
    $upd->bind_param("i", $user["id"]);
    $upd->execute();
    $upd->close();
}

// Advanced Extension: Secure Remember-Me Token Handling
if ($remember_me) {
    $token = bin2hex(random_bytes(32));
    $token_hash = hash("sha256", $token);
    $expiry_time = time() + (30 * 24 * 60 * 60); // 30 days
    $expiry_datetime = date("Y-m-d H:i:s", $expiry_time);

    $rem_stmt = $conn->prepare("UPDATE users SET remember_token_hash = ?, remember_token_expiry = ? WHERE id = ?");
    if ($rem_stmt) {
        $rem_stmt->bind_param("ssi", $token_hash, $expiry_datetime, $user["id"]);
        $rem_stmt->execute();
        $rem_stmt->close();
    }

    setcookie(
        "remember_token",
        $token,
        [
            "expires" => $expiry_time,
            "path" => "/",
            "secure" => false, // Set to true if served over HTTPS
            "httponly" => true,
            "samesite" => "Lax"
        ]
    );
}

$stmt->close();
$conn->close();

// Redirect to common dashboard
header("Location: dashboard.php");
exit;
?>
