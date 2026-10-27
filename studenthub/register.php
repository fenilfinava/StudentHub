<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username      = trim($_POST["username"] ?? "");
    $email         = trim($_POST["email"] ?? "");
    $password      = $_POST["password"] ?? "";
    $enrollment_no = trim($_POST["enrollment_no"] ?? "");
    $full_name     = trim($_POST["full_name"] ?? "");
    $course        = trim($_POST["course"] ?? "Computer Science");
    $gender        = trim($_POST["gender"] ?? "Male");

    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if username or email already exists in users table
        $chk = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $chk->bind_param("ss", $username, $email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = "Username or email is already registered.";
            $chk->close();
        } else {
            $chk->close();
            $hash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {
                $new_user_id = $conn->insert_id;
                $stmt->close();

                // Also insert or update student profile in students table
                $st_stmt = $conn->prepare("INSERT INTO students (enrollment_no, name, email, course, gender, status) VALUES (?, ?, ?, ?, ?, 'Active') ON DUPLICATE KEY UPDATE name = VALUES(name), course = VALUES(course)");
                if ($st_stmt) {
                    $st_stmt->bind_param("sssss", $enrollment_no, $full_name, $email, $course, $gender);
                    $st_stmt->execute();
                    $st_stmt->close();
                }

                // Log registration in audit_logs
                log_audit($conn, $new_user_id, $username, "USER_REGISTER", "New student account registered: $full_name ($email)", "SUCCESS");

                header("Location: login.php?message=Account created successfully! Please sign in.");
                exit();
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - StudentHub Portal</title>
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
            padding: 30px 20px;
        }
        .reg-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            max-width: 540px;
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
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="reg-card">
    <div class="text-center mb-4">
        <span class="brand-badge">StudentHub</span>
        <h3 class="fw-bold text-dark mb-1">Create Student Account</h3>
        <p class="text-muted small">CSPIT Campus Portal Registration (Practical 9 & 15)</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="full_name" class="form-label small fw-semibold text-secondary">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Fenil Finava" required>
            </div>
            <div class="col-md-6">
                <label for="enrollment_no" class="form-label small fw-semibold text-secondary">Enrollment No</label>
                <input type="text" class="form-control" id="enrollment_no" name="enrollment_no" placeholder="25CS011" required>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="username" class="form-label small fw-semibold text-secondary">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="fenilfinava" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="fenil@charusat.edu.in" required>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="course" class="form-label small fw-semibold text-secondary">Course / Branch</label>
                <select class="form-select" id="course" name="course" required>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Data Science">Data Science</option>
                    <option value="Cyber Security">Cyber Security</option>
                    <option value="AI & Machine Learning">AI & Machine Learning</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="gender" class="form-label small fw-semibold text-secondary">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Create strong password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            Create Account &rarr;
        </button>

        <div class="text-center mt-3 small text-muted">
            Already have an account? <a href="login.php" class="text-primary fw-semibold text-decoration-none">Sign in here</a>
        </div>
    </form>
</div>

</body>
</html>
