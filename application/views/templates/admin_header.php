<?php
$current_uri = uri_string();
$user = $this->session->userdata();
$hour = (int) (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('H');
$greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));

$CI =& get_instance();
$CI->load->model('Setting_model');
$admin_site_name    = site_name();
$admin_site_logo    = $CI->Setting_model->get('site_logo', '');
$admin_site_favicon = $CI->Setting_model->get('site_favicon', '');
$admin_name_short   = trim(explode('&', $admin_site_name)[0]) ?: 'Mission Control';

// FIX: Sub-page active detection
function is_active_menu($current_uri, $menu_key) {
    if ($current_uri === 'admin/' . $menu_key) return true;
    return strpos($current_uri, 'admin/' . $menu_key . '/') === 0;
}

// Notification counts (dummy — replace with actual query)
$notif_counts = [
    'pending_alumni' => isset($pending_alumni_count) ? $pending_alumni_count : 0,
    'new_messages'   => isset($new_messages_count) ? $new_messages_count : 0,
    'pending_posts'  => isset($pending_posts_count) ? $pending_posts_count : 0,
];
$total_notif = array_sum($notif_counts);
?>
<!DOCTYPE html>
<html lang="id" class="admin-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Mission Control') ?> — <?= html_escape($admin_site_name) ?></title>

    <?php if ($admin_site_favicon): ?>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/uploads/' . $admin_site_favicon) ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/uploads/' . $admin_site_favicon) ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600;9..144,700;9..144,800;9..144,900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { 'navy': '#0B2239', 'navy-deep': '#061420', 'navy-light': '#13334F', 'ivory': '#F7F5F0', 'ivory-warm': '#EDE8DE', 'gold': '#C9A227', 'gold-soft': '#D4AF37', 'gold-muted': '#B8941F', 'slate': '#475569' },
                fontFamily: { 'serif': ['Fraunces', 'Georgia', 'serif'], 'sans': ['Inter', 'system-ui', 'sans-serif'], 'mono': ['JetBrains Mono', 'monospace'] },
                letterSpacing: { 'editorial': '0.2em', 'wide-xl': '0.35em' }
            }}}
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">

    <style>
        /* ============================================================
           FIX #1: ROOT SCROLLBAR STABIL (anti-layout-shift)
           ============================================================ */
        html {
            overflow-y: scroll;
            scrollbar-gutter: stable;
        }

        /* ============================================================
           FIX #2: MAIN CONTENT SCROLLBAR STABIL
           Saat klik menu berbeda, tinggi konten berubah →
           scrollbar main muncul/hilang → topbar sticky geser.
           Solusi: paksa scrollbar main selalu ada.
           ============================================================ */
        main.content-scroll {
            overflow-y: scroll;          /* selalu ada scrollbar */
            scrollbar-gutter: stable;    /* lebar konstan */
        }

        /* ============================================================
           FIX #3: TOPBAR ANTI-GESER (lock layout)
           ============================================================ */
        .topbar-right {
            flex-wrap: nowrap !important;
            white-space: nowrap;
            gap: 1rem !important;
        }
        .topbar-right > * {
            flex-shrink: 0 !important;   /* jangan pernah menyusut */
        }

        /* FIX #4: JAM DIKUNCI LEBARNYA */
        #liveClock, #footerClock {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Consolas, monospace !important;
            font-variant-numeric: tabular-nums;   /* ⭐ semua digit lebar sama */
            font-feature-settings: "tnum";
            display: inline-block;
            min-width: 22ch;                      /* ⭐ lebar konstan = "29 Agt 2026 · 14:59:59" */
            max-width: 22ch;
            white-space: nowrap;
            text-align: left;
            letter-spacing: -0.01em;
        }

        body { font-family: 'Inter', sans-serif; background: #F7F5F0; color: #1C1917; transition: background .3s, color .3s; }
        h1, h2, h3, h4, .font-serif { font-family: 'Fraunces', Georgia, serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* =====  DARK MODE ===== */
        html.admin-dark body { background: #0B2239; color: #F7F5F0; }
        html.admin-dark .bg-white { background: #13334F !important; }
        html.admin-dark .bg-ivory { background: #061420 !important; }
        html.admin-dark .bg-gray-50, html.admin-dark .bg-gray-100 { background: #13334F !important; }
        html.admin-dark .text-navy { color: #F7F5F0 !important; }
        html.admin-dark .text-slate { color: #cbd5e1 !important; }
        html.admin-dark .border-gray-200 { border-color: rgba(255,255,255,.08) !important; }
        html.admin-dark .refined-table tbody td { border-color: rgba(255,255,255,.08); color: #cbd5e1; }
        html.admin-dark .refined-table tbody tr:hover { background: rgba(201,162,39,.05); }
        html.admin-dark input, html.admin-dark textarea, html.admin-dark select {
            background: #061420 !important; color: #F7F5F0 !important; border-color: rgba(255,255,255,.1) !important;
        }
        html.admin-dark .mission-card { background: #13334F; border-color: rgba(255,255,255,.08); }

        /* ===== SIDEBAR ===== */
        .sidebar-link { position: relative; transition: all 0.25s ease; border-left: 3px solid transparent; }
        .sidebar-link:hover { background: rgba(247, 245, 240, 0.06); color: #F7F5F0; }
        .sidebar-link.active { background: rgba(201, 162, 39, 0.08); border-left-color: #C9A227; color: #F7F5F0; }
        .sidebar-link.active .nav-icon { color: #C9A227; }
        .hairline-light { border-color: rgba(255, 255, 255, 0.08); }

        /* ===== SCROLLBARS ===== */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0B2239; }
        ::-webkit-scrollbar-thumb { background: #13334F; border-radius: 0; }
        ::-webkit-scrollbar-thumb:hover { background: #C9A227; }
        .content-scroll::-webkit-scrollbar-track { background: #F7F5F0; }
        .content-scroll::-webkit-scrollbar-thumb { background: #0B2239; }
        ::selection { background: #C9A227; color: #0B2239; }

        /* ===== CARDS & TABLES ===== */
        .mission-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(11, 34, 57, 0.08); }
        .mission-card:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(11, 34, 57, 0.08); border-color: rgba(201, 162, 39, 0.3); }
        .stat-num { font-family: 'Fraunces', serif; font-weight: 300; letter-spacing: -0.03em; line-height: 1; }
        .section-label { font-size: 0.65rem; letter-spacing: 0.25em; text-transform: uppercase; font-weight: 600; color: rgba(247, 245, 240, 0.4); padding: 0 1rem; margin-top: 1.5rem; margin-bottom: 0.5rem; }
        .btn-navy { background: #0B2239; color: #F7F5F0; transition: all 0.3s ease; }
        .btn-navy:hover { background: #C9A227; color: #0B2239; }
        .btn-gold { background: #C9A227; color: #0B2239; transition: all 0.3s ease; }
        .btn-gold:hover { background: #B8941F; }
        .btn-outline-navy { border: 1px solid #0B2239; color: #0B2239; transition: all 0.3s ease; }
        .btn-outline-navy:hover { background: #0B2239; color: #F7F5F0; }
        .flash-anim { animation: flashIn 0.4s ease; }
        @keyframes flashIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #0B2239 !important; box-shadow: 0 0 0 2px rgba(11, 34, 57, 0.1); }
        .refined-table thead th { font-size: 0.65rem; letter-spacing: 0.15em; text-transform: uppercase; font-weight: 600; color: #475569; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(11, 34, 57, 0.12); }
        .refined-table tbody td { padding: 1rem; border-bottom: 1px solid rgba(11, 34, 57, 0.06); font-size: 0.875rem; }
        .refined-table tbody tr:hover { background: rgba(247, 245, 240, 0.5); }
        .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(11, 34, 57, 0.1); }

        /* ===== MOBILE SIDEBAR ===== */
        @media (max-width: 767px) {
            #adminSidebar {
                position: fixed; top: 0; left: -100%; bottom: 0; z-index: 50;
                transition: left .3s cubic-bezier(.22,1,.36,1);
            }
            #adminSidebar.open { left: 0; }
        }

        /* ===== COMMAND PALETTE ===== */
        .cmd-palette {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(6,20,32,.85); backdrop-filter: blur(8px);
            display: none; align-items: flex-start; justify-content: center;
            padding-top: 10vh;
        }
        .cmd-palette.open { display: flex; animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .cmd-palette-box {
            background: #fff; width: 90%; max-width: 640px;
            box-shadow: 0 24px 64px rgba(0,0,0,.3); border-radius: 12px;
            overflow: hidden; border: 1px solid rgba(201,162,39,.3);
        }
        html.admin-dark .cmd-palette-box { background: #13334F; border-color: rgba(201,162,39,.5); }
        .cmd-palette-input {
            width: 100%; padding: 20px 24px; border: none; font-size: 18px;
            background: transparent; outline: none; color: inherit;
        }
        .cmd-palette-results { max-height: 400px; overflow-y: auto; border-top: 1px solid rgba(11,34,57,.1); }
        .cmd-palette-item {
            padding: 12px 24px; display: flex; align-items: center; gap: 12px;
            cursor: pointer; transition: background .15s; text-decoration: none;
            color: inherit;
        }
        .cmd-palette-item:hover, .cmd-palette-item.active { background: rgba(201,162,39,.1); }
        .cmd-palette-item i { width: 20px; color: #C9A227; }
        .cmd-palette-hint {
            padding: 12px 24px; background: rgba(247,245,240,.5); font-size: 11px;
            color: #64748b; display: flex; justify-content: center; gap: 8px; align-items: center;
        }
        html.admin-dark .cmd-palette-hint { background: #061420; color: #94a3b8; }
        .cmd-kbd {
            padding: 2px 8px; background: #fff; border: 1px solid #cbd5e1;
            border-radius: 3px; font-family: 'JetBrains Mono', monospace;
            font-size: 10px; color: #475569;
        }
        html.admin-dark .cmd-kbd { background: #13334F; border-color: #475569; color: #cbd5e1; }

        /* ===== NOTIFICATION DROPDOWN ===== */
        .notif-dropdown {
            position: absolute; top: 100%; right: 0; margin-top: 8px;
            width: 340px; background: #fff; border: 1px solid #e5e7eb;
            box-shadow: 0 12px 32px rgba(0,0,0,.15); border-radius: 8px;
            display: none; z-index: 60;
        }
        .notif-dropdown.open { display: block; animation: slideDown .2s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
        html.admin-dark .notif-dropdown { background: #13334F; border-color: rgba(255,255,255,.1); }
        .notif-item {
            padding: 12px 16px; display: flex; gap: 12px; align-items: flex-start;
            border-bottom: 1px solid rgba(11,34,57,.08); cursor: pointer;
            transition: background .15s; text-decoration: none; color: inherit;
        }
        .notif-item:hover { background: rgba(201,162,39,.05); }
        .notif-item:last-child { border-bottom: 0; }
        .notif-badge {
            position: absolute; top: -4px; right: -4px;
            min-width: 18px; height: 18px; padding: 0 5px;
            background: #ef4444; color: #fff; border-radius: 9px;
            font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }

        /* ===== USER DROPDOWN ===== */
        .user-dropdown {
            position: absolute; top: 100%; right: 0; margin-top: 8px;
            width: 240px; background: #fff; border: 1px solid #e5e7eb;
            box-shadow: 0 12px 32px rgba(0,0,0,.15); border-radius: 8px;
            display: none; z-index: 60; overflow: hidden;
        }
        .user-dropdown.open { display: block; animation: slideDown .2s ease; }
        html.admin-dark .user-dropdown { background: #13334F; border-color: rgba(255,255,255,.1); }
        .user-dropdown a {
            padding: 10px 16px; display: flex; align-items: center; gap: 10px;
            color: #0B2239; font-size: 13px; text-decoration: none;
            transition: background .15s;
        }
        html.admin-dark .user-dropdown a { color: #F7F5F0; }
        .user-dropdown a:hover { background: rgba(201,162,39,.08); }
        .user-dropdown a.danger { color: #ef4444; }
        .user-dropdown a.danger:hover { background: rgba(239,68,68,.08); }

        /* ===== IDLE WARNING ===== */
        .idle-warning {
            position: fixed; inset: 0; z-index: 10000;
            background: rgba(6,20,32,.9); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center;
        }
        .idle-warning.open { display: flex; }
        .idle-box {
            background: #fff; padding: 40px; max-width: 480px;
            border-radius: 12px; text-align: center;
            border-top: 4px solid #C9A227;
        }
        html.admin-dark .idle-box { background: #13334F; color: #F7F5F0; }

        /* ===== TOAST ===== */
        .admin-toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9998;
            background: #0B2239; color: #F7F5F0; padding: 12px 20px;
            font-size: 13px; border-radius: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
            transform: translateX(120%); transition: transform .3s;
            border-left: 4px solid #C9A227;
        }
        .admin-toast.show { transform: translateX(0); }
        .admin-toast i { color: #C9A227; margin-right: 8px; }
    </style>
<!-- 🛡️ Token CSRF global (dibaca JavaScript untuk semua POST dinamis) -->
<meta name="csrf-name" content="<?= $this->security->get_csrf_token_name() ?>">
<meta name="csrf-hash" content="<?= $this->security->get_csrf_hash() ?>">
</head>
<body>

<!-- Mobile Topbar -->
<div id="adminTopbar" class="md:hidden bg-navy text-ivory flex items-center justify-between px-4 py-3 sticky top-0 z-40 border-b border-gold/30">
    <div class="flex items-center gap-2">
        <?php if ($admin_site_logo): ?>
            <img src="<?= base_url('assets/uploads/' . $admin_site_logo) ?>" class="w-8 h-8 object-contain bg-white p-0.5" alt="Logo">
        <?php else: ?>
            <div class="w-8 h-8 bg-gold text-navy flex items-center justify-center font-serif font-bold text-sm">F</div>
        <?php endif; ?>
        <span class="font-serif font-semibold tracking-tight">Mission Control</span>
    </div>
    <div class="flex items-center gap-2">
        <button id="cmdToggleMobile" class="w-9 h-9 border border-ivory/20 flex items-center justify-center" aria-label="Search" title="Search (Ctrl+K)">
            <i class="fas fa-search text-sm"></i>
        </button>
        <button id="sidebarToggle" class="w-9 h-9 border border-ivory/20 flex items-center justify-center" aria-label="Buka menu" aria-expanded="false">
            <i class="fas fa-bars text-sm"></i>
        </button>
    </div>
</div>

<div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/60 z-40 md:hidden backdrop-blur-sm"></div>

<div class="flex min-h-screen">

    <!-- ============ SIDEBAR ============ -->
    <aside id="adminSidebar" class="w-64 bg-navy text-ivory flex-shrink-0 flex flex-col">

        <div class="px-6 py-8 border-b hairline-light">
            <div class="flex items-center gap-3">
                <?php if ($admin_site_logo): ?>
                    <img src="<?= base_url('assets/uploads/' . $admin_site_logo) ?>" class="w-10 h-10 object-contain bg-white p-1 border border-gold/30" alt="Logo">
                <?php else: ?>
                    <div class="w-10 h-10 bg-gold text-navy flex items-center justify-center font-serif font-bold text-lg">F</div>
                <?php endif; ?>
                <div class="leading-tight min-w-0 flex-1">
                    <h2 class="font-serif font-semibold text-ivory tracking-tight text-base">Mission Control</h2>
                    <p class="text-[10px] uppercase tracking-editorial text-gold truncate" title="<?= html_escape($admin_site_name) ?>"><?= html_escape($admin_name_short) ?></p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4" id="sidebarNav">

            <p class="section-label">Overview</p>
            <a href="<?= base_url('admin/dashboard') ?>" data-shortcut="1" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'dashboard') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-th-large w-4 text-center nav-icon"></i><span class="flex-1">Dashboard</span><kbd class="text-[9px] text-ivory/30 font-mono">1</kbd>
            </a>
            <a href="<?= base_url('admin/analytics') ?>" data-shortcut="2" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'analytics') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-chart-line w-4 text-center nav-icon"></i><span class="flex-1">Analytics</span><kbd class="text-[9px] text-ivory/30 font-mono">2</kbd>
            </a>

            <p class="section-label">Content</p>
            <a href="<?= base_url('admin/posts') ?>" data-shortcut="3" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'posts') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-newspaper w-4 text-center nav-icon"></i><span class="flex-1">Berita & Pengumuman</span><kbd class="text-[9px] text-ivory/30 font-mono">3</kbd>
            </a>
            <a href="<?= base_url('admin/lecturerblog') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'lecturerblog') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-pen-nib w-4 text-center nav-icon"></i><span>Blog Dosen</span>
            </a>
            <a href="<?= base_url('admin/documents') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'documents') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-file-pdf w-4 text-center nav-icon"></i><span>Dokumen</span>
            </a>
            <a href="<?= base_url('admin/research') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'research') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-flask w-4 text-center nav-icon"></i><span>Riset & Pengabdian</span>
            </a>

            <p class="section-label">Academic</p>
            <a href="<?= base_url('admin/programs') ?>" data-shortcut="4" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'programs') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-graduation-cap w-4 text-center nav-icon"></i><span class="flex-1">Program Studi</span><kbd class="text-[9px] text-ivory/30 font-mono">4</kbd>
            </a>
            <a href="<?= base_url('admin/courses') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'courses') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-book w-4 text-center nav-icon"></i><span>Mata Kuliah</span>
            </a>
            <a href="<?= base_url('admin/curriculum') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'curriculum') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-layer-group w-4 text-center nav-icon"></i><span>Kurikulum</span>
            </a>
            <a href="<?= base_url('admin/kalender') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'kalender') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-calendar-alt w-4 text-center nav-icon"></i><span>Kalender Akademik</span>
            </a>

            <p class="section-label">People & Engagement</p>
            <a href="<?= base_url('admin/lecturers') ?>" data-shortcut="5" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'lecturers') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-user-tie w-4 text-center nav-icon"></i><span class="flex-1">Dosen</span><kbd class="text-[9px] text-ivory/30 font-mono">5</kbd>
            </a>
            <a href="<?= base_url('admin/alumni') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'alumni') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-id-badge w-4 text-center nav-icon"></i><span>Alumni</span>
                <?php if ($notif_counts['pending_alumni'] > 0): ?>
                <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center"><?= $notif_counts['pending_alumni'] ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= base_url('admin/achievements') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'achievements') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-trophy w-4 text-center nav-icon"></i><span>Prestasi Mahasiswa</span>
            </a>
            <a href="<?= base_url('admin/portal') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'portal') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-user-graduate w-4 text-center nav-icon"></i><span>Portal Mahasiswa</span>
            </a>
            <a href="<?= base_url('admin/tracer') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'tracer') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-poll w-4 text-center nav-icon"></i><span>Tracer Study</span>
            </a>
            <a href="<?= base_url('admin/elearning') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'elearning') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-laptop-code w-4 text-center nav-icon"></i><span>E-Learning</span>
            </a>
            <a href="<?= base_url('admin/facilities') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'facilities') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-building w-4 text-center nav-icon"></i><span>Fasilitas</span>
            </a>

            <p class="section-label">Administration</p>
            <a href="<?= base_url('admin/users') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'users') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-users-cog w-4 text-center nav-icon"></i><span>Manajemen Users</span>
            </a>

            <p class="section-label">Configuration</p>
            <a href="<?= base_url('admin/profile') ?>" data-shortcut="6" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'profile') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-university w-4 text-center nav-icon"></i><span class="flex-1">Profil Fakultas</span><kbd class="text-[9px] text-ivory/30 font-mono">6</kbd>
            </a>
            <a href="<?= base_url('admin/appearance') ?>" data-shortcut="7" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'appearance') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-palette w-4 text-center nav-icon"></i><span class="flex-1">Tampilan Website</span><kbd class="text-[9px] text-ivory/30 font-mono">7</kbd>
            </a>

            <p class="section-label">Sistem</p>
            <a href="<?= base_url('admin/backup') ?>" data-shortcut="8" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'backup') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-database w-4 text-center nav-icon"></i><span class="flex-1">Backup & Restore</span><kbd class="text-[9px] text-ivory/30 font-mono">8</kbd>
            </a>
            <a href="<?= base_url('admin/update') ?>" data-shortcut="9" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm <?= is_active_menu($current_uri, 'update') ? 'active' : 'text-ivory/70' ?>">
                <i class="fas fa-sync-alt w-4 text-center nav-icon"></i><span class="flex-1">Update Sistem</span><kbd class="text-[9px] text-ivory/30 font-mono">9</kbd>
            </a>

            <p class="section-label">Actions</p>
            <a href="<?= base_url() ?>" target="_blank" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm text-ivory/70 hover:text-gold">
                <i class="fas fa-external-link-alt w-4 text-center nav-icon"></i><span>Lihat Website</span>
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="sidebar-link flex items-center gap-3 px-6 py-2.5 text-sm text-red-300 hover:text-red-200 hover:bg-red-900/20">
                <i class="fas fa-sign-out-alt w-4 text-center nav-icon"></i><span>Logout</span>
            </a>
        </nav>

        <div class="p-5 border-t hairline-light bg-navy-deep">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gold text-navy flex items-center justify-center font-serif font-bold rounded-full flex-shrink-0">
                    <?= strtoupper(substr($user['full_name'] ?? $user['username'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-ivory truncate"><?= html_escape($user['full_name'] ?? $user['username'] ?? 'Admin') ?></p>
                    <p class="text-[10px] uppercase tracking-editorial text-gold"><?= ucfirst(str_replace('_', ' ', $user['role'] ?? 'editor')) ?></p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="flex-1 min-w-0 content-scroll">

        <div class="hidden md:flex justify-between items-center px-10 py-5 border-b border-gray-200/60 bg-white/50 backdrop-blur-sm sticky top-0 z-30">
            <div class="text-sm">
                <p class="text-slate"><?= $greeting ?>,</p>
                <p class="font-serif font-semibold text-navy text-lg tracking-tight"><?= html_escape($user['full_name'] ?? $user['username'] ?? 'Admin') ?></p>
            </div>
            <div class="topbar-right flex items-center gap-4 text-xs text-slate">
                <!-- SEARCH TRIGGER -->
                <button id="cmdToggleDesktop" class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-md hover:border-gold transition" title="Ctrl+K">
                    <i class="fas fa-search text-slate"></i>
                    <span class="hidden lg:inline">Search...</span>
                    <kbd class="cmd-kbd">Ctrl+K</kbd>
                </button>

                <!-- DARK MODE TOGGLE -->
                <button id="themeToggle" class="w-9 h-9 border border-gray-200 rounded-md hover:border-gold transition flex items-center justify-center" title="Toggle dark mode" aria-label="Toggle dark mode">
                    <i class="fas fa-moon text-slate" id="themeIcon"></i>
                </button>

                <!-- NOTIFICATION BELL -->
                <div class="relative">
                    <button id="notifToggle" class="w-9 h-9 border border-gray-200 rounded-md hover:border-gold transition flex items-center justify-center relative" aria-label="Notifications" aria-expanded="false">
                        <i class="fas fa-bell text-slate"></i>
                        <?php if ($total_notif > 0): ?>
                        <span class="notif-badge"><?= $total_notif ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown" role="menu">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-serif font-semibold text-navy">Notifications</span>
                            <span class="text-[10px] uppercase tracking-wider text-slate"><?= $total_notif ?> new</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <?php if ($notif_counts['pending_alumni'] > 0): ?>
                            <a href="<?= base_url('admin/alumni?status=pending') ?>" class="notif-item">
                                <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user-clock text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-navy"><?= $notif_counts['pending_alumni'] ?> alumni pending approval</p>
                                    <p class="text-[10px] text-slate mt-0.5">Menunggu verifikasi admin</p>
                                </div>
                            </a>
                            <?php endif; ?>
                            <?php if ($notif_counts['new_messages'] > 0): ?>
                            <a href="<?= base_url('admin/messages') ?>" class="notif-item">
                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-navy"><?= $notif_counts['new_messages'] ?> pesan baru</p>
                                    <p class="text-[10px] text-slate mt-0.5">Dari form kontak website</p>
                                </div>
                            </a>
                            <?php endif; ?>
                            <?php if ($notif_counts['pending_posts'] > 0): ?>
                            <a href="<?= base_url('admin/posts?status=draft') ?>" class="notif-item">
                                <div class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-newspaper text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-navy"><?= $notif_counts['pending_posts'] ?> artikel draft</p>
                                    <p class="text-[10px] text-slate mt-0.5">Siap dipublikasikan</p>
                                </div>
                            </a>
                            <?php endif; ?>
                            <?php if ($total_notif === 0): ?>
                            <div class="p-8 text-center text-slate">
                                <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm">Tidak ada notifikasi</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('admin/notifications') ?>" class="block px-4 py-3 text-center text-xs text-gold-muted font-semibold hover:bg-ivory-warm transition border-t border-gray-100">
                            View all notifications
                        </a>
                    </div>
                </div>

                <span class="font-mono" id="liveClock"><?= date('d M Y · H:i:s') ?></span>
                <span class="text-gray-300">|</span>

                <!-- USER DROPDOWN -->
                <div class="relative">
                    <button id="userToggle" class="flex items-center gap-2 hover:bg-ivory-warm/50 px-2 py-1.5 rounded-md transition" aria-expanded="false" aria-haspopup="true">
                        <div class="w-8 h-8 bg-gold text-navy rounded-full flex items-center justify-center font-serif font-bold text-sm flex-shrink-0">
                            <?= strtoupper(substr($user['full_name'] ?? $user['username'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div class="text-left hidden xl:block">
                            <p class="text-[11px] font-semibold text-navy truncate max-w-[120px]"><?= html_escape($user['full_name'] ?? $user['username'] ?? 'Admin') ?></p>
                            <p class="text-[9px] uppercase tracking-wider text-slate"><?= ucfirst(str_replace('_', ' ', $user['role'] ?? 'editor')) ?></p>
                        </div>
                        <i class="fas fa-chevron-down text-[9px] text-slate"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown" role="menu">
                        <a href="<?= base_url('admin/users/profile') ?>" role="menuitem">
                            <i class="fas fa-user w-4 text-slate"></i><span>My Profile</span>
                        </a>
                        <a href="<?= base_url('admin/users/settings') ?>" role="menuitem">
                            <i class="fas fa-cog w-4 text-slate"></i><span>Account Settings</span>
                        </a>
                        <a href="<?= base_url('admin/users/activity') ?>" role="menuitem">
                            <i class="fas fa-history w-4 text-slate"></i><span>Activity Log</span>
                        </a>
                        <div class="h-px bg-gray-100 my-1"></div>
                        <a href="<?= base_url('auth/logout') ?>" class="danger" role="menuitem">
                            <i class="fas fa-sign-out-alt w-4"></i><span>Logout</span>
                        </a>
                    </div>
                </div>

                <span class="uppercase tracking-editorial hidden lg:inline">
                    <i class="fas fa-circle text-green-500 text-[6px] mr-1.5 animate-pulse"></i>Online
                </span>
            </div>
        </div>

        <div class="p-4 md:p-10">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-3.5 mb-6 flex items-start gap-3 flash-anim">
                    <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                    <div class="flex-1 text-sm"><?= $this->session->flashdata('success') ?></div>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 flash-anim">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                    <div class="flex-1 text-sm"><?= $this->session->flashdata('error') ?></div>
                </div>
            <?php endif; ?>