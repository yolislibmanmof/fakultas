            <!-- Content continues here -->
        </div><!-- /p-4 md:p-10 -->
    </main>
</div><!-- /flex -->

<?php
$CI =& get_instance();
$CI->load->model('Setting_model');
$footer_name = $CI->Setting_model->get('site_name', 'Fakultas Ilmu Komputer');
?>

<!-- 🔥 COMMAND PALETTE -->
<div class="cmd-palette" id="cmdPalette" role="dialog" aria-label="Quick navigation">
    <div class="cmd-palette-box">
        <input type="text" class="cmd-palette-input" id="cmdInput" placeholder="Type to search pages, actions..." autocomplete="off" aria-label="Search">
        <div class="cmd-palette-results" id="cmdResults"></div>
        <div class="cmd-palette-hint">
            <kbd class="cmd-kbd">↑↓</kbd> navigate
            <kbd class="cmd-kbd">Enter</kbd> select
            <kbd class="cmd-kbd">Esc</kbd> close
        </div>
    </div>
</div>

<!-- 🔥 IDLE WARNING MODAL -->
<div class="idle-warning" id="idleWarning" role="alertdialog" aria-labelledby="idleTitle">
    <div class="idle-box">
        <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clock text-3xl"></i>
        </div>
        <h3 class="font-serif text-2xl font-medium mb-2" id="idleTitle">Session Expiring</h3>
        <p class="text-sm text-slate mb-6" id="idleMsg">Your session will expire in <span id="idleCountdown">60</span> seconds due to inactivity.</p>
        <div class="flex gap-3 justify-center">
            <button id="idleStay" class="btn-gold px-6 py-2.5 text-xs uppercase tracking-editorial font-bold">Stay Logged In</button>
            <a href="<?= base_url('auth/logout') ?>" class="btn-outline-navy px-6 py-2.5 text-xs uppercase tracking-editorial font-bold">Logout Now</a>
        </div>
    </div>
</div>

<!-- 🔥 KEYBOARD SHORTCUTS MODAL -->
<div class="cmd-palette" id="shortcutsModal" role="dialog" aria-label="Keyboard shortcuts">
    <div class="cmd-palette-box" style="max-width: 500px;">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-serif text-xl font-medium text-navy">Keyboard Shortcuts</h3>
            <button onclick="document.getElementById('shortcutsModal').classList.remove('open')" class="w-8 h-8 hover:bg-ivory-warm rounded flex items-center justify-center" aria-label="Close">
                <i class="fas fa-times text-slate"></i>
            </button>
        </div>
        <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Open search</span>
                <div><kbd class="cmd-kbd">Ctrl</kbd> <kbd class="cmd-kbd">K</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Toggle dark mode</span>
                <div><kbd class="cmd-kbd">Ctrl</kbd> <kbd class="cmd-kbd">D</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Show shortcuts</span>
                <div><kbd class="cmd-kbd">?</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Dashboard</span>
                <div><kbd class="cmd-kbd">1</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Analytics</span>
                <div><kbd class="cmd-kbd">2</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Posts</span>
                <div><kbd class="cmd-kbd">3</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Programs</span>
                <div><kbd class="cmd-kbd">4</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Lecturers</span>
                <div><kbd class="cmd-kbd">5</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Faculty Profile</span>
                <div><kbd class="cmd-kbd">6</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">Go to Appearance</span>
                <div><kbd class="cmd-kbd">7</kbd></div>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm">View public website</span>
                <div><kbd class="cmd-kbd">Ctrl</kbd> <kbd class="cmd-kbd">Shift</kbd> <kbd class="cmd-kbd">V</kbd></div>
            </div>
        </div>
    </div>
</div>

<!-- 🔥 TOAST -->
<div class="admin-toast" id="adminToast"><i class="fas fa-check-circle"></i><span id="adminToastMsg"></span></div>

<!-- ============ FOOTER ============ -->
<footer class="bg-navy-deep text-ivory/60 text-xs py-4 border-t border-gold/20">
    <div class="flex flex-col md:flex-row items-center justify-between gap-3 px-6">
        <div class="flex items-center gap-4">
            <p>&copy; <?= date('Y') ?> <?= html_escape($footer_name) ?> — <span class="text-gold font-semibold">Mission Control v4.2</span></p>
            <span class="hidden md:inline text-ivory/30">|</span>
            <!-- 🔥 QUICK ACTIONS -->
            <div class="hidden md:flex items-center gap-2">
                <button onclick="clearCache()" class="hover:text-gold transition" title="Clear cache">
                    <i class="fas fa-broom"></i>
                </button>
                <button onclick="location.reload()" class="hover:text-gold transition" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button onclick="document.getElementById('shortcutsModal').classList.add('open')" class="hover:text-gold transition" title="Keyboard shortcuts">
                    <i class="fas fa-keyboard"></i>
                </button>
                <a href="<?= base_url() ?>" target="_blank" class="hover:text-gold transition" title="View public site">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <p class="font-mono"><span class="text-green-400">●</span> System Online</p>
            <span class="text-ivory/30">|</span>
            <p class="font-mono" id="footerClock"><?= date('H:i:s') ?> WIB</p>
            <span class="text-ivory/30 hidden md:inline">|</span>
            <p class="font-mono hidden md:block"><i class="fas fa-server mr-1"></i>Uptime: <span id="uptimeCounter">0d 0h</span></p>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/admin.js') ?>"></script>

<script>
(function(){
    var BASE = <?= json_encode(base_url()) ?>;

    // ===== 🔥 TOAST =====
    function showToast(msg) {
        var t = document.getElementById('adminToast');
        document.getElementById('adminToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
    window.showToast = showToast;

    // ===== 🔥 MOBILE SIDEBAR TOGGLE (FIX KRITIS!) =====
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');
    var overlay = document.getElementById('sidebarOverlay');

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.add('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'false');
        sidebarToggle.innerHTML = '<i class="fas fa-bars text-sm"></i>';
    }
    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.remove('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'true');
        sidebarToggle.innerHTML = '<i class="fas fa-times text-sm"></i>';
    }

    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', function(){
            if (sidebar.classList.contains('open')) closeSidebar();
            else openSidebar();
        });
        overlay.addEventListener('click', closeSidebar);

        // Close sidebar saat klik link
        sidebar.querySelectorAll('a').forEach(function(a){
            a.addEventListener('click', function(){
                if (window.innerWidth < 768) closeSidebar();
            });
        });
    }

    // ===== 🔥 LIVE CLOCK =====
    function updateClock() {
        var now = new Date();
        var time = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
        var date = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        var headerClock = document.getElementById('liveClock');
        var footerClock = document.getElementById('footerClock');
        if (headerClock) headerClock.textContent = date + ' · ' + time;
        if (footerClock) footerClock.textContent = time + ' WIB';
    }
    setInterval(updateClock, 1000);

    // ===== 🔥 UPTIME COUNTER =====
    var startTime = Date.now();
    function updateUptime() {
        var diff = Date.now() - startTime;
        var days = Math.floor(diff / 86400000);
        var hours = Math.floor((diff % 86400000) / 3600000);
        var el = document.getElementById('uptimeCounter');
        if (el) el.textContent = days + 'd ' + hours + 'h';
    }
    setInterval(updateUptime, 60000);

    // ===== 🔥 DARK MODE TOGGLE =====
    var themeToggle = document.getElementById('themeToggle');
    var themeIcon = document.getElementById('themeIcon');
    var htmlEl = document.documentElement;

    function applyTheme(dark) {
        htmlEl.classList.toggle('admin-dark', dark);
        htmlEl.classList.toggle('admin-light', !dark);
        if (themeIcon) themeIcon.className = dark ? 'fas fa-sun text-gold' : 'fas fa-moon text-slate';
        localStorage.setItem('admin_theme', dark ? 'dark' : 'light');
    }

    // Load saved theme
    var savedTheme = localStorage.getItem('admin_theme');
    if (savedTheme === 'dark') applyTheme(true);

    if (themeToggle) {
        themeToggle.addEventListener('click', function(){
            applyTheme(!htmlEl.classList.contains('admin-dark'));
        });
    }

    // ===== 🔥 COMMAND PALETTE =====
    var cmdPalette = document.getElementById('cmdPalette');
    var cmdInput = document.getElementById('cmdInput');
    var cmdResults = document.getElementById('cmdResults');
    var cmdItems = [
        { title: 'Dashboard', url: BASE + 'admin/dashboard', icon: 'fa-th-large', category: 'Overview' },
        { title: 'Analytics', url: BASE + 'admin/analytics', icon: 'fa-chart-line', category: 'Overview' },
        { title: 'Berita & Pengumuman', url: BASE + 'admin/posts', icon: 'fa-newspaper', category: 'Content' },
        { title: 'Create New Post', url: BASE + 'admin/posts/create', icon: 'fa-plus', category: 'Content' },
        { title: 'Blog Dosen', url: BASE + 'admin/lecturerblog', icon: 'fa-pen-nib', category: 'Content' },
        { title: 'Dokumen', url: BASE + 'admin/documents', icon: 'fa-file-pdf', category: 'Content' },
        { title: 'Upload Document', url: BASE + 'admin/documents/create', icon: 'fa-upload', category: 'Content' },
        { title: 'Riset & Pengabdian', url: BASE + 'admin/research', icon: 'fa-flask', category: 'Content' },
        { title: 'Program Studi', url: BASE + 'admin/programs', icon: 'fa-graduation-cap', category: 'Academic' },
        { title: 'Mata Kuliah', url: BASE + 'admin/courses', icon: 'fa-book', category: 'Academic' },
        { title: 'Kurikulum', url: BASE + 'admin/curriculum', icon: 'fa-layer-group', category: 'Academic' },
        { title: 'Kalender Akademik', url: BASE + 'admin/kalender', icon: 'fa-calendar-alt', category: 'Academic' },
        { title: 'Dosen', url: BASE + 'admin/lecturers', icon: 'fa-user-tie', category: 'People' },
        { title: 'Alumni', url: BASE + 'admin/alumni', icon: 'fa-id-badge', category: 'People' },
        { title: 'Prestasi Mahasiswa', url: BASE + 'admin/achievements', icon: 'fa-trophy', category: 'People' },
        { title: 'Portal Mahasiswa', url: BASE + 'admin/portal', icon: 'fa-user-graduate', category: 'People' },
        { title: 'Tracer Study', url: BASE + 'admin/tracer', icon: 'fa-poll', category: 'People' },
        { title: 'E-Learning', url: BASE + 'admin/elearning', icon: 'fa-laptop-code', category: 'People' },
        { title: 'Fasilitas', url: BASE + 'admin/facilities', icon: 'fa-building', category: 'People' },
        { title: 'Manajemen Users', url: BASE + 'admin/users', icon: 'fa-users-cog', category: 'Admin' },
        { title: 'Profil Fakultas', url: BASE + 'admin/profile', icon: 'fa-university', category: 'Config' },
        { title: 'Tampilan Website', url: BASE + 'admin/appearance', icon: 'fa-palette', category: 'Config' },
        { title: 'My Profile', url: BASE + 'admin/users/profile', icon: 'fa-user', category: 'Account' },
        { title: 'Account Settings', url: BASE + 'admin/users/settings', icon: 'fa-cog', category: 'Account' },
        { title: 'View Public Website', url: BASE, icon: 'fa-external-link-alt', category: 'Actions', external: true },
        { title: 'Logout', url: BASE + 'auth/logout', icon: 'fa-sign-out-alt', category: 'Actions' },
    ];

    var activeIdx = 0;

    function renderCmdResults(filter) {
        filter = (filter || '').toLowerCase();
        var filtered = cmdItems.filter(function(item) {
            return item.title.toLowerCase().indexOf(filter) > -1 || item.category.toLowerCase().indexOf(filter) > -1;
        });
        if (filtered.length === 0) {
            cmdResults.innerHTML = '<div class="p-8 text-center text-slate text-sm">No results found for "' + filter.replace(/</g, '&lt;') + '"</div>';
            return;
        }
        activeIdx = 0;
        cmdResults.innerHTML = filtered.map(function(item, idx) {
            return '<a href="' + item.url + '" ' + (item.external ? 'target="_blank"' : '') + ' class="cmd-palette-item' + (idx === 0 ? ' active' : '') + '" data-idx="' + idx + '"><i class="fas ' + item.icon + '"></i><div class="flex-1 min-w-0"><div class="font-medium text-sm">' + item.title + '</div><div class="text-[10px] uppercase tracking-wider text-slate mt-0.5">' + item.category + '</div></div></a>';
        }).join('');
    }

    function openCmdPalette() {
        cmdPalette.classList.add('open');
        cmdInput.value = '';
        renderCmdResults('');
        setTimeout(function(){ cmdInput.focus(); }, 100);
    }
    function closeCmdPalette() {
        cmdPalette.classList.remove('open');
    }

    document.getElementById('cmdToggleDesktop').addEventListener('click', openCmdPalette);
    var cmdMobile = document.getElementById('cmdToggleMobile');
    if (cmdMobile) cmdMobile.addEventListener('click', openCmdPalette);

    cmdInput.addEventListener('input', function(){ renderCmdResults(this.value); });

    cmdInput.addEventListener('keydown', function(e){
        var items = cmdResults.querySelectorAll('.cmd-palette-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = (activeIdx + 1) % items.length;
            items.forEach(function(it, i){ it.classList.toggle('active', i === activeIdx); });
            items[activeIdx].scrollIntoView({ block: 'nearest' });
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = (activeIdx - 1 + items.length) % items.length;
            items.forEach(function(it, i){ it.classList.toggle('active', i === activeIdx); });
            items[activeIdx].scrollIntoView({ block: 'nearest' });
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            if (items[activeIdx]) items[activeIdx].click();
        }
    });

    cmdPalette.addEventListener('click', function(e){
        if (e.target === cmdPalette) closeCmdPalette();
    });

    // ===== 🔥 NOTIFICATION & USER DROPDOWNS =====
    var notifToggle = document.getElementById('notifToggle');
    var notifDropdown = document.getElementById('notifDropdown');
    var userToggle = document.getElementById('userToggle');
    var userDropdown = document.getElementById('userDropdown');

    function closeAllDropdowns() {
        if (notifDropdown) notifDropdown.classList.remove('open');
        if (userDropdown) userDropdown.classList.remove('open');
        if (notifToggle) notifToggle.setAttribute('aria-expanded', 'false');
        if (userToggle) userToggle.setAttribute('aria-expanded', 'false');
    }

    if (notifToggle && notifDropdown) {
        notifToggle.addEventListener('click', function(e){
            e.stopPropagation();
            var isOpen = notifDropdown.classList.toggle('open');
            if (userDropdown) userDropdown.classList.remove('open');
            notifToggle.setAttribute('aria-expanded', isOpen);
        });
    }

    if (userToggle && userDropdown) {
        userToggle.addEventListener('click', function(e){
            e.stopPropagation();
            var isOpen = userDropdown.classList.toggle('open');
            if (notifDropdown) notifDropdown.classList.remove('open');
            userToggle.setAttribute('aria-expanded', isOpen);
        });
    }

    document.addEventListener('click', function(e){
        if (notifDropdown && !notifDropdown.contains(e.target) && !notifToggle.contains(e.target)) {
            notifDropdown.classList.remove('open');
        }
        if (userDropdown && !userDropdown.contains(e.target) && !userToggle.contains(e.target)) {
            userDropdown.classList.remove('open');
        }
    });

    // ===== 🔥 KEYBOARD SHORTCUTS =====
    var shortcuts = {
        '1': BASE + 'admin/dashboard',
        '2': BASE + 'admin/analytics',
        '3': BASE + 'admin/posts',
        '4': BASE + 'admin/programs',
        '5': BASE + 'admin/lecturers',
        '6': BASE + 'admin/profile',
        '7': BASE + 'admin/appearance',
    };

    document.addEventListener('keydown', function(e){
        var tag = (document.activeElement.tagName || '').toLowerCase();
        var isInput = tag === 'input' || tag === 'textarea' || tag === 'select' || document.activeElement.isContentEditable;

        // Ctrl+K - Command palette
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openCmdPalette();
            return;
        }

        // Ctrl+D - Dark mode
        if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
            e.preventDefault();
            applyTheme(!htmlEl.classList.contains('admin-dark'));
            return;
        }

        // Ctrl+Shift+V - View public site
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'V' || e.key === 'v')) {
            e.preventDefault();
            window.open(BASE, '_blank');
            return;
        }

        // Escape - Close modals
        if (e.key === 'Escape') {
            closeCmdPalette();
            document.getElementById('shortcutsModal').classList.remove('open');
            closeAllDropdowns();
            return;
        }

        // ? - Show shortcuts
        if (e.key === '?' && !isInput) {
            e.preventDefault();
            document.getElementById('shortcutsModal').classList.add('open');
            return;
        }

        // Number shortcuts (1-7)
        if (!isInput && !e.ctrlKey && !e.metaKey && !e.altKey && shortcuts[e.key]) {
            e.preventDefault();
            window.location.href = shortcuts[e.key];
            return;
        }
    });

    // ===== 🔥 SESSION IDLE WARNING =====
    var idleTimeout = 25 * 60 * 1000; // 25 min
    var warningTime = 60 * 1000; // 1 min warning
    var idleTimer, warningTimer;
    var idleWarning = document.getElementById('idleWarning');
    var idleCountdown = document.getElementById('idleCountdown');
    var idleStay = document.getElementById('idleStay');

    function resetIdle() {
        clearTimeout(idleTimer);
        clearTimeout(warningTimer);
        if (idleWarning) idleWarning.classList.remove('open');

        warningTimer = setTimeout(function(){
            if (idleWarning) {
                idleWarning.classList.add('open');
                var remaining = 60;
                idleCountdown.textContent = remaining;
                var countdownInt = setInterval(function(){
                    remaining--;
                    idleCountdown.textContent = remaining;
                    if (remaining <= 0) {
                        clearInterval(countdownInt);
                        window.location.href = BASE + 'auth/logout';
                    }
                }, 1000);
            }
        }, idleTimeout - warningTime);

        idleTimer = setTimeout(function(){
            window.location.href = BASE + 'auth/logout';
        }, idleTimeout);
    }

    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function(evt){
        document.addEventListener(evt, resetIdle);
    });
    resetIdle();

    if (idleStay) {
        idleStay.addEventListener('click', function(){
            // Ping server to extend session
            fetch(BASE + 'auth/ping', { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest'} }).catch(function(){});
            resetIdle();
            showToast('Session extended');
        });
    }

    // ===== 🔥 QUICK ACTIONS =====
    window.clearCache = function(){
        if (!confirm('Clear application cache?')) return;
        fetch(BASE + 'admin/system/clear_cache', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(function(r){ return r.json(); })
        .then(function(d){ showToast(d.success ? 'Cache cleared!' : 'Failed to clear cache'); })
        .catch(function(){ showToast('Network error'); });
    };

})();
</script>

</body>
</html>