<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
http_response_code(403);

$username = $_SESSION["username"] ?? "Guest";
$role     = $_SESSION["role"] ?? "unauthenticated";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Access Restricted | StudentHub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            max-width: 520px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }
        .error-icon {
            font-size: 64px;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 16px;
        }
        .status-pill {
            background: #fee2e2;
            color: #991b1b;
            font-weight: 700;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .user-info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 20px 0;
            font-size: 13.5px;
            color: #475569;
        }
    </style>
</head>
<body>

<div class="error-card">
    <div class="error-icon">
        <i class="bi bi-shield-slash"></i>
    </div>
    <span class="status-pill">HTTP 403 Forbidden</span>
    <h2 class="fw-bold text-dark mb-2">Access Denied: Admins Only</h2>
    <p class="text-muted" style="font-size: 14.5px;">
        You do not have administrative privileges to access the Administrator Management Console.
    </p>

    <div class="user-info-box">
        <div>Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></div>
        <div class="mt-1">
            Current Role: 
            <span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($role); ?></span>
            <span class="text-danger ms-2 fw-semibold">(Unauthorized for Admin Areas)</span>
        </div>
        <div class="small text-muted mt-2">
            Security Notice: This unauthorized access attempt has been logged in the <code>audit_logs</code> database.
        </div>
    </div>

    <div class="d-flex justify-content-center gap-2 mt-4">
        <a href="student_dashboard.php" class="btn btn-primary px-3">
            <i class="bi bi-speedometer2 me-1"></i> Student Dashboard
        </a>
        <a href="logout.php" class="btn btn-outline-danger px-3">
            <i class="bi bi-box-arrow-right me-1"></i> Logout & Switch Account
        </a>
    </div>
</div>

</body>
</html>
