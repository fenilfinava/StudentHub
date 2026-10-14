<?php
// Start output buffering to prevent header errors
ob_start();
include 'db.php';

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $course_filter = isset($_GET['course_filter']) ? mysqli_real_escape_string($conn, $_GET['course_filter']) : '';

    $conditions = [];
    if (!empty($search)) {
        $conditions[] = "(name LIKE '%$search%' OR email LIKE '%$search%')";
    }
    if (!empty($course_filter)) {
        $conditions[] = "course = '$course_filter'";
    }

    $export_sql = "SELECT * FROM students";
    if (count($conditions) > 0) {
        $export_sql .= " WHERE " . implode(' AND ', $conditions);
    }
    $export_sql .= " ORDER BY id DESC";
    $export_res = mysqli_query($conn, $export_sql);

    if (ob_get_length()) {
        ob_end_clean();
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Course', 'Gender', 'Status', 'Created At'], ',', '"', "\\");

    if ($export_res && mysqli_num_rows($export_res) > 0) {
        while ($row = mysqli_fetch_assoc($export_res)) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['course'],
                $row['gender'],
                $row['status'] ?? 'Active',
                $row['created_at']
            ], ',', '"', "\\");
        }
    }
    fclose($output);
    exit();
}

// Handle Student Creation (CREATE)
if (isset($_POST['add_student'])) {
    $name   = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email  = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $course = mysqli_real_escape_string($conn, trim($_POST['course'] ?? ''));
    $gender = mysqli_real_escape_string($conn, trim($_POST['gender'] ?? ''));

    if ($name === '' || $email === '' || $course === '' || $gender === '') {
        header("Location: index.php?error=All fields are required.");
        exit();
    }

    $insert_sql = "INSERT INTO students (name, email, course, gender, status) 
                    VALUES ('$name', '$email', '$course', '$gender', 'Active')";
    if (mysqli_query($conn, $insert_sql)) {
        header("Location: index.php?msg=Student added successfully!");
        exit();
    } else {
        die("Insert Error: " . mysqli_error($conn));
    }
}

// Handle Soft Delete (Change status to Inactive)
if (isset($_GET['soft_delete'])) {
    $id = (int)$_GET['soft_delete'];
    if ($id > 0) {
        $soft_sql = "UPDATE students SET status = 'Inactive' WHERE id = $id";
        if (mysqli_query($conn, $soft_sql)) {
            header("Location: index.php?msg=Student status changed to Inactive (Soft Deleted)!");
            exit();
        } else {
            die("Soft Delete Error: " . mysqli_error($conn));
        }
    }
}

// Handle Status Activation (Restore student)
if (isset($_GET['activate'])) {
    $id = (int)$_GET['activate'];
    if ($id > 0) {
        $act_sql = "UPDATE students SET status = 'Active' WHERE id = $id";
        if (mysqli_query($conn, $act_sql)) {
            header("Location: index.php?msg=Student status changed to Active!");
            exit();
        } else {
            die("Activation Error: " . mysqli_error($conn));
        }
    }
}

// Handle Hard Student Deletion (DELETE)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $delete_sql = "DELETE FROM students WHERE id = $id";
        if (mysqli_query($conn, $delete_sql)) {
            header("Location: index.php?msg=Student record deleted successfully!");
            exit();
        } else {
            die("Delete Error: " . mysqli_error($conn));
        }
    } else {
        die("Invalid ID for deletion.");
    }
}

// Build Search and Filter Query (READ)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$course_filter = isset($_GET['course_filter']) ? mysqli_real_escape_string($conn, trim($_GET['course_filter'])) : '';

$conditions = [];
if (!empty($search)) {
    $conditions[] = "(name LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($course_filter)) {
    $conditions[] = "course = '$course_filter'";
}

$query_sql = "SELECT * FROM students";
if (count($conditions) > 0) {
    $query_sql .= " WHERE " . implode(' AND ', $conditions);
}
$query_sql .= " ORDER BY id DESC";
$result = mysqli_query($conn, $query_sql);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div>
            <h1 style="margin:0; font-size:24px; color:#1a202c;">Student Management System</h1>
            <p style="margin:4px 0 0 0; color:#718096; font-size:13px;">CRUD with Search, Course Filter, Soft Delete & CSV Export</p>
        </div>
        <div>
            <a href="index.php?export=csv<?php echo !empty($search)?'&search='.urlencode($search):''; ?><?php echo !empty($course_filter)?'&course_filter='.urlencode($course_filter):''; ?>" class="btn btn-success">📥 Export CSV</a>
        </div>
    </div>

    <!-- Success and Error Messages -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Add New Student Section -->
    <div class="card">
        <h2>Add New Student</h2>
        <form method="POST" action="index.php">
            <div class="form-inline">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <select name="course" required>
                    <option value="">Select Course</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Data Science">Data Science</option>
                </select>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <button type="submit" name="add_student" class="btn btn-success">+ Add Student</button>
            </div>
        </form>
    </div>

    <!-- Student List Section -->
    <div class="card" style="padding: 20px 20px 10px 20px;">
        <h2>Search & Filter Records</h2>
        <form method="GET" action="index.php" class="form-inline">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="course_filter">
                <option value="">All Courses</option>
                <option value="Computer Science" <?php if ($course_filter == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                <option value="Web Development" <?php if ($course_filter == 'Web Development') echo 'selected'; ?>>Web Development</option>
                <option value="Data Science" <?php if ($course_filter == 'Data Science') echo 'selected'; ?>>Data Science</option>
            </select>
            <button type="submit">Search / Filter</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <!-- Records Table -->
    <h2>Student Records</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php $status = $row['status'] ?? 'Active'; ?>
                    <tr>
                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td>
                            <span class="badge <?php echo $status === 'Active' ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td class="action-links">
                            <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <?php if ($status === 'Active'): ?>
                                <a href="index.php?soft_delete=<?php echo $row['id']; ?>" class="link-soft-delete" title="Mark student as Inactive">Soft Delete</a> | 
                            <?php else: ?>
                                <a href="index.php?activate=<?php echo $row['id']; ?>" class="link-activate" title="Activate student">Activate</a> | 
                            <?php endif; ?>
                            <a href="index.php?delete=<?php echo $row['id']; ?>" class="link-delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #718096; padding: 20px;">No student records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
