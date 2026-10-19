<?php
session_start();
require_once 'db.php';
require_once 'upload.php';

// Access Control: Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login to access the Event Management Dashboard.");
    exit();
}

// Access Control: Check Admin Role (Unauthorized user access restriction)
if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>403 - Unauthorized Access</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container auth-container" style="text-align:center;">
        <div style="font-size:48px; margin-bottom:10px;">🚫</div>
        <h2 style="color:#c53030; border:none; margin-bottom:8px;">Unauthorized Access</h2>
        <p style="color:#4a5568; font-size:15px; margin-bottom:15px;">
            Access Denied: <strong>Admins only</strong>. You do not have permission to manage campus events.
        </p>
        <p style="font-size:13px; color:#718096; background:#edf2f7; padding:8px; border-radius:6px; display:inline-block;">
            Current User: <strong><?php echo htmlspecialchars($_SESSION["username"] ?? "Unknown"); ?></strong> | Role: <span class="badge badge-student">STUDENT</span>
        </p>
        <div style="margin-top:20px;">
            <a href="logout.php" class="btn btn-secondary">Logout & Switch Account</a>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit();
}

$error = "";

// ---------- CREATE EVENT ----------
if (isset($_POST['add'])) {
    $title    = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $date     = mysqli_real_escape_string($conn, trim($_POST['event_date'] ?? ''));
    $location = mysqli_real_escape_string($conn, trim($_POST['location'] ?? ''));
    $category = mysqli_real_escape_string($conn, trim($_POST['category'] ?? ''));

    if ($title === '' || $date === '' || $location === '' || $category === '') {
        $error = "All text fields are required.";
    } else {
        list($poster, $upload_error) = upload_poster($_FILES['poster'] ?? null);

        if ($upload_error) {
            $error = $upload_error;
        } else {
            $poster_sql = $poster ? "'$poster'" : "NULL";
            $insert_query = "INSERT INTO events (title, event_date, location, category, poster)
                             VALUES ('$title', '$date', '$location', '$category', $poster_sql)";
            if (mysqli_query($conn, $insert_query)) {
                header("Location: index.php?msg=" . urlencode("Event added successfully with poster!"));
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}

// ---------- DELETE EVENT ----------
if (isset($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT poster FROM events WHERE id = $id"));

    if (mysqli_query($conn, "DELETE FROM events WHERE id = $id")) {
        if ($row && $row['poster'] && file_exists("uploads/" . $row['poster'])) {
            unlink("uploads/" . $row['poster']); // Remove poster file from uploads folder
        }
        header("Location: index.php?msg=" . urlencode("Event and poster deleted successfully!"));
        exit();
    } else {
        $error = "Delete Error: " . mysqli_error($conn);
    }
}

// ---------- READ (Search & Filter) ----------
$search = mysqli_real_escape_string($conn, trim($_GET['search'] ?? ''));
$cat    = mysqli_real_escape_string($conn, trim($_GET['category'] ?? ''));

$sql = "SELECT * FROM events WHERE 1=1";
if ($search !== '') {
    $sql .= " AND (title LIKE '%$search%' OR location LIKE '%$search%')";
}
if ($cat !== '') {
    $sql .= " AND category = '$cat'";
}
$sql .= " ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div>
            <h1>Event Management System</h1>
            <p style="margin:4px 0 0 0; color:#718096; font-size:13.5px;">
                CSPIT Campus Events with Secure Poster Upload & Validation (Practical 12)
            </p>
        </div>
        <div class="user-badge">
            <span class="badge badge-admin">ADMIN PORTAL</span>
            <span style="font-size:13.5px; color:#4a5568;">👤 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="logout.php" class="btn btn-danger" style="padding:6px 14px; font-size:13px;">Logout</a>
        </div>
    </div>

    <!-- Feedback Notifications (Success & Error Messages) -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success">
            ✅ <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-error">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Add New Event Form -->
    <div class="card">
        <h2>Add New Event with Poster Upload</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div>
                    <input type="text" name="title" placeholder="Event Title (e.g. Odoo Hackathon)" required>
                </div>
                <div>
                    <input type="date" name="event_date" required>
                </div>
                <div>
                    <input type="text" name="location" placeholder="Location (e.g. CSPIT A7)" required>
                </div>
                <div>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Hackathon">Hackathon</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Conference">Conference</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Sports">Sports</option>
                        <option value="Music">Music</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <label style="font-size:13.5px; font-weight:600; color:#4a5568;">Event Poster (Max 2MB, JPG/PNG/WEBP):</label>
                    <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
                </div>
                <button type="submit" name="add" class="btn btn-success">+ Add Event</button>
            </div>
        </form>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card" style="padding:18px 20px 10px 20px;">
        <h2>Search & Filter Events</h2>
        <form method="GET" class="form-inline">
            <input type="text" name="search" placeholder="Search by title or location..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach (['Hackathon', 'Workshop', 'Conference', 'Seminar', 'Sports', 'Music'] as $c): ?>
                    <option value="<?php echo $c; ?>" <?php if ($cat === $c) echo 'selected'; ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter Events</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <!-- Events List Table -->
    <h2>Event Records & Posters</h2>
    <table>
        <thead>
            <tr>
                <th style="width:50px;">ID</th>
                <th style="width:90px;">Poster</th>
                <th>Event Title</th>
                <th style="width:110px;">Date</th>
                <th>Location</th>
                <th style="width:120px;">Category</th>
                <th style="width:130px;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                        <td>
                            <?php if ($row['poster'] && file_exists("uploads/" . $row['poster'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['poster']); ?>" class="poster-thumb" alt="Poster">
                            <?php else: ?>
                                <span class="no-poster">No Poster</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td>
                            <span class="badge" style="background:#e2e8f0; color:#4a5568;">
                                <?php echo htmlspecialchars($row['category']); ?>
                            </span>
                        </td>
                        <td class="action-links">
                            <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                            <a href="index.php?delete=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this event and poster?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:25px; color:#718096;">
                        No events found matching your criteria.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
