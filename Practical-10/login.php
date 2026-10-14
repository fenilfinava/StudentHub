<?php
session_start();
require_once "db.php";

// If already logged in, redirect to dashboard
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

// Auto-login via Remember-Me token if cookie is set
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_token"])) {
    $token = $_COOKIE["remember_token"];
    $token_hash = hash("sha256", $token);

    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE remember_token_hash = ? AND remember_token_expiry > NOW()");
    if ($stmt) {
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            // Update last_login
            $upd = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $upd->bind_param("i", $user["id"]);
            $upd->execute();
            $upd->close();

            $stmt->close();
            $conn->close();
            header("Location: dashboard.php");
            exit;
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
    <title>Secure Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Secure Login</h2>

    <?php
    if (isset($_GET["error"])) {
        echo "<p class='error'>" . htmlspecialchars($_GET["error"]) . "</p>";
    }
    if (isset($_GET["message"])) {
        echo "<p class='success'>" . htmlspecialchars($_GET["message"]) . "</p>";
    }
    ?>

    <form action="authenticate.php" method="POST">
        <label for="email">Email:</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="e.g. student01@gmail.com"
            required
        >

        <label for="password">Password:</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <div class="remember-row">
            <label class="remember-label">
                <input type="checkbox" name="remember_me" value="1">
                <span>Remember Me</span>
            </label>
        </div>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
