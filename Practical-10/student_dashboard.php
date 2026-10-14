<?php
session_start();
require_once "timeout.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

if ($_SESSION["role"] !== "student") {
    http_response_code(403);
    die("Unauthorized Access: Students only.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container dashboard-container">
    <h2>Student Dashboard</h2>
    <p class="subtitle">Protected Student Area</p>

    <div class="alert-box info-box">
        <p><strong>Welcome, Student (<?php echo htmlspecialchars($_SESSION["username"]); ?>)!</strong></p>
        <p>You have access to student features, course enrollment, and personal profile details.</p>
    </div>

    <div class="user-card">
        <p><strong>Role:</strong> <span class="badge student">STUDENT</span></p>
        <p><strong>Session ID:</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>
    </div>

    <div class="footer-actions">
        <a class="btn secondary-btn" href="dashboard.php">Back to Main Dashboard</a>
        <a class="btn logout-btn" href="logout.php">Logout</a>
    </div>
</div>
</body>
</html>
