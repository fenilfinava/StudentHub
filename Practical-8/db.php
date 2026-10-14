<?php
// db.php - Secure PHP PDO connection file

// We are using SQLite database to make it completely portable and serverless on Mac.
// It connects directly to the 'studenthub.db' file in the same directory.
$db_file = __DIR__ . '/studenthub.db';
$dsn = "sqlite:" . $db_file;

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn);
    
    // Set error mode to Exceptions for robust try-catch handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative Arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Connection successful message is handled in the frontend scripts
    // e.g. test_connection.php
} catch (PDOException $e) {
    // Catch block for handling connection errors securely
    die("<div style='color:red; font-weight:bold; padding:20px; border:1px solid red;'>
            Database Connection Failed: " . htmlspecialchars($e->getMessage()) . "
         </div>");
}
?>
