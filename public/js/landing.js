/**
 * FoodE — Premium Landing Page Interactions
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ===== NAVBAR: Scroll Effect ===== */
    const navbar = document.getElementById('navbar');
    const scrollHandler = () => {
        if (window.scrollY > 60) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', scrollHandler, { passive: true });
    scrollHandler(); // run once on load

    /* ===== HAMBURGER MENU (Mobile) ===== */
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.querySelector('.nav-links');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('nav-open');
            hamburger.setAttribute('aria-expanded', String(isOpen));

            // Animate hamburger lines
            const spans = hamburger.querySelectorAll('span');
            if (isOpen) {
                spans[0].style.transform = 'translateY(7px) rotate(45deg)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
            } else {
                spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
            }
        });

        // Close on nav link click
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('nav-open');
                hamburger.setAttribute('aria-expanded', 'false');
                const spans = hamburger.querySelectorAll('span');
                spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
            });
        });
    }

    // Mobile nav styles
    const mobileNavStyle = document.createElement('style');
    mobileNavStyle.textContent = `
        @media (max-width: 768px) {
            .nav-links.nav-open {
                display: flex !important;
                flex-direction: column;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: rgba(9, 9, 15, 0.97);
                backdrop-filter: blur(24px);
                padding: 24px;
                gap: 8px;
                border-bottom: 1px solid rgba(255,255,255,0.06);
                animation: slideDown 0.2s ease;
                z-index: 999;
            }
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .nav-links.nav-open .nav-link,
            .nav-links.nav-open .nav-btn {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 14px 20px;
            }
        }
    `;
    document.head.appendChild(mobileNavStyle);

    /* ===== SCROLL ANIMATIONS ===== */
    const animatedElements = document.querySelectorAll(
        '.feature-card, .stat-item, .step-card, .section-header'
    );

    animatedElements.forEach(el => {
        el.classList.add('animate-on-scroll');
    });

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, i * 80);
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animatedElements.forEach(el => revealObserver.observe(el));

    /* ===== SMOOTH SCROLLING for anchor links ===== */
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId && targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });

    /* ===== ACTIVE NAV LINK on scroll ===== */
    const sections = document.querySelectorAll('section[id]');
    const navLinkItems = document.querySelectorAll('.nav-link');

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinkItems.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { rootMargin: '-40% 0px -55% 0px' });

    sections.forEach(sec => sectionObserver.observe(sec));

    /* ===== CARD TILT EFFECT (Hero cards) ===== */
    document.querySelectorAll('.showcase-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = `perspective(600px) rotateY(${x * 12}deg) rotateX(${-y * 12}deg) translateY(-8px)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    /* ===== FLOATING INGREDIENT click ripple ===== */
    document.querySelectorAll('.floating-ingredient').forEach(ing => {
        ing.addEventListener('click', () => {
            ing.style.animation = 'none';
            ing.style.transform = 'scale(1.5) rotate(20deg)';
            setTimeout(() => {
                ing.style.transform = '';
                ing.style.animation = '';
            }, 300);
        });
    });

    console.log('%c🍽 FoodE Loaded!', 'color: #ff6b35; font-size: 18px; font-weight: bold;');
});
