<?php
session_start();
require_once "db.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to view notifications.");
    exit();
}

$events = [];
$e_res = $conn->query("SELECT id, title, category, event_date, location, description FROM events ORDER BY event_date ASC");
if ($e_res) {
    while ($r = $e_res->fetch_assoc()) {
        $events[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Notifications – CSPIT Charusat</title>
    <link rel="stylesheet" href="assets/css/global.css?v=5">
    <script src="assets/js/theme.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Campus Announcements & Bulletins</h1>
        <p>A7 Building, CSPIT – CHARUSAT University Campus</p>
    </header>

    <?php include "sidebar.php"; ?>

    <main>
        <section>
            <h2>Latest Campus Notices & Events</h2>

            <?php if (!empty($events)): ?>
                <?php foreach ($events as $ev): ?>
                    <article style="background:var(--table-bg); border:1px solid var(--table-border); padding:20px; border-radius:10px; margin-bottom:20px;">
                        <h3 style="margin-top:0;">📢 <?= htmlspecialchars($ev['title']) ?></h3>
                        <p><?= htmlspecialchars($ev['description'] ?: 'Official announcement from CSPIT administration.') ?></p>
                        <p><small>Date: <time datetime="<?= htmlspecialchars($ev['event_date']) ?>"><strong><?= date("M d, Y", strtotime($ev['event_date'])) ?></strong></time> | Venue: <?= htmlspecialchars($ev['location']) ?> | Category: <mark><?= htmlspecialchars($ev['category']) ?></mark></small></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <article style="background:var(--table-bg); border:1px solid var(--table-border); padding:20px; border-radius:10px; margin-bottom:20px;">
                <h3 style="margin-top:0;">🚀 Odoo Hackathon at LDCE on 22nd August!</h3>
                <p>The upcoming Odoo Hackathon is scheduled at L.D. College of Engineering on <time datetime="2026-08-22">August 22, 2026</time>. CSE students interested in open-source software development and competitive programming are strongly encouraged to participate and form teams!</p>
                <p><small>Posted on: <time datetime="2026-08-05">Aug 5, 2026</time> | Category: <mark>Hackathon Alert</mark></small></p>
            </article>

            <article style="background:var(--table-bg); border:1px solid var(--table-border); padding:20px; border-radius:10px; margin-bottom:20px;">
                <h3 style="margin-top:0;">Semester 3 Academic Timetable (With Effect From July 6)</h3>
                <p>The revised timetable for B.Tech Semester 3 CSE (Division I) has been released. Most regular theory lectures will be conducted in Room 506 (A7 Building). Practical batches A1, B1, and C1 must attend sessions in computer laboratories 631, 632, and 633.</p>
                <p><small>Posted on: <time datetime="2026-07-06">July 6, 2026</time> | Category: Academic Notice</small></p>
            </article>

            <article style="background:var(--table-bg); border:1px solid var(--table-border); padding:20px; border-radius:10px;">
                <h3 style="margin-top:0;">Independence Day Celebration & Flag Hoisting</h3>
                <p>All faculty members and students are invited to celebrate Independence Day on <time datetime="2026-08-15">August 15</time> at the central university grounds.</p>
                <p><small>Category: Campus Event</small></p>
            </article>
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
