<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

// 1. RBAC Authentication Check
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to access the Admin Dashboard.");
    exit();
}

// 2. RBAC Authorization Check (Admins Only)
if (($_SESSION["role"] ?? "") !== "admin") {
    // Record unauthorized security block in audit logs
    log_audit(
        $conn,
        $_SESSION["user_id"],
        $_SESSION["username"] ?? "Unknown",
        "ACCESS_DENIED",
        "Unauthorized role=" . ($_SESSION["role"] ?? "guest") . " attempted to access /Practical-14/dashboard.php",
        "SECURITY_BLOCK"
    );
    // Display 403 Forbidden
    include "403.php";
    exit();
}

// Record successful dashboard view in audit log
log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "VIEW_DASHBOARD", "Admin accessed central management dashboard.", "SUCCESS");

$admin_username = $_SESSION["username"];
$admin_email    = $_SESSION["email"] ?? "admin01@gmail.com";
$msg            = $_GET["msg"] ?? "";

// 3. Dynamic Statistics Queries
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

// 4. Dynamic Navigation Menu according to Role (RBAC)
$menu_items = [];
$menu_res = $conn->query("SELECT title, link, icon FROM menu_items WHERE min_role IN ('user', 'editor', 'admin') ORDER BY id ASC");
if ($menu_res) {
    while ($m = $menu_res->fetch_assoc()) {
        $menu_items[] = $m;
    }
}

// 5. Query Latest Registered Users
$latest_users = [];
$u_res = $conn->query("SELECT id, username, email, role, last_login, created_at FROM users ORDER BY id DESC LIMIT 5");
if ($u_res) {
    while ($u = $u_res->fetch_assoc()) {
        $latest_users[] = $u;
    }
}

// 6. Query Live Audit Logs (Recent recorded admin and security events)
$recent_logs = [];
$log_res = $conn->query("SELECT id, username, action, details, ip_address, status, created_at FROM audit_logs ORDER BY id DESC LIMIT 6");
if ($log_res) {
    while ($l = $log_res->fetch_assoc()) {
        $recent_logs[] = $l;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard with RBAC & Audit Logging - StudentHub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #2d3748;
            padding-bottom: 40px;
        }
        .main-wrapper {
            max-width: 1140px;
            margin: 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
            border: 1px solid #e2e8f0;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 18px;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .header-title h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-title p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 13.5px;
        }
        .tag-pill {
            font-size: 11px;
            font-weight: 700;
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .user-cluster {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            transition: transform 0.15s, box-shadow 0.15s;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }
        .stat-users::before { background: #2563eb; }
        .stat-students::before { background: #16a34a; }
        .stat-events::before { background: #ea580c; }
        .stat-audit::before { background: #7c3aed; }

        .stat-num {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-icon {
            font-size: 32px;
            opacity: 0.85;
        }
        .nav-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .nav-pills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-pills-list a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-pills-list a:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            transform: translateY(-1px);
        }
        .nav-pills-list a.active-mod {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #93c5fd;
            font-weight: 700;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .section-header h3 {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .badge-action {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <!-- Header Bar -->
    <div class="header-bar">
        <div class="header-title">
            <h1>
                StudentHub Admin Dashboard
                <span class="tag-pill">RBAC & Audit Logging</span>
            </h1>
            <p>
                Centralized Administration Console | CSPIT - CHARUSAT (Practical 14)
            </p>
        </div>
        <div class="user-cluster">
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase">
                <i class="bi bi-shield-fill-check me-1"></i> ADMIN PORTAL
            </span>
            <span class="small fw-bold text-secondary">
                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($admin_username); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>

    <!-- Feedback Message Banner -->
    <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 py-2 px-3 small" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?php echo htmlspecialchars($msg); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Dynamic Navigation Menu according to Role (RBAC) -->
    <div class="nav-card">
        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary-subtle">
            <div class="small fw-bold text-dark">
                <i class="bi bi-compass-fill me-1 text-primary"></i> Dynamic Navigation (RBAC: Admin Role)
            </div>
            <span class="badge bg-light text-dark border small">
                <?php echo count($menu_items); ?> Modules Available
            </span>
        </div>
        <ul class="nav-pills-list">
            <?php foreach ($menu_items as $item): ?>
                <?php $isActive = ($item['link'] === 'dashboard.php' || $item['link'] === '#'); ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['link']); ?>" class="<?php echo $isActive ? 'active-mod' : ''; ?>">
                        <i class="bi <?php echo htmlspecialchars($item['icon'] ?: 'bi-link'); ?>"></i>
                        <span><?php echo htmlspecialchars($item['title']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Dynamic Statistics Grid (User, Student, Event, Audit Logs counts) -->
    <div class="row g-3 mb-4">
        <!-- Users Count -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-users">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-num text-primary"><?php echo $total_users; ?></div>
                        <small class="text-muted" style="font-size:11px;">Registered Accounts</small>
                    </div>
                    <div class="stat-icon text-primary"><i class="bi bi-person-fill-gear"></i></div>
                </div>
            </div>
        </div>

        <!-- Students Count -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-students">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Enrolled Students</div>
                        <div class="stat-num text-success"><?php echo $total_students; ?></div>
                        <small class="text-muted" style="font-size:11px;">Active Academic Profiles</small>
                    </div>
                    <div class="stat-icon text-success"><i class="bi bi-mortarboard-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- Campus Events Count -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-events">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Campus Events</div>
                        <div class="stat-num text-warning"><?php echo $total_events; ?></div>
                        <small class="text-muted" style="font-size:11px;">Published Events & Hackathons</small>
                    </div>
                    <div class="stat-icon text-warning"><i class="bi bi-calendar2-check-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- Audit Logs Count -->
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-audit">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Audit Logs</div>
                        <div class="stat-num text-purple" style="color:#7c3aed;"><?php echo $total_audits; ?></div>
                        <small class="text-muted" style="font-size:11px;">Tracked Security Actions</small>
                    </div>
                    <div class="stat-icon" style="color:#7c3aed;"><i class="bi bi-shield-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid: Latest Registrations (Left) & Quick Actions (Right) -->
    <div class="row g-4 mb-4">
        <!-- Latest Registered Users -->
        <div class="col-lg-8">
            <div class="card h-100 p-3 bg-white border">
                <div class="section-header">
                    <h3><i class="bi bi-people-fill me-2 text-primary"></i> Latest Registrations</h3>
                    <a href="../Practical-9/users.php" class="btn btn-sm btn-outline-secondary">View All Users</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($latest_users) > 0): ?>
                                <?php foreach ($latest_users as $user): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        </td>
                                        <td class="text-muted"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <span class="badge bg-warning text-dark text-uppercase">ADMIN</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark text-uppercase">STUDENT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary small">
                                            <?php echo htmlspecialchars(substr($user['created_at'], 0, 16)); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- StudentHub Modules Hub -->
        <div class="col-lg-4">
            <div class="card h-100 p-3 bg-white border">
                <div class="section-header">
                    <h3><i class="bi bi-grid-fill me-2 text-secondary"></i> StudentHub Suite</h3>
                </div>
                <div class="list-group list-group-flush small">
                    <a href="../Practical-11/index.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1">
                        <div>
                            <div class="fw-bold text-dark"><i class="bi bi-person-lines-fill me-2 text-success"></i> Student Management</div>
                            <small class="text-muted">CRUD with Search & Filter (Prac 11)</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="../Practical-12/index.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1">
                        <div>
                            <div class="fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-warning"></i> Event Management</div>
                            <small class="text-muted">Poster Upload & Validation (Prac 12)</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="../Practical-13/index.html" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1">
                        <div>
                            <div class="fw-bold text-dark"><i class="bi bi-lightning-charge me-2 text-primary"></i> AJAX / Fetch API CRUD</div>
                            <small class="text-muted">Single-Page App No Reload (Prac 13)</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="../Practical-10/student_dashboard.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1">
                        <div>
                            <div class="fw-bold text-dark"><i class="bi bi-speedometer2 me-2 text-info"></i> Student Dashboard</div>
                            <small class="text-muted">Timetable & Attendance (Prac 10)</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Audit Logs Section (Proof of Administrative and Security Actions) -->
    <div class="card p-3 bg-white border" id="audit-logs">
        <div class="section-header">
            <div>
                <h3><i class="bi bi-journal-text me-2 text-purple" style="color:#7c3aed;"></i> System Audit Logs (Security Tracking)</h3>
                <p class="text-muted small mb-0">Automated administrative audit trail recording logins, queries, updates, and blocked requests</p>
            </div>
            <a href="dashboard.php" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Logs
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">Log ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_logs) > 0): ?>
                        <?php foreach ($recent_logs as $log): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $log['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($log['action'] === 'ADMIN_LOGIN' || $log['action'] === 'USER_LOGIN'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-action">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> <?php echo htmlspecialchars($log['action']); ?>
                                        </span>
                                    <?php elseif ($log['action'] === 'ACCESS_DENIED'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-action">
                                            <i class="bi bi-shield-slash me-1"></i> <?php echo htmlspecialchars($log['action']); ?>
                                        </span>
                                    <?php elseif ($log['action'] === 'VIEW_DASHBOARD'): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle badge-action">
                                            <i class="bi bi-eye me-1"></i> <?php echo htmlspecialchars($log['action']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle badge-action">
                                            <i class="bi bi-check-circle me-1"></i> <?php echo htmlspecialchars($log['action']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small">
                                    <?php echo htmlspecialchars($log['details']); ?>
                                </td>
                                <td>
                                    <code class="small text-dark"><?php echo htmlspecialchars($log['ip_address']); ?></code>
                                </td>
                                <td>
                                    <?php if ($log['status'] === 'SUCCESS'): ?>
                                        <span class="badge bg-success text-white small">SUCCESS</span>
                                    <?php elseif ($log['status'] === 'SECURITY_BLOCK'): ?>
                                        <span class="badge bg-danger text-white small">BLOCKED</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white small"><?php echo htmlspecialchars($log['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?php echo htmlspecialchars($log['created_at']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No audit logs recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
