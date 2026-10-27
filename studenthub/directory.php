<?php
session_start();
require_once "db.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to view campus directory.");
    exit();
}

$students = [];
$res = $conn->query("SELECT enrollment_no, name, email, course, semester, gender FROM students ORDER BY id ASC");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $students[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Data Directory – StudentHub</title>
    <link rel="stylesheet" href="assets/css/global.css?v=5">
    <style>
        .data-controls { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; background: var(--table-bg); padding: 20px; border-radius: 12px; border: 1px solid var(--table-border);}
        .data-controls input, .data-controls select { padding: 10px; border: 1px solid var(--input-border); border-radius: 8px; font-size: 0.95rem; flex: 1; min-width: 200px; margin: 0;}
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tabs button { background: var(--border); color: var(--text-dark); padding: 10px 20px; box-shadow: none; border-radius: 8px 8px 0 0; }
        .tabs button.active { background: var(--primary); color: white; }
    </style>
    <script src="assets/js/theme.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Campus Data Directory</h1>
        <p>Dynamic Student & Academic Directory – CSPIT, CHARUSAT</p>
    </header>

    <?php include "sidebar.php"; ?>

    <main>
        <section>
            <h2>Enrolled Students Directory (Live MySQL)</h2>
            <div class="data-controls">
                <input type="text" id="liveSearch" placeholder="🔍 Search student name, enrollment, or department..." onkeyup="filterTable()">
            </div>

            <table border="1" cellpadding="8" cellspacing="0" width="100%" id="studentDirTable">
                <thead>
                    <tr align="left">
                        <th>Enrollment No</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $st): ?>
                        <tr>
                            <td><mark><?= htmlspecialchars($st['enrollment_no'] ?: '25CS011') ?></mark></td>
                            <td><strong><?= htmlspecialchars($st['name']) ?></strong></td>
                            <td><a href="mailto:<?= htmlspecialchars($st['email']) ?>"><?= htmlspecialchars($st['email']) ?></a></td>
                            <td><?= htmlspecialchars($st['course']) ?></td>
                            <td>Sem <?= htmlspecialchars($st['semester'] ?: 3) ?></td>
                            <td><?= htmlspecialchars($st['gender']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; <time datetime="2026">2026</time> StudentHub – CSPIT, CHARUSAT. All rights reserved.</p>
        <address>
            Email: <a href="mailto:support@charusat.edu.in">support@charusat.edu.in</a> | Phone: <a href="tel:+91-2697-265011">+91-2697-265011</a>
        </address>
    </footer>
</div>

<script>
function filterTable() {
    let input = document.getElementById("liveSearch").value.toUpperCase();
    let rows = document.getElementById("studentDirTable").getElementsByTagName("tr");
    for (let i = 1; i < rows.length; i++) {
        let text = rows[i].textContent || rows[i].innerText;
        rows[i].style.display = (text.toUpperCase().indexOf(input) > -1) ? "" : "none";
    }
}
</script>
</body>
</html>
