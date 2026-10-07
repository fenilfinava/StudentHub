// nav.js — Single source of truth for navigation on internal pages
document.addEventListener('DOMContentLoaded', () => {
    const navContainer = document.querySelector('nav');
    if (!navContainer) return;
    
    const isLogged = !!window.sh_session;
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    const links = isLogged ? [
        { name: '📊 Dashboard', url: 'dashboard.html' },
        { name: '👤 Profile', url: 'profile.html' },
        { name: '📚 Courses', url: 'courses.html' },
        { name: '📋 Attendance', url: 'attendance.html' },
        { name: '📝 Assignments', url: 'assignments.html' },
        { name: '🏆 Results', url: 'results.html' },
        { name: '🎖️ Achievements', url: 'achievements.html' },
        { name: '🔔 Notifications', url: 'notifications.html' },
        { name: '📁 Directory', url: 'directory.html' },
        { name: '✉️ Contact Us', url: 'contact.html' },
        { name: '🗄️ Database Demo', url: 'test_connection.php' },
        { name: '🚪 Logout', url: '#', onclick: 'logout()' }
    ] : [
        { name: 'Home', url: 'index.html' },
        { name: 'Login', url: 'login.html' },
        { name: 'Register', url: 'register.html' }
    ];
    
    const ul = document.createElement('ul');
    links.forEach(link => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = link.url;
        if (link.onclick) a.setAttribute('onclick', link.onclick);
        if (currentPage === link.url) {
            a.innerHTML = '<strong>' + link.name + '</strong>';
            a.setAttribute('aria-current', 'page');
        } else {
            a.textContent = link.name;
        }
        li.appendChild(a);
        ul.appendChild(li);
    });
    
    navContainer.innerHTML = '';
    navContainer.appendChild(ul);
    
    // Add theme toggle button inside sidebar
    const themeBtn = document.createElement('button');
    themeBtn.className = 'theme-toggle-nav';
    const currentTheme = document.documentElement.getAttribute('data-theme');
    themeBtn.textContent = currentTheme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
    themeBtn.addEventListener('click', () => {
        window.toggleTheme();
        const newTheme = document.documentElement.getAttribute('data-theme');
        themeBtn.textContent = newTheme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
    });
    navContainer.appendChild(themeBtn);
    
    // Update dashboard header with session data
    if (isLogged) {
        const headerP = document.querySelector('header p');
        if (headerP && currentPage === 'dashboard.html') {
            headerP.innerHTML = 'Welcome, <strong>' + window.sh_session.name + '</strong> | Enrolment: <mark>' + window.sh_session.enrollment + '</mark> | Sem: ' + window.sh_session.sem;
        }
    }
});