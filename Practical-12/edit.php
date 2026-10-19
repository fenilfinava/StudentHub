<?php
session_start();
require_once 'db.php';
require_once 'upload.php';

// Access Control: Check Admin
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php?error=Unauthorized Access: Admins only.");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid event ID.");
}

$res = mysqli_query($conn, "SELECT * FROM events WHERE id = $id");
$event = mysqli_fetch_assoc($res);
if (!$event) {
    die("Event not found.");
}

$error = "";

// ---------- UPDATE EVENT ----------
if (isset($_POST['update'])) {
    $title    = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $date     = mysqli_real_escape_string($conn, trim($_POST['event_date'] ?? ''));
    $location = mysqli_real_escape_string($conn, trim($_POST['location'] ?? ''));
    $category = mysqli_real_escape_string($conn, trim($_POST['category'] ?? ''));

    if ($title === '' || $date === '' || $location === '' || $category === '') {
        $error = "All text fields are required.";
    } else {
        list($new_poster, $upload_error) = upload_poster($_FILES['poster'] ?? null);

        if ($upload_error) {
            $error = $upload_error;
        } else {
            $poster = $new_poster ? $new_poster : $event['poster']; // Keep old poster if no new file
            $poster_sql = $poster ? "'$poster'" : "NULL";

            $update_sql = "UPDATE events 
                           SET title = '$title', event_date = '$date', location = '$location', 
                               category = '$category', poster = $poster_sql 
                           WHERE id = $id";

            if (mysqli_query($conn, $update_sql)) {
                // If a new poster was successfully uploaded, delete the old poster file
                if ($new_poster && $event['poster'] && file_exists("uploads/" . $event['poster'])) {
                    unlink("uploads/" . $event['poster']);
                }
                header("Location: index.php?msg=" . urlencode("Event updated successfully!"));
                exit();
            } else {
                $error = "Update Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-box {
            max-width: 600px;
            margin: 30px auto;
        }
    </style>
</head>
<body>
<div class="container edit-box">
    <div class="top-bar">
        <h2>Edit Event Details</h2>
        <a href="index.php" class="btn btn-secondary" style="padding:6px 14px; font-size:13px;">&larr; Back to Events</a>
    </div>

    <?php if ($error): ?>
        <div class="alert-error">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Event Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="event_date">Event Date</label>
            <input type="date" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>" required>
        </div>

        <div class="form-group">
            <label for="location">Location / Venue</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <?php foreach (['Hackathon', 'Workshop', 'Conference', 'Seminar', 'Sports', 'Music'] as $c): ?>
                    <option value="<?php echo $c; ?>" <?php if ($event['category'] === $c) echo 'selected'; ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Current Poster:</label>
            <div style="margin-top:6px;">
                <?php if ($event['poster'] && file_exists("uploads/" . $event['poster'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($event['poster']); ?>" class="poster-thumb" style="width:100px; height:130px;" alt="Current Poster">
                <?php else: ?>
                    <span class="no-poster">No Poster Uploaded</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="poster">Change Poster (Optional - Max 2MB, JPG/PNG/WEBP):</label>
            <input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png,.webp">
        </div>

        <div style="margin-top:25px; display:flex; gap:10px;">
            <button type="submit" name="update" class="btn btn-success">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
