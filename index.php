<?php
session_start();

// Smart gateway router for StudentHub Portal (Practical 15 Final Submission)
if (isset($_SESSION["user_id"])) {
    if (($_SESSION["role"] ?? "") === "admin") {
        header("Location: studenthub/admin_dashboard.php");
    } else {
        header("Location: studenthub/student_dashboard.php");
    }
} else {
    header("Location: studenthub/login.php");
}
exit();
