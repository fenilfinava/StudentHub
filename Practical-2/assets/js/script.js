document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Notification Banner
    const banner = document.getElementById('notification-banner');
    const closeBannerBtn = document.getElementById('close-banner');
    if (closeBannerBtn && banner) {
        closeBannerBtn.addEventListener('click', () => {
            banner.style.display = 'none';
        });
    }

    // 2. Theme Toggle Button (for index.html only — internal pages use nav.js)
    const themeToggleBtn = document.querySelector('.theme-toggle');
    if (themeToggleBtn) {
        // Update button text based on current theme
        const current = document.documentElement.getAttribute('data-theme');
        themeToggleBtn.textContent = current === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
        
        themeToggleBtn.addEventListener('click', () => {
            window.toggleTheme();
            const newTheme = document.documentElement.getAttribute('data-theme');
            themeToggleBtn.textContent = newTheme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
        });
    }

    // 3. Hamburger Menu (handled solely here — no inline onclick)
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const navLinks = document.getElementById('nav-links');
    if (hamburgerMenu && navLinks) {
        hamburgerMenu.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // 4. Modal Popup
    const modal = document.getElementById('custom-modal');
    const openModalBtn = document.getElementById('open-modal-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');
    
    if (openModalBtn && modal && closeModalBtn) {
        openModalBtn.addEventListener('click', () => {
            modal.classList.add('show-modal');
        });

        closeModalBtn.addEventListener('click', () => {
            modal.classList.remove('show-modal');
        });

        // Close on outside click
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show-modal');
            }
        });
        
        // Close on Escape key
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('show-modal')) {
                modal.classList.remove('show-modal');
            }
        });
    }

    // 5. FAQ Open/Close (Custom JS implementation)
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const isOpen = answer.style.display === 'block';
            
            // Close all others
            document.querySelectorAll('.faq-answer').forEach(ans => ans.style.display = 'none');
            faqQuestions.forEach(q => q.classList.remove('active'));

            if (!isOpen) {
                answer.style.display = 'block';
                question.classList.add('active');
            }
        });
    });

    // 6. Image/Content Slider
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.getElementById('slider-prev');
    const nextBtn = document.getElementById('slider-next');
    let currentSlide = 0;

    function showSlide(index) {
        if (!slides.length) return;
        slides.forEach((slide, i) => {
            slide.style.transform = `translateX(${100 * (i - index)}%)`;
        });
    }

    if (prevBtn && nextBtn && slides.length > 0) {
        showSlide(currentSlide);

        nextBtn.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        });

        prevBtn.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        });
        
        // Auto-play every 5 seconds
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);
    }
});
