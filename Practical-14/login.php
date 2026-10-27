<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

// If already logged in as Admin, redirect to dashboard
if (isset($_SESSION["user_id"]) && ($_SESSION["role"] ?? "") === "admin") {
    header("Location: dashboard.php");
    exit();
}

$error   = $_GET["error"] ?? "";
$message = $_GET["message"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"]  = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"]    = $user["email"];
                $_SESSION["role"]     = $user["role"];

                // Update last_login in users table
                $up_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $up_stmt->bind_param("i", $user["id"]);
                $up_stmt->execute();
                $up_stmt->close();

                // Record Audit Log
                log_audit($conn, $user["id"], $user["username"], "USER_LOGIN", "User logged in with role=" . $user["role"], "SUCCESS");

                if ($user["role"] === "admin") {
                    header("Location: dashboard.php?msg=Welcome Administrator! Dashboard loaded successfully.");
                    exit();
                } else {
                    // Non-admin attempting to access dashboard
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $error = "Invalid email or password.";
                log_audit($conn, null, $email, "LOGIN_FAILED", "Invalid password attempt for email: $email", "FAILED");
            }
        } else {
            $error = "Invalid email or password.";
            log_audit($conn, null, $email, "LOGIN_FAILED", "Account not found for email: $email", "FAILED");
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Portal - StudentHub (Practical 14)</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            max-width: 440px;
            width: 100%;
            padding: 36px 32px;
        }
        .brand-badge {
            background: #2563eb;
            color: #fff;
            font-weight: 800;
            font-size: 15px;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <span class="brand-badge">StudentHub</span>
        <h3 class="fw-bold text-dark mb-1">Administrative Login</h3>
        <p class="text-muted small">Central Dashboard with RBAC & Audit Logging (Practical 14)</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div><?php echo htmlspecialchars($message); ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-2">
            Sign In to Dashboard &rarr;
        </button>
    </form>
</div>

</body>
</html>
