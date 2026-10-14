<?php
session_start();
require_once "timeout.php";
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    http_response_code(403);
    die("Unauthorized Access: Admins only.");
}

// Fetch user last login
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

// Fetch stats
$total_users = 0;
$u_cnt = $conn->query("SELECT COUNT(*) as cnt FROM users");
if ($u_cnt) { $total_users = $u_cnt->fetch_assoc()["cnt"]; }

$total_students = 0;
$s_cnt = $conn->query("SELECT COUNT(*) as cnt FROM students");
if ($s_cnt) { $total_students = $s_cnt->fetch_assoc()["cnt"]; }

$total_events = 0;
$e_cnt = $conn->query("SELECT COUNT(*) as cnt FROM events");
if ($e_cnt) { $total_events = $e_cnt->fetch_assoc()["cnt"]; }

// Fetch all registered users
$all_users = $conn->query("SELECT id, username, email, role, last_login, created_at FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container rich-dashboard">
    <div class="dashboard-header-bar">
        <div>
            <h2>Admin Dashboard</h2>
            <p style="margin:4px 0 0 0; color:#718096; font-size:14px;">
                Administrator Management Console | <strong>CSPIT - CHARUSAT</strong>
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge admin">ADMIN</span>
            <a href="logout.php" class="btn logout-btn" style="padding:7px 14px; font-size:13px; width:auto; margin:0;">Logout</a>
        </div>
    </div>

    <div class="user-card" style="margin:15px 0;">
        <p><strong>Admin Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?> | <strong>Last Login:</strong> <?php echo htmlspecialchars($last_login); ?> | <strong>Session ID:</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>
    </div>

    <!-- Metrics Cards -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-title">Registered Users</div>
            <div class="stat-val" style="color:#2b6cb0;"><?php echo $total_users; ?> Accounts</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">3NF Students Table</div>
            <div class="stat-val" style="color:#38a169;"><?php echo $total_students; ?> Students</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Campus Events</div>
            <div class="stat-val" style="color:#d69e2e;"><?php echo $total_events; ?> Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Password Security</div>
            <div class="stat-val" style="color:#805ad5; font-size:17px; margin-top:5px;">bcrypt / Hashed</div>
        </div>
    </div>

    <!-- Live Registered Users Table -->
    <div class="dashboard-section">
        <h3>User Management & Role Permissions (MySQL users table)</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>System Role</th>
                    <th>Last Login</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($all_users && $all_users->num_rows > 0): ?>
                    <?php while ($u = $all_users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $u["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($u["username"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($u["email"]); ?></td>
                            <td>
                                <span class="badge <?php echo htmlspecialchars($u["role"]); ?>">
                                    <?php echo htmlspecialchars(strtoupper($u["role"])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($u["last_login"] ?? "Never"); ?></td>
                            <td><?php echo htmlspecialchars($u["created_at"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No users registered in database.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="event-box" style="border-left-color:#3182ce; background:#ebf8ff;">
        <h4 style="margin:0 0 6px 0; color:#2b6cb0;">🛡️ Role-Based Access Control (RBAC) Active</h4>
        <p style="margin:0;">This dashboard is strictly protected. Non-admin users attempting direct URL access are automatically denied with <code>HTTP 403 Forbidden</code>.</p>
    </div>

    <div class="footer-actions" style="margin-top:25px;">
        <a class="btn secondary-btn" href="dashboard.php" style="width:auto; padding:10px 20px;">&larr; Back to Dashboard</a>
        <a class="btn logout-btn" href="logout.php" style="width:auto; padding:10px 20px;">Logout</a>
    </div>
</div>
</body>
</html>
