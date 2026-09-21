/* ============================================================
   ADMIN PANEL - MISSION CONTROL JAVASCRIPT
   Version: 3.0
   ============================================================ */

(function () {
    'use strict';

    // ============================================================
    // 1. SIDEBAR TOGGLE (MOBILE)
    // ============================================================
    class SidebarToggle {
        constructor() {
            this.sidebar = document.getElementById('adminSidebar');
            this.toggle = document.getElementById('sidebarToggle');
            this.overlay = document.getElementById('sidebarOverlay');
        }

        init() {
            if (!this.toggle || !this.sidebar) return;

            this.toggle.addEventListener('click', () => {
                this.sidebar.classList.toggle('open');
                if (this.overlay) this.overlay.classList.toggle('hidden');
            });

            if (this.overlay) {
                this.overlay.addEventListener('click', () => {
                    this.sidebar.classList.remove('open');
                    this.overlay.classList.add('hidden');
                });
            }
        }
    }

    // ============================================================
    // 2. AUTO-SAVE INDICATOR
    // ============================================================
    class AutoSave {
        constructor() {
            this.forms = document.querySelectorAll('form');
        }

        init() {
            this.forms.forEach((form) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (!submitBtn) return;

                form.addEventListener('submit', () => {
                    submitBtn.classList.add('btn-saving');
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                    
                    setTimeout(() => {
                        submitBtn.classList.remove('btn-saving');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
                });
            });
        }
    }

    // ============================================================
    // 3. IMAGE PREVIEW (untuk upload foto)
    // ============================================================
    class ImagePreview {
        constructor() {
            this.inputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        }

        init() {
            this.inputs.forEach((input) => {
                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        // Find or create preview element
                        let preview = input.parentElement.querySelector('.preview-img');
                        if (!preview) {
                            preview = document.createElement('img');
                            preview.className = 'preview-img mt-3 w-32 h-32 object-cover border-2 border-gray-200';
                            input.parentElement.appendChild(preview);
                        }
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            });
        }
    }

    // ============================================================
    // 4. CONFIRM DELETE (custom dialog)
    // ============================================================
    class ConfirmDelete {
        constructor() {
            this.forms = document.querySelectorAll('form[onsubmit*="confirm"]');
        }

        init() {
            this.forms.forEach((form) => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const message = form.getAttribute('onsubmit').match(/'([^']+)'/)?.[1] || 'Yakin ingin menghapus?';
                    
                    if (this.showDialog(message)) {
                        form.submit();
                    }
                });
                // Remove old onsubmit
                form.removeAttribute('onsubmit');
            });
        }

        showDialog(message) {
            return confirm(message);
        }
    }

    // ============================================================
    // 5. TOAST NOTIFICATIONS
    // ============================================================
    class AdminToast {
        constructor() {
            this.container = document.createElement('div');
            this.container.className = 'admin-toast-container';
            document.body.appendChild(this.container);
        }

        show(message, type = 'success', duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `admin-toast ${type}`;
            
            const icon = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            }[type];

            toast.innerHTML = `
                <div style="display:flex;align-items:center;gap:12px;">
                    <i class="fas fa-${icon}" style="color:var(--${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'gold'});"></i>
                    <div style="flex:1;">
                        <div style="font-size:0.875rem;font-weight:600;">${type.toUpperCase()}</div>
                        <div style="font-size:0.8125rem;color:var(--slate);">${message}</div>
                    </div>
                </div>
            `;
            
            this.container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        }
    }

    // ============================================================
    // 6. AUTO-HIDE FLASH MESSAGES
    // ============================================================
    class FlashAutoHide {
        constructor() {
            this.flashes = document.querySelectorAll('.flash-anim, [class*="bg-green-100"], [class*="bg-red-100"]');
        }

        init() {
            this.flashes.forEach((flash) => {
                setTimeout(() => {
                    flash.style.transition = 'opacity 0.5s, transform 0.5s';
                    flash.style.opacity = '0';
                    flash.style.transform = 'translateY(-10px)';
                    setTimeout(() => flash.remove(), 500);
                }, 5000);
            });
        }
    }

    // ============================================================
    // 7. COUNTER ANIMATION (dashboard stats)
    // ============================================================
    class CounterAnimation {
        constructor() {
            this.counters = document.querySelectorAll('.stat-num');
        }

        init() {
            this.counters.forEach((counter) => {
                const text = counter.textContent;
                const suffix = text.replace(/[\d.,]/g, '');
                const target = parseFloat(text.replace(/[^\d.]/g, ''));
                if (isNaN(target)) return;

                let current = 0;
                const duration = 1500;
                const step = target / (duration / 16);

                const update = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.floor(current).toLocaleString() + suffix;
                        requestAnimationFrame(update);
                    } else {
                        counter.textContent = text;
                    }
                };
                update();
            });
        }
    }

    // ============================================================
    // 8. LIVE CLOCK (dashboard)
    // ============================================================
    class LiveClock {
        constructor() {
            this.clock = document.querySelector('[data-live-clock]');
        }

        init() {
            if (!this.clock) return;
            
            const update = () => {
                const now = new Date();
                this.clock.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            };
            
            update();
            setInterval(update, 1000);
        }
    }

    // ============================================================
    // 9. FORM DIRTY CHECK (prevent accidental leave)
    // ============================================================
    class DirtyCheck {
        constructor() {
            this.forms = document.querySelectorAll('form');
            this.isDirty = false;
        }

        init() {
            this.forms.forEach((form) => {
                form.addEventListener('input', () => {
                    this.isDirty = true;
                });
                form.addEventListener('submit', () => {
                    this.isDirty = false;
                });
            });

            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }
    }

    // ============================================================
    // 10. KEYBOARD SHORTCUTS
    // ============================================================
    class KeyboardShortcuts {
        constructor() {}

        init() {
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + S = Save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    const form = document.querySelector('form');
                    if (form) form.submit();
                }
                // Escape = Close modals
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal.show, .confirm-dialog.show').forEach((m) => {
                        m.classList.remove('show');
                    });
                }
            });
        }
    }

    // ============================================================
    // 11. LIVE SEARCH FILTER (untuk tabel)
    // ============================================================
    class TableFilter {
        constructor() {
            this.tables = document.querySelectorAll('.refined-table');
        }

        init() {
            this.tables.forEach((table) => {
                const searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.placeholder = '🔍 Filter tabel...';
                searchInput.className = 'mb-4 px-4 py-2 border border-gray-200 w-full max-w-xs';
                table.parentElement.insertBefore(searchInput, table);

                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach((row) => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            });
        }
    }

    // ============================================================
    // 12. AUTO-RESIZE TEXTAREA
    // ============================================================
    class TextareaAutoResize {
        constructor() {
            this.textareas = document.querySelectorAll('textarea');
        }

        init() {
            this.textareas.forEach((textarea) => {
                const resize = () => {
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                };
                textarea.addEventListener('input', resize);
                resize();
            });
        }
    }

    // ============================================================
    // 13. SELECT ALL CHECKBOX
    // ============================================================
    class SelectAll {
        constructor() {
            this.triggers = document.querySelectorAll('[data-select-all]');
        }

        init() {
            this.triggers.forEach((trigger) => {
                const target = document.querySelector(trigger.dataset.selectAll);
                if (!target) return;

                trigger.addEventListener('change', () => {
                    target.querySelectorAll('input[type="checkbox"]').forEach((cb) => {
                        cb.checked = trigger.checked;
                    });
                });
            });
        }
    }

    // ============================================================
    // 14. DARK MODE TOGGLE
    // ============================================================
    class DarkMode {
        constructor() {
            this.toggle = document.querySelector('[data-dark-toggle]');
            this.storage = localStorage.getItem('admin-dark-mode');
        }

        init() {
            if (this.storage === 'true') {
                document.body.classList.add('dark-mode');
            }

            if (this.toggle) {
                this.toggle.addEventListener('click', () => {
                    document.body.classList.toggle('dark-mode');
                    localStorage.setItem('admin-dark-mode', document.body.classList.contains('dark-mode'));
                });
            }
        }
    }

    // ============================================================
    // 15. INITIALIZE
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        new SidebarToggle().init();
        new AutoSave().init();
        new ImagePreview().init();
        new ConfirmDelete().init();
        new FlashAutoHide().init();
        new CounterAnimation().init();
        new LiveClock().init();
        new DirtyCheck().init();
        new KeyboardShortcuts().init();
        new TableFilter().init();
        new TextareaAutoResize().init();
        new SelectAll().init();
        new DarkMode().init();

        // Global toast instance
        window.adminToast = new AdminToast();

        // Branding
        console.log('%c🛡️ Mission Control v3.0', 'font-size: 16px; font-weight: bold; color: #C9A227;');
        console.log('%cFaculty Admin Panel', 'color: #0B2239;');
    });

})();