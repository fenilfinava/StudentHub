<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = (($_SESSION['role'] ?? '') === 'admin');
?>
<nav>
    <ul>
        <li><a href="dashboard.php" class="<?= ($current_page === 'dashboard.php' || $current_page === 'student_dashboard.php') ? 'active' : '' ?>">📊 Dashboard</a></li>
        <li><a href="profile.php" class="<?= ($current_page === 'profile.php') ? 'active' : '' ?>">👤 Profile</a></li>
        <li><a href="courses.php" class="<?= ($current_page === 'courses.php') ? 'active' : '' ?>">📚 Courses</a></li>
        <li><a href="attendance.php" class="<?= ($current_page === 'attendance.php') ? 'active' : '' ?>">📋 Attendance</a></li>
        <li><a href="assignments.php" class="<?= ($current_page === 'assignments.php') ? 'active' : '' ?>">📝 Assignments</a></li>
        <li><a href="results.php" class="<?= ($current_page === 'results.php') ? 'active' : '' ?>">🏆 Results</a></li>
        <li><a href="achievements.php" class="<?= ($current_page === 'achievements.php') ? 'active' : '' ?>">🎖️ Achievements</a></li>
        <li><a href="notifications.php" class="<?= ($current_page === 'notifications.php') ? 'active' : '' ?>">🔔 Notifications</a></li>
        <li><a href="directory.php" class="<?= ($current_page === 'directory.php') ? 'active' : '' ?>">📁 Directory</a></li>
        <li><a href="contact.php" class="<?= ($current_page === 'contact.php') ? 'active' : '' ?>">✉️ Contact Us</a></li>
        <?php if ($is_admin): ?>
            <li><a href="admin_dashboard.php" style="background: rgba(220, 38, 38, 0.28); color: #fee2e2;">👑 Admin Console</a></li>
        <?php endif; ?>
        <li><a href="index.php">🏠 Home Portal</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
    <button class="theme-toggle-nav" onclick="window.toggleTheme()">🌙 Dark Mode</button>
</nav>
