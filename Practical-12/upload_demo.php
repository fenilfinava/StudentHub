<?php
// STEP 1 DEMO: Test the upload alone, before database integration
require_once 'upload.php';

$msg = "";
$error = "";

if (isset($_POST['upload'])) {
    list($name, $err) = upload_poster($_FILES['poster'] ?? null);
    if ($err) {
        $error = $err;
    } elseif ($name) {
        $msg = "File uploaded successfully! Saved as: uploads/" . $name;
    } else {
        $error = "No file chosen.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Poster Upload Standalone Test - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container auth-container">
    <h2>Poster Upload Test (Step 1)</h2>
    <p style="color:#718096; font-size:13.5px;">Testing validation logic (JPG/PNG/WEBP, Max 2MB, getimagesize)</p>

    <?php if ($msg): ?>
        <div class="alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Select Poster Image:</label>
            <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp" required>
        </div>
        <button type="submit" name="upload" style="width:100%;">Upload & Test Validation</button>
    </form>

    <div style="margin-top:20px; text-align:center;">
        <a href="index.php" style="color:#3182ce; font-size:13.5px; text-decoration:none;">&larr; Back to Event Management</a>
    </div>
</div>
</body>
</html>
