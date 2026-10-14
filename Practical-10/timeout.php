<?php
// timeout.php - Session Timeout Validation Handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 300; // 5 minutes inactivity timeout (can be changed to 30 for testing)

if (isset($_SESSION["user_id"])) {
    if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $timeout)) {
        // Clear session variables
        $_SESSION = [];
        // Expire session cookie
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
        // Destroy session
        session_destroy();
        header("Location: login.php?error=Session expired. Please login again.");
        exit;
    }
    // Update last activity time
    $_SESSION["last_activity"] = time();
}
?>
