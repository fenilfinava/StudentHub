<?php
session_start();
require_once "db.php";

// If already logged in as Admin, redirect to index.php
if (isset($_SESSION["user_id"]) && ($_SESSION["role"] ?? "") === "admin") {
    header("Location: index.php");
    exit();
}

$error = $_GET["error"] ?? "";
$message = $_GET["message"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter both email and password.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, email, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                if ($user["role"] === "admin") {
                    header("Location: index.php?msg=Admin login successful! Welcome to Event Dashboard.");
                    exit();
                } else {
                    // Non-admin user logged in - access restricted
                    header("Location: index.php");
                    exit();
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Event Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container auth-container">
    <h1 style="text-align:center; font-size:22px; margin-top:0;">Event Management Portal</h1>
    <p style="text-align:center; color:#718096; font-size:13.5px; margin-top:-5px; margin-bottom:20px;">
        Practical 12 - Admin Authentication
    </p>

    <?php if ($error): ?>
        <div class="alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" style="width:100%; margin-top:10px;">Sign In to Dashboard &rarr;</button>
    </form>
</div>
</body>
</html>
