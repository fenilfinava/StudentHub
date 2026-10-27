<?php
session_start();

// StudentHub Portal Entrypoint
if (isset($_SESSION["user_id"])) {
    if (($_SESSION["role"] ?? "") === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
} else {
    header("Location: login.php");
}
exit();
