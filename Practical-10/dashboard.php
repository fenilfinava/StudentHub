<?php
session_start();
require_once "timeout.php";
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

// Fetch current user details including last_login
$user_id = $_SESSION["user_id"];
$last_login = "N/A";
$stmt = $conn->prepare("SELECT last_login FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $last_login = $row["last_login"] ?? "First Login";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Dashboard - Practical 10</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
    <p class="subtitle">Main Application Dashboard</p>

    <div class="user-card">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
        <p><strong>Role:</strong> <span class="badge <?php echo htmlspecialchars($_SESSION["role"]); ?>"><?php echo htmlspecialchars(strtoupper($_SESSION["role"])); ?></span></p>
        <p><strong>Last Login:</strong> <?php echo htmlspecialchars($last_login); ?></p>
        <p><strong>Session ID:</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>
    </div>

    <div class="role-section">
        <?php if ($_SESSION["role"] === "admin"): ?>
            <div class="panel admin-panel">
                <h3>👑 Admin Controls</h3>
                <p>Welcome to the Admin Portal. You have administrative privileges.</p>
                <a class="btn primary-btn" href="admin_dashboard.php">Go to Admin Dashboard</a>
            </div>
        <?php elseif ($_SESSION["role"] === "student"): ?>
            <div class="panel student-panel">
                <h3>👨‍🎓 Student Controls</h3>
                <p>Welcome to the Student Portal. You have student privileges.</p>
                <a class="btn primary-btn" href="student_dashboard.php">Go to Student Dashboard</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="test-panel">
        <h4>RBAC Test Links:</h4>
        <div class="test-links">
            <a href="admin_dashboard.php" class="test-link">Test Admin Dashboard Access</a>
            <a href="student_dashboard.php" class="test-link">Test Student Dashboard Access</a>
        </div>
    </div>

    <div class="footer-actions">
        <a class="btn logout-btn" href="logout.php">Logout</a>
    </div>
</div>
</body>
</html>
