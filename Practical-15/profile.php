<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to view profile.");
    exit();
}

$user_id  = $_SESSION["user_id"];
$username = $_SESSION["username"];
$email    = $_SESSION["email"] ?? "";
$student_name  = "Fenil Finava";
$enrollment_no = "25CS011";
$course        = "Computer Science & Engineering (CSE)";
$sem           = "Semester 3 (Division I)";

$st_stmt = $conn->prepare("SELECT enrollment_no, name, course, semester, gender FROM students WHERE email = ? LIMIT 1");
if ($st_stmt) {
    $st_stmt->bind_param("s", $email);
    $st_stmt->execute();
    $res = $st_stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $student_name  = $row["name"];
        $enrollment_no = $row["enrollment_no"] ?: "25CS011";
        $course        = $row["course"];
        $sem           = "Semester " . ($row["semester"] ?: 3) . " (Division I)";
    }
    $st_stmt->close();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone   = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $msg = "Profile information updated successfully!";
    log_audit($conn, $user_id, $username, "UPDATE_PROFILE", "Student updated contact details.", "SUCCESS");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – <?= htmlspecialchars($student_name) ?></title>
    <link rel="stylesheet" href="assets/css/global.css?v=5">
    <script src="assets/js/theme.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Student Profile</h1>
        <p>Personal and Academic Profile Record – CSPIT, CHARUSAT</p>
    </header>

    <?php include "sidebar.php"; ?>

    <main>
        <?php if (!empty($msg)): ?>
            <section style="border-left-color: #10b981; padding: 20px 30px; margin-bottom: 20px;">
                <p style="margin:0; color:#10b981; font-weight:600;">✓ <?= htmlspecialchars($msg) ?></p>
            </section>
        <?php endif; ?>

        <section>
            <h2>Personal Information</h2>
            <dl>
                <dt><strong>Student Name:</strong></dt>
                <dd><?= htmlspecialchars($student_name) ?></dd>
                
                <dt><strong>Enrollment Number:</strong></dt>
                <dd><mark><?= htmlspecialchars($enrollment_no) ?></mark></dd>
                
                <dt><strong>Institute & Department:</strong></dt>
                <dd>Chandubhai S. Patel Institute of Technology (CSPIT) – <?= htmlspecialchars($course) ?></dd>
                
                <dt><strong>Campus Building:</strong></dt>
                <dd>A7 Building, CHARUSAT University Campus, Changa</dd>
                
                <dt><strong>Current Semester:</strong></dt>
                <dd><?= htmlspecialchars($sem) ?></dd>

                <dt><strong>Student Official Email:</strong></dt>
                <dd><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></dd>
            </dl>
        </section>

        <section>
            <h2>Update Contact & Address Details</h2>
            <form action="profile.php" method="POST">
                <fieldset>
                    <legend>Edit Student Records</legend>
                    <p>
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" value="+91-2697-265011">
                    </p>
                    <p>
                        <label for="address">Hostel / Residential Address:</label>
                        <textarea id="address" name="address" rows="3">A7 Building, CSPIT, CHARUSAT University Campus, Changa, Gujarat</textarea>
                    </p>
                    <p>
                        <button type="submit">Save Details &rarr;</button>
                    </p>
                </fieldset>
            </form>
        </section>

        <section>
            <h2>Account Security</h2>
            <details>
                <summary>Change Login Password</summary>
                <br>
                <form action="profile.php" method="POST">
                    <p>
                        <label for="oldpass">Current Password:</label>
                        <input type="password" id="oldpass" name="oldpass" required>
                    </p>
                    <p>
                        <label for="newpass">New Password:</label>
                        <input type="password" id="newpass" name="newpass" required minlength="6">
                    </p>
                    <button type="submit">Update Password</button>
                </form>
            </details>
        </section>
    </main>

    <footer>
        <p>&copy; <time datetime="2026">2026</time> StudentHub – CSPIT, CHARUSAT. All rights reserved.</p>
        <address>
            Email: <a href="mailto:support@charusat.edu.in">support@charusat.edu.in</a> | Phone: <a href="tel:+91-2697-265011">+91-2697-265011</a>
        </address>
    </footer>
</div>

</body>
</html>
