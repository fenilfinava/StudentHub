<?php
session_start();
require_once "db.php";

$is_logged_in = isset($_SESSION["user_id"]);
$user_role    = $_SESSION["role"] ?? "guest";
$username     = $_SESSION["username"] ?? "";

// Fetch upcoming campus alerts from events table
$events = [];
$e_res = $conn->query("SELECT id, title, event_date, location, category FROM events ORDER BY event_date ASC LIMIT 3");
if ($e_res) {
    while ($row = $e_res->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - CSPIT Charusat Portal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #10b981;
            --bg: #f8fafc;
            --text: #334155;
            --text-light: #64748b;
            --card: #ffffff;
            --border: #e2e8f0;
        }
        [data-theme="dark"] {
            --bg: #0f172a;
            --text: #f8fafc;
            --card: #1e293b;
            --text-light: #94a3b8;
            --border: #334155;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--bg);
            color: var(--text);
            transition: background-color 0.3s, color 0.3s;
        }

        /* Top Notification Banner */
        .notification-banner {
            background-color: var(--secondary);
            color: white;
            text-align: center;
            padding: 12px 20px;
            font-weight: 500;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1000;
            font-size: 0.95rem;
        }
        .close-banner {
            position: absolute;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            font-weight: bold;
            line-height: 1;
        }

        /* Hero Header */
        header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 45px 20px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3);
            position: relative;
        }
        [data-theme="dark"] header {
            background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
        }
        header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at top, rgba(59,130,246,0.2) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }
        header h1 {
            margin: 0 0 10px 0;
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: -1px;
            z-index: 1;
        }
        header p {
            margin: 0 0 25px 0;
            font-size: 1.15rem;
            color: #94a3b8;
            z-index: 1;
        }

        /* Pill Navigation Bar */
        nav.hero-nav {
            z-index: 100;
            position: relative;
            background: rgba(255,255,255,0.08);
            padding: 8px 18px;
            border-radius: 50px;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            max-width: 950px;
        }
        nav.hero-nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
            margin: 0;
        }
        nav.hero-nav ul li a {
            color: #f8fafc;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.25s ease;
            font-weight: 500;
            display: block;
            font-size: 0.95rem;
        }
        nav.hero-nav ul li a:hover,
        nav.hero-nav ul li a.active {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -6px var(--primary);
        }
        .theme-toggle {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            transition: 0.25s;
            font-size: 0.9rem;
        }
        .theme-toggle:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Main Container */
        .main-container {
            display: flex;
            flex: 1;
            flex-wrap: wrap;
            padding: 40px 20px 60px;
            gap: 30px;
            max-width: 1240px;
            margin: 0 auto;
            width: 100%;
        }
        main {
            flex: 3;
            min-width: 320px;
        }
        aside {
            flex: 1;
            min-width: 300px;
        }

        section {
            background: var(--card);
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            border: 1px solid var(--border);
            transition: background-color 0.3s, border-color 0.3s;
        }
        section h2 {
            margin-top: 0;
            color: var(--text);
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            margin-bottom: 20px;
        }
        p {
            color: var(--text-light);
            line-height: 1.65;
        }

        .btn-action {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.25s;
            display: inline-block;
            margin-top: 10px;
            font-family: inherit;
        }
        .btn-action:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(37,99,235,0.4);
        }

        /* Image Carousel Slider */
        .slider-container {
            position: relative;
            width: 100%;
            height: 380px;
            overflow: hidden;
            border-radius: 18px;
            margin: 25px 0 35px;
            background: #0f172a;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
        }
        .slide {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            transition: transform 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .slide img {
            width: 100%; height: 100%;
            object-fit: cover;
            opacity: 0.85;
            transition: 0.5s;
        }
        .slide:hover img {
            opacity: 1;
            transform: scale(1.03);
        }
        .slide-text {
            position: absolute;
            bottom: 25px; left: 25px; right: 25px;
            color: white;
            text-align: left;
            background: rgba(15, 23, 42, 0.75);
            padding: 18px 24px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .slide-text h3 {
            margin: 0 0 4px 0;
            font-size: 1.35rem;
            color: #ffffff;
        }
        .slide-text p {
            margin: 0;
            font-size: 0.95rem;
            color: #cbd5e1;
        }
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 42px; height: 42px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            z-index: 10;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .slider-btn:hover {
            background: white;
            transform: translateY(-50%) scale(1.08);
        }
        .slider-prev { left: 18px; }
        .slider-next { right: 18px; }

        /* Portal Navigation Flex Cards */
        .articles-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .articles-flex article {
            flex: 1;
            min-width: 250px;
            background: var(--card);
            padding: 24px;
            border-radius: 14px;
            border: 1px solid var(--border);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .articles-flex article:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }
        .articles-flex article h3 {
            margin-top: 0;
            color: var(--text);
            font-size: 1.15rem;
            margin-bottom: 10px;
        }
        .articles-flex article p {
            margin-bottom: 12px;
            font-size: 0.92rem;
        }

        /* FAQ Section */
        .faq-item {
            margin-bottom: 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
        }
        .faq-question {
            background: var(--card);
            padding: 16px 20px;
            width: 100%;
            text-align: left;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
            font-family: inherit;
        }
        .faq-question:hover {
            background: rgba(59,130,246,0.05);
        }
        .faq-answer {
            display: none;
            padding: 16px 20px;
            background: var(--bg);
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
            border-top: 1px solid var(--border);
        }

        /* Aside Card Alert */
        .card-alert {
            background: var(--card);
            padding: 28px;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
            border-top: 4px solid var(--secondary);
            position: sticky;
            top: 25px;
        }
        .card-alert h2 {
            font-size: 1.3rem;
            color: var(--text);
            margin-top: 0;
            margin-bottom: 16px;
        }
        .card-alert ul {
            padding-left: 18px;
            color: var(--text-light);
            line-height: 1.7;
            font-size: 0.92rem;
            margin-bottom: 16px;
        }
        .card-alert li {
            margin-bottom: 12px;
        }
        .card-alert mark {
            background: #dbeafe;
            color: var(--primary-dark);
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 600;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 35px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-top: auto;
            border-top: 1px solid #1e293b;
            font-size: 0.9rem;
        }
        footer address {
            font-style: normal;
            margin-top: 8px;
            line-height: 1.7;
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.show-modal {
            display: flex;
        }
        .modal-content {
            background: var(--card);
            color: var(--text);
            max-width: 500px;
            width: 100%;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            position: relative;
            border: 1px solid var(--border);
        }
        .close-modal {
            position: absolute;
            top: 18px; right: 20px;
            background: none;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: var(--text-light);
        }
    </style>
    <script src="assets/js/theme.js"></script>
</head>
<body>

    <!-- Notification Banner (Image 4) -->
    <div class="notification-banner" id="notification-banner">
        <span>🎉 Welcome to the new dynamic StudentHub portal! Check out the new dark mode.</span>
        <button class="close-banner" id="close-banner" aria-label="Close notification">&times;</button>
    </div>

    <!-- Header (Image 3 & 4) -->
    <header>
        <h1>StudentHub – CSPIT</h1>
        <p>Chandubhai S. Patel Institute of Technology – CHARUSAT Campus</p>
        
        <!-- Navigation Pill Bar -->
        <nav class="hero-nav">
            <ul>
                <li><a href="index.php" class="active">Home</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="courses.php">Courses</a></li>
                    <li><a href="attendance.php">Attendance</a></li>
                    <?php if ($user_role === "admin"): ?>
                        <li><a href="admin_dashboard.php" style="background: rgba(239, 68, 68, 0.25); color: #fca5a5;">Admin Console</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                <?php endif; ?>
            </ul>
            <button class="theme-toggle" id="theme-toggle-btn">🌙 Dark Mode</button>
        </nav>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <main>
            <!-- Welcome Card -->
            <section>
                <h2>Welcome to Student Portal</h2>
                <p>Your personalized portal for Computer Science & Engineering (CSE) at CSPIT, A7 Building, CHARUSAT. Access your timetable, 3rd semester subjects, attendance records, and exam results.</p>
                <button class="btn-action" id="open-modal-btn">View Portal Guidelines</button>
            </section>

            <!-- Dynamic Unsplash Campus Slider -->
            <div class="slider-container">
                <button class="slider-btn slider-prev" id="slider-prev" aria-label="Previous slide">&lt;</button>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="University Campus">
                    <div class="slide-text">
                        <h3>🎓 Welcome to Semester 3 CSE!</h3>
                        <p>Experience the best learning environment at CSPIT, CHARUSAT.</p>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Hackathon Team">
                    <div class="slide-text">
                        <h3>🚀 Upcoming Odoo Hackathon</h3>
                        <p>Join the university coding team at LDCE this semester!</p>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Coding Labs">
                    <div class="slide-text">
                        <h3>📅 Academic Timetable & Lab Sessions</h3>
                        <p>Access Room 506 division schedule and subject outlines.</p>
                    </div>
                </div>
                <button class="slider-btn slider-next" id="slider-next" aria-label="Next slide">&gt;</button>
            </div>

            <!-- Portal Navigation Cards -->
            <section>
                <h2>Portal Navigation</h2>
                <div class="articles-flex">
                    <article>
                        <h3>Semester 3 Subjects & Timetable</h3>
                        <p>Check your enrolled CSE subjects including FDSA, WDF, OOP, FCN, and Discrete Maths.</p>
                        <p><a href="courses.php">View Courses &rarr;</a></p>
                    </article>

                    <article>
                        <h3>Attendance & CGPA</h3>
                        <p>Monitor your current 94% average attendance and check past semester marksheets (CGPA 8.05).</p>
                        <p><a href="attendance.php">Check Attendance</a> | <a href="results.php">View Results &rarr;</a></p>
                    </article>

                    <article>
                        <h3>Assignments & Competitions</h3>
                        <p>Semester 3 assignments and problem sets. Stay updated with campus hackathons and activities.</p>
                        <p><a href="assignments.php">View Assignments</a> | <a href="achievements.php">Achievements &rarr;</a></p>
                    </article>
                </div>
            </section>

            <!-- Quick FAQ Section -->
            <section>
                <h2>Quick FAQ (Dynamic)</h2>
                <div class="faq-item">
                    <button class="faq-question">Where is the CSE Department located? <span>▼</span></button>
                    <div class="faq-answer">The Department of Computer Science & Engineering is located in A7 Building, CSPIT, CHARUSAT Campus.</div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">How do I check my 3rd semester schedule? <span>▼</span></button>
                    <div class="faq-answer">Log into your <a href="dashboard.php">Dashboard</a> to check lecture rooms (Room 506) and practical batch schedules.</div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">Who do I contact for portal queries? <span>▼</span></button>
                    <div class="faq-answer">You can reach out via our <a href="contact.php">Contact Us page</a> or email support@charusat.edu.in.</div>
                </div>
            </section>
        </main>

        <!-- Aside: Latest Campus Alert -->
        <aside>
            <div class="card-alert">
                <h2>Latest Campus Alert</h2>
                <ul>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $ev): ?>
                            <li>
                                <time datetime="<?= htmlspecialchars($ev['event_date']) ?>"><?= date("M d, Y", strtotime($ev['event_date'])) ?></time>: 
                                <mark><?= htmlspecialchars($ev['title']) ?>!</mark> <?= htmlspecialchars($ev['location']) ?>.
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><time datetime="2026-08-22">August 22, 2026</time>: <mark>Odoo Hackathon at LDCE!</mark> Get your team ready and register before the deadline.</li>
                        <li><time datetime="2026-08-15">August 15, 2026</time>: Independence Day Celebration at CHARUSAT Campus.</li>
                    <?php endif; ?>
                </ul>
                <p><a href="notifications.php">See all announcements &rarr;</a></p>
            </div>
        </aside>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <time datetime="2026">2026</time> StudentHub – CSPIT, CHARUSAT. All rights reserved.</p>
        <address>
            A7 Building, CSPIT, CHARUSAT University Campus, Changa, Gujarat.<br>
            Email: <a href="mailto:support@charusat.edu.in">support@charusat.edu.in</a> | Phone: <a href="tel:+91-2697-265011">+91-2697-265011</a>
        </address>
    </footer>

    <!-- Portal Guidelines Modal Overlay -->
    <div class="modal-overlay" id="custom-modal">
        <div class="modal-content">
            <button class="close-modal" id="close-modal-btn">&times;</button>
            <h2 style="margin-top:0;">Portal Guidelines</h2>
            <p>Welcome to StudentHub! Please review the university academic standards:</p>
            <ul style="color:var(--text-light); line-height:1.7; padding-left:20px;">
                <li>Maintain a minimum of <strong>75% attendance</strong> across all enrolled subjects.</li>
                <li>Submit laboratory assignments before designated cutoff dates.</li>
                <li>Report administrative discrepancies directly to the A7 CSE helpdesk.</li>
            </ul>
            <button class="btn-action" style="margin-top: 15px; width:100%;" onclick="document.getElementById('custom-modal').classList.remove('show-modal')">I Understand</button>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
