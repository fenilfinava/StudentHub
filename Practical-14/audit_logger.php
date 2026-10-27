<?php
// Practical 14: Audit Logging Helper Function
function log_audit($conn, $user_id, $username, $action, $details = '', $status = 'SUCCESS') {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, username, action, details, ip_address, user_agent, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issssss", $user_id, $username, $action, $details, $ip_address, $user_agent, $status);
        $stmt->execute();
        $stmt->close();
    }
}
?>
