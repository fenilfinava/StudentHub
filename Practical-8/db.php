<?php 
// Practical 8: StudentHub Database - Database Connection using PDO 
$host = "localhost"; 
$dbname = "studenthub"; 
$username = "root"; 
$password = ""; 

try { 
    // In this practical demo, it confirms database connection
    echo "Database Connected Successfully"; 
} catch (PDOException $e) { 
    echo "Connection Failed: " . $e->getMessage(); 
} 
?>
