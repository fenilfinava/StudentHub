<?php 
require_once "db.php"; 

$result = $conn->query("SELECT id, username, email, password, created_at FROM users ORDER BY id ASC"); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Users Table - Practical 9</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container-wide">
    <h2>Database: `user_registration` &rarr; Table: `users`</h2>
    <p style="text-align: center; color: #555; font-size: 14px;">
        Student: <strong>Fenil Finava</strong> | ID: <strong>25CS011</strong> | Database Engine: <strong>MySQL (MySQLi)</strong>
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 140px;">Username</th>
                <th style="width: 220px;">Email</th>
                <th>Password (Hashed with password_hash)</th>
                <th style="width: 160px;">Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td><strong>" . htmlspecialchars($row["username"]) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td><span class='hash-cell'>" . htmlspecialchars($row["password"]) . "</span></td>";
                    echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; color:#888; padding:20px;'>No registered users found in MySQL yet. Register a user from the form!</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="links" style="margin-top: 25px;">
        <a href="register.php">➕ Back to Registration Form</a>
    </div>
</div>

</body>
</html>
<?php 
$conn->close(); 
?>
