<?php
// test_connection.php
require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en" class="auth-pending">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection - StudentHub</title>
    <link rel="stylesheet" href="assets/css/global.css?v=4">
    <style>.auth-pending body { visibility: hidden; }</style>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/auth.js"></script>
</head>
<body>

<div class="grid-container">
    <header>
        <h1>Database Connection (Practical 8)</h1>
        <p>PHP PDO & SQLite Integration | Fetching data via Prepared Statements</p>
    </header>

    <nav></nav>

    <main>
        <section>
            <h2>Event Registrations Database (3NF)</h2>
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; border: 1px solid #34d399; font-weight: bold; margin-bottom: 25px;">
                ✅ Connection to Database established successfully using PHP PDO!
            </div>
            
            <p>This data is securely fetched from the 3NF SQLite database using an SQL <code>JOIN</code> query.</p>

            <table>
                <thead>
                    <tr>
                        <th>Registration ID</th>
                        <th>Student Name</th>
                        <th>Enrollment No.</th>
                        <th>Event Name</th>
                        <th>Event Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT 
                                    r.registration_id, 
                                    s.full_name, 
                                    s.enrollment_no, 
                                    e.event_name, 
                                    e.event_date
                                FROM registrations r
                                JOIN students s ON r.student_id = s.student_id
                                JOIN events e ON r.event_id = e.event_id
                                ORDER BY r.registration_id ASC";
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetchAll();

                        if (count($results) > 0) {
                            foreach ($results as $row) {
                                $enrollment_display = ($row['enrollment_no'] === '25CS011') 
                                    ? "<mark>{$row['enrollment_no']}</mark>" 
                                    : htmlspecialchars($row['enrollment_no']);

                                echo "<tr>
                                        <td>" . htmlspecialchars($row['registration_id']) . "</td>
                                        <td><strong>" . htmlspecialchars($row['full_name']) . "</strong></td>
                                        <td>{$enrollment_display}</td>
                                        <td>" . htmlspecialchars($row['event_name']) . "</td>
                                        <td>" . htmlspecialchars($row['event_date']) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No records found.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5' style='color:red;'>Error fetching data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 StudentHub - CSPIT, CHARUSAT. All rights reserved.</p>
    </footer>
</div>

    <script src="assets/js/nav.js"></script>
</body>
</html>
