<?php
// Start output buffering to prevent header errors
ob_start();
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid record ID.");
}

// Fetch existing record
$select_sql = "SELECT * FROM students WHERE id = $id";
$fetch_result = mysqli_query($conn, $select_sql);
if (!$fetch_result || mysqli_num_rows($fetch_result) == 0) {
    die("Student record not found.");
}
$student = mysqli_fetch_assoc($fetch_result);

// Handle Form Update (UPDATE)
if (isset($_POST['update_student'])) {
    $name   = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email  = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $course = mysqli_real_escape_string($conn, trim($_POST['course'] ?? ''));
    $gender = mysqli_real_escape_string($conn, trim($_POST['gender'] ?? ''));
    $status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'Active'));

    if ($name === '' || $email === '' || $course === '' || $gender === '') {
        $error = "All fields are required.";
    } else {
        $update_sql = "UPDATE students 
                       SET name = '$name', email = '$email', course = '$course', gender = '$gender', status = '$status' 
                       WHERE id = $id";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: index.php?msg=" . urlencode("Student details updated successfully!"));
            exit();
        } else {
            die("Update Error: " . mysqli_error($conn));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Practical 11</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container {
            max-width: 550px;
            margin: 40px auto;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }
        .form-group input, .form-group select {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container edit-container">
    <h2>Edit Student Details</h2>
    <p style="color:#718096; font-size:14px; margin-top:-5px; margin-bottom:20px;">
        Updating record for Student ID <strong>#<?php echo $id; ?></strong>
    </p>

    <?php if (isset($error)): ?>
        <div class="alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="course">Course:</label>
            <select id="course" name="course" required>
                <option value="Computer Science" <?php if ($student['course'] == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                <option value="Web Development" <?php if ($student['course'] == 'Web Development') echo 'selected'; ?>>Web Development</option>
                <option value="Data Science" <?php if ($student['course'] == 'Data Science') echo 'selected'; ?>>Data Science</option>
            </select>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Active" <?php if (($student['status'] ?? 'Active') == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Inactive" <?php if (($student['status'] ?? '') == 'Inactive') echo 'selected'; ?>>Inactive (Soft Deleted)</option>
            </select>
        </div>

        <div style="margin-top:25px; display:flex; gap:10px;">
            <button type="submit" name="update_student" class="btn btn-success">Update Student Details</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
