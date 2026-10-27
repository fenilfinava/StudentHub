<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to view achievements.");
    exit();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event = trim($_POST["event"] ?? "");
    $date  = trim($_POST["date"] ?? "");
    $msg = "Achievement for '$event' submitted successfully!";
    log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "ADD_ACHIEVEMENT", "Added achievement: $event ($date)", "SUCCESS");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements – Fenil Finava (CSPIT)</title>
    <link rel="stylesheet" href="assets/css/global.css?v=5">
    <script src="assets/js/theme.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Student Achievements & Hackathons</h1>
        <p>Fenil Finava | Computer Science & Engineering – CSPIT, CHARUSAT</p>
    </header>

    <?php include "sidebar.php"; ?>

    <main>
        <?php if (!empty($msg)): ?>
            <section style="border-left-color: #10b981; padding: 18px 25px; margin-bottom: 20px;">
                <p style="margin:0; color:#10b981; font-weight:600;">✓ <?= htmlspecialchars($msg) ?></p>
            </section>
        <?php endif; ?>

        <section>
            <h2>Upcoming Hackathon Participation</h2>
            <article>
                <h3>🚀 Odoo Hackathon at LDCE</h3>
                <p><strong>Event Date:</strong> <time datetime="2026-08-22">August 22, 2026</time> | Venue: L.D. College of Engineering (LDCE)</p>
                <p>Preparing to represent Computer Science & Engineering (CSPIT, CHARUSAT) at the prestigious Odoo Hackathon!</p>
                <p>Status: <mark>Registered & Active Preparation</mark></p>
            </article>
        </section>

        <section>
            <h2>Academic Badges & Honors</h2>
            <article>
                <h3>Web Development Frameworks Specialist</h3>
                <p>Demonstrated strong skills in building pure HTML5 semantic web structures, PHP server-side apps, and MySQL database platforms.</p>
                <p>Status: <mark>Verified Badge</mark></p>
            </article>
            <br>
            <article>
                <h3>Consistent Academic Honors</h3>
                <p>Achieved impressive results across previous semesters (8.32 SGPA in Sem 1 and overall 8.05 CGPA).</p>
            </article>
        </section>

        <section>
            <h2>Submit New Hackathon or Competition Record</h2>
            <form action="achievements.php" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>Add Achievement Details</legend>
                    <p>
                        <label for="event">Event Name:</label>
                        <input type="text" id="event" name="event" value="Odoo Hackathon 2026" required>
                    </p>
                    <p>
                        <label for="date">Event Date:</label>
                        <input type="date" id="date" name="date" value="2026-08-22" required>
                    </p>
                    <p>
                        <label for="cert">Upload Certificate / Proof (Optional):</label>
                        <input type="file" id="cert" name="cert">
                    </p>
                    <p>
                        <button type="submit">Save Achievement &rarr;</button>
                    </p>
                </fieldset>
            </form>
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
