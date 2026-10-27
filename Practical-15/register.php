<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name         = trim($_POST["name"] ?? "");
    $email        = trim($_POST["email"] ?? "");
    $mobile       = trim($_POST["mobile"] ?? "");
    $gender       = trim($_POST["gender"] ?? "Male");
    $course       = trim($_POST["course"] ?? "");
    $adm_year     = trim($_POST["adm_year"] ?? "2026");
    $password     = $_POST["password"] ?? "";
    $confirm_pass = $_POST["confirm_password"] ?? "";
    $agree        = isset($_POST["agree"]);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_pass) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!$agree) {
        $error = "Please agree to the Terms & Conditions.";
    } else {
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "An account with that email address already exists.";
            $check->close();
        } else {
            $check->close();
            // Generate username from email or name
            $username = strtolower(explode("@", $email)[0]);
            // Generate Enrollment No (e.g., 25CS + random 3 digits)
            $enrollment_no = "25CS" . sprintf("%03d", rand(15, 999));
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insert into users table
            $u_stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'student', NOW())");
            $u_stmt->bind_param("sss", $username, $email, $hashed);

            if ($u_stmt->execute()) {
                $new_user_id = $u_stmt->insert_id;
                $u_stmt->close();

                // Insert into students table
                $sem = 3;
                $s_stmt = $conn->prepare("INSERT INTO students (enrollment_no, name, email, course, semester, gender, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW())");
                $s_stmt->bind_param("ssssis", $enrollment_no, $name, $email, $course, $sem, $gender);
                $s_stmt->execute();
                $s_stmt->close();

                // Log audit
                log_audit($conn, $new_user_id, $username, "USER_REGISTER", "New student self-registered: $name ($email)", "SUCCESS");

                header("Location: login.php?message=Account registered successfully! You may now sign in.");
                exit();
            } else {
                $error = "Registration failed: " . htmlspecialchars($conn->error);
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
    <title>Student Registration - StudentHub</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg: #f8fafc;
            --text: #334155;
            --border: #e2e8f0;
            --error: #ef4444;
            --success: #10b981;
        }
        [data-theme="dark"] {
            --bg: #0f172a;
            --text: #f8fafc;
            --border: #334155;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            color: var(--text);
        }
        .register-container {
            background: white;
            padding: 40px 45px;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 680px;
        }
        h1 {
            margin-top: 0;
            color: #0f172a;
            text-align: center;
            margin-bottom: 25px;
            font-size: 2rem;
            font-weight: 700;
        }
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.92rem;
            border: 1px solid #fca5a5;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .form-group {
            flex: 1;
            min-width: 260px;
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #0f172a;
            font-size: 0.95rem;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="password"], select {
            padding: 12px 15px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: 0.2s;
            outline: none;
            width: 100%;
            background: #ffffff;
        }
        input:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .gender-options {
            display: flex;
            gap: 20px;
            padding: 10px 0;
            align-items: center;
        }
        .gender-options label {
            font-weight: 400;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 0;
        }
        .terms-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }
        .terms-group label {
            font-weight: 400;
            margin-bottom: 0;
            cursor: pointer;
            font-size: 0.9rem;
            color: #64748b;
        }
        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.25s;
            width: 100%;
            margin-top: 22px;
            font-family: inherit;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37,99,235,0.3);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1>Student Registration</h1>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required autofocus>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="john@studenthub.edu" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mobile">Mobile Number *</label>
                    <input type="tel" id="mobile" name="mobile" placeholder="9876543210" required>
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <div class="gender-options">
                        <label><input type="radio" name="gender" value="Male" checked> Male</label>
                        <label><input type="radio" name="gender" value="Female"> Female</label>
                        <label><input type="radio" name="gender" value="Other"> Other</label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="course">Course *</label>
                    <select id="course" name="course" required>
                        <option value="">-- Select Course --</option>
                        <option value="Computer Science Engineering" selected>Computer Science Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Data Science">Data Science</option>
                        <option value="Web Development">Web Development</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="adm_year">Admission Year *</label>
                    <select id="adm_year" name="adm_year" required>
                        <option value="">-- Select Year --</option>
                        <option value="2026" selected>2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                </div>
            </div>

            <div class="terms-group">
                <input type="checkbox" id="agree" name="agree" required checked>
                <label for="agree">I agree to the Terms & Conditions and Privacy Policy.</label>
            </div>

            <button type="submit" class="btn-submit">Register Account</button>
        </form>

        <a href="login.php" class="back-link">&larr; Back to Login</a>
    </div>

</body>
</html>
