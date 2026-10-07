<?php
// Suppress deprecation notices for fputcsv in PHP 8.4+
error_reporting(E_ALL & ~E_DEPRECATED);

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = clean_input($_POST["name"] ?? "");
    $enrollment = clean_input($_POST["id"] ?? "");
    $email = clean_input($_POST["mail"] ?? "");
    $message = clean_input($_POST["msg"] ?? "");

    $errors = [];
    if (empty($name)) { $errors[] = "Name is required."; }
    elseif (!preg_match("/^[a-zA-Z\s]*$/", $name)) { $errors[] = "Name can only contain letters and spaces."; }

    if (empty($enrollment)) { $errors[] = "Enrollment Number is required."; }

    if (empty($email)) { $errors[] = "Email is required."; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Invalid email format."; }

    if (empty($message)) { $errors[] = "Message cannot be empty."; }

    if (count($errors) > 0) {
        $error_string = implode(" | ", $errors);
        header("Location: /contact.html?error=" . urlencode($error_string));
        exit();
    } else {
        $file = __DIR__ . '/../private/contacts.csv';
        $file_exists = file_exists($file);
        $handle = fopen($file, 'a');
        if ($handle) {
            // Add flock for concurrency as per PRD
            if (flock($handle, LOCK_EX)) {
                if (!$file_exists) {
                    fputcsv($handle, ['Timestamp', 'Name', 'Enrollment', 'Email', 'Message'], ",", "\"", "\\");
                }
                $timestamp = date("Y-m-d H:i:s");
                fputcsv($handle, [$timestamp, $name, $enrollment, $email, $message], ",", "\"", "\\");
                flock($handle, LOCK_UN);
            }
            fclose($handle);
            
            header("Location: /contact.html?success=1");
            exit();
        } else {
            header("Location: /contact.html?error=" . urlencode("Server error: Could not open file for writing."));
            exit();
        }
    }
} else {
    header("Location: /contact.html");
    exit();
}
?>
