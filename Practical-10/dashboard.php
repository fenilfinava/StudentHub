<?php
session_start();
require_once "timeout.php";
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

// Fetch user details including last_login
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

$role = $_SESSION["role"];

// If admin, fetch admin stats
$total_users = 0;
if ($role === "admin") {
    $u_cnt = $conn->query("SELECT COUNT(*) as cnt FROM users");
    if ($u_cnt) { $total_users = $u_cnt->fetch_assoc()["cnt"]; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container rich-dashboard">
    <div class="dashboard-header-bar">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
            <p style="margin:4px 0 0 0; color:#718096; font-size:14px;">
                Your role: <strong><?php echo htmlspecialchars($_SESSION["role"]); ?></strong>
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge <?php echo htmlspecialchars($role); ?>">
                <?php echo strtoupper(htmlspecialchars($role)); ?>
            </span>
            <a href="logout.php" class="btn logout-btn" style="padding:7px 14px; font-size:13px; width:auto; margin:0;">Logout</a>
        </div>
    </div>

    <div class="user-card" style="margin:15px 0;">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?> | <strong>Last Login:</strong> <?php echo htmlspecialchars($last_login); ?> | <strong>Session ID:</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>
    </div>

    <?php if ($role === "admin"): ?>
        <!-- Admin Dashboard Section -->
        <div class="dashboard-section">
            <h3>Admin Dashboard</h3>
            <p style="color:#718096; font-size:14px;">Welcome to the Admin Dashboard. You have administrator features and full system control.</p>
            <div style="margin-bottom:15px;">
                <a class="btn" href="admin_dashboard.php" style="width:auto; padding:9px 18px; display:inline-block; margin-top:0;">Go to Admin Dashboard &rarr;</a>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-title">Total Registered Users</div>
                    <div class="stat-val" style="color:#2b6cb0;"><?php echo $total_users; ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-title">Role Privileges</div>
                    <div class="stat-val" style="color:#c05621; font-size:18px;">Administrator</div>
                </div>
                <div class="stat-box">
                    <div class="stat-title">Session Security</div>
                    <div class="stat-val" style="color:#38a169; font-size:18px;">Regenerated ID</div>
                </div>
            </div>
        </div>

    <?php elseif ($role === "student"): ?>
        <!-- Student Dashboard Section (from Practical 1 to 7) -->
        <div class="dashboard-section">
            <h3>Student Dashboard</h3>
            <p style="color:#718096; font-size:14px;">Welcome to the Student Dashboard. Student: <strong>Fenil Finava (25CS011)</strong> - B.Tech CSE (3rd Semester).</p>
            <div style="margin-bottom:15px;">
                <a class="btn" href="student_dashboard.php" style="width:auto; padding:9px 18px; display:inline-block; margin-top:0;">Go to Student Dashboard &rarr;</a>
            </div>

            <!-- Practical 1-7 Academic Summary Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Current Status</th>
                        <th>Progress / Meter</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Overall Attendance</strong></td>
                        <td>94.0% (Excellent)</td>
                        <td><meter value="94" min="0" max="100" style="width:130px; height:12px;">94%</meter></td>
                        <td><span style="color:#2b6cb0; font-weight:600;">Eligible for Exams</span></td>
                    </tr>
                    <tr>
                        <td><strong>Assignments</strong></td>
                        <td>Practical 10 Active (Sem 3)</td>
                        <td><progress value="100" max="100" style="width:130px; height:12px;">100%</progress></td>
                        <td><span style="color:#2b6cb0; font-weight:600;">Submitted</span></td>
                    </tr>
                    <tr>
                        <td><strong>Overall CGPA</strong></td>
                        <td>8.05 / 10.0</td>
                        <td><meter value="8.05" min="0" max="10" style="width:130px; height:12px;">8.05</meter></td>
                        <td><span style="color:#2b6cb0; font-weight:600;">Grade Card Active</span></td>
                    </tr>
                    <tr>
                        <td><strong>Enrolled Credits</strong></td>
                        <td>21 Credits (6 Subjects)</td>
                        <td><progress value="21" max="21" style="width:130px; height:12px;">100%</progress></td>
                        <td><span style="color:#2b6cb0; font-weight:600;">Active Semester</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Timetable Preview -->
        <div class="dashboard-section">
            <h3>Semester 3 Timetable (Division I - Room 506)</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>09:10 - 10:10 AM</th>
                        <th>10:10 - 11:10 AM</th>
                        <th>11:10 - 12:10 PM</th>
                        <th>02:20 - 04:20 PM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Monday</strong></td>
                        <td>FDSA - Room 506</td>
                        <td>FCN - Room 506</td>
                        <td>Lunch Break</td>
                        <td>Labs: A1-FDSA / B1-FCN / C1-WDF</td>
                    </tr>
                    <tr>
                        <td><strong>Tuesday</strong></td>
                        <td>MATHS - Room 506</td>
                        <td>FDSA - Room 506</td>
                        <td>Lunch Break</td>
                        <td>FCN & WDF - Room 506</td>
                    </tr>
                    <tr>
                        <td><strong>Wednesday</strong></td>
                        <td>MATHS - Room 506</td>
                        <td>OOP - Room 506</td>
                        <td>Lunch Break</td>
                        <td>WDF & FDSA - Room 506</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="event-box">
            <h4 style="margin:0 0 6px 0; color:#c05621;">🔥 Upcoming Event Alert: Odoo Hackathon at LDCE!</h4>
            <p style="margin:0;">Date: August 22, 2026. Register your CSE team for the hackathon!</p>
        </div>
    <?php endif; ?>

    <div class="footer-actions" style="margin-top:25px;">
        <a class="btn logout-btn" href="logout.php" style="width:auto; padding:10px 20px;">Logout</a>
    </div>
</div>
</body>
</html>
