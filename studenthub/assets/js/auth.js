// auth.js — Session & Route Guard
(function() {
    const publicPages = ['index.html', 'login.html', 'register.html', '404.html', ''];
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    let session = null;
    try {
        session = JSON.parse(localStorage.getItem('sh_session') || sessionStorage.getItem('sh_session') || 'null');
    } catch(e) {
        localStorage.removeItem('sh_session');
        sessionStorage.removeItem('sh_session');
    }
    
    if (session) {
        const now = new Date().getTime();
        if (now > session.expiresAt) {
            localStorage.removeItem('sh_session'); sessionStorage.removeItem('sh_session'); session = null;
            if (!publicPages.includes(currentPage)) { window.location.href = 'login.html?reason=expired'; return; }
        } else {
            session.expiresAt = now + 30 * 60 * 1000;
            if (localStorage.getItem('sh_session')) localStorage.setItem('sh_session', JSON.stringify(session));
            else sessionStorage.setItem('sh_session', JSON.stringify(session));
        }
    }
    
    const isPublic = publicPages.includes(currentPage) || currentPage === 'test_connection.php';
    if (!session && !isPublic) { window.location.href = 'login.html?reason=login_required&next=' + encodeURIComponent(currentPage); return; }
    if (session && (currentPage === 'login.html' || currentPage === 'register.html')) { window.location.href = 'dashboard.html'; return; }
    
    document.documentElement.classList.remove('auth-pending');
    window.sh_session = session;
})();

function logout() {
    localStorage.removeItem('sh_session');
    sessionStorage.removeItem('sh_session');
    window.location.href = 'login.html?reason=logged_out';
}