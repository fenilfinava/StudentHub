<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

// 1. RBAC Authentication Check
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please sign in with administrator credentials.");
    exit();
}

// 2. RBAC Authorization Check (Admins Only)
if (($_SESSION["role"] ?? "") !== "admin") {
    log_audit(
        $conn,
        $_SESSION["user_id"],
        $_SESSION["username"] ?? "Unknown",
        "ACCESS_DENIED",
        "Unauthorized role='" . ($_SESSION["role"] ?? "guest") . "' attempted to access admin_dashboard.php",
        "SECURITY_BLOCK"
    );
    include "403.php";
    exit();
}

// 3. Handle CSV Export for Students
if (isset($_GET["export"]) && $_GET["export"] === "csv") {
    log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "EXPORT_CSV", "Admin exported student records to CSV.", "SUCCESS");
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=StudentHub_Students_" . date("Ymd_His") . ".csv");
    $output = fopen("php://output", "w");
    fputcsv($output, ["ID", "Enrollment No", "Name", "Email", "Course", "Semester", "Gender", "Status", "Created At"]);
    $csv_res = $conn->query("SELECT id, enrollment_no, name, email, course, semester, gender, status, created_at FROM students ORDER BY id ASC");
    if ($csv_res) {
        while ($row = $csv_res->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit();
}

$success_msg = $_GET["success"] ?? "";
$error_msg   = $_GET["error"] ?? "";
$current_tab = $_GET["tab"] ?? "overview";

// 4. Handle POST Actions (Add Student, Add Event)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add_student") {
        $enrollment = trim($_POST["enrollment_no"] ?? "");
        $name       = trim($_POST["name"] ?? "");
        $email      = trim($_POST["email"] ?? "");
        $course     = trim($_POST["course"] ?? "");
        $semester   = (int)($_POST["semester"] ?? 1);
        $gender     = trim($_POST["gender"] ?? "Male");

        if (empty($name) || empty($email) || empty($course)) {
            header("Location: admin_dashboard.php?tab=students&error=Name, Email, and Course are required fields.");
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO students (enrollment_no, name, email, course, semester, gender, status) VALUES (?, ?, ?, ?, ?, ?, 'Active')");
            if ($stmt) {
                $stmt->bind_param("ssssis", $enrollment, $name, $email, $course, $semester, $gender);
                if ($stmt->execute()) {
                    log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "CREATE_STUDENT", "Added student: $name ($email)", "SUCCESS");
                    header("Location: admin_dashboard.php?tab=students&success=Student successfully registered.");
                    exit();
                } else {
                    header("Location: admin_dashboard.php?tab=students&error=" . urlencode($stmt->error));
                    exit();
                }
            }
        }
    }

    if ($action === "add_event") {
        $title       = trim($_POST["title"] ?? "");
        $category    = trim($_POST["category"] ?? "Academic");
        $event_date  = trim($_POST["event_date"] ?? date("Y-m-d"));
        $location    = trim($_POST["location"] ?? "Campus Auditorium");
        $description = trim($_POST["description"] ?? "");
        $poster_path = null;

        // Poster upload handling
        if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES["poster"]["tmp_name"];
            $file_name = basename($_FILES["poster"]["name"]);
            $file_size = $_FILES["poster"]["size"];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ["jpg", "jpeg", "png", "webp", "gif"];

            if (!in_array($file_ext, $allowed)) {
                header("Location: admin_dashboard.php?tab=events&error=Invalid image format. Allowed: JPG, PNG, WEBP.");
                exit();
            } elseif ($file_size > 2 * 1024 * 1024) {
                header("Location: admin_dashboard.php?tab=events&error=File exceeds 2MB limit.");
                exit();
            } else {
                $upload_dir = __DIR__ . "/uploads/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_filename = "event_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $file_ext;
                $destination  = $upload_dir . $new_filename;
                if (move_uploaded_file($file_tmp, $destination)) {
                    $poster_path = "uploads/" . $new_filename;
                }
            }
        }

        if (empty($title)) {
            header("Location: admin_dashboard.php?tab=events&error=Event title is required.");
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO events (title, category, event_date, location, description, poster) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssss", $title, $category, $event_date, $location, $description, $poster_path);
                if ($stmt->execute()) {
                    log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "CREATE_EVENT", "Created campus event: $title", "SUCCESS");
                    header("Location: admin_dashboard.php?tab=events&success=Campus event published successfully.");
                    exit();
                } else {
                    header("Location: admin_dashboard.php?tab=events&error=" . urlencode($stmt->error));
                    exit();
                }
            }
        }
    }
}

// 5. Handle GET Actions (Delete Student, Delete Event, Delete User)
if (isset($_GET["action"])) {
    $get_action = $_GET["action"];
    $target_id  = (int)($_GET["id"] ?? 0);

    if ($get_action === "delete_student" && $target_id > 0) {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $target_id);
            $stmt->execute();
            log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "DELETE_STUDENT", "Deleted student ID: $target_id", "SUCCESS");
            header("Location: admin_dashboard.php?tab=students&success=Student record deleted.");
            exit();
        }
    }

    if ($get_action === "delete_event" && $target_id > 0) {
        // Fetch poster file to unlink
        $p_stmt = $conn->prepare("SELECT poster FROM events WHERE id = ?");
        if ($p_stmt) {
            $p_stmt->bind_param("i", $target_id);
            $p_stmt->execute();
            $res = $p_stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                if (!empty($row["poster"]) && file_exists(__DIR__ . "/" . $row["poster"])) {
                    @unlink(__DIR__ . "/" . $row["poster"]);
                }
            }
            $p_stmt->close();
        }

        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $target_id);
            $stmt->execute();
            log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "DELETE_EVENT", "Deleted event ID: $target_id", "SUCCESS");
            header("Location: admin_dashboard.php?tab=events&success=Event deleted successfully.");
            exit();
        }
    }

    if ($get_action === "delete_user" && $target_id > 0) {
        if ($target_id === (int)$_SESSION["user_id"]) {
            header("Location: admin_dashboard.php?tab=users&error=You cannot delete your own active administrator account.");
            exit();
        }
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $target_id);
            $stmt->execute();
            log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "DELETE_USER", "Deleted user ID: $target_id", "SUCCESS");
            header("Location: admin_dashboard.php?tab=users&success=User account deleted.");
            exit();
        }
    }
}

// 6. Dynamic Metrics Counters
$total_users = 0;
$res = $conn->query("SELECT COUNT(*) as cnt FROM users");
if ($res) { $total_users = (int)$res->fetch_assoc()["cnt"]; }

$total_students = 0;
$res = $conn->query("SELECT COUNT(*) as cnt FROM students");
if ($res) { $total_students = (int)$res->fetch_assoc()["cnt"]; }

$total_events = 0;
$res = $conn->query("SELECT COUNT(*) as cnt FROM events");
if ($res) { $total_events = (int)$res->fetch_assoc()["cnt"]; }

$total_audits = 0;
$res = $conn->query("SELECT COUNT(*) as cnt FROM audit_logs");
if ($res) { $total_audits = (int)$res->fetch_assoc()["cnt"]; }

// 7. Dynamic Menu Items
$menu_items = [];
$m_res = $conn->query("SELECT title, link, icon FROM menu_items WHERE min_role IN ('user', 'editor', 'admin') ORDER BY id ASC");
if ($m_res) {
    while ($m = $m_res->fetch_assoc()) {
        $menu_items[] = $m;
    }
}

// 8. Filter / Search for Students
$search_term = trim($_GET["search"] ?? "");
$filter_course = trim($_GET["course_filter"] ?? "");

$sql_students = "SELECT * FROM students WHERE 1=1";
$params = [];
$types  = "";

if (!empty($search_term)) {
    $sql_students .= " AND (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ?)";
    $st = "%" . $search_term . "%";
    $params[] = $st; $params[] = $st; $params[] = $st;
    $types .= "sss";
}
if (!empty($filter_course)) {
    $sql_students .= " AND course = ?";
    $params[] = $filter_course;
    $types .= "s";
}
$sql_students .= " ORDER BY id DESC";

$students_list = [];
if (!empty($params)) {
    $st_stmt = $conn->prepare($sql_students);
    $st_stmt->bind_param($types, ...$params);
    $st_stmt->execute();
    $s_res = $st_stmt->get_result();
    while ($r = $s_res->fetch_assoc()) { $students_list[] = $r; }
} else {
    $s_res = $conn->query($sql_students);
    if ($s_res) {
        while ($r = $s_res->fetch_assoc()) { $students_list[] = $r; }
    }
}

// 9. Events List
$events_list = [];
$ev_res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
if ($ev_res) {
    while ($ev = $ev_res->fetch_assoc()) { $events_list[] = $ev; }
}

// 10. Users List
$users_list = [];
$u_res = $conn->query("SELECT id, username, email, role, last_login, created_at FROM users ORDER BY id DESC");
if ($u_res) {
    while ($u = $u_res->fetch_assoc()) { $users_list[] = $u; }
}

// 11. Recent Audit Logs
$audit_list = [];
$a_res = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 50");
if ($a_res) {
    while ($a = $a_res->fetch_assoc()) { $audit_list[] = $a; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management Console - StudentHub Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #172554;
            --primary-light: #3b82f6;
            --secondary: #0f766e;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        body {
            background-color: var(--background);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text-main);
            padding-bottom: 50px;
        }
        .main-container {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 16px;
        }
        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            margin-bottom: 24px;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: transform 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .nav-tabs .nav-link {
            font-weight: 600;
            color: var(--text-muted);
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: transparent;
        }
        .tab-content {
            background: #ffffff;
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        }
        .badge-role-admin {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 700;
        }
        .badge-role-student {
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: 700;
        }
        .badge-role-editor {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 700;
        }
        .table > :not(caption) > * > * {
            vertical-align: middle;
            padding: 12px 16px;
        }
        .event-poster-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Top Navigation Bar -->
    <div class="navbar-custom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 26px;">🎓</span>
                <span class="fs-4 fw-bold text-dark">StudentHub <span class="badge bg-primary text-uppercase" style="font-size: 11px;">Admin Console</span></span>
            </div>
            <p class="text-muted small mb-0">CSPIT - CHARUSAT | Unified Administration & Resource Management (Practical 15)</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="student_dashboard.php" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-person-workspace"></i> Student View
            </a>
            <div class="d-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill border">
                <i class="bi bi-shield-check text-primary"></i>
                <span class="small fw-semibold"><?= htmlspecialchars($_SESSION["username"]) ?></span>
                <span class="badge bg-danger" style="font-size: 10px;">ADMIN</span>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div><?= htmlspecialchars($success_msg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= htmlspecialchars($error_msg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Dynamic Metric Stat Cards (Practicals 11 & 14) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div>
                    <span class="text-muted small fw-bold">TOTAL USERS</span>
                    <h2 class="fw-bold mb-0 text-primary"><?= $total_users ?></h2>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div>
                    <span class="text-muted small fw-bold">STUDENT RECORDS</span>
                    <h2 class="fw-bold mb-0 text-success"><?= $total_students ?></h2>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div>
                    <span class="text-muted small fw-bold">CAMPUS EVENTS</span>
                    <h2 class="fw-bold mb-0 text-warning"><?= $total_events ?></h2>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div>
                    <span class="text-muted small fw-bold">AUDIT LOGS</span>
                    <h2 class="fw-bold mb-0 text-danger"><?= $total_audits ?></h2>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-shield-shaded"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic RBAC Navigation Bar (Practical 14) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="small fw-bold text-muted text-uppercase"><i class="bi bi-compass me-1"></i> RBAC Dynamic Directory:</span>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($menu_items as $item): ?>
                        <a href="<?= htmlspecialchars($item['link']) ?>" class="btn btn-sm btn-light border d-flex align-items-center gap-1 text-secondary" style="font-size: 12.5px;">
                            <i class="<?= htmlspecialchars($item['icon']) ?>"></i> <?= htmlspecialchars($item['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Management Workspaces -->
    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link <?= ($current_tab === 'overview') ? 'active' : '' ?>" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button"><i class="bi bi-grid-1x2 me-1"></i> Overview</button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= ($current_tab === 'students') ? 'active' : '' ?>" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button"><i class="bi bi-mortarboard me-1"></i> Student Records (CRUD)</button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= ($current_tab === 'events') ? 'active' : '' ?>" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button"><i class="bi bi-calendar-check me-1"></i> Campus Events</button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= ($current_tab === 'users') ? 'active' : '' ?>" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button"><i class="bi bi-person-gear me-1"></i> User Accounts</button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= ($current_tab === 'audits') ? 'active' : '' ?>" id="audits-tab" data-bs-toggle="tab" data-bs-target="#audits" type="button"><i class="bi bi-shield-check me-1"></i> Security Audit Trail</button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">
        <!-- 1. OVERVIEW TAB -->
        <div class="tab-pane fade <?= ($current_tab === 'overview') ? 'show active' : '' ?>" id="overview" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-7">
                    <h5 class="fw-bold mb-3"><i class="bi bi-activity text-primary me-2"></i>Recent System Activity</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($audit_list, 0, 6) as $a): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($a['username']) ?></td>
                                        <td><code><?= htmlspecialchars($a['action']) ?></code></td>
                                        <td>
                                            <span class="badge bg-<?= ($a['status'] === 'SUCCESS') ? 'success' : 'danger' ?>-subtle text-<?= ($a['status'] === 'SUCCESS') ? 'success' : 'danger' ?>">
                                                <?= htmlspecialchars($a['status']) ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars(substr($a['created_at'], 11, 8)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Platform Specifications</h5>
                    <div class="card border bg-light">
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-2"><strong>Architecture:</strong> Monolithic PHP & MySQL (PDO/MySQLi Dual Driver)</li>
                                <li class="mb-2"><strong>Authentication:</strong> Bcrypt (Cost 10) + Session Fingerprinting</li>
                                <li class="mb-2"><strong>RBAC Enforcement:</strong> 403 Barrier with Audit Trail</li>
                                <li class="mb-2"><strong>Export Formats:</strong> CSV Stream Generator (`fputcsv`)</li>
                                <li class="mb-2"><strong>Database:</strong> <code>studenthub_db</code> (UTF8mb4)</li>
                                <li><strong>Environment:</strong> Local / Cloud Ready (Docker/Render/Heroku/cPanel)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. STUDENTS CRUD TAB (Practicals 11 & 13) -->
        <div class="tab-pane fade <?= ($current_tab === 'students') ? 'show active' : '' ?>" id="students" role="tabpanel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Student Information Records</h5>
                    <p class="text-muted small mb-0">Manage enrolled student profiles, filter by department, and export to CSV.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="admin_dashboard.php?export=csv" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                    </a>
                    <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-person-plus-fill"></i> Add Student
                    </button>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <form method="GET" action="admin_dashboard.php" class="row g-2 mb-3">
                <input type="hidden" name="tab" value="students">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, or enrollment..." value="<?= htmlspecialchars($search_term) ?>">
                </div>
                <div class="col-md-4">
                    <select name="course_filter" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        <option value="Computer Science Engineering" <?= ($filter_course === 'Computer Science Engineering') ? 'selected' : '' ?>>Computer Science Engineering</option>
                        <option value="Information Technology" <?= ($filter_course === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                        <option value="Data Science" <?= ($filter_course === 'Data Science') ? 'selected' : '' ?>>Data Science</option>
                        <option value="Web Development" <?= ($filter_course === 'Web Development') ? 'selected' : '' ?>>Web Development</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm flex-grow-1"><i class="bi bi-search"></i> Filter</button>
                    <a href="admin_dashboard.php?tab=students" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Enrollment</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Sem</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students_list)): ?>
                            <tr><td colspan="9" class="text-center py-4 text-muted">No student records found matching criteria.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students_list as $st): ?>
                                <tr>
                                    <td><span class="text-muted fw-bold">#<?= $st['id'] ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($st['enrollment_no'] ?: '25CS011') ?></span></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($st['name']) ?></td>
                                    <td class="small"><?= htmlspecialchars($st['email']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($st['course']) ?></span></td>
                                    <td><?= htmlspecialchars($st['semester']) ?></td>
                                    <td><?= htmlspecialchars($st['gender']) ?></td>
                                    <td><span class="badge bg-success-subtle text-success"><?= htmlspecialchars($st['status']) ?></span></td>
                                    <td class="text-center">
                                        <a href="admin_dashboard.php?tab=students&action=delete_student&id=<?= $st['id'] ?>" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="return confirm('Delete student record for <?= htmlspecialchars($st['name']) ?>?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. CAMPUS EVENTS TAB (Practical 12) -->
        <div class="tab-pane fade <?= ($current_tab === 'events') ? 'show active' : '' ?>" id="events" role="tabpanel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Campus Events Management</h5>
                    <p class="text-muted small mb-0">Publish technical symposia, hackathons, and cultural campus events with poster uploads.</p>
                </div>
                <button class="btn btn-warning btn-sm d-flex align-items-center gap-1 text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-calendar-plus-fill"></i> Add New Event
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Poster</th>
                            <th>Event Title</th>
                            <th>Category</th>
                            <th>Event Date</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events_list)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No campus events published yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($events_list as $ev): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($ev['poster']) && file_exists(__DIR__ . "/" . $ev['poster'])): ?>
                                            <img src="<?= htmlspecialchars($ev['poster']) ?>" alt="Poster" class="event-poster-thumb">
                                        <?php else: ?>
                                            <div class="event-poster-thumb bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($ev['title']) ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($ev['category']) ?></span></td>
                                    <td class="small fw-semibold"><?= htmlspecialchars($ev['event_date']) ?></td>
                                    <td class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($ev['location']) ?></td>
                                    <td class="small text-muted" style="max-width: 250px;"><?= htmlspecialchars(substr($ev['description'] ?? '', 0, 80)) ?>...</td>
                                    <td class="text-center">
                                        <a href="admin_dashboard.php?tab=events&action=delete_event&id=<?= $ev['id'] ?>" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="return confirm('Delete event <?= htmlspecialchars($ev['title']) ?> and its poster image?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. USERS TAB (Practicals 9 & 10) -->
        <div class="tab-pane fade <?= ($current_tab === 'users') ? 'show active' : '' ?>" id="users" role="tabpanel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h5 class="fw-bold mb-1">User Accounts & Role Management</h5>
                    <p class="text-muted small mb-0">RBAC accounts with encrypted Bcrypt credentials and authentication tracking.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Assigned Role</th>
                            <th>Last Login</th>
                            <th>Registered On</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users_list as $u): ?>
                            <tr>
                                <td><span class="text-muted fw-bold">#<?= $u['id'] ?></span></td>
                                <td class="fw-semibold">
                                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                                    <?= htmlspecialchars($u['username']) ?>
                                </td>
                                <td class="small"><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge badge-role-admin px-2 py-1"><i class="bi bi-shield-fill me-1"></i>Admin</span>
                                    <?php elseif ($u['role'] === 'editor'): ?>
                                        <span class="badge badge-role-editor px-2 py-1"><i class="bi bi-pen-fill me-1"></i>Editor</span>
                                    <?php else: ?>
                                        <span class="badge badge-role-student px-2 py-1"><i class="bi bi-person-fill me-1"></i>Student</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($u['last_login'] ?? 'Never') ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($u['created_at']) ?></td>
                                <td class="text-center">
                                    <?php if ($u['id'] != $_SESSION["user_id"]): ?>
                                        <a href="admin_dashboard.php?tab=users&action=delete_user&id=<?= $u['id'] ?>" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="return confirm('Delete user <?= htmlspecialchars($u['username']) ?>?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Current Session</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. AUDIT LOGS TAB (Practical 14) -->
        <div class="tab-pane fade <?= ($current_tab === 'audits') ? 'show active' : '' ?>" id="audits" role="tabpanel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Live Security & Operational Audit Trail</h5>
                    <p class="text-muted small mb-0">Tamper-evident system logs recording logins, CRUD events, exports, and 403 blocks.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Timestamp</th>
                            <th>Username</th>
                            <th>Action Type</th>
                            <th>Details</th>
                            <th>Client IP</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($audit_list as $log): ?>
                            <tr>
                                <td><span class="text-muted fw-bold">#<?= $log['id'] ?></span></td>
                                <td class="small text-muted"><?= htmlspecialchars($log['created_at']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($log['username']) ?></td>
                                <td>
                                    <?php
                                    $act = $log['action'];
                                    $badge_class = "bg-primary-subtle text-primary";
                                    if (strpos($act, "LOGIN") !== false) $badge_class = "bg-info-subtle text-info";
                                    if (strpos($act, "DELETE") !== false) $badge_class = "bg-danger-subtle text-danger";
                                    if (strpos($act, "CREATE") !== false) $badge_class = "bg-success-subtle text-success";
                                    if (strpos($act, "DENIED") !== false) $badge_class = "bg-danger text-white";
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><code><?= htmlspecialchars($act) ?></code></span>
                                </td>
                                <td class="small"><?= htmlspecialchars($log['details']) ?></td>
                                <td class="small text-muted"><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                <td>
                                    <?php if ($log['status'] === 'SUCCESS'): ?>
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check me-1"></i>SUCCESS</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x me-1"></i><?= htmlspecialchars($log['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Student -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="admin_dashboard.php?tab=students" class="modal-content">
            <input type="hidden" name="action" value="add_student">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>Register New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Enrollment Number</label>
                    <input type="text" name="enrollment_no" class="form-control" placeholder="e.g., 25CS011" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., Fenil Finava" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g., fenil@charusat.edu.in" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="form-label small fw-bold">Department / Course</label>
                        <select name="course" class="form-select" required>
                            <option value="Computer Science Engineering">Computer Science Engineering</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Web Development">Web Development</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold">Semester</label>
                        <input type="number" name="semester" class="form-control" value="3" min="1" max="8" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Gender</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gMale" value="Male" checked>
                            <label class="form-check-label" for="gMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gFemale" value="Female">
                            <label class="form-check-label" for="gFemale">Female</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Save Student Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Campus Event -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="admin_dashboard.php?tab=events" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="action" value="add_event">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus text-warning me-2"></i>Publish Campus Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Event Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., National Hackathon 2026" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="Technical">Technical</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Sports">Sports</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Event Date</label>
                        <input type="date" name="event_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Venue / Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g., CSPIT Auditorium, Room 506" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Event Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief event description..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Event Poster (Optional, Max 2MB)</label>
                    <input type="file" name="poster" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold">Publish Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
