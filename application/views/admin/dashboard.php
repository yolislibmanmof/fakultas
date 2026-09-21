<?php
$now  = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$hour = (int)$now->format('H');
if ($hour < 4)       { $greet = 'Selamat Malam'; $greet_emo = '🌙'; }
elseif ($hour < 11)  { $greet = 'Selamat Pagi';  $greet_emo = '☀️'; }
elseif ($hour < 15)  { $greet = 'Selamat Siang'; $greet_emo = '🌤️'; }
elseif ($hour < 18)  { $greet = 'Selamat Sore';  $greet_emo = '🌇'; }
else                 { $greet = 'Selamat Malam'; $greet_emo = '🌙'; }

$days_id   = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$months_id = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
$date_id   = $days_id[$now->format('l')] . ', ' . $now->format('d') . ' ' . $months_id[$now->format('F')] . ' ' . $now->format('Y');
$full_name = $this->session->userdata('full_name') ?? 'Admin';
$weeks     = array_chunk($heatmap, 7);

$trend_icon = $trend_dir == 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
$trend_cls  = $trend_dir == 'up' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50';
?>

<style>
/* ===== DASHBOARD OVERRIDES (pakai html.admin-dark dari header) ===== */
html.admin-dark .refresh-btn { background: #13334F; border-color: rgba(255,255,255,.1); color: #F7F5F0; }
html.admin-dark .chart-export button { background: #13334F; border-color: rgba(255,255,255,.1); color: #F7F5F0; }
html.admin-dark .bg-white { background: #13334F !important; }
html.admin-dark .mission-card { background: #13334F; border-color: rgba(255,255,255,.08); }
html.admin-dark .text-navy { color: #F7F5F0 !important; }
html.admin-dark .text-slate { color: #cbd5e1 !important; }
html.admin-dark textarea, html.admin-dark input, html.admin-dark select {
    background: #061420 !important; color: #F7F5F0 !important; border-color: rgba(255,255,255,.1) !important;
}
html.admin-dark .gauge-tooltip { background: #061420; }
html.admin-dark .sk-panel { background: #13334F; border-color: rgba(255,255,255,.1); }
html.admin-dark .sk-row { color: #cbd5e1; }
html.admin-dark .sk-row kbd { background: #061420; border-color: #475569; color: #F7F5F0; }
html.admin-dark .pulse-live { background: #13334F; border-color: rgba(255,255,255,.1); }
html.admin-dark .heat-modal-card { background: #13334F; }

/* ===== REFRESH BUTTON ===== */
.refresh-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; background: #fff; border: 1px solid #e5e7eb;
    border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 600;
    transition: all .2s;
}
.refresh-btn:hover { background: #F7F5F0; border-color: #C9A227; }
.refresh-btn.loading i { animation: spin 1s linear infinite; }

/* ===== CHART EXPORT ===== */
.chart-export {
    position: absolute; top: 1rem; right: 1rem;
    opacity: 0; transition: opacity .3s;
}
.chart-card:hover .chart-export { opacity: 1; }
.chart-export button {
    width: 32px; height: 32px; background: #fff; border: 1px solid #e5e7eb;
    border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all .2s;
}
.chart-export button:hover { background: #C9A227; color: #fff; border-color: #C9A227; }

/* ===== PERFORMANCE BREAKDOWN TOOLTIP ===== */
.gauge-tooltip {
    position: absolute; background: #0B2239; color: #F7F5F0;
    padding: 1rem 1.25rem; border-radius: 4px; font-size: 11px;
    box-shadow: 0 12px 32px rgba(0,0,0,.3); pointer-events: none;
    opacity: 0; transition: opacity .3s; z-index: 50;
    border-left: 3px solid #C9A227; min-width: 200px;
}
.gauge-tooltip.visible { opacity: 1; }
.gauge-tooltip h4 { font-size: 10px; text-transform: uppercase; letter-spacing: .1em; color: #C9A227; margin-bottom: 8px; }
.gauge-tooltip .breakdown-item { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed rgba(255,255,255,.1); }
.gauge-tooltip .breakdown-item:last-child { border: 0; }
.gauge-tooltip .breakdown-val { font-family: 'JetBrains Mono', monospace; font-weight: 700; }

/* ===== LAST UPDATED ===== */
.last-updated {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 10px; color: #64748b; font-family: 'JetBrains Mono', monospace;
}
.last-updated .dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; animation: pulseDot 2s infinite; }
@keyframes pulseDot { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

/* ===== EXISTING STYLES ===== */
.hero-mesh { position:absolute; inset:0; overflow:hidden; pointer-events:none; }
.hero-mesh::before, .hero-mesh::after, .hero-mesh .blob-3, .hero-mesh .blob-4 {
    content:''; position:absolute; border-radius:50%; filter:blur(80px); opacity:.55; mix-blend-mode:screen;
}
.hero-mesh::before { width:45%; height:70%; top:-20%; left:-10%; background:radial-gradient(circle, rgba(201,162,39,.65), transparent 60%); animation: meshA 14s ease-in-out infinite alternate; }
.hero-mesh::after { width:40%; height:60%; bottom:-25%; right:-5%; background:radial-gradient(circle, rgba(19,51,79,.85), transparent 60%); animation: meshB 18s ease-in-out infinite alternate; }
.hero-mesh .blob-3 { width:30%; height:50%; top:30%; left:50%; background:radial-gradient(circle, rgba(212,175,55,.45), transparent 60%); animation: meshC 16s ease-in-out infinite alternate; }
.hero-mesh .blob-4 { width:25%; height:45%; top:55%; left:25%; background:radial-gradient(circle, rgba(255,246,214,.3), transparent 60%); animation: meshD 20s ease-in-out infinite alternate; }
@keyframes meshA { 0% { transform: translate(0,0) scale(1); } 50% { transform: translate(15%,20%) scale(1.2); } 100% { transform: translate(-10%,30%) scale(.9); } }
@keyframes meshB { 0% { transform: translate(0,0) scale(1); } 50% { transform: translate(-20%,-15%) scale(1.3); } 100% { transform: translate(10%,-25%) scale(.85); } }
@keyframes meshC { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(-30%,25%) scale(1.4); } }
@keyframes meshD { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(25%,-20%) scale(1.2); } }
.shimmer { position:relative; overflow:hidden; background:#fff; }
.shimmer::before { content:''; position:absolute; inset:0; z-index:2; background:linear-gradient(90deg, transparent, rgba(201,162,39,.18), transparent); transform:translateX(-100%); animation:shimmerSweep .9s ease-out; }
@keyframes shimmerSweep { to { transform:translateX(100%); } }
.trend-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:2px; font-family:'JetBrains Mono',monospace; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
.trend-badge.up { background:#ecfdf5; color:#10b981; }
.trend-badge.down { background:#fef2f2; color:#ef4444; }
.chart-tooltip { position:absolute; pointer-events:none; z-index:50; background:#0B2239; color:#F7F5F0; padding:.5rem .75rem; border-radius:2px; font-family:'JetBrains Mono',monospace; font-size:10px; letter-spacing:.1em; box-shadow:0 8px 24px rgba(0,0,0,.25); opacity:0; transform:translateY(8px); transition:all .2s; border-left:3px solid #C9A227; min-width:140px; }
.chart-tooltip.visible { opacity:1; transform:translateY(0); }
.chart-tooltip .tt-label { color:#C9A227; margin-bottom:4px; }
.chart-tooltip .tt-val { font-family:'Fraunces',serif; font-size:20px; font-weight:600; line-height:1; }
.heat-modal { position:fixed; inset:0; z-index:100; background:rgba(6,20,32,.8); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; padding:1rem; }
.heat-modal.open { display:flex; animation:heatIn .3s ease; }
@keyframes heatIn { from { opacity:0; } to { opacity:1; } }
.heat-modal-card { background:#fff; width:100%; max-width:480px; max-height:80vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.3); }
.heat-modal-head { padding:1.25rem 1.5rem; background:#0B2239; color:#F7F5F0; display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #C9A227; }
.heat-modal-head h3 { font-family:'Fraunces',serif; font-weight:500; }
.heat-modal-close { background:none; border:none; color:#C9A227; font-size:20px; cursor:pointer; padding:0; width:28px; height:28px; }
.heat-modal-body { padding:1.25rem 1.5rem; }
.heat-item { display:flex; align-items:center; gap:.75rem; padding:.75rem 0; border-bottom:1px dashed #f1f5f9; text-decoration: none; }
.heat-item:last-child { border-bottom:0; }
.heat-item .hi-icon { width:32px; height:32px; background:#0B2239; color:#C9A227; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.heat-item .hi-title { font-size:.85rem; color:#0B2239; line-height:1.3; }
.heat-item .hi-meta { font-family:'JetBrains Mono',monospace; font-size:10px; color:#64748b; text-transform:uppercase; letter-spacing:.1em; margin-top:2px; }
.heat-empty { text-align:center; padding:2rem; color:#64748b; font-size:.85rem; }
.skb { position:fixed; bottom:1.5rem; right:1.5rem; z-index:60; width:44px; height:44px; background:#0B2239; color:#C9A227; border:1px solid rgba(201,162,39,.4); font-family:'JetBrains Mono',monospace; font-weight:700; font-size:18px; cursor:pointer; box-shadow:0 8px 24px rgba(0,0,0,.2); transition:all .3s; display:flex; align-items:center; justify-content:center; }
.skb:hover { background:#C9A227; color:#0B2239; transform:scale(1.08) rotate(-5deg); }
.sk-panel { position:fixed; bottom:5rem; right:1.5rem; z-index:61; background:#fff; width:320px; max-height:70vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.3); display:none; border:1px solid #e5e7eb; }
.sk-panel.open { display:block; animation:skIn .3s cubic-bezier(.22,1,.36,1); }
@keyframes skIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:none; } }
.sk-panel-head { padding:.9rem 1.2rem; background:#0B2239; color:#F7F5F0; border-bottom:2px solid #C9A227; display:flex; justify-content:space-between; align-items:center; }
.sk-panel-head h3 { font-family:'Fraunces',serif; font-size:14px; font-weight:500; }
.sk-group { padding:.5rem 1.2rem; }
.sk-group-title { font-size:10px; letter-spacing:.25em; text-transform:uppercase; font-weight:700; color:#B8941F; padding:.5rem 0 .25rem; }
.sk-row { display:flex; justify-content:space-between; align-items:center; padding:.4rem 0; font-size:12px; color:#334155; }
.sk-row kbd { background:#F7F5F0; border:1px solid #e5e7eb; border-bottom-width:2px; padding:2px 8px; font-family:'JetBrains Mono',monospace; font-size:10px; color:#0B2239; font-weight:600; }
/* Pulse moved down to avoid overlap with sticky topbar */
.pulse-live { position:fixed; top:160px; right:1.5rem; z-index:55; display:inline-flex; align-items:center; gap:6px; padding:5px 12px; background:#fff; border:1px solid #e5e7eb; font-family:'JetBrains Mono',monospace; font-size:10px; letter-spacing:.2em; text-transform:uppercase; color:#10b981; font-weight:700; box-shadow:0 4px 12px rgba(0,0,0,.08); transition:all .4s; opacity:.7; }
.pulse-live.active { opacity:1; transform:scale(1.05); box-shadow:0 8px 20px rgba(16,185,129,.25); }
.pulse-live .dot { width:6px; height:6px; border-radius:50%; background:#10b981; position:relative; }
.pulse-live .dot::after { content:''; position:absolute; inset:-4px; border-radius:50%; border:2px solid #10b981; animation:pulseRing 2s ease-out infinite; }
@keyframes pulseRing { 0% { transform:scale(.8); opacity:1; } 100% { transform:scale(1.8); opacity:0; } }
.heat-cell-adv { cursor:pointer; transition:all .2s; }
.heat-cell-adv:hover { outline:2px solid #C9A227; outline-offset:1px; transform:scale(1.3); }
.heat-cell-adv.has-data { box-shadow:inset 0 0 0 1px rgba(255,255,255,.1); }
.type-caret { animation: caretBlink 1s steps(1) infinite; color:#C9A227; }
@keyframes caretBlink { 50% { opacity: 0; } }
.spotlight::before { content:''; position:absolute; inset:0; background: radial-gradient(600px circle at var(--mx,50%) var(--my,50%), rgba(201,162,39,.18), transparent 40%); pointer-events:none; }
.tilt { transition: transform .25s ease, box-shadow .25s ease; will-change: transform; }
.shine { position:relative; overflow:hidden; }
.shine::after { content:''; position:absolute; top:0; left:-80%; width:50%; height:100%; background:linear-gradient(105deg,transparent,rgba(255,255,255,.35),transparent); transform:skewX(-20deg); transition:left .6s ease; pointer-events:none; }
.shine:hover::after { left:130%; }
.sparkline { animation: sparkDraw 1.4s ease forwards .3s; }
@keyframes sparkDraw { to { stroke-dashoffset: 0; } }
.chart-line { animation: lineDraw 1.6s ease forwards .5s; }
@keyframes lineDraw { to { stroke-dashoffset: 0; } }
.chart-area { opacity:0; animation: areaFade .8s ease forwards 1.8s; }
@keyframes areaFade { to { opacity:1; } }
.chart-dot { opacity:0; animation: dotPop .4s cubic-bezier(.34,1.56,.64,1) forwards; cursor:pointer; }
@keyframes dotPop { from{opacity:0;transform:scale(0)} to{opacity:1;transform:scale(1)} }
.chart-val { animation: valFade .4s ease forwards 2.1s; pointer-events:none; }
@keyframes valFade { to { opacity:1; } }
.donut-seg { transition: stroke-dasharray 1.2s cubic-bezier(.22,1,.36,1); }
.bar-anim { transition: width 1.4s cubic-bezier(.22,1,.36,1); }
#gaugeArc { transition: stroke-dasharray 1.6s cubic-bezier(.22,1,.36,1); }
#greetName { transition: opacity .2s; }
#greetName:hover { opacity:.8; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<!-- DARK MODE TOGGLE DIHAPUS (pakai yang di header admin) -->

<!-- PULSE INDICATOR -->
<div class="pulse-live" id="pulseLive">
    <span class="dot"></span> LIVE
</div>

<!-- CINEMATIC HERO -->
<div id="heroDash" class="spotlight relative mb-10 overflow-hidden border-b border-gray-200 bg-gradient-to-br from-navy via-navy to-navy-light text-ivory p-10 md:p-14">
    <div class="hero-mesh"><span class="blob-3"></span><span class="blob-4"></span></div>
    <canvas id="heroParticles" class="absolute inset-0 w-full h-full pointer-events-none opacity-60"></canvas>
    <div class="absolute -bottom-20 -right-10 font-serif text-[18rem] leading-none text-ivory/5 select-none pointer-events-none">M</div>

    <div class="relative grid md:grid-cols-3 gap-8 items-end">
        <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <span class="editorial-label text-gold"><?= $greet_emo ?> Mission Control</span>
                <span class="w-10 h-px bg-gold"></span>
                <span class="editorial-label text-ivory/60"><?= $date_id ?> • WIB</span>
            </div>
            <h1 class="font-serif font-light tracking-[-0.02em] leading-[1.05] text-3xl md:text-5xl lg:text-6xl">
                <?= $greet ?>,
                <em class="italic text-gold cursor-pointer" id="greetName" title="Klik untuk putar ulang animasi ketik">
                    <span id="typeTarget" data-name="<?= html_escape($full_name) ?>"><?= html_escape($full_name) ?></span><span class="type-caret">|</span>
                </em>.
            </h1>
            <p class="text-ivory/70 mt-4 max-w-2xl text-base md:text-lg">Berikut ringkasan performa digital <strong class="text-ivory"><?= site_name() ?></strong> hari ini.</p>
            <div class="mt-6 flex flex-wrap gap-2">
                <!-- Trigger Command Palette dari header (Ctrl+K) -->
                <button onclick="document.dispatchEvent(new KeyboardEvent('keydown',{key:'k',ctrlKey:true}))" class="btn-gold px-5 py-3 text-[10px] uppercase tracking-editorial font-bold shine">
                    <i class="fas fa-terminal mr-2"></i>Command Palette <span class="ml-2 font-mono opacity-60">Ctrl K</span>
                </button>
                <a href="<?= base_url('admin/posts/create') ?>" class="border border-ivory/30 text-ivory hover:bg-ivory hover:text-navy px-5 py-3 text-[10px] uppercase tracking-editorial font-bold transition"><i class="fas fa-plus mr-2"></i>Tulis Cepat</a>
                <a href="<?= base_url('admin/dashboard/export') ?>" target="_blank" class="border border-ivory/30 text-ivory hover:bg-ivory hover:text-navy px-5 py-3 text-[10px] uppercase tracking-editorial font-bold transition"><i class="fas fa-file-pdf mr-2"></i>Export PDF</a>
                <button id="refreshBtn" class="refresh-btn" title="Refresh Data">
                    <i class="fas fa-sync-alt"></i><span>Refresh</span>
                </button>
            </div>
        </div>

        <div class="text-right">
            <!-- Rename: liveClock → dashClock (hindari bentrok dengan header) -->
            <div class="font-mono text-5xl md:text-7xl font-light text-gold leading-none tracking-tighter" id="dashClock">--:--</div>
            <div class="font-mono text-xs text-ivory/50 mt-2 uppercase tracking-wider"><span class="text-green-400">●</span> Online • <span id="liveSec">--</span>s WIB</div>
            <div class="last-updated mt-3 text-ivory/40">
                <span class="dot"></span>
                <span id="lastUpdated">Updated just now</span>
            </div>
        </div>
    </div>
</div>

<!-- ACTION CENTER -->
<?php
$action_items = [];
if ($stats['alumni_pending'] > 0) $action_items[] = ['icon'=>'fa-user-clock','label'=>'Alumni Pending Review','count'=>$stats['alumni_pending'],'cls'=>'bg-amber-50 border-amber-200 text-amber-900','url'=>'admin/alumni?status=pending'];
if ($stats['posts_draft'] > 0)     $action_items[] = ['icon'=>'fa-file-alt','label'=>'Draft Berita','count'=>$stats['posts_draft'],'cls'=>'bg-yellow-50 border-yellow-200 text-yellow-900','url'=>'admin/posts?status=draft'];
if (!empty($ongoing_research))     $action_items[] = ['icon'=>'fa-flask','label'=>'Riset Berjalan','count'=>count($ongoing_research),'cls'=>'bg-blue-50 border-blue-200 text-blue-900','url'=>'admin/research?status=ongoing'];
?>
<?php if (!empty($action_items)): ?>
<div class="mb-10">
    <div class="flex items-center gap-3 mb-4">
        <span class="editorial-label text-gold-muted">⚡ Action Center</span>
        <span class="flex-1 h-px bg-gray-200"></span>
        <span class="text-xs text-slate uppercase tracking-wider"><?= count($action_items) ?> butuh perhatian</span>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        <?php foreach ($action_items as $ai): ?>
        <a href="<?= base_url($ai['url']) ?>" class="mission-card tilt <?= $ai['cls'] ?> border-2 p-5 flex items-center gap-4 group shine">
            <div class="w-12 h-12 bg-white/70 flex items-center justify-center flex-shrink-0"><i class="fas <?= $ai['icon'] ?> text-xl"></i></div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm"><?= $ai['label'] ?></p>
                <p class="font-serif text-3xl font-light mt-1 leading-none"><?= $ai['count'] ?></p>
            </div>
            <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition"></i>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- HEALTH + HEATMAP -->
<div class="grid lg:grid-cols-12 gap-6 mb-10">
    <div class="lg:col-span-4 bg-white border border-gray-200 p-8 tilt shimmer relative">
        <p class="editorial-label text-gold-muted mb-2">🩺 Digital Health</p>
        <h2 class="font-serif text-2xl font-light text-navy mb-4">Faculty <em class="italic text-gold-muted">Score</em></h2>
        <div class="relative" id="gaugeContainer">
            <svg viewBox="0 0 200 120" class="w-full">
                <defs>
                    <linearGradient id="ggrad" x1="0" x2="1">
                        <stop offset="0%" stop-color="#C9A227"/><stop offset="100%" stop-color="#0B2239"/>
                    </linearGradient>
                </defs>
                <path d="M20 110 A 80 80 0 0 1 180 110" fill="none" stroke="#EDE8DE" stroke-width="14" stroke-linecap="round"/>
                <path id="gaugeArc" d="M20 110 A 80 80 0 0 1 180 110" fill="none" stroke="url(#ggrad)" stroke-width="14" stroke-linecap="round" stroke-dasharray="0 999" data-target="<?= round(($health_score / 100) * 251.3) ?> 999"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-end pb-1">
                <div class="font-serif text-5xl font-light text-navy leading-none" data-count="<?= $health_score ?>">0</div>
                <div class="text-[10px] uppercase tracking-wider text-slate mt-1">/ 100</div>
            </div>
            <div class="gauge-tooltip" id="gaugeTooltip">
                <h4>Breakdown Skor</h4>
                <div class="breakdown-item"><span>Berita Published</span><span class="breakdown-val"><?= min(30, $stats['posts_published']) ?>/30</span></div>
                <div class="breakdown-item"><span>Dosen Aktif</span><span class="breakdown-val"><?= min(20, $stats['lecturers']) ?>/20</span></div>
                <div class="breakdown-item"><span>Riset</span><span class="breakdown-val"><?= min(25, $stats['research']) ?>/25</span></div>
                <div class="breakdown-item"><span>Alumni</span><span class="breakdown-val"><?= min(15, $stats['alumni_total']) ?>/15</span></div>
                <div class="breakdown-item"><span>Aktivitas 7 Hari</span><span class="breakdown-val"><?= min(10, array_sum(array_column($trend_7d, 'total'))) ?>/10</span></div>
            </div>
        </div>
        <p class="text-xs text-slate text-center mt-3">
            <?php if ($health_score >= 80): ?> Luar biasa! Fakultas sangat sehat digital.<?php elseif ($health_score >= 50): ?>💪 Bagus! Terus tingkatkan konten.<?php else: ?>🌱 Mulai bangun konten & data.<?php endif; ?>
        </p>
    </div>

    <div class="lg:col-span-8 bg-white border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="editorial-label text-gold-muted mb-2">Contribution Graph</p>
                <h2 class="font-serif text-2xl font-light text-navy">12 Minggu <em class="italic text-gold-muted">Aktivitas</em></h2>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] text-slate">
                <span>Sedikit</span>
                <span class="w-3 h-3 bg-[#EDE8DE]"></span><span class="w-3 h-3 bg-[#E5D9A8]"></span><span class="w-3 h-3 bg-[#D4AF37]"></span><span class="w-3 h-3 bg-[#B8941F]"></span><span class="w-3 h-3 bg-navy"></span>
                <span>Banyak</span>
            </div>
        </div>
        <div class="flex gap-1.5 overflow-x-auto pb-2" id="heatmapGrid">
            <?php foreach ($weeks as $week): ?>
            <div class="flex flex-col gap-1.5">
                <?php foreach ($week as $day):
                    $c = $day['c'];
                    $cls = $c <= 0 ? 'bg-[#EDE8DE]' : ($c == 1 ? 'bg-[#E5D9A8]' : ($c == 2 ? 'bg-[#D4AF37]' : ($c <= 4 ? 'bg-[#B8941F]' : 'bg-navy')));
                ?>
                <div class="w-4 h-4 heat-cell-adv <?= $cls ?> <?= $c > 0 ? 'has-data' : '' ?>"
                     data-date="<?= $day['d'] ?>" data-count="<?= $c ?>"
                     title="<?= date('d M Y', strtotime($day['d'])) ?>: <?= $c ?> aktivitas"></div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-xs text-slate mt-2"><i class="fas fa-hand-pointer text-gold-muted mr-1"></i>Hover = tooltip, <strong>klik</strong> = detail aktivitas hari itu.</p>
    </div>
</div>

<!-- KEY STATS -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 md:gap-5 mb-10">
    <?php
    $primary_stats = [
        ['label'=>'Berita Published','value'=>$stats['posts_published'],'sub'=>'+' . $stats['posts_draft'] . ' draft','icon'=>'fa-newspaper','url'=>'admin/posts'],
        ['label'=>'Dosen Aktif','value'=>$stats['lecturers'],'sub'=>'Tenaga pendidik','icon'=>'fa-user-tie','url'=>'admin/lecturers'],
        ['label'=>'Penelitian','value'=>$stats['research'],'sub'=>'Riset & pengabdian','icon'=>'fa-flask','url'=>'admin/research'],
        ['label'=>'Total Views','value'=>number_format($stats['total_views']),'sub'=>'Pembacaan artikel','icon'=>'fa-eye','url'=>'admin/posts'],
        ['label'=>'Alumni Network','value'=>$stats['alumni_total'],'sub'=>($stats['alumni_pending'] > 0 ? '<span class="text-amber-600 font-bold">' . $stats['alumni_pending'] . ' pending</span>' : 'Verified'),'icon'=>'fa-id-badge','url'=>'admin/alumni'],
    ];
    foreach ($primary_stats as $st): ?>
    <a href="<?= base_url($st['url']) ?>" class="mission-card tilt shine bg-white p-6 relative overflow-hidden block shimmer">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-ivory-warm flex items-center justify-center text-gold-muted"><i class="fas <?= $st['icon'] ?>"></i></div>
            <?php if ($st['label'] == 'Berita Published'): ?>
                <span class="trend-badge <?= $trend_dir ?>"><?= $trend_dir == 'up' ? '▲' : '▼' ?> <?= abs($trend_pct) ?>% WoW</span>
            <?php else: ?>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-green-600"><i class="fas fa-arrow-up mr-1"></i>Growth</span>
            <?php endif; ?>
        </div>
        <div class="stat-num text-4xl md:text-5xl text-navy mb-1" data-count="<?= (int)str_replace(['.', ','], '', (string)$st['value']) ?>"><?= $st['value'] ?></div>
        <p class="editorial-label text-slate"><?= $st['label'] ?></p>
        <p class="text-[10px] mt-1"><?= $st['sub'] ?></p>
        <svg viewBox="0 0 100 24" class="w-full h-6 mt-3" preserveAspectRatio="none">
            <?php
            $max_t = max(array_column($trend_7d, 'total')) ?: 1;
            $points = [];
            foreach ($trend_7d as $i => $t) {
                $x = ($i / 6) * 100;
                $y = 22 - (($t['total'] / $max_t) * 18);
                $points[] = "$x,$y";
            }
            ?>
            <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="#C9A227" stroke-width="1.5" class="sparkline" stroke-dasharray="300" stroke-dashoffset="300"/>
        </svg>
    </a>
    <?php endforeach; ?>
</div>

<!-- CHART + DONUT -->
<div class="grid lg:grid-cols-12 gap-6 mb-10">
    <div class="lg:col-span-7 bg-white border border-gray-200 p-8 relative chart-card">
        <div class="chart-export">
            <button id="exportChart" title="Export chart as PNG">
                <i class="fas fa-download text-xs"></i>
            </button>
        </div>
        <div class="flex items-start justify-between mb-6">
            <div>
                <p class="editorial-label text-gold-muted mb-2">Content Momentum</p>
                <h2 class="font-serif text-2xl font-light text-navy">Tren 7 Hari <em class="italic text-gold-muted">Terakhir</em></h2>
            </div>
            <div class="text-right">
                <div class="font-serif text-4xl font-light text-navy" data-count="<?= array_sum(array_column($trend_7d, 'total')) ?>">0</div>
                <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total Activities</p>
                <span class="trend-badge <?= $trend_dir ?> mt-2"><?= $trend_dir == 'up' ? '▲' : '▼' ?> <?= abs($trend_pct) ?>% vs minggu lalu</span>
            </div>
        </div>
        <div class="relative">
            <svg id="trendSvg" viewBox="0 0 700 220" class="w-full h-56" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="areaGrad" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#C9A227" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#C9A227" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <?php for ($y = 0; $y <= 4; $y++): ?>
                <line x1="40" x2="690" y1="<?= 20 + ($y * 45) ?>" y2="<?= 20 + ($y * 45) ?>" stroke="#eee" stroke-dasharray="2 4"/>
                <?php endfor; ?>
                <?php
                $max = max(array_column($trend_7d, 'total')) ?: 1;
                $points = [];
                foreach ($trend_7d as $i => $t) {
                    $x = 40 + ($i * 108);
                    $y = 200 - (($t['total'] / $max) * 170);
                    $points[] = "$x,$y";
                }
                $area = $points; $area[] = '690,200'; $area[] = '40,200';
                ?>
                <polygon points="<?= implode(' ', $area) ?>" fill="url(#areaGrad)" class="chart-area"/>
                <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="#0B2239" stroke-width="2.5" class="chart-line" stroke-dasharray="2000" stroke-dashoffset="2000"/>
                <?php foreach ($trend_7d as $i => $t):
                    $x = 40 + ($i * 108);
                    $y = 200 - (($t['total'] / $max) * 170);
                ?>
                <circle cx="<?= $x ?>" cy="<?= $y ?>" r="5" fill="#C9A227" stroke="#fff" stroke-width="2" class="chart-dot"
                    data-label="<?= $t['label'] ?>" data-date="<?= $t['date'] ?>" data-val="<?= $t['total'] ?>"/>
                <text x="<?= $x ?>" y="215" font-size="10" text-anchor="middle" fill="#475569" font-family="JetBrains Mono, monospace"><?= $t['label'] ?></text>
                <text x="<?= $x ?>" y="<?= $y - 10 ?>" font-size="11" font-weight="600" text-anchor="middle" fill="#0B2239" class="chart-val" opacity="0"><?= $t['total'] ?></text>
                <?php endforeach; ?>
            </svg>
            <div class="chart-tooltip" id="chartTooltip"></div>
        </div>
    </div>

    <div class="lg:col-span-5 bg-white border border-gray-200 p-8">
        <div class="mb-6">
            <p class="editorial-label text-gold-muted mb-2">Content Distribution</p>
            <h2 class="font-serif text-2xl font-light text-navy">Per <em class="italic text-gold-muted">Kategori</em></h2>
        </div>
        <?php
        $total_posts = array_sum(array_map(function($c){ return (int)$c->total; }, $categories_breakdown)) ?: 1;
        $donut_colors = ['#C9A227', '#0B2239', '#B8941F', '#475569', '#13334F', '#D4AF37'];
        $circ = 2 * M_PI * 70;
        $offset = 0;
        ?>
        <div class="flex items-center gap-6">
            <div class="relative flex-shrink-0">
                <svg viewBox="0 0 180 180" class="w-40 h-40 -rotate-90">
                    <circle cx="90" cy="90" r="70" fill="none" stroke="#F7F5F0" stroke-width="20"/>
                    <?php foreach ($categories_breakdown as $i => $cat):
                        $len = ($cat->total / $total_posts) * $circ;
                        $color = $donut_colors[$i % count($donut_colors)];
                    ?>
                    <circle cx="90" cy="90" r="70" fill="none" stroke="<?= $color ?>" stroke-width="20"
                        stroke-dasharray="0 <?= $circ ?>" data-target="<?= $len ?> <?= $circ - $len ?>"
                        stroke-dashoffset="-<?= $offset ?>" class="donut-seg"/>
                    <?php $offset += $len; endforeach; ?>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div class="font-serif text-3xl font-light text-navy leading-none"><?= $total_posts ?></div>
                    <div class="text-[9px] uppercase tracking-wider text-slate mt-1">Total</div>
                </div>
            </div>
            <div class="flex-1 space-y-2 text-sm">
                <?php foreach ($categories_breakdown as $i => $cat):
                    $pct = round(($cat->total / $total_posts) * 100);
                    $color = $donut_colors[$i % count($donut_colors)];
                ?>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 flex-shrink-0" style="background:<?= $color ?>"></span>
                    <span class="text-navy truncate flex-1"><?= html_escape($cat->name) ?></span>
                    <span class="font-mono text-xs text-slate"><?= $cat->total ?> (<?= $pct ?>%)</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- TOP + EVENTS + HEALTH -->
<div class="grid lg:grid-cols-3 gap-6 mb-10">
    <div class="lg:col-span-2 bg-white border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="editorial-label text-gold-muted mb-2">🏆 Top Performers</p>
                <h2 class="font-serif text-2xl font-light text-navy">Artikel Paling <em class="italic text-gold-muted">Dibaca</em></h2>
            </div>
            <a href="<?= base_url('admin/posts') ?>" class="text-xs uppercase tracking-editorial text-navy hover:text-gold font-semibold">Semua &rarr;</a>
        </div>
        <div class="space-y-3">
            <?php if (empty($top_articles)): ?>
                <p class="text-sm text-slate text-center py-6">Belum ada data views.</p>
            <?php else:
                $max_views = max(array_map(function($a){ return (int)$a->views; }, $top_articles)) ?: 1;
                foreach ($top_articles as $idx => $a):
                    $pct = round(($a->views / $max_views) * 100);
            ?>
            <a href="<?= base_url('admin/posts/edit/' . $a->id) ?>" class="group flex items-center gap-4 p-3 hover:bg-ivory-warm/50 transition">
                <div class="w-8 h-8 bg-navy text-gold flex items-center justify-center flex-shrink-0 font-serif font-bold text-sm"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-navy group-hover:text-gold-muted transition line-clamp-1"><?= html_escape($a->title) ?></p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex-1 h-1.5 bg-ivory-warm overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-navy to-gold bar-anim" style="width:0%" data-w="<?= $pct ?>%"></div>
                        </div>
                        <span class="font-mono text-[10px] text-slate font-semibold w-16 text-right"><?= number_format($a->views) ?> views</span>
                    </div>
                </div>
            </a>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-navy text-ivory p-6 relative overflow-hidden">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-gold/10 rounded-full blur-2xl"></div>
            <div class="relative">
                <p class="editorial-label text-gold mb-3">📅 Agenda Dekat</p>
                <?php if (empty($upcoming_events)): ?>
                    <p class="text-sm text-ivory/60">Tidak ada agenda.</p>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($upcoming_events as $e): ?>
                    <div class="flex items-start gap-3 pb-3 border-b border-ivory/10 last:border-0">
                        <div class="w-12 text-center flex-shrink-0">
                            <div class="font-serif text-xl leading-none text-gold"><?= date('d', strtotime($e->start_date)) ?></div>
                            <div class="text-[9px] uppercase tracking-wider text-ivory/60 mt-0.5"><?= date('M', strtotime($e->start_date)) ?></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium truncate"><?= html_escape($e->event_name) ?></p>
                            <p class="text-[10px] text-ivory/60 uppercase tracking-wider mt-0.5"><?= ucfirst($e->category) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-6">
            <p class="editorial-label text-gold-muted mb-3">🩺 System Health</p>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center pb-2 border-b border-gray-100"><span class="text-slate">PHP</span><span class="font-mono text-xs text-navy"><?= $system['php_version'] ?></span></div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-100"><span class="text-slate">CodeIgniter</span><span class="font-mono text-xs text-navy"><?= $system['ci_version'] ?></span></div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-100"><span class="text-slate">Upload Folder</span><span class="font-mono text-xs text-navy"><?= $system['upload_size_mb'] ?> MB</span></div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-100"><span class="text-slate">DB Tables</span><span class="font-mono text-xs text-navy"><?= $system['db_tables'] ?></span></div>
                <div class="flex justify-between items-center"><span class="text-slate">Status</span>
                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2 py-0.5 text-[10px] uppercase tracking-wider font-bold"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Healthy</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BOTTOM GRID -->
<div class="grid lg:grid-cols-3 gap-6 mb-10">
    <div class="bg-white border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-5">
            <div><p class="editorial-label text-gold-muted mb-1">Latest</p><h3 class="font-serif text-xl font-light text-navy">Berita</h3></div>
            <a href="<?= base_url('admin/posts') ?>" class="text-xs uppercase tracking-editorial text-navy hover:text-gold font-semibold">&rarr;</a>
        </div>
        <div class="space-y-4">
            <?php if (empty($recent_news)): ?><p class="text-sm text-slate text-center py-6">Belum ada berita.</p>
            <?php else: foreach ($recent_news as $n): ?>
            <a href="<?= base_url('admin/posts/edit/' . $n->id) ?>" class="flex gap-3 group">
                <div class="w-14 h-14 bg-gray-200 flex-shrink-0 overflow-hidden">
                    <?php if ($n->featured_image): ?><img src="<?= base_url('assets/uploads/' . $n->featured_image) ?>" class="w-full h-full object-cover" alt="">
                    <?php else: ?><div class="w-full h-full flex items-center justify-center"><i class="fas fa-newspaper text-slate/40"></i></div><?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-navy line-clamp-2 group-hover:text-gold-muted transition leading-snug"><?= html_escape($n->title) ?></h4>
                    <p class="text-[10px] text-slate mt-1 uppercase tracking-wider"><?= date('d M Y', strtotime($n->created_at)) ?></p>
                </div>
            </a>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <div class="bg-white border border-gray-200 p-8">
        <div class="mb-5">
            <p class="editorial-label text-gold-muted mb-1">Audit Trail</p>
            <h3 class="font-serif text-xl font-light text-navy">Aktivitas <span class="inline-flex items-center gap-1.5 text-xs font-sans align-middle ml-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span><span class="text-slate uppercase tracking-wider">Live</span></span></h3>
        </div>
        <div class="space-y-3">
            <?php if (empty($recent_activity)): ?><p class="text-sm text-slate text-center py-6">Belum ada aktivitas.</p>
            <?php else: foreach ($recent_activity as $a):
                $icon = ['create_post'=>'fa-plus text-blue-600 bg-blue-50','update_post'=>'fa-edit text-blue-600 bg-blue-50','delete_post'=>'fa-trash text-red-600 bg-red-50','create_lecturer'=>'fa-user-plus text-purple-600 bg-purple-50','login'=>'fa-sign-in-alt text-green-600 bg-green-50'][$a->action] ?? 'fa-circle text-gray-400 bg-gray-100';
                $parts = explode(' ', $icon);
            ?>
            <div class="flex gap-3 text-sm">
                <div class="w-7 h-7 <?= $parts[2] ?? 'bg-gray-100' ?> flex items-center justify-center flex-shrink-0"><i class="fas <?= $parts[0] ?> text-[10px] <?= $parts[1] ?? 'text-gray-400' ?>"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-navy leading-snug"><strong><?= html_escape($a->full_name ?? 'System') ?></strong> <?= html_escape(character_limiter($a->description, 50)) ?></p>
                    <p class="text-[10px] text-slate mt-0.5 font-mono relative-time" data-time="<?= strtotime($a->created_at) ?>"></p>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <div class="bg-ivory-warm border border-gold/30 p-8 relative overflow-hidden">
        <div class="absolute top-3 right-3"><i class="fas fa-sticky-note text-gold-muted text-xl"></i></div>
        <p class="editorial-label text-gold-muted mb-3">📝 Quick Notes</p>
        <h3 class="font-serif text-xl font-light text-navy mb-3">Catatan <em class="italic text-gold-muted">Pribadi</em></h3>
        <textarea id="quickNote" rows="7" class="w-full px-4 py-3 bg-white border border-navy/10 text-navy text-sm leading-relaxed focus:border-gold outline-none" placeholder="Tulis ide, to-do, atau catatan penting... Auto-save..."><?= html_escape($quick_note) ?></textarea>
        <div class="flex items-center justify-between mt-3 text-xs">
            <span class="text-slate" id="noteStatus"><i class="fas fa-check-circle text-green-500 mr-1"></i>Tersimpan</span>
            <span class="font-mono text-slate">Auto-save</span>
        </div>
    </div>
</div>

<!-- COMMAND PALETTE MODAL DIHAPUS (pakai dari header admin) -->

<!-- HEATMAP MODAL (unik dashboard, dipertahankan) -->
<div class="heat-modal" id="heatModal" onclick="if(event.target===this)closeHeatModal()">
    <div class="heat-modal-card">
        <div class="heat-modal-head">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-gold">Contribution Details</p>
                <h3 id="heatModalTitle">—</h3>
            </div>
            <button class="heat-modal-close" onclick="closeHeatModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="heat-modal-body" id="heatModalBody">
            <p class="heat-empty">Memuat...</p>
        </div>
    </div>
</div>

<!-- KEYBOARD SHORTCUTS PANEL (unik dashboard, dipertahankan) -->
<button class="skb" id="skbBtn" title="Keyboard Shortcuts (?)">?</button>
<div class="sk-panel" id="skPanel">
    <div class="sk-panel-head">
        <h3>Dashboard Shortcuts</h3>
        <button onclick="document.getElementById('skPanel').classList.remove('open')" style="background:none;border:none;color:#C9A227;cursor:pointer"><i class="fas fa-times"></i></button>
    </div>
    <div class="sk-group">
        <p class="sk-group-title">Navigasi (Global)</p>
        <div class="sk-row"><span>Command Palette</span><kbd>Ctrl + K</kbd></div>
        <div class="sk-row"><span>Lihat Website</span><kbd>V</kbd></div>
        <div class="sk-row"><span>Export Dashboard</span><kbd>E</kbd></div>
        <div class="sk-row"><span>Toggle Dark Mode</span><kbd>Ctrl + D</kbd></div>
    </div>
    <div class="sk-group">
        <p class="sk-group-title">Aksi Cepat</p>
        <div class="sk-row"><span>Tulis Berita Baru</span><kbd>N</kbd></div>
        <div class="sk-row"><span>Refresh Data</span><kbd>R</kbd></div>
    </div>
    <div class="sk-group">
        <p class="sk-group-title">Umum</p>
        <div class="sk-row"><span>Tampilkan Shortcuts</span><kbd>?</kbd></div>
        <div class="sk-row"><span>Tutup Modal</span><kbd>Esc</kbd></div>
    </div>
</div>

<script>
(function(){
    // ===== SKELETON REMOVAL =====
    setTimeout(function(){ document.querySelectorAll('.shimmer').forEach(function(el){ el.classList.remove('shimmer'); }); }, 900);

    // DARK MODE DIHAPUS (pakai yang di header admin via html.admin-dark)

    // ===== DASHBOARD CLOCK (unique ID: dashClock, hindari bentrok dengan header) =====
    function dashTick(){
        var n = new Date();
        var c = document.getElementById('dashClock');
        var s = document.getElementById('liveSec');
        if(c) c.textContent = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0');
        if(s) s.textContent = String(n.getSeconds()).padStart(2,'0');
    }
    dashTick(); setInterval(dashTick, 1000);

    // ===== TYPEWRITER =====
    var tt = document.getElementById('typeTarget');
    function playType(){
        if(!tt || !tt.dataset.name) return;
        var name = tt.dataset.name, i = 0;
        tt.textContent = '';
        (function type(){
            if(i <= name.length){ tt.textContent = name.slice(0, i); i++; setTimeout(type, 60); }
        })();
    }
    if(tt && tt.dataset.name){ setTimeout(playType, 400); }
    var gn = document.getElementById('greetName');
    if(gn){ gn.addEventListener('click', playType); }

    // ===== SPOTLIGHT =====
    var hero = document.getElementById('heroDash');
    if(hero){
        hero.addEventListener('mousemove', function(e){
            var r = hero.getBoundingClientRect();
            hero.style.setProperty('--mx', (e.clientX - r.left) + 'px');
            hero.style.setProperty('--my', (e.clientY - r.top) + 'px');
        });
    }

    // ===== HERO PARTICLES =====
    var cv = document.getElementById('heroParticles');
    if(cv){
        var ctx = cv.getContext('2d'), pts = [];
        function rs(){ cv.width = cv.offsetWidth; cv.height = cv.offsetHeight; }
        rs(); window.addEventListener('resize', rs);
        for(var i=0;i<40;i++) pts.push({x:Math.random()*cv.width, y:Math.random()*cv.height, vx:(Math.random()-.5)*.4, vy:(Math.random()-.5)*.4});
        (function anim(){
            ctx.clearRect(0,0,cv.width,cv.height);
            pts.forEach(function(p){
                p.x+=p.vx; p.y+=p.vy;
                if(p.x<0||p.x>cv.width)p.vx*=-1;
                if(p.y<0||p.y>cv.height)p.vy*=-1;
                ctx.beginPath(); ctx.arc(p.x,p.y,1.2,0,6.28); ctx.fillStyle='rgba(201,162,39,.6)'; ctx.fill();
            });
            requestAnimationFrame(anim);
        })();
    }

    // ===== 3D TILT =====
    document.querySelectorAll('.tilt').forEach(function(card){
        card.addEventListener('mousemove', function(e){
            var r = card.getBoundingClientRect();
            var x = (e.clientX - r.left)/r.width - .5;
            var y = (e.clientY - r.top)/r.height - .5;
            card.style.transform = 'perspective(800px) rotateY(' + (x*8) + 'deg) rotateX(' + (-y*8) + 'deg) translateY(-4px)';
        });
        card.addEventListener('mouseleave', function(){ card.style.transform = ''; });
    });

    // ===== COUNTERS + DONUT + GAUGE + BARS =====
    var io = new IntersectionObserver(function(es){
        es.forEach(function(en){
            if(!en.isIntersecting) return;
            var el = en.target;
            el.querySelectorAll('[data-count]').forEach(function(c){
                if(c.dataset.done) return; c.dataset.done='1';
                var t=+c.dataset.count, st=performance.now();
                (function f(n){ var p=Math.min(1,(n-st)/1400), e=1-Math.pow(1-p,3);
                    c.textContent=Math.round(t*e).toLocaleString('id-ID'); if(p<1)requestAnimationFrame(f); })(st);
            });
            el.querySelectorAll('.donut-seg').forEach(function(s){ if(s.dataset.target) s.setAttribute('stroke-dasharray', s.dataset.target); });
            var g = el.querySelector('#gaugeArc'); if(g) g.setAttribute('stroke-dasharray', g.dataset.target);
            el.querySelectorAll('.bar-anim').forEach(function(b){ b.style.width = b.dataset.w; });
            io.unobserve(el);
        });
    }, {threshold:.15});
    document.querySelectorAll('.grid, .lg\\:col-span-4, .bg-white').forEach(function(el){ io.observe(el); });

    // ===== RELATIVE TIME =====
    function relTime(ts){
        var d = Math.floor((Date.now()/1000) - ts);
        if(d<60) return d + ' detik lalu';
        if(d<3600) return Math.floor(d/60) + ' menit lalu';
        if(d<86400) return Math.floor(d/3600) + ' jam lalu';
        if(d<604800) return Math.floor(d/86400) + ' hari lalu';
        return Math.floor(d/604800) + ' minggu lalu';
    }
    function updRel(){ document.querySelectorAll('.relative-time').forEach(function(el){ el.textContent = relTime(+el.dataset.time); }); }
    updRel(); setInterval(updRel, 60000);

    // ===== GAUGE TOOLTIP =====
    var gaugeContainer = document.getElementById('gaugeContainer');
    var gaugeTooltip = document.getElementById('gaugeTooltip');
    if (gaugeContainer && gaugeTooltip) {
        gaugeContainer.addEventListener('mouseenter', function() { gaugeTooltip.classList.add('visible'); });
        gaugeContainer.addEventListener('mousemove', function(e) {
            var rect = gaugeContainer.getBoundingClientRect();
            gaugeTooltip.style.left = (e.clientX - rect.left + 15) + 'px';
            gaugeTooltip.style.top = (e.clientY - rect.top - 10) + 'px';
        });
        gaugeContainer.addEventListener('mouseleave', function() { gaugeTooltip.classList.remove('visible'); });
    }

    // ===== REFRESH BUTTON =====
    var refreshBtn = document.getElementById('refreshBtn');
    var lastUpdated = document.getElementById('lastUpdated');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            refreshBtn.classList.add('loading');
            refreshBtn.disabled = true;
            fetch('<?= base_url('admin/dashboard/refresh_stats') ?>', {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    refreshBtn.classList.remove('loading');
                    refreshBtn.disabled = false;
                    if (data.ok) {
                        lastUpdated.textContent = 'Updated just now';
                        setTimeout(function() { location.reload(); }, 500);
                    }
                })
                .catch(function() {
                    refreshBtn.classList.remove('loading');
                    refreshBtn.disabled = false;
                    alert('Gagal refresh data.');
                });
        });
    }

    // ===== AUTO REFRESH (60 detik) =====
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            fetch('<?= base_url('admin/dashboard/refresh_stats') ?>', {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.ok && data.has_changes) {
                        lastUpdated.textContent = 'New data available';
                        lastUpdated.style.color = '#C9A227';
                    }
                })
                .catch(function() {});
        }
    }, 60000);

    // ===== CHART EXPORT =====
    var exportChart = document.getElementById('exportChart');
    var trendSvg = document.getElementById('trendSvg');
    if (exportChart && trendSvg) {
        exportChart.addEventListener('click', function() {
            var svgData = new XMLSerializer().serializeToString(trendSvg);
            var canvas = document.createElement('canvas');
            canvas.width = 700; canvas.height = 220;
            var ctx = canvas.getContext('2d');
            var img = new Image();
            img.onload = function() {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                var link = document.createElement('a');
                link.download = 'chart-' + new Date().toISOString().slice(0,10) + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            };
            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
        });
    }

    // ===== QUICK NOTE =====
    var note = document.getElementById('quickNote'), status = document.getElementById('noteStatus'), tmr;
    var pulse = document.getElementById('pulseLive');
    if(note){
        note.addEventListener('input', function(){
            status.innerHTML = '<i class="fas fa-circle-notch fa-spin text-amber-500 mr-1"></i>Menyimpan...';
            if(pulse){ pulse.classList.add('active'); }
            clearTimeout(tmr);
            tmr = setTimeout(function(){
                var fd = new FormData(); fd.append('note', note.value);
                fetch('<?= base_url('admin/dashboard/save_note') ?>', {method:'POST', body:fd})
                    .then(function(r){return r.json();})
                    .then(function(){
                        status.innerHTML='<i class="fas fa-check-circle text-green-500 mr-1"></i>Tersimpan';
                        setTimeout(function(){ if(pulse) pulse.classList.remove('active'); }, 1500);
                    })
                    .catch(function(){ status.innerHTML='<i class="fas fa-exclamation-circle text-red-500 mr-1"></i>Gagal'; });
            }, 800);
        });
    }

    // COMMAND PALETTE DIHAPUS (pakai global dari header admin)
    // Trigger via button: document.dispatchEvent(new KeyboardEvent('keydown',{key:'k',ctrlKey:true}))

    // ===== KEYBOARD SHORTCUTS (Dashboard-specific, pakai global untuk Ctrl+K/D) =====
    document.addEventListener('keydown', function(e){
        if(e.key==='Escape') { closeHeatModal(); var skP=document.getElementById('skPanel'); if(skP) skP.classList.remove('open'); }
        var tag = (document.activeElement.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') return;
        if (e.key === '?') { var skP=document.getElementById('skPanel'); if(skP) skP.classList.toggle('open'); }
        else if (e.key === 'n' || e.key === 'N') { window.location = '<?= base_url('admin/posts/create') ?>'; }
        else if (e.key === 'v' || e.key === 'V') { window.open('<?= base_url() ?>', '_blank'); }
        else if (e.key === 'e' || e.key === 'E') { window.open('<?= base_url('admin/dashboard/export') ?>', '_blank'); }
        else if (e.key === 'r' || e.key === 'R') { location.reload(); }
    });

    // ===== SMART CHART TOOLTIP =====
    var tooltip = document.getElementById('chartTooltip');
    var svg = document.getElementById('trendSvg');
    if (tooltip && svg) {
        svg.addEventListener('mouseover', function(e){
            var t = e.target;
            if (!t.classList.contains('chart-dot')) return;
            var label = t.dataset.label, val = t.dataset.val, date = t.dataset.date;
            tooltip.innerHTML = '<div class="tt-label">' + label + ' · ' + date + '</div><div class="tt-val">' + val + ' <span style="font-size:10px;color:#94a3b8">aktivitas</span></div>';
            tooltip.classList.add('visible');
        });
        svg.addEventListener('mousemove', function(e){
            if (!tooltip.classList.contains('visible')) return;
            var rect = svg.getBoundingClientRect();
            tooltip.style.left = (e.clientX - rect.left + 12) + 'px';
            tooltip.style.top = (e.clientY - rect.top - 50) + 'px';
        });
        svg.addEventListener('mouseout', function(e){
            if (!e.target.classList.contains('chart-dot')) return;
            tooltip.classList.remove('visible');
        });
    }

    // ===== HEATMAP DRILL-DOWN =====
    var heatGrid = document.getElementById('heatmapGrid');
    if (heatGrid) {
        heatGrid.addEventListener('click', function(e){
            var cell = e.target.closest('.heat-cell-adv');
            if (!cell) return;
            var count = cell.dataset.count;
            if (parseInt(count) === 0) return;
            openHeatModal(cell.dataset.date);
        });
    }

    window.openHeatModal = function(date){
        var modal = document.getElementById('heatModal');
        var title = document.getElementById('heatModalTitle');
        var body = document.getElementById('heatModalBody');
        modal.classList.add('open');
        title.textContent = formatDate(date);
        body.innerHTML = '<p class="heat-empty"><i class="fas fa-circle-notch fa-spin text-gold-muted mr-2"></i>Memuat aktivitas...</p>';
        fetch('<?= base_url('admin/dashboard/heatmap_detail/') ?>' + date, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (!data.items || data.items.length === 0) {
                    body.innerHTML = '<p class="heat-empty"><i class="fas fa-inbox text-slate mr-2"></i>Tidak ada aktivitas tercatat untuk tanggal ini.</p>';
                    return;
                }
                var iconMap = { post: 'fa-newspaper', research: 'fa-flask', document: 'fa-file-alt' };
                var labelMap = { post: 'Berita', research: 'Riset', document: 'Dokumen' };
                var urlMap = { posts: 'admin/posts/edit/', research: 'admin/research/edit/', documents: 'admin/documents/edit/' };
                var html = '<p class="text-xs text-slate uppercase tracking-wider font-semibold mb-3">' + data.items.length + ' Aktivitas</p>';
                data.items.forEach(function(item){
                    html += '<a href="<?= base_url() ?>' + (urlMap[item.table] || '#') + item.id + '" class="heat-item" onclick="closeHeatModal()">';
                    html += '<div class="hi-icon"><i class="fas ' + (iconMap[item.type] || 'fa-file') + '"></i></div>';
                    html += '<div class="flex-1 min-w-0"><p class="hi-title line-clamp-2">' + escapeHtml(item.title) + '</p><p class="hi-meta">' + (labelMap[item.type] || item.type) + '</p></div>';
                    html += '</a>';
                });
                body.innerHTML = html;
            })
            .catch(function(){ body.innerHTML = '<p class="heat-empty text-red-600">Gagal memuat data.</p>'; });
    };

    window.closeHeatModal = function(){
        document.getElementById('heatModal').classList.remove('open');
    };

    function formatDate(d){
        var parts = d.split('-');
        var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];
    }

    function escapeHtml(s){
        var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML;
    }
})();
</script>