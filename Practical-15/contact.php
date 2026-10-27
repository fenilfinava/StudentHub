<?php
session_start();
require_once "db.php";
require_once "audit_logger.php";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST["name"] ?? "");
    $email = trim($_POST["mail"] ?? "");
    $enr   = trim($_POST["id"] ?? "");
    $query = trim($_POST["msg"] ?? "");

    $msg = "Thank you $name! Your inquiry has been submitted to the A7 CSE helpdesk.";
    if (isset($_SESSION["user_id"])) {
        log_audit($conn, $_SESSION["user_id"], $_SESSION["username"], "SUBMIT_INQUIRY", "Student inquiry: $query", "SUCCESS");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support – CSPIT Charusat</title>
    <link rel="stylesheet" href="assets/css/global.css?v=5">
    <script src="assets/js/theme.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Contact Support & Helpdesk</h1>
        <p>Department of Computer Science & Engineering (CSPIT, CHARUSAT)</p>
    </header>

    <?php include "sidebar.php"; ?>

    <main>
        <?php if (!empty($msg)): ?>
            <section style="border-left-color: #10b981; padding: 18px 25px; margin-bottom: 20px;">
                <p style="margin:0; color:#10b981; font-weight:600;">✓ <?= htmlspecialchars($msg) ?></p>
            </section>
        <?php endif; ?>

        <section>
            <h2>Department & Campus Directory</h2>
            <p>Reach out to the student section or department coordinators during university hours.</p>
            
            <ul>
                <li><strong>Department:</strong> Computer Science & Engineering (CSE)</li>
                <li><strong>Institute:</strong> Chandubhai S. Patel Institute of Technology (CSPIT)</li>
                <li><strong>Location:</strong> A7 Building, CHARUSAT University Campus, Changa, Gujarat</li>
                <li><strong>Official Support Email:</strong> <a href="mailto:support@charusat.edu.in">support@charusat.edu.in</a></li>
                <li><strong>Helpline Call No:</strong> <a href="tel:+91-2697-265011">+91-2697-265011</a></li>
            </ul>
        </section>

        <section>
            <h2>Submit Student Inquiry</h2>
            <p>Send us your technical query regarding portal access or academic schedules.</p>

            <form action="contact.php" method="POST">
                <fieldset>
                    <legend>Inquiry Form</legend>
                    <p>
                        <label for="name">Student Name:</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_SESSION['username'] ?? 'Fenil Finava') ?>" required>
                    </p>
                    <p>
                        <label for="id">Enrollment Number:</label>
                        <input type="text" id="id" name="id" value="25CS011" required>
                    </p>
                    <p>
                        <label for="mail">Student Email:</label>
                        <input type="email" id="mail" name="mail" value="<?= htmlspecialchars($_SESSION['email'] ?? '25cs011@charusat.edu.in') ?>" required>
                    </p>
                    <p>
                        <label for="msg">Message or Question:</label>
                        <textarea id="msg" name="msg" rows="4" required placeholder="Type your query here..."></textarea>
                    </p>
                    <p>
                        <button type="submit">Send Inquiry &rarr;</button>
                        <button type="reset" style="background:#64748b; margin-left: 10px;">Clear</button>
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
