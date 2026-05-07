// =============================================
// BANINA Men Wear - Main JavaScript
// =============================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- Mobile Menu Toggle ----
    const navToggle = document.getElementById('navToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    if (navToggle && mobileMenu) {
        navToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            const spans = navToggle.querySelectorAll('span');
            spans[0].style.transform = mobileMenu.classList.contains('open') ? 'rotate(45deg) translate(5px,5px)' : '';
            spans[1].style.opacity = mobileMenu.classList.contains('open') ? '0' : '';
            spans[2].style.transform = mobileMenu.classList.contains('open') ? 'rotate(-45deg) translate(5px,-5px)' : '';
        });
    }

    // ---- Hero Slider ----
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    let currentSlide = 0;
    let slideInterval;

    function goToSlide(n) {
        slides[currentSlide]?.classList.remove('active');
        dots[currentSlide]?.classList.remove('active');
        currentSlide = (n + slides.length) % slides.length;
        slides[currentSlide]?.classList.add('active');
        dots[currentSlide]?.classList.add('active');
    }

    function startSlider() {
        if (slides.length > 1) {
            slideInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
        }
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            clearInterval(slideInterval);
            goToSlide(i);
            startSlider();
        });
    });

    if (slides.length > 0) {
        slides[0].classList.add('active');
        dots[0]?.classList.add('active');
        startSlider();
    }

    // ---- Product Image Thumbnails ----
    const thumbs = document.querySelectorAll('.thumb');
    const mainImg = document.getElementById('mainImage');

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            const src = thumb.dataset.src;
            if (mainImg && src) {
                mainImg.src = src;
                thumbs.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            }
        });
    });

    // ---- Scroll Fade In ----
    const fadeEls = document.querySelectorAll('.fade-in');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    fadeEls.forEach(el => observer.observe(el));

    // ---- Navbar Scroll Effect ----
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar?.classList.toggle('scrolled', window.scrollY > 50);
    });

    // ---- Auto dismiss alerts ----
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 400);
        }, 3500);
    });
});

    // ---- Accordion (global, for pages that don't have inline script) ----
    document.querySelectorAll('.accordion-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const item = trigger.closest('.accordion-item');
            const isOpen = item.classList.contains('open');
            item.closest('.accordion-wrap')?.querySelectorAll('.accordion-item')
                .forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });

    // ---- Tab Switcher (global) ----
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            btn.closest('.tab-switcher').querySelectorAll('.tab-btn')
                .forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            btn.closest('.tab-switcher').querySelectorAll('.tab-panel')
                .forEach(p => p.classList.remove('active'));
            document.getElementById(target)?.classList.add('active');
        });
    });

    // ---- FAQ Category Tabs (global) ----
    document.querySelectorAll('.faq-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.faq-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.accordion-wrap[id^="faq-"]')
                .forEach(w => w.style.display = 'none');
            const target = document.getElementById('faq-' + btn.dataset.faq);
            if (target) target.style.display = 'flex';
        });
    });