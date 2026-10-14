<?php
session_start();
require_once "timeout.php";
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

if ($_SESSION["role"] !== "student") {
    http_response_code(403);
    die("Unauthorized Access: Students only.");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Fenil Finava (CSPIT)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container rich-dashboard">
    <div class="dashboard-header-bar">
        <div>
            <h2>Student Dashboard</h2>
            <p style="margin:4px 0 0 0; color:#718096; font-size:14px;">
                Welcome, <strong>Fenil Finava</strong> | Enrolment: <mark style="background:#fef08a; padding:1px 6px; border-radius:3px;">25CS011</mark> | B.Tech CSE (3rd Semester) - CSPIT
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge student">STUDENT</span>
            <a href="logout.php" class="btn logout-btn" style="padding:7px 14px; font-size:13px; width:auto; margin:0;">Logout</a>
        </div>
    </div>

    <div class="user-card" style="margin:15px 0;">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?> | <strong>Last Login:</strong> <?php echo htmlspecialchars($last_login); ?> | <strong>Session ID:</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>
    </div>

    <!-- Academic Summary -->
    <div class="dashboard-section">
        <h3>Semester 3 - Academic Summary</h3>
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
                    <td><meter value="94" min="0" max="100" style="width:140px; height:12px;">94%</meter></td>
                    <td><span style="color:#2b6cb0; font-weight:600;">Eligible for Exams</span></td>
                </tr>
                <tr>
                    <td><strong>Assignments</strong></td>
                    <td>Practical 10 Active (Sem 3)</td>
                    <td><progress value="100" max="100" style="width:140px; height:12px;">100%</progress></td>
                    <td><span style="color:#2b6cb0; font-weight:600;">Submitted</span></td>
                </tr>
                <tr>
                    <td><strong>Overall CGPA</strong></td>
                    <td>8.05 / 10.0</td>
                    <td><meter value="8.05" min="0" max="10" style="width:140px; height:12px;">8.05</meter></td>
                    <td><span style="color:#2b6cb0; font-weight:600;">First Class with Dist.</span></td>
                </tr>
                <tr>
                    <td><strong>Enrolled Credits</strong></td>
                    <td>21 Credits (6 Subjects)</td>
                    <td><progress value="21" max="21" style="width:140px; height:12px;">100%</progress></td>
                    <td><span style="color:#2b6cb0; font-weight:600;">6 Subjects Active</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Timetable -->
    <div class="dashboard-section">
        <h3>Semester 3 Timetable (Division I - Room 506)</h3>
        <p style="font-size:13px; color:#718096; margin-top:-6px;">Department of Computer Science & Engineering (CSPIT, A7 Building)</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Day / Time</th>
                    <th>09:10 - 10:10 AM</th>
                    <th>10:10 - 11:10 AM</th>
                    <th>11:10 - 12:10 PM</th>
                    <th>12:10 - 02:10 PM</th>
                    <th>02:20 - 04:20 PM</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Monday</strong></td>
                    <td>FDSA - Room 506</td>
                    <td>FCN - Room 506</td>
                    <td>Lunch Break</td>
                    <td>HS - Room 506</td>
                    <td>Labs: A1-FDSA / B1-FCN / C1-WDF</td>
                </tr>
                <tr>
                    <td><strong>Tuesday</strong></td>
                    <td>MATHS - Room 506</td>
                    <td>FDSA - Room 506</td>
                    <td>Lunch Break</td>
                    <td>Labs: A1-FCN / B1-WDF</td>
                    <td>FCN & WDF - Room 506</td>
                </tr>
                <tr>
                    <td><strong>Wednesday</strong></td>
                    <td>MATHS - Room 506</td>
                    <td>OOP - Room 506</td>
                    <td>Lunch Break</td>
                    <td>Labs: A1-WDF / B1-WDF</td>
                    <td>WDF & FDSA - Room 506</td>
                </tr>
                <tr>
                    <td><strong>Thursday</strong></td>
                    <td>WDF - Room 506</td>
                    <td>FCN - Room 506</td>
                    <td>Lunch Break</td>
                    <td>Labs: A1-WDF / B1-FDSA</td>
                    <td>Labs: A1-OOP / B1-WDF</td>
                </tr>
                <tr>
                    <td><strong>Friday</strong></td>
                    <td>MATHS - Room 506</td>
                    <td>FDSA - Room 506</td>
                    <td>Lunch Break</td>
                    <td>Counselling Sessions</td>
                    <td>FCN & OOP - Room 506</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Event Alert -->
    <div class="event-box">
        <h4 style="margin:0 0 6px 0; color:#c05621;">🔥 Upcoming Event Alert: Odoo Hackathon at LDCE!</h4>
        <p style="margin:0;"><strong>Date:</strong> August 22, 2026 | Location: LDCE Ahmedabad. Get ready for the exciting coding hackathon! Register your CSPIT CSE team now.</p>
    </div>

    <div class="footer-actions" style="margin-top:25px;">
        <a class="btn secondary-btn" href="dashboard.php" style="width:auto; padding:10px 20px;">&larr; Back to Dashboard</a>
        <a class="btn logout-btn" href="logout.php" style="width:auto; padding:10px 20px;">Logout</a>
    </div>
</div>
</body>
</html>
