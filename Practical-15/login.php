<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

$error   = $_GET["error"] ?? "";
$message = $_GET["message"] ?? "";
$already_logged_user = $_SESSION["username"] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login_input = trim($_POST["login_input"] ?? ($_POST["email"] ?? ($_POST["username"] ?? "")));
    $password    = $_POST["password"] ?? "";

    if ($login_input === "" || $password === "") {
        $error = "Please enter both username/email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user["password"])) {
                // Wipe prior session & regenerate ID
                session_unset();
                session_regenerate_id(true);
                $_SESSION["user_id"]  = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"]    = $user["email"];
                $_SESSION["role"]     = $user["role"];

                // Update last_login
                $up = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $up->bind_param("i", $user["id"]);
                $up->execute();
                $up->close();

                // Audit log
                log_audit($conn, $user["id"], $user["username"], "USER_LOGIN", "User logged in with role=" . $user["role"], "SUCCESS");

                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php?success=Welcome Administrator! Master console loaded successfully.");
                    exit();
                } else {
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $error = "Invalid credentials. Please verify your password.";
                log_audit($conn, null, $login_input, "LOGIN_FAILED", "Invalid password attempt for: $login_input", "FAILED");
            }
        } else {
            $error = "Account not found for: " . htmlspecialchars($login_input);
            log_audit($conn, null, $login_input, "LOGIN_FAILED", "Non-existent account attempted: $login_input", "FAILED");
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
    <title>StudentHub Portal - Unified Login</title>
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
            padding: 38px 32px;
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
        <h3 class="fw-bold text-dark mb-1">Unified Portal Login</h3>
        <p class="text-muted small">CSPIT Campus Portal | Academic Year 2026-27 (Practical 15)</p>
    </div>

    <?php if ($already_logged_user): ?>
        <div class="alert alert-info py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="bi bi-info-circle-fill me-1"></i> Active: <strong><?= htmlspecialchars($already_logged_user) ?></strong>
            </div>
            <div>
                <a href="dashboard.php" class="text-decoration-none fw-bold me-2">Dashboard &rarr;</a>
                <a href="logout.php" class="text-danger text-decoration-none fw-bold">Logout</a>
            </div>
        </div>
    <?php endif; ?>

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
            <label for="login_input" class="form-label small fw-semibold text-secondary">Username or Email Address</label>
            <input type="text" class="form-control" id="login_input" name="login_input" placeholder="e.g. charusat123@gmail.com" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-2">
            Sign In to StudentHub &rarr;
        </button>

        <div class="text-center mt-3 small text-muted">
            New student? <a href="register.php" class="text-primary fw-semibold text-decoration-none">Create an account</a>
        </div>
        <div class="text-center mt-2 small">
            <a href="index.php" class="text-secondary text-decoration-none">&larr; Back to Home</a>
        </div>
    </form>
</div>

</body>
</html>
