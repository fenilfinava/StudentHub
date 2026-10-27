<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

// Access Control: Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please sign in to access your student dashboard.");
    exit();
}

$user_id  = $_SESSION["user_id"];
$username = $_SESSION["username"];
$email    = $_SESSION["email"] ?? "";
$role     = $_SESSION["role"] ?? "student";

// Log student dashboard view
log_audit($conn, $user_id, $username, "VIEW_STUDENT_DASHBOARD", "Student accessed dashboard.", "SUCCESS");

// Fetch student profile details from students table
$student_name   = "Fenil Finava";
$enrollment_no  = "25CS011";
$course         = "Computer Science Engineering";
$semester       = 3;
$gender         = "Male";

$st_stmt = $conn->prepare("SELECT enrollment_no, name, course, semester, gender FROM students WHERE email = ? LIMIT 1");
if ($st_stmt) {
    $st_stmt->bind_param("s", $email);
    $st_stmt->execute();
    $res = $st_stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $student_name  = $row["name"];
        $enrollment_no = $row["enrollment_no"] ?: "25CS011";
        $course        = $row["course"];
        $semester      = $row["semester"] ?: 3;
        $gender        = $row["gender"];
    }
    $st_stmt->close();
}

// Fetch upcoming campus events from events table
$events = [];
$e_res = $conn->query("SELECT id, title, event_date, location, category, poster FROM events ORDER BY event_date ASC LIMIT 4");
if ($e_res) {
    while ($ev = $e_res->fetch_assoc()) {
        $events[] = $ev;
    }
}

// Fetch student catalog
$classmates = [];
$c_res = $conn->query("SELECT id, name, email, course, status FROM students WHERE status = 'Active' ORDER BY id ASC LIMIT 6");
if ($c_res) {
    while ($cl = $c_res->fetch_assoc()) {
        $classmates[] = $cl;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?php echo htmlspecialchars($student_name); ?> | StudentHub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #2d3748;
            padding-bottom: 50px;
        }
        .main-container {
            max-width: 1140px;
            margin: 30px auto;
            background: #ffffff;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
            border: 1px solid #e2e8f0;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 18px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .brand-badge {
            background: #2563eb;
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .user-cluster {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            padding: 6px 14px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 24px;
        }
        .card-custom h3 {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-card-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            background: #f8fafc;
            height: 100%;
        }
        .event-poster-thumb {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #e2e8f0;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Top Header -->
    <div class="header-top">
        <div>
            <h1 class="h3 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <span>Student Dashboard</span>
                <span class="brand-badge">StudentHub Portal</span>
            </h1>
            <p class="text-muted small m-0 mt-1">
                Welcome, <strong><?php echo htmlspecialchars($student_name); ?></strong> | Enrolment: <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($enrollment_no); ?></span> | B.Tech CSE (Sem <?php echo htmlspecialchars($semester); ?>) - CSPIT CHARUSAT
            </p>
        </div>
        <div class="user-cluster">
            <span class="badge bg-primary text-uppercase px-3 py-2 fw-semibold">
                <i class="bi bi-person-fill me-1"></i> STUDENT
            </span>
            <span class="small text-secondary fw-semibold">
                <?php echo htmlspecialchars($email); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>

    <!-- Academic Summary with HTML5 Meters & Progress Bars -->
    <div class="card-custom">
        <h3><i class="bi bi-bar-chart-line-fill text-primary"></i> Semester <?php echo htmlspecialchars($semester); ?> Academic Progress Summary</h3>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th>Metric</th>
                        <th>Current Score</th>
                        <th>Visual Progress Indicator</th>
                        <th>Academic Eligibility Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Overall Attendance</strong></td>
                        <td><span class="fw-bold text-success">94.0%</span> (Excellent)</td>
                        <td><meter value="94" min="0" max="100" style="width:180px; height:16px;">94%</meter></td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">Eligible for University Exams</span></td>
                    </tr>
                    <tr>
                        <td><strong>Practical Assignments</strong></td>
                        <td><span class="fw-bold text-primary">15 / 15 Practicals</span></td>
                        <td><progress value="100" max="100" style="width:180px; height:16px;">100%</progress></td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">All Submissions Complete</span></td>
                    </tr>
                    <tr>
                        <td><strong>Cumulative CGPA</strong></td>
                        <td><span class="fw-bold text-dark">8.05 / 10.0</span></td>
                        <td><meter value="8.05" min="0" max="10" style="width:180px; height:16px;">8.05</meter></td>
                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">First Class with Distinction</span></td>
                    </tr>
                    <tr>
                        <td><strong>Enrolled Course Credits</strong></td>
                        <td><span class="fw-bold text-dark">21 Credits (6 Subjects)</span></td>
                        <td><progress value="21" max="21" style="width:180px; height:16px;">100%</progress></td>
                        <td><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Active Enrolled Status</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Division I Weekly Class Timetable -->
    <div class="card-custom">
        <h3><i class="bi bi-calendar3 text-success"></i> Division I Weekly Class Timetable (Room 506 - CSPIT A7 Building)</h3>
        <p class="text-muted small" style="margin-top:-8px;">Department of Computer Science & Engineering | Academic Year 2026-27</p>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle mb-0" style="font-size:13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width:110px;">Day / Time</th>
                        <th>09:10 - 10:10 AM</th>
                        <th>10:10 - 11:10 AM</th>
                        <th>11:10 - 12:10 PM</th>
                        <th style="background:#fef3c7; color:#92400e;">12:10 - 02:10 PM</th>
                        <th>02:20 - 04:20 PM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold table-light">Monday</td>
                        <td>FDSA (Room 506)</td>
                        <td>FCN (Room 506)</td>
                        <td>HS (Room 506)</td>
                        <td rowspan="5" class="bg-warning-subtle text-warning-emphasis fw-bold">Lunch Break & Mentoring</td>
                        <td>Labs: A1-FDSA / B1-FCN / C1-WDF</td>
                    </tr>
                    <tr>
                        <td class="fw-bold table-light">Tuesday</td>
                        <td>MATHS (Room 506)</td>
                        <td>FDSA (Room 506)</td>
                        <td>WDF (Room 506)</td>
                        <td>Labs: A1-FCN / B1-WDF</td>
                    </tr>
                    <tr>
                        <td class="fw-bold table-light">Wednesday</td>
                        <td>MATHS (Room 506)</td>
                        <td>OOP (Room 506)</td>
                        <td>FCN (Room 506)</td>
                        <td>Labs: A1-WDF / B1-OOP</td>
                    </tr>
                    <tr>
                        <td class="fw-bold table-light">Thursday</td>
                        <td>WDF (Room 506)</td>
                        <td>FCN (Room 506)</td>
                        <td>FDSA (Room 506)</td>
                        <td>Labs: A1-OOP / B1-FDSA</td>
                    </tr>
                    <tr>
                        <td class="fw-bold table-light">Friday</td>
                        <td>MATHS (Room 506)</td>
                        <td>FDSA (Room 506)</td>
                        <td>Counseling</td>
                        <td>FCN & OOP Review</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Campus Events Feed -->
    <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="m-0"><i class="bi bi-calendar-event text-warning"></i> Campus Events & Hackathons</h3>
            <span class="badge bg-light text-secondary border">Live Events Feed</span>
        </div>
        <div class="row g-3">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $ev): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="event-card-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary-subtle text-primary small"><?php echo htmlspecialchars($ev['category']); ?></span>
                                <small class="text-muted"><i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($ev['event_date']); ?></small>
                            </div>
                            <h6 class="fw-bold text-dark mt-2 mb-1"><?php echo htmlspecialchars($ev['title']); ?></h6>
                            <p class="text-secondary small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($ev['location']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Classmates / Student Directory -->
    <div class="card-custom mb-0">
        <h3><i class="bi bi-people-fill text-info"></i> Department Enrolled Students Directory</h3>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13.5px;">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Student Full Name</th>
                        <th>Email Address</th>
                        <th>Enrolled Course</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($classmates) > 0): ?>
                        <?php foreach ($classmates as $cm): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $cm['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($cm['name']); ?></strong></td>
                                <td class="text-muted"><?php echo htmlspecialchars($cm['email']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($cm['course']); ?></span></td>
                                <td><span class="badge bg-success-subtle text-success">Active</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="text-center text-muted small mt-4 mb-2">
    StudentHub Portal &copy; 2026 | Chandubhai S. Patel Institute of Technology (CSPIT) - CHARUSAT | Fenil Finava (25CS011)
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
