<?php
// Practical 15: Universal Database Connection for StudentHub Portal
// Supports both MySQLi and PDO, localhost and Cloud Deployment environment variables

$host     = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'studenthub_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    // Graceful fallback to user_registration
    $database = 'user_registration';
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("StudentHub Database Connection Failed: " . $conn->connect_error);
    }
}

$conn->set_charset("utf8mb4");

// Also initialize PDO for Fetch/AJAX API modules
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    $pdo = null;
}
?>
