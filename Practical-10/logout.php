<?php
session_start();
require_once "db.php";

// Clear remember token from database if user is logged in
if (isset($_SESSION["user_id"])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token_hash = NULL, remember_token_expiry = NULL WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $stmt->close();
    }
}

// 1. Remove all session variables
$_SESSION = [];

// 2. Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Clear remember-me cookie
if (isset($_COOKIE["remember_token"])) {
    setcookie("remember_token", "", time() - 42000, "/");
}

// 4. Destroy session
session_destroy();

// Redirect to login with confirmation message
header("Location: login.php?message=You have been logged out successfully.");
exit;
?>
