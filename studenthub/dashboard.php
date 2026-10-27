<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to access your student dashboard.");
    exit();
}

$user_id  = $_SESSION["user_id"];
$username = $_SESSION["username"];
$email    = $_SESSION["email"] ?? "";
$role     = $_SESSION["role"] ?? "student";

// Log student dashboard view
log_audit($conn, $user_id, $username, "VIEW_DASHBOARD", "Student viewed academic dashboard.", "SUCCESS");

// Fetch student profile details from students table
$student_name   = "Fenil Finava";
$enrollment_no  = "25CS011";
$course         = "B.Tech CSE";
$semester       = "3rd Semester";

$st_stmt = $conn->prepare("SELECT enrollment_no, name, course, semester FROM students WHERE email = ? LIMIT 1");
if ($st_stmt) {
    $st_stmt->bind_param("s", $email);
    $st_stmt->execute();
    $res = $st_stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $student_name  = $row["name"];
        $enrollment_no = $row["enrollment_no"] ?: "25CS011";
        $course        = "B.Tech " . (strpos($row["course"], "Computer") !== false ? "CSE" : $row["course"]);
        $semester      = ($row["semester"] ?: 3) . "rd Semester";
    }
    $st_stmt->close();
}

// Fetch upcoming campus events from events table
$events = [];
$e_res = $conn->query("SELECT id, title, event_date, location, description FROM events ORDER BY event_date ASC LIMIT 2");
if ($e_res) {
    while ($ev = $e_res->fetch_assoc()) {
        $events[] = $ev;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($student_name) ?> (CSPIT)</title>
    <link rel="stylesheet" href="assets/css/global.css?v=5">
    <script src="assets/js/theme.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Student Dashboard</h1>
        <p>Welcome, <strong><?= htmlspecialchars($student_name) ?></strong> | Enrollment: <mark><?= htmlspecialchars($enrollment_no) ?></mark> | <?= htmlspecialchars($course) ?> (<?= htmlspecialchars($semester) ?>) – CSPIT</p>
    </header>

    <?php include "sidebar.php"; ?>

    <main>
        <section>
            <h2>Semester 3 – Academic Summary</h2>
            
            <table border="1" cellpadding="8" cellspacing="0" width="100%">
                <thead>
                    <tr align="left">
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
                        <td><meter value="94" min="0" max="100">94%</meter></td>
                        <td><a href="attendance.php">View Subjects &rarr;</a></td>
                    </tr>
                    <tr>
                        <td><strong>Assignments</strong></td>
                        <td>Coming Soon for Sem 3</td>
                        <td><progress value="0" max="100">0%</progress></td>
                        <td><a href="assignments.php">Check Updates &rarr;</a></td>
                    </tr>
                    <tr>
                        <td><strong>Overall CGPA</strong></td>
                        <td>8.05 / 10.0</td>
                        <td><meter value="8.05" min="0" max="10">8.05</meter></td>
                        <td><a href="results.php">Grade Cards &rarr;</a></td>
                    </tr>
                    <tr>
                        <td><strong>Enrolled Credits</strong></td>
                        <td>21 Credits (6 Subjects)</td>
                        <td><progress value="21" max="21">100%</progress></td>
                        <td><a href="courses.php">View Syllabus &rarr;</a></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Semester 3 Timetable (Division I – Room 506)</h2>
            <p>Department of Computer Science & Engineering (CSPIT, A7 Building)</p>
            
            <table border="1" cellpadding="6" cellspacing="0" width="100%">
                <thead>
                    <tr align="left">
                        <th>Day / Time</th>
                        <th>09:10 AM - 10:10 AM</th>
                        <th>10:10 AM - 11:10 AM</th>
                        <th>11:10 AM - 12:10 PM</th>
                        <th>12:10 PM - 02:10 PM (After Lunch)</th>
                        <th>02:20 PM - 04:20 PM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Monday</strong></td>
                        <td>FDSA - Room 506 (VMP)</td>
                        <td>FCN - Room 506 (HYY)</td>
                        <td>Lunch Break</td>
                        <td>HS - Room 506 (ADK)</td>
                        <td>Labs: A1-FDSA / B1-FCN / C1-WDF</td>
                    </tr>
                    <tr>
                        <td><strong>Tuesday</strong></td>
                        <td>MATHS - Room 506 (PM)</td>
                        <td>FDSA - Room 506 (ART)</td>
                        <td>Lunch Break</td>
                        <td>Labs: A1-FCN / B1-WDF / C1-FCN</td>
                        <td>FCN (HYY) & WDF (SRG) - Room 506</td>
                    </tr>
                    <tr>
                        <td><strong>Wednesday</strong></td>
                        <td>MATHS - Room 506 (PM)</td>
                        <td>OOP - Room 506 (SSS)</td>
                        <td>Lunch Break</td>
                        <td>Labs: A1-WDF / B1-WDF / C1-FDSA</td>
                        <td>WDF (SRG) & FDSA (VMP) - Room 506</td>
                    </tr>
                    <tr>
                        <td><strong>Thursday</strong></td>
                        <td>WDF - Room 506 (VYP)</td>
                        <td>FCN - Room 506 (HYY)</td>
                        <td>Lunch Break</td>
                        <td>Labs: A1-WDF / B1-FDSA / C1-WDF</td>
                        <td>Labs: A1-OOP / B1-WDF / C1-OOP</td>
                    </tr>
                    <tr>
                        <td><strong>Friday</strong></td>
                        <td>MATHS - Room 506 (PM)</td>
                        <td>FDSA - Room 506 (ART)</td>
                        <td>Lunch Break</td>
                        <td>Counselling Sessions (Rooms 631-633)</td>
                        <td>FCN (HYY) & OOP (SSS) - Room 506</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <aside>
        <h2>Upcoming Event Alert</h2>
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $ev): ?>
                <article style="margin-bottom: 15px;">
                    <h3>🔥 <?= htmlspecialchars($ev['title']) ?></h3>
                    <p><strong>Date:</strong> <time datetime="<?= htmlspecialchars($ev['event_date']) ?>"><?= date("M d, Y", strtotime($ev['event_date'])) ?></time> | Venue: <?= htmlspecialchars($ev['location']) ?></p>
                    <p><?= htmlspecialchars($ev['description'] ?: 'Join campus hackathons and activities.') ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <article>
                <h3>🔥 Odoo Hackathon at LDCE!</h3>
                <p><strong>Date:</strong> <time datetime="2026-08-22">August 22, 2026</time></p>
                <p>Get ready for the exciting Odoo Hackathon taking place at L.D. College of Engineering on 22nd August. Register your CSE team now!</p>
            </article>
        <?php endif; ?>
        <p><a href="notifications.php"><strong>See all notifications &rarr;</strong></a></p>
    </aside>

    <footer>
        <p>&copy; <time datetime="2026">2026</time> StudentHub – CSPIT, CHARUSAT. All rights reserved.</p>
        <address>
            A7 Building, CSPIT | Email: <a href="mailto:support@charusat.edu.in">support@charusat.edu.in</a> | Phone: <a href="tel:+91-2697-265011">+91-2697-265011</a>
        </address>
    </footer>
</div>
    
</body>
</html>
