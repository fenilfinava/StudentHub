<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

if (isset($_SESSION["user_id"])) {
    log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "USER_LOGOUT", "User logged out successfully.", "SUCCESS");
}

session_unset();
session_destroy();

header("Location: login.php?message=You have been logged out successfully.");
exit();
?>
