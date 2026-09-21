/* ============================================================
   International Standard Editorial JavaScript
   Version: 3.0 "The Oxford Standard"
   ============================================================ */

(function () {
    'use strict';

    // ============================================================
    // 1. GLOBAL CONFIG
    // ============================================================
    const CONFIG = {
        scrollThreshold: 100,
        animationThreshold: 0.15,
        smoothScrollDuration: 800,
        parallaxStrength: 0.3,
        magneticStrength: 0.3,
        cursorEnabled: window.matchMedia('(hover: hover)').matches,
        reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    };

    // ============================================================
    // 2. UTILITIES
    // ============================================================
    const $ = (selector, context = document) => context.querySelector(selector);
    const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

    const debounce = (fn, delay) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(null, args), delay);
        };
    };

    const throttle = (fn, delay) => {
        let last = 0;
        return (...args) => {
            const now = Date.now();
            if (now - last >= delay) {
                last = now;
                fn.apply(null, args);
            }
        };
    };

    // Easing functions
    const easing = {
        easeOutExpo: (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t)),
        easeOutQuart: (t) => 1 - Math.pow(1 - t, 4),
        easeInOutCubic: (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2,
    };

    // ============================================================
    // 3. PAGE LOADER
    // ============================================================
    class PageLoader {
        constructor() {
            this.loader = $('.page-loader');
            this.content = $('.page-content');
        }

        init() {
            window.addEventListener('load', () => {
                setTimeout(() => {
                    if (this.loader) {
                        this.loader.classList.add('hidden');
                        setTimeout(() => this.loader.remove(), 600);
                    }
                    if (this.content) this.content.classList.add('page-content');
                }, 200);
            });
        }
    }

    // ============================================================
    // 4. REVEAL ON SCROLL (Intersection Observer)
    // ============================================================
    class RevealObserver {
        constructor() {
            this.elements = $$('.fade-in, .fade-in-left, .fade-in-right, .fade-in-scale, .image-reveal');
            this.observer = null;
        }

        init() {
            if (CONFIG.reducedMotion) {
                this.elements.forEach(el => el.classList.add('visible'));
                return;
            }

            this.observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            this.observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: CONFIG.animationThreshold, rootMargin: '0px 0px -50px 0px' }
            );

            this.elements.forEach((el) => this.observer.observe(el));
        }
    }

    // ============================================================
    // 5. NAVBAR (Shrink on scroll + active state)
    // ============================================================
    class Navbar {
        constructor() {
            this.nav = $('#mainNav');
            this.searchToggle = $('#searchToggle');
            this.searchBar = $('#searchBar');
            this.mobileBtn = $('#mobile-menu-btn');
            this.mobileMenu = $('#mobile-menu');
        }

        init() {
            if (!this.nav) return;

            // Scroll handler for navbar shrink
            const handleScroll = throttle(() => {
                const scrolled = window.scrollY > CONFIG.scrollThreshold;
                this.nav.classList.toggle('nav-scrolled', scrolled);
            }, 50);

            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll();

            // Search toggle
            if (this.searchToggle && this.searchBar) {
                this.searchToggle.addEventListener('click', () => {
                    this.searchBar.classList.toggle('hidden');
                    if (!this.searchBar.classList.contains('hidden')) {
                        const input = this.searchBar.querySelector('input');
                        if (input) setTimeout(() => input.focus(), 300);
                    }
                });
            }

            // Mobile menu
            if (this.mobileBtn && this.mobileMenu) {
                this.mobileBtn.addEventListener('click', () => {
                    this.mobileMenu.classList.toggle('hidden');
                    const icon = this.mobileBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-bars');
                        icon.classList.toggle('fa-times');
                    }
                });
            }

            // Close dropdowns on outside click
            document.addEventListener('click', (e) => {
                if (this.searchBar && !this.searchBar.classList.contains('hidden') &&
                    !this.searchBar.contains(e.target) && !this.searchToggle.contains(e.target)) {
                    this.searchBar.classList.add('hidden');
                }
            });
        }
    }

    // ============================================================
    // 6. BACK TO TOP BUTTON
    // ============================================================
    class BackToTop {
        constructor() {
            this.button = $('#back-to-top');
            this.init();
        }

        init() {
            // Create button if not exists
            if (!this.button) {
                this.button = document.createElement('button');
                this.button.id = 'back-to-top';
                this.button.setAttribute('aria-label', 'Back to top');
                this.button.innerHTML = '<i class="fas fa-arrow-up"></i>';
                document.body.appendChild(this.button);
            }

            // Scroll handler
            window.addEventListener('scroll', throttle(() => {
                this.button.classList.toggle('show', window.scrollY > 400);
            }, 100), { passive: true });

            // Click handler - smooth scroll
            this.button.addEventListener('click', () => {
                this.smoothScrollTo(0);
            });
        }

        smoothScrollTo(target) {
            if (CONFIG.reducedMotion) {
                window.scrollTo(0, target);
                return;
            }

            const start = window.scrollY;
            const distance = target - start;
            const duration = CONFIG.smoothScrollDuration;
            let startTime = null;

            const step = (currentTime) => {
                if (!startTime) startTime = currentTime;
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = easing.easeOutExpo(progress);

                window.scrollTo(0, start + distance * easedProgress);

                if (elapsed < duration) {
                    requestAnimationFrame(step);
                }
            };

            requestAnimationFrame(step);
        }
    }

    // ============================================================
    // 7. READING PROGRESS BAR (untuk artikel detail)
    // ============================================================
    class ReadingProgress {
        constructor() {
            this.progressBar = null;
            this.article = $('article') || $('.prose') || $('[data-article]');
        }

        init() {
            if (!this.article) return;

            this.progressBar = document.createElement('div');
            this.progressBar.className = 'reading-progress';
            document.body.appendChild(this.progressBar);

            window.addEventListener('scroll', throttle(() => this.update(), 50), { passive: true });
        }

        update() {
            if (!this.progressBar || !this.article) return;

            const rect = this.article.getBoundingClientRect();
            const articleTop = rect.top + window.scrollY;
            const articleHeight = rect.height;
            const viewportHeight = window.innerHeight;

            const scrolled = window.scrollY - articleTop + viewportHeight * 0.3;
            const progress = Math.max(0, Math.min(100, (scrolled / articleHeight) * 100));

            this.progressBar.style.width = `${progress}%`;
        }
    }

    // ============================================================
    // 8. SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================================
    class SmoothScroll {
        constructor() {
            this.init();
        }

        init() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="#"]');
                if (!link) return;

                const targetId = link.getAttribute('href');
                if (targetId === '#' || targetId.length < 2) return;

                const target = $(targetId);
                if (!target) return;

                e.preventDefault();
                const targetPosition = target.getBoundingClientRect().top + window.scrollY - 80;

                if (CONFIG.reducedMotion) {
                    window.scrollTo(0, targetPosition);
                } else {
                    this.animateScroll(targetPosition);
                }

                history.pushState(null, null, targetId);
            });
        }

        animateScroll(target) {
            const start = window.scrollY;
            const distance = target - start;
            const duration = 800;
            let startTime = null;

            const step = (currentTime) => {
                if (!startTime) startTime = currentTime;
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = easing.easeOutExpo(progress);

                window.scrollTo(0, start + distance * easedProgress);

                if (elapsed < duration) requestAnimationFrame(step);
            };

            requestAnimationFrame(step);
        }
    }

    // ============================================================
    // 9. NUMBER COUNTER ANIMATION
    // ============================================================
    class CounterAnimation {
        constructor() {
            this.counters = $$('.counter, .stat-num');
        }

        init() {
            if (CONFIG.reducedMotion || this.counters.length === 0) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !entry.target.dataset.counted) {
                        this.animateCounter(entry.target);
                        entry.target.dataset.counted = 'true';
                    }
                });
            }, { threshold: 0.5 });

            this.counters.forEach((counter) => observer.observe(counter));
        }

        animateCounter(element) {
            const text = element.textContent.trim();
            const suffix = text.replace(/[\d.,]/g, '');
            const targetValue = parseFloat(text.replace(/[^\d.]/g, ''));

            if (isNaN(targetValue)) return;

            const duration = 2000;
            const startTime = performance.now();
            const formatNumber = (num) => {
                if (targetValue >= 1000) return Math.floor(num).toLocaleString();
                if (Number.isInteger(targetValue)) return Math.floor(num).toString();
                return num.toFixed(1);
            };

            const update = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = easing.easeOutExpo(progress);
                const currentValue = targetValue * easedProgress;

                element.textContent = formatNumber(currentValue) + suffix;

                if (progress < 1) requestAnimationFrame(update);
            };

            requestAnimationFrame(update);
        }
    }

    // ============================================================
    // 10. PARALLAX EFFECT
    // ============================================================
    class Parallax {
        constructor() {
            this.elements = $$('.parallax');
        }

        init() {
            if (CONFIG.reducedMotion || this.elements.length === 0) return;

            window.addEventListener('scroll', throttle(() => {
                const scrolled = window.scrollY;
                this.elements.forEach((el) => {
                    const speed = parseFloat(el.dataset.parallaxSpeed) || CONFIG.parallaxStrength;
                    const rect = el.getBoundingClientRect();
                    if (rect.bottom > 0 && rect.top < window.innerHeight) {
                        const yPos = -(scrolled * speed);
                        el.style.transform = `translate3d(0, ${yPos}px, 0)`;
                    }
                });
            }, 20), { passive: true });
        }
    }

    // ============================================================
    // 11. MAGNETIC BUTTONS
    // ============================================================
    class MagneticButtons {
        constructor() {
            this.buttons = $$('.btn-primary, .btn-gold, .magnetic-btn');
        }

        init() {
            if (CONFIG.reducedMotion || !CONFIG.cursorEnabled) return;

            this.buttons.forEach((btn) => {
                btn.addEventListener('mousemove', (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    btn.style.transform = `translate(${x * CONFIG.magneticStrength}px, ${y * CONFIG.magneticStrength}px)`;
                });

                btn.addEventListener('mouseleave', () => {
                    btn.style.transform = '';
                });
            });
        }
    }

    // ============================================================
    // 12. CUSTOM CURSOR
    // ============================================================
    class CustomCursor {
        constructor() {
            this.dot = null;
            this.outline = null;
            this.mouseX = 0;
            this.mouseY = 0;
            this.outlineX = 0;
            this.outlineY = 0;
        }

        init() {
            if (!CONFIG.cursorEnabled || CONFIG.reducedMotion) return;

            this.dot = document.createElement('div');
            this.dot.className = 'cursor-dot';
            this.outline = document.createElement('div');
            this.outline.className = 'cursor-outline';
            document.body.appendChild(this.dot);
            document.body.appendChild(this.outline);

            document.addEventListener('mousemove', (e) => {
                this.mouseX = e.clientX;
                this.mouseY = e.clientY;
                this.dot.style.left = this.mouseX + 'px';
                this.dot.style.top = this.mouseY + 'px';
            });

            // Smooth follow for outline
            const animate = () => {
                this.outlineX += (this.mouseX - this.outlineX) * 0.15;
                this.outlineY += (this.mouseY - this.outlineY) * 0.15;
                this.outline.style.left = this.outlineX + 'px';
                this.outline.style.top = this.outlineY + 'px';
                requestAnimationFrame(animate);
            };
            animate();

            // Hover effects
            const hoverTargets = $$('a, button, input, textarea, .hover-lift');
            hoverTargets.forEach((el) => {
                el.addEventListener('mouseenter', () => {
                    document.body.classList.add('cursor-hover');
                });
                el.addEventListener('mouseleave', () => {
                    document.body.classList.remove('cursor-hover');
                });
            });
        }
    }

    // ============================================================
    // 13. ACCORDION
    // ============================================================
    class Accordion {
        constructor() {
            this.items = $$('.accordion-item');
        }

        init() {
            this.items.forEach((item) => {
                const trigger = item.querySelector('.accordion-trigger');
                if (!trigger) return;

                trigger.addEventListener('click', () => {
                    const isOpen = item.classList.contains('open');
                    
                    // Close all (optional: remove this for multi-open)
                    this.items.forEach((i) => i.classList.remove('open'));
                    
                    if (!isOpen) item.classList.add('open');
                });
            });
        }
    }

    // ============================================================
    // 14. TABS
    // ============================================================
    class Tabs {
        constructor() {
            this.groups = $$('[data-tabs]');
        }

        init() {
            this.groups.forEach((group) => {
                const buttons = group.querySelectorAll('.tab-button');
                const panels = group.querySelectorAll('.tab-panel');

                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const target = btn.dataset.tab;
                        buttons.forEach((b) => b.classList.remove('active'));
                        panels.forEach((p) => p.classList.remove('active'));
                        btn.classList.add('active');
                        const panel = group.querySelector(`#${target}`);
                        if (panel) panel.classList.add('active');
                    });
                });
            });
        }
    }

    // ============================================================
    // 15. TOAST NOTIFICATION SYSTEM
    // ============================================================
    class ToastSystem {
        constructor() {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }

        show(message, type = 'info', duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `<div style="display:flex;align-items:center;gap:12px;">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>`;
            this.container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        }
    }

    // ============================================================
    // 16. LAZY LOADING IMAGES
    // ============================================================
    class LazyLoad {
        constructor() {
            this.images = $$('img[data-src]');
        }

        init() {
            if (this.images.length === 0) return;

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    });
                });
                this.images.forEach((img) => observer.observe(img));
            } else {
                this.images.forEach((img) => {
                    img.src = img.dataset.src;
                });
            }
        }
    }

    // ============================================================
    // 17. IMAGE LIGHTBOX
    // ============================================================
    class Lightbox {
        constructor() {
            this.images = $$('[data-lightbox] img, .lightbox-trigger img');
        }

        init() {
            if (this.images.length === 0) return;

            this.images.forEach((img) => {
                img.style.cursor = 'zoom-in';
                img.addEventListener('click', () => this.open(img.src));
            });
        }

        open(src) {
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed; inset: 0; background: rgba(11, 34, 57, 0.95);
                display: flex; align-items: center; justify-content: center;
                z-index: 99999; cursor: zoom-out; padding: 2rem;
                animation: fadeIn 0.3s ease;
            `;
            const img = document.createElement('img');
            img.src = src;
            img.style.cssText = 'max-width: 90vw; max-height: 90vh; object-fit: contain;';
            overlay.appendChild(img);
            document.body.appendChild(overlay);

            overlay.addEventListener('click', () => {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 300);
            });
        }
    }

    // ============================================================
    // 18. SCROLL INDICATOR
    // ============================================================
    class ScrollIndicator {
        constructor() {
            this.indicator = null;
        }

        init() {
            if (window.innerWidth < 768) return;

            this.indicator = document.createElement('div');
            this.indicator.className = 'scroll-indicator';
            this.indicator.innerHTML = '<div class="scroll-indicator-progress"></div>';
            document.body.appendChild(this.indicator);

            window.addEventListener('scroll', throttle(() => this.update(), 50), { passive: true });
        }

        update() {
            const scrolled = window.scrollY;
            const total = document.documentElement.scrollHeight - window.innerHeight;
            const progress = total > 0 ? (scrolled / total) * 100 : 0;
            const progressBar = this.indicator.querySelector('.scroll-indicator-progress');
            if (progressBar) progressBar.style.height = `${progress}%`;
        }
    }

    // ============================================================
    // 19. TEXT SPLIT ANIMATION (untuk hero titles)
    // ============================================================
    class TextSplit {
        constructor() {
            this.elements = $$('[data-split-text]');
        }

        init() {
            if (CONFIG.reducedMotion) return;

            this.elements.forEach((el) => {
                const text = el.textContent;
                el.textContent = '';
                const words = text.split(' ');
                words.forEach((word, i) => {
                    const span = document.createElement('span');
                    span.style.cssText = 'display: inline-block; opacity: 0; transform: translateY(20px); transition: all 0.6s ease;';
                    span.style.transitionDelay = `${i * 0.05}s`;
                    span.textContent = word + ' ';
                    el.appendChild(span);
                });

                setTimeout(() => {
                    el.querySelectorAll('span').forEach((span) => {
                        span.style.opacity = '1';
                        span.style.transform = 'translateY(0)';
                    });
                }, 300);
            });
        }
    }

    // ============================================================
    // 20. TYPING EFFECT
    // ============================================================
    class TypingEffect {
        constructor() {
            this.elements = $$('[data-typing]');
        }

        init() {
            if (CONFIG.reducedMotion) return;

            this.elements.forEach((el) => {
                const text = el.dataset.typing;
                el.textContent = '';
                el.classList.add('typing-cursor');
                let i = 0;
                const type = () => {
                    if (i < text.length) {
                        el.textContent += text.charAt(i);
                        i++;
                        setTimeout(type, 50 + Math.random() * 50);
                    }
                };
                setTimeout(type, 500);
            });
        }
    }

    // ============================================================
    // 21. FORM VALIDATION
    // ============================================================
    class FormValidation {
        constructor() {
            this.forms = $$('form[data-validate]');
        }

        init() {
            this.forms.forEach((form) => {
                form.addEventListener('submit', (e) => {
                    let valid = true;
                    const inputs = form.querySelectorAll('[required]');
                    inputs.forEach((input) => {
                        if (!input.value.trim()) {
                            valid = false;
                            input.style.borderColor = '#dc2626';
                        } else {
                            input.style.borderColor = '';
                        }
                    });
                    if (!valid) {
                        e.preventDefault();
                        if (window.toastSystem) {
                            window.toastSystem.show('Mohon lengkapi semua field yang wajib diisi.', 'error');
                        }
                    }
                });
            });
        }
    }

    // ============================================================
    // 22. COPY TO CLIPBOARD
    // ============================================================
    class CopyToClipboard {
        constructor() {
            this.elements = $$('[data-copy]');
        }

        init() {
            this.elements.forEach((el) => {
                el.style.cursor = 'pointer';
                el.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(el.dataset.copy);
                        if (window.toastSystem) {
                            window.toastSystem.show('Berhasil disalin ke clipboard!', 'success');
                        }
                    } catch (err) {
                        console.error('Copy failed:', err);
                    }
                });
            });
        }
    }

    // ============================================================
    // 23. DYNAMIC YEAR UPDATE
    // ============================================================
    class DynamicYear {
        constructor() {
            this.elements = $$('[data-year]');
        }

        init() {
            const year = new Date().getFullYear();
            this.elements.forEach((el) => {
                el.textContent = year;
            });
        }
    }

    // ============================================================
    // 24. ENHANCE ARTICLE CARDS
    // ============================================================
    class ArticleEnhancer {
        constructor() {
            this.articles = $$('article, .news-card, [class*="hover-lift"]');
        }

        init() {
            this.articles.forEach((el) => {
                if (!el.classList.contains('fade-in')) {
                    el.classList.add('fade-in');
                }
            });
        }
    }

    // ============================================================
    // 25. SMOOTH DROPDOWN HOVER (dengan delay untuk UX)
    // ============================================================
    class DropdownEnhancer {
        constructor() {
            this.dropdowns = $$('.dropdown');
        }

        init() {
            this.dropdowns.forEach((dropdown) => {
                let timeout;
                dropdown.addEventListener('mouseenter', () => {
                    clearTimeout(timeout);
                    dropdown.classList.add('open');
                });
                dropdown.addEventListener('mouseleave', () => {
                    timeout = setTimeout(() => {
                        dropdown.classList.remove('open');
                    }, 200);
                });
            });
        }
    }

    // ============================================================
    // 26. INITIALIZE ALL MODULES
    // ============================================================
    class App {
        constructor() {
            this.modules = [];
        }

        add(Module) {
            this.modules.push(new Module());
        }

        init() {
            this.modules.forEach((module) => {
                if (module.init) module.init();
            });
        }
    }

    // ============================================================
    // 27. BOOTSTRAP APPLICATION
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        const app = new App();

        // Core modules
        app.add(PageLoader);
        app.add(Navbar);
        app.add(RevealObserver);
        app.add(SmoothScroll);
        app.add(BackToTop);
        app.add(ReadingProgress);

        // Animation modules
        app.add(CounterAnimation);
        app.add(Parallax);
        app.add(TextSplit);
        app.add(TypingEffect);

        // Interaction modules
        app.add(MagneticButtons);
        app.add(CustomCursor);
        app.add(Accordion);
        app.add(Tabs);
        app.add(DropdownEnhancer);

        // Media modules
        app.add(LazyLoad);
        app.add(Lightbox);

        // UI modules
        app.add(ScrollIndicator);
        app.add(ArticleEnhancer);
        app.add(FormValidation);
        app.add(CopyToClipboard);
        app.add(DynamicYear);

        app.init();

        // Global toast system
        window.toastSystem = new ToastSystem();

        // Console branding
        //console.log('%c🎓 Fakultas Teknik & Ilmu Komputer', 'font-size: 20px; font-weight: bold; color: #C9A227; font-family: Georgia;');
        //console.log('%cInternational Standard Editorial Design System v3.0', 'color: #0B2239; font-size: 12px;');
    });

})();