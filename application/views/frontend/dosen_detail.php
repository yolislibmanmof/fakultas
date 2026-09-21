<?php
$EN = (get_site_lang() == 'en');
$full_name = trim(($l->title_front ?? '') . ' ' . $l->name . ', ' . ($l->title_back ?? ''), ' ,');
$expertise_tags = array_values(array_filter(array_map('trim', preg_split('/[,;|]/', (string)($l->expertise ?? '')))));
$nidn    = $l->nidn ?? '';
$email   = $l->email ?? '';
$scholar = $l->google_scholar_url ?? '';
$scopus  = $l->scopus_id ?? '';
$sinta   = $l->sinta_url ?? '';

// Expertise ranking untuk radar chart (max 8)
$radar_tags = array_slice($expertise_tags, 0, 8);
$radar_data = [];
foreach ($radar_tags as $i => $t) {
    // Skor pseudo: 100 untuk pertama, menurun
    $radar_data[] = ['label' => $t, 'value' => max(40, 100 - ($i * 8))];
}

// Reading time estimate
function reading_time($text) {
    $words = count(preg_split('/\s+/', strip_tags($text)));
    return max(1, (int)ceil($words / 200));
}
?>

<!-- JSON-LD Schema -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $full_name,
    'jobTitle' => $EN ? 'Faculty Member' : 'Tenaga Pengajar',
    'worksFor' => ['@type' => 'CollegeOrUniversity', 'name' => site_name()],
    'email' => $email,
    'knowsAbout' => $expertise_tags,
    'sameAs' => array_values(array_filter([$scholar, $sinta, $scopus ? 'https://www.scopus.com/authid/detail.uri?authorId=' . $scopus : ''])),
]) ?>
</script>

<style>
/* ===== ANIMATED GRID HERO ===== */
.dz-hero { position: relative; background: linear-gradient(165deg, #061420 0%, #0B2239 60%, #13334F 100%); overflow: hidden; }
.dz-grid {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(201,162,39,.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(201,162,39,.08) 1px, transparent 1px);
    background-size: 48px 48px;
    animation: dzGridPulse 4s ease-in-out infinite;
}
@keyframes dzGridPulse {
    0%,100% { opacity: .5; }
    50% { opacity: .9; }
}
#dzParticles { position: absolute; inset: 0; pointer-events: none; z-index: 1; }
.dz-hero-glow {
    position: absolute; top: -30%; left: 50%; width: 140%; height: 100%; transform: translateX(-50%);
    background: conic-gradient(from 0deg at 50% 0%, transparent 0deg, rgba(201,162,39,.15) 20deg, transparent 45deg, transparent 315deg, rgba(201,162,39,.1) 340deg, transparent 360deg);
    animation: dzSway 10s ease-in-out infinite alternate; pointer-events: none; z-index: 0;
}
@keyframes dzSway { from { transform: translateX(-50%) rotate(-6deg); } to { transform: translateX(-50%) rotate(6deg); } }

/* ===== HOLOGRAPHIC ID CARD ===== */
.dz-idcard {
    position: relative; max-width: 340px; margin: 0 auto;
    background: #F7F5F0; box-shadow: 0 30px 80px rgba(0,0,0,.5), 0 0 0 1px rgba(201,162,39,.2);
    transform: rotate(-1.5deg);
    transition: transform .6s cubic-bezier(.22,1,.36,1), box-shadow .6s;
    overflow: hidden;
}
.dz-idcard:hover { transform: rotate(0deg) scale(1.02); box-shadow: 0 40px 100px rgba(0,0,0,.6), 0 0 0 2px #C9A227; }
.dz-idcard::before {
    content: ''; position: absolute; inset: 0; z-index: 3; pointer-events: none;
    background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,.4) 50%, transparent 70%);
    transform: translateX(-100%);
    animation: dzHoloSheen 4s ease-in-out infinite;
}
@keyframes dzHoloSheen {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}
.dz-idcard::after {
    content: ''; position: absolute; inset: 0; z-index: 2; pointer-events: none;
    background:
        radial-gradient(circle at 20% 30%, rgba(255,0,128,.08), transparent 40%),
        radial-gradient(circle at 80% 70%, rgba(0,200,255,.08), transparent 40%),
        radial-gradient(circle at 50% 50%, rgba(201,162,39,.05), transparent 60%);
    mix-blend-mode: overlay;
}
.dz-barcode {
    height: 42px;
    background: repeating-linear-gradient(90deg,
        #0B2239 0 2px, transparent 2px 5px,
        #0B2239 5px 7px, transparent 7px 11px,
        #0B2239 11px 12px, transparent 12px 16px);
    position: relative; overflow: hidden;
}
.dz-barcode::after {
    content:''; position:absolute; inset:0;
    background: linear-gradient(120deg, transparent 30%, rgba(201,162,39,.6) 50%, transparent 70%);
    transform: translateX(-100%);
    animation: dzScan 3s ease infinite;
}
@keyframes dzScan { to { transform: translateX(100%); } }

/* Animated Verified Stamp (seal berputar) */
.dz-stamp {
    position: absolute; top: 14px; right: 14px; z-index: 4;
    width: 64px; height: 64px; border-radius: 50%;
    border: 2px solid #C9A227; background: rgba(247,245,240,.9);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    transform: rotate(12deg);
    animation: dzStampPulse 2.5s ease-in-out infinite;
    box-shadow: 0 0 0 3px rgba(201,162,39,.15);
}
.dz-stamp i { color: #C9A227; font-size: 18px; margin-bottom: 2px; }
.dz-stamp span { font-size: 7px; font-weight: 900; letter-spacing: .15em; color: #0B2239; }
@keyframes dzStampPulse {
    0%,100% { transform: rotate(12deg) scale(1); box-shadow: 0 0 0 3px rgba(201,162,39,.15); }
    50% { transform: rotate(12deg) scale(1.05); box-shadow: 0 0 0 8px rgba(201,162,39,.1); }
}

.dz-photo-pop { animation: dzPop 1s cubic-bezier(.34,1.56,.64,1) both; }
@keyframes dzPop { from { transform: scale(.6) rotate(-8deg); opacity: 0; } to { transform: scale(1) rotate(-1.5deg); opacity: 1; } }

/* ===== PROGRESS RING STATS ===== */
.dz-ring-wrap { position: relative; width: 110px; height: 110px; margin: 0 auto; }
.dz-ring { transform: rotate(-90deg); }
.dz-ring circle { fill: none; stroke-width: 6; }
.dz-ring .bg { stroke: rgba(247,245,240,.1); }
.dz-ring .fg { stroke: #C9A227; stroke-linecap: round; stroke-dasharray: 314; stroke-dashoffset: 314; transition: stroke-dashoffset 1.6s cubic-bezier(.22,1,.36,1); }
.dz-ring-num { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-family: 'Fraunces', serif; font-size: 2rem; font-weight: 300; color: #C9A227; }

/* ===== GLITCH TYPEWRITER ===== */
.dz-glitch { position: relative; display: inline-block; }
.dz-glitch::before, .dz-glitch::after {
    content: attr(data-text); position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0;
}
.dz-glitch.active::before {
    left: 2px; text-shadow: -1px 0 #C9A227; opacity: .7;
    animation: dzGlitch1 3s infinite linear alternate-reverse;
}
.dz-glitch.active::after {
    left: -2px; text-shadow: -1px 0 #3b82f6; opacity: .7;
    animation: dzGlitch2 2s infinite linear alternate-reverse;
}
@keyframes dzGlitch1 {
    0% { clip-path: inset(40% 0 61% 0); } 20% { clip-path: inset(92% 0 1% 0); }
    40% { clip-path: inset(43% 0 1% 0); } 60% { clip-path: inset(25% 0 58% 0); }
    80% { clip-path: inset(54% 0 7% 0); } 100% { clip-path: inset(58% 0 43% 0); }
}
@keyframes dzGlitch2 {
    0% { clip-path: inset(65% 0 13% 0); } 20% { clip-path: inset(15% 0 62% 0); }
    40% { clip-path: inset(78% 0 2% 0); } 60% { clip-path: inset(35% 0 28% 0); }
    80% { clip-path: inset(12% 0 69% 0); } 100% { clip-path: inset(45% 0 25% 0); }
}

/* ===== RADAR CHART ===== */
.dz-radar-wrap { position: relative; max-width: 440px; margin: 0 auto; }
.dz-radar-svg { width: 100%; height: auto; }
.dz-radar-poly { fill: rgba(201,162,39,.2); stroke: #C9A227; stroke-width: 2; transition: all .4s; }
.dz-radar-svg:hover .dz-radar-poly { fill: rgba(201,162,39,.35); }
.dz-radar-label { font-family: 'Inter', sans-serif; font-size: 11px; fill: #0B2239; font-weight: 600; }

/* ===== STICKY TOC ===== */
.dz-toc { position: sticky; top: 100px; }
.dz-toc-item {
    display: block; padding: .6rem 1rem; font-size: 12px; color: #64748b;
    border-left: 2px solid #e5e7eb; transition: all .2s; text-transform: uppercase; letter-spacing: .1em; font-weight: 600;
}
.dz-toc-item:hover { color: #C9A227; border-color: #C9A227; }
.dz-toc-item.active { color: #0B2239; border-color: #C9A227; font-weight: 700; }

/* ===== EXPANDABLE TIMELINE ===== */
.dz-tl-item { transition: all .3s; }
.dz-tl-head { cursor: pointer; user-select: none; }
.dz-tl-head:hover { background: #FAF8F3; }
.dz-tl-body {
    max-height: 0; overflow: hidden; transition: max-height .5s cubic-bezier(.22,1,.36,1), padding .3s;
    padding: 0 1.5rem; background: #FAF8F3;
}
.dz-tl-item.open .dz-tl-body { max-height: 500px; padding: 1rem 1.5rem 1.5rem; }
.dz-tl-chevron { transition: transform .3s; }
.dz-tl-item.open .dz-tl-chevron { transform: rotate(180deg); }

.dz-line { position: relative; }
.dz-line::before { content:''; position:absolute; left: 19px; top: 0; bottom: 0; width: 2px; background: linear-gradient(#C9A227, rgba(201,162,39,.1)); }
.dz-dot { position: absolute; left: 11px; top: 6px; width: 18px; height: 18px; border-radius: 50%; background: #0B2239; border: 3px solid #C9A227; box-shadow: 0 0 0 4px rgba(201,162,39,.15); z-index: 1; }

/* ===== SHARE FAB & PANEL ===== */
.share-fab {
    position: fixed; bottom: 24px; right: 24px; z-index: 90;
    width: 56px; height: 56px; border-radius: 50%;
    background: #C9A227; color: #0B2239;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; cursor: pointer; border: none;
    box-shadow: 0 8px 24px rgba(201,162,39,.4); transition: all .3s;
}
.share-fab:hover { transform: scale(1.1); background: #0B2239; color: #C9A227; }
.share-panel {
    position: fixed; bottom: 96px; right: 24px; z-index: 90;
    background: #fff; border: 1px solid #e5e7eb; border-top: 3px solid #C9A227;
    box-shadow: 0 12px 40px rgba(0,0,0,.15); padding: 20px; width: 260px;
    display: none;
}
.share-panel.open { display: block; animation: slideUp .3s ease; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
.share-option { display: flex; align-items: center; gap: 12px; padding: 10px 12px; cursor: pointer; transition: background .15s; font-size: 13px; color: #0B2239; }
.share-option:hover { background: #F7F5F0; }
.share-option i { width: 20px; text-align: center; color: #C9A227; }

/* ===== 🎓 CITATION MODAL ===== */
.dz-modal { position: fixed; inset: 0; z-index: 100; background: rgba(6,20,32,.85); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; padding: 1rem; }
.dz-modal.open { display: flex; }
.dz-modal-card { background: #fff; width: 100%; max-width: 560px; max-height: 85vh; overflow-y: auto; border-top: 4px solid #C9A227; }
.dz-modal-head { padding: 1.5rem; background: #0B2239; color: #F7F5F0; display: flex; justify-content: space-between; align-items: center; }
.dz-modal-close { background: none; border: none; color: #C9A227; font-size: 20px; cursor: pointer; }
.dz-modal-body { padding: 1.5rem; }
.dz-cite-tab { display: flex; gap: 2px; border-bottom: 1px solid #e5e7eb; margin-bottom: 1rem; }
.dz-cite-tab button { padding: .6rem 1rem; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; border: none; background: transparent; cursor: pointer; color: #64748b; border-bottom: 2px solid transparent; }
.dz-cite-tab button.active { color: #0B2239; border-color: #C9A227; }
.dz-cite-code { background: #F7F5F0; padding: 1rem; font-family: 'JetBrains Mono', monospace; font-size: 12px; white-space: pre-wrap; word-break: break-word; border-left: 3px solid #C9A227; }

/* ===== TOAST ===== */
.dz-toast {
    position: fixed; bottom: 24px; right: 100px; z-index: 100;
    background: #0B2239; color: #F7F5F0; padding: 12px 20px; font-size: 13px;
    box-shadow: 0 8px 24px rgba(0,0,0,.2); border-left: 3px solid #C9A227;
    transform: translateX(-120%); transition: transform .3s;
}
.dz-toast.show { transform: translateX(0); }
.dz-toast i { color: #C9A227; margin-right: 8px; }

.dz-tag { transition: all .3s; cursor: pointer; }
.dz-tag:hover { background: #C9A227; color: #0B2239; transform: translateY(-2px); }
.dz-reveal { opacity: 0; transform: translateY(40px); transition: all .9s cubic-bezier(.22,1,.36,1); }
.dz-reveal.in { opacity: 1; transform: none; }

.related-card { background: #fff; border: 1px solid #e5e7eb; padding: 16px; transition: all .3s; }
.related-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(11,34,57,.1); border-color: #C9A227; }

@media print {
    .dz-toc, .share-fab, .share-panel, .dz-toast, .dz-modal, #dzParticles, .dz-hero-glow { display: none !important; }
    .dz-idcard { transform: none !important; break-inside: avoid; }
    .dz-reveal { opacity: 1 !important; transform: none !important; }
}
</style>

<!-- ============ DOSSIER HERO ============ -->
<section class="dz-hero text-ivory">
    <div class="dz-grid"></div>
    <div class="dz-hero-glow"></div>
    <canvas id="dzParticles"></canvas>

    <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
        <a href="<?= base_url('dosen') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-ivory/60 hover:text-gold transition mb-10">
            <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            <?= $EN ? 'Faculty Directory' : 'Direktori Dosen' ?>
        </a>

        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <!-- HOLOGRAPHIC ID CARD -->
            <div class="lg:col-span-4">
                <div class="dz-idcard dz-photo-pop">
                    <!-- Animated Stamp -->
                    <div class="dz-stamp">
                        <i class="fas fa-certificate"></i>
                        <span>VERIFIED</span>
                    </div>
                    <div class="p-6 pb-4 relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-navy text-gold flex items-center justify-center font-serif font-bold text-sm">F</div>
                            <div class="leading-tight">
                                <p class="text-[9px] uppercase tracking-editorial text-slate"><?= html_escape(site_name()) ?></p>
                                <p class="text-[9px] uppercase tracking-editorial text-gold-muted">Academic Dossier</p>
                            </div>
                        </div>
                        <div class="bg-navy overflow-hidden mb-4 relative">
                            <?php if (!empty($l->photo) && file_exists(FCPATH . 'assets/uploads/' . $l->photo)): ?>
                                <img src="<?= base_url('assets/uploads/' . $l->photo) ?>" class="w-full h-56 object-cover" alt="<?= html_escape($l->name) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-56 flex items-center justify-center"><i class="fas fa-user-tie text-6xl text-gold/40"></i></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-navy/40 to-transparent"></div>
                        </div>
                        <p class="font-serif font-bold text-lg leading-tight"><?= html_escape($l->name) ?></p>
                        <p class="text-xs text-slate mt-1"><?= html_escape(trim(($l->title_front ?? '') . ' / ' . ($l->title_back ?? ''), ' /')) ?></p>
                        <div class="dz-barcode mt-4"></div>
                        <div class="flex justify-between items-center mt-2">
                            <p class="font-mono text-[10px] text-slate">ID · <?= str_pad($l->id, 6, '0', STR_PAD_LEFT) ?><?= $nidn ? ' · NIDN ' . html_escape($nidn) : '' ?></p>
                            <p class="font-mono text-[10px] text-gold-muted">AKTIF</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFO -->
            <div class="lg:col-span-8">
                <p class="editorial-label text-gold mb-4"><?= $EN ? 'Faculty Member' : 'Tenaga Pengajar' ?></p>
                <h1 class="font-serif font-light text-4xl md:text-6xl leading-[1.05] tracking-tight mb-6 text-balance dz-glitch" data-text="<?= html_escape($l->name) ?>">
                    <?= html_escape($l->name) ?><?= $l->title_back ? ', <em class="italic text-gold">' . html_escape($l->title_back) . '</em>' : '' ?>
                </h1>

                <?php if (!empty($prodi)): ?>
                <div class="flex flex-wrap gap-2 mb-8">
                    <?php foreach ($prodi as $p): ?>
                        <span class="bg-ivory/10 border border-ivory/20 px-3 py-1.5 text-xs uppercase tracking-wider"><?= html_escape($p) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- PROGRESS RING STATS -->
                <div class="grid grid-cols-3 gap-6 max-w-lg mb-8">
                    <?php
                    $stat_data = [
                        ['n' => count($research), 'max' => 50, 'label' => $EN ? 'Research' : 'Riset'],
                        ['n' => count($blogs),    'max' => 30, 'label' => $EN ? 'Articles' : 'Artikel'],
                        ['n' => count($prodi),    'max' => 5,  'label' => $EN ? 'Programs' : 'Prodi'],
                    ];
                    foreach ($stat_data as $sd):
                        $pct = min(100, ($sd['max'] > 0) ? round($sd['n'] / $sd['max'] * 100) : 0);
                        $offset = 314 - (314 * $pct / 100);
                    ?>
                    <div class="text-center">
                        <div class="dz-ring-wrap">
                            <svg class="dz-ring" viewBox="0 0 120 120">
                                <circle class="bg" cx="60" cy="60" r="50"/>
                                <circle class="fg" cx="60" cy="60" r="50" data-offset="<?= $offset ?>"/>
                            </svg>
                            <div class="dz-ring-num dz-count" data-count="<?= $sd['n'] ?>">0</div>
                        </div>
                        <p class="text-[10px] uppercase tracking-wider text-ivory/70 mt-2 font-semibold"><?= $sd['label'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Links -->
                <div class="flex flex-wrap gap-3">
                    <?php if ($email): ?>
                    <a href="mailto:<?= html_escape($email) ?>" class="btn-gold px-5 py-3 text-[10px] uppercase tracking-editorial font-bold"><i class="fas fa-envelope mr-2"></i>Email</a>
                    <?php endif; ?>
                    <?php if ($scholar): ?>
                    <a href="<?= html_escape($scholar) ?>" target="_blank" rel="noopener" class="border border-ivory/30 px-5 py-3 text-[10px] uppercase tracking-editorial font-bold hover:bg-ivory hover:text-navy transition"><i class="fas fa-graduation-cap mr-2"></i>Scholar</a>
                    <?php endif; ?>
                    <?php if ($scopus): ?>
                    <a href="https://www.scopus.com/authid/detail.uri?authorId=<?= html_escape($scopus) ?>" target="_blank" rel="noopener" class="border border-ivory/30 px-5 py-3 text-[10px] uppercase tracking-editorial font-bold hover:bg-ivory hover:text-navy transition"><i class="fas fa-fingerprint mr-2"></i>Scopus</a>
                    <?php endif; ?>
                    <?php if ($sinta): ?>
                    <a href="<?= html_escape($sinta) ?>" target="_blank" rel="noopener" class="border border-ivory/30 px-5 py-3 text-[10px] uppercase tracking-editorial font-bold hover:bg-ivory hover:text-navy transition"><i class="fas fa-chart-line mr-2"></i>SINTA</a>
                    <?php endif; ?>
                    <button type="button" onclick="openCitation()" class="border border-ivory/30 px-5 py-3 text-[10px] uppercase tracking-editorial font-bold hover:bg-ivory hover:text-navy transition"><i class="fas fa-quote-left mr-2"></i>Cite</button>
                    <button type="button" onclick="downloadCV()" class="border border-ivory/30 px-5 py-3 text-[10px] uppercase tracking-editorial font-bold hover:bg-ivory hover:text-navy transition"><i class="fas fa-file-pdf mr-2"></i><?= $EN ? 'Download CV' : 'Unduh CV' ?></button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ BODY WITH TOC ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-12 gap-10">

            <!-- STICKY TOC (desktop) -->
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="dz-toc">
                    <p class="editorial-label text-gold-muted mb-3 px-4"><?= $EN ? 'Contents' : 'Daftar Isi' ?></p>
                    <a href="#sec-expertise" class="dz-toc-item" data-sec="sec-expertise"><?= $EN ? 'Expertise' : 'Keahlian' ?></a>
                    <a href="#sec-research" class="dz-toc-item" data-sec="sec-research"><?= $EN ? 'Research' : 'Riset' ?></a>
                    <a href="#sec-articles" class="dz-toc-item" data-sec="sec-articles"><?= $EN ? 'Articles' : 'Artikel' ?></a>
                    <?php if (!empty($related_lecturers)): ?>
                    <a href="#sec-related" class="dz-toc-item" data-sec="sec-related"><?= $EN ? 'Colleagues' : 'Rekan' ?></a>
                    <?php endif; ?>
                </nav>
            </aside>

            <!-- MAIN CONTENT -->
            <div class="lg:col-span-9 space-y-16">

                <!-- KEAHLIAN + RADAR -->
                <?php if (!empty($expertise_tags)): ?>
                <div id="sec-expertise" class="dz-reveal scroll-mt-24">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Expertise' : 'Bidang Keahlian' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy mb-8"><?= $EN ? 'Areas of <em class="italic text-gold-muted">expertise</em>' : 'Area <em class="italic text-gold-muted">keahlian</em>' ?></h2>

                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <!-- RADAR CHART -->
                        <?php if (count($radar_data) >= 3): ?>
                        <div class="dz-radar-wrap" id="dzRadar">
                            <svg class="dz-radar-svg" viewBox="0 0 400 400"></svg>
                        </div>
                        <?php endif; ?>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-3">
                            <?php foreach ($expertise_tags as $i => $tag): ?>
                                <span class="dz-tag bg-white border border-navy/15 px-4 py-2.5 text-sm text-navy font-medium shadow-sm"><?= html_escape($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- EXPANDABLE RESEARCH TIMELINE -->
                <div id="sec-research" class="dz-reveal scroll-mt-24">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Research & Service' : 'Riset & Pengabdian' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy mb-10"><?= $EN ? 'Research <em class="italic text-gold-muted">timeline</em>' : 'Linimasa <em class="italic text-gold-muted">riset</em>' ?></h2>

                    <?php if (empty($research)): ?>
                        <p class="text-slate bg-white border border-gray-200 p-8"><?= $EN ? 'No research recorded yet.' : 'Belum ada riset yang tercatat.' ?></p>
                    <?php else: ?>
                    <div class="dz-line space-y-4 pl-0">
                        <?php foreach ($research as $idx => $r): ?>
                        <div class="dz-tl-item relative pl-14" data-idx="<?= $idx ?>">
                            <div class="dz-dot"></div>
                            <div class="dz-tl-head bg-white border border-gray-200 p-5 flex items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="bg-navy text-gold font-mono text-[10px] px-2 py-0.5"><?= $r->year ?></span>
                                        <span class="text-[10px] uppercase tracking-wider font-bold <?= ($r->type ?? '') == 'research' ? 'text-purple-600' : 'text-teal-600' ?>">
                                            <?= ($r->type ?? '') == 'research' ? ($EN ? 'Research' : 'Penelitian') : ($EN ? 'Community Service' : 'Pengabdian') ?>
                                        </span>
                                        <?php if (!empty($r->funding_source)): ?>
                                        <span class="text-[10px] text-gold-muted"><i class="fas fa-coins mr-1"></i><?= html_escape($r->funding_source) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="font-serif text-base md:text-lg font-medium text-navy leading-snug"><?= html_escape($r->title) ?></h3>
                                </div>
                                <i class="fas fa-chevron-down dz-tl-chevron text-gold-muted"></i>
                            </div>
                            <div class="dz-tl-body">
                                <?php if (!empty($r->description)): ?>
                                <p class="text-sm text-slate leading-relaxed mb-3"><?= nl2br(html_escape($r->description)) ?></p>
                                <?php endif; ?>
                                <div class="flex flex-wrap gap-4 text-xs">
                                    <?php if (!empty($r->title_front)): ?>
                                    <span><i class="fas fa-user-tie text-gold-muted mr-1"></i><?= html_escape($r->title_front) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($r->role)): ?>
                                    <span><i class="fas fa-id-badge text-gold-muted mr-1"></i><?= html_escape($r->role) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- BLOG WITH READING TIME -->
                <div id="sec-articles" class="dz-reveal scroll-mt-24">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Writings' : 'Tulisan' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy mb-10"><?= $EN ? 'Latest <em class="italic text-gold-muted">articles</em>' : 'Artikel <em class="italic text-gold-muted">terbaru</em>' ?></h2>

                    <?php if (empty($blogs)): ?>
                        <p class="text-slate bg-white border border-gray-200 p-8"><?= $EN ? 'No articles yet.' : 'Belum ada artikel.' ?></p>
                    <?php else: ?>
                    <div class="grid md:grid-cols-2 gap-6">
                        <?php foreach ($blogs as $b): $rt = reading_time($b->content ?? ''); ?>
                        <a href="<?= base_url('blog-dosen/detail/' . $b->slug) ?>" class="bg-white border border-gray-200 p-6 hover-lift group hover:border-gold transition">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="editorial-label text-gold-muted"><?= date('d M Y', strtotime($b->published_at)) ?></span>
                                <span class="text-[10px] uppercase tracking-wider text-slate font-semibold"><i class="far fa-clock mr-1"></i><?= $rt ?> min read</span>
                            </div>
                            <h3 class="font-serif text-lg font-medium text-navy leading-snug group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($b->title) ?></h3>
                            <p class="text-sm text-slate mt-3 line-clamp-3"><?= html_escape(character_limiter(strip_tags($b->content ?? ''), 140)) ?></p>
                            <p class="text-[10px] uppercase tracking-wider text-gold-muted font-bold mt-4"><?= $EN ? 'Read Article' : 'Baca Artikel' ?> →</p>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- RELATED -->
                <?php if (!empty($related_lecturers)): ?>
                <div id="sec-related" class="dz-reveal scroll-mt-24">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Same Department' : 'Departemen yang Sama' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy mb-10"><?= $EN ? 'More <em class="italic text-gold-muted">faculty</em>' : 'Dosen <em class="italic text-gold-muted">lainnya</em>' ?></h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php foreach ($related_lecturers as $rl): ?>
                        <a href="<?= base_url('dosen/detail/' . $rl->id) ?>" class="related-card text-center">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-navy text-gold flex items-center justify-center font-serif text-3xl mx-auto mb-4 border-2 border-gold/30">
                                <?php if (!empty($rl->photo)): ?>
                                    <img src="<?= base_url('assets/uploads/' . $rl->photo) ?>" class="w-full h-full object-cover" alt="" loading="lazy">
                                <?php else: ?>
                                    <?= strtoupper(substr($rl->name, 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <p class="font-serif font-medium text-navy text-sm"><?= html_escape($rl->name) ?></p>
                            <p class="text-[10px] text-slate mt-1 uppercase tracking-wider">NIDN <?= html_escape($rl->nidn ?? '-') ?></p>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<!-- SHARE FAB + PANEL -->
<button class="share-fab" id="shareFab" title="<?= $EN ? 'Share Profile' : 'Bagikan Profil' ?>"><i class="fas fa-share-alt"></i></button>
<div class="share-panel" id="sharePanel">
    <p class="editorial-label text-slate mb-3"><?= $EN ? 'Share this profile' : 'Bagikan profil ini' ?></p>
    <div class="share-option" onclick="shareLinkedIn()"><i class="fab fa-linkedin"></i>LinkedIn</div>
    <div class="share-option" onclick="shareWhatsApp()"><i class="fab fa-whatsapp"></i>WhatsApp</div>
    <div class="share-option" onclick="shareTwitter()"><i class="fab fa-twitter"></i>Twitter / X</div>
    <div class="share-option" onclick="shareTelegram()"><i class="fab fa-telegram"></i>Telegram</div>
    <div class="share-option" onclick="shareEmail()"><i class="fas fa-envelope"></i>Email</div>
    <div class="share-option" onclick="downloadProfileCard()" style="border-top:1px solid #e5e7eb; margin-top:4px; padding-top:14px;"><i class="fas fa-image"></i><?= $EN ? 'Download Card (PNG)' : 'Unduh Kartu (PNG)' ?></div>
    <div class="share-option" onclick="copyLink()"><i class="fas fa-link"></i><?= $EN ? 'Copy Link' : 'Salin Link' ?></div>
</div>

<!-- CITATION MODAL -->
<div class="dz-modal" id="citeModal" onclick="if(event.target===this)closeCite()">
    <div class="dz-modal-card">
        <div class="dz-modal-head">
            <h3 class="font-serif text-lg"><i class="fas fa-quote-left mr-2"></i><?= $EN ? 'Cite this Faculty' : 'Kutip Profil Dosen' ?></h3>
            <button class="dz-modal-close" onclick="closeCite()"><i class="fas fa-times"></i></button>
        </div>
        <div class="dz-modal-body">
            <div class="dz-cite-tab">
                <button class="active" data-tab="apa">APA</button>
                <button data-tab="bibtex">BibTeX</button>
                <button data-tab="ris">RIS</button>
            </div>
            <div id="citeContent" class="dz-cite-code"></div>
            <button onclick="copyCitation()" class="mt-4 bg-gold text-navy px-5 py-2.5 text-[10px] uppercase tracking-editorial font-bold hover:bg-navy hover:text-gold transition"><i class="fas fa-copy mr-2"></i><?= $EN ? 'Copy Citation' : 'Salin Kutipan' ?></button>
        </div>
    </div>
</div>

<div class="dz-toast" id="dzToast"><i class="fas fa-check-circle"></i><span id="dzToastMsg"></span></div>

<script>
(function(){
    var EN = <?= $EN ? 'true' : 'false' ?>;
    var name = <?= json_encode($full_name) ?>;
    var nidn = <?= json_encode($nidn) ?>;
    var prodi = <?= json_encode(implode(', ', $prodi ?? [])) ?>;
    var siteName = <?= json_encode(site_name()) ?>;
    var photoUrl = <?= json_encode(!empty($l->photo) ? base_url('assets/uploads/' . $l->photo) : '') ?>;

    // ===== PARTICLES =====
    var pc = document.getElementById('dzParticles');
    if (pc) {
        var ctx = pc.getContext('2d'), pts = [];
        function rs(){ pc.width = pc.offsetWidth; pc.height = pc.offsetHeight; }
        rs(); window.addEventListener('resize', rs);
        for (var i=0;i<50;i++) pts.push({x:Math.random()*pc.width, y:Math.random()*pc.height, vx:(Math.random()-.5)*.4, vy:(Math.random()-.5)*.4, s:Math.random()*1.8+.5});
        (function loop(){
            ctx.clearRect(0,0,pc.width,pc.height);
            pts.forEach(function(p){ p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>pc.width)p.vx*=-1; if(p.y<0||p.y>pc.height)p.vy*=-1;
                ctx.beginPath(); ctx.arc(p.x,p.y,p.s,0,Math.PI*2); ctx.fillStyle='rgba(201,162,39,.5)'; ctx.fill(); });
            for (var a=0;a<pts.length;a++) for (var b=a+1;b<pts.length;b++){
                var dx=pts[a].x-pts[b].x, dy=pts[a].y-pts[b].y, d=Math.sqrt(dx*dx+dy*dy);
                if (d<120){ ctx.beginPath(); ctx.moveTo(pts[a].x,pts[a].y); ctx.lineTo(pts[b].x,pts[b].y); ctx.strokeStyle='rgba(201,162,39,'+(.25*(1-d/120))+')'; ctx.lineWidth=.5; ctx.stroke(); }
            }
            requestAnimationFrame(loop);
        })();
    }

    // ===== GLITCH =====
    document.querySelectorAll('.dz-glitch').forEach(function(el){ setTimeout(function(){ el.classList.add('active'); }, 800); });

    // ===== REVEAL =====
    var obs = new IntersectionObserver(function(es){ es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } }); }, { threshold: .12 });
    document.querySelectorAll('.dz-reveal').forEach(function(el){ obs.observe(el); });

    // ===== COUNTERS + RING =====
    var cio = new IntersectionObserver(function(es){ es.forEach(function(e){
        if (!e.isIntersecting) return;
        var el = e.target; if (el.dataset.done) return; el.dataset.done = '1';
        var t = +el.dataset.count, st = performance.now();
        (function f(n){ var p = Math.min(1,(n-st)/1400), ease = 1-Math.pow(1-p,3); el.textContent = Math.round(t*ease).toLocaleString('id-ID'); if(p<1)requestAnimationFrame(f); })(st);
        cio.unobserve(el);
    }); }, { threshold: .3 });
    document.querySelectorAll('.dz-count').forEach(function(el){ cio.observe(el); });

    var rio = new IntersectionObserver(function(es){ es.forEach(function(e){
        if (!e.isIntersecting) return;
        e.target.querySelectorAll('.dz-ring .fg').forEach(function(c){ setTimeout(function(){ c.style.strokeDashoffset = c.dataset.offset; }, 200); });
        rio.unobserve(e.target);
    }); }, { threshold: .2 });
    document.querySelectorAll('.dz-ring-wrap').forEach(function(el){ rio.observe(el); });

    // ===== RADAR CHART =====
    var radarData = <?= json_encode($radar_data) ?>;
    if (radarData && radarData.length >= 3) {
        var svg = document.querySelector('#dzRadar svg');
        var cx = 200, cy = 200, maxR = 150, levels = 4, n = radarData.length;
        var html = '';
        // Grid levels
        for (var l=1; l<=levels; l++) {
            var r = maxR * l/levels; var pts = [];
            for (var i=0;i<n;i++) {
                var a = Math.PI * 2 * i/n - Math.PI/2;
                pts.push((cx + r*Math.cos(a)).toFixed(1) + ',' + (cy + r*Math.sin(a)).toFixed(1));
            }
            html += '<polygon points="' + pts.join(' ') + '" fill="none" stroke="rgba(11,34,57,.15)" stroke-width="1"/>';
        }
        // Axes
        for (var i=0;i<n;i++) {
            var a = Math.PI * 2 * i/n - Math.PI/2;
            html += '<line x1="' + cx + '" y1="' + cy + '" x2="' + (cx + maxR*Math.cos(a)).toFixed(1) + '" y2="' + (cy + maxR*Math.sin(a)).toFixed(1) + '" stroke="rgba(11,34,57,.1)" stroke-width="1"/>';
        }
        // Data polygon
        var dataPts = [];
        for (var i=0;i<n;i++) {
            var a = Math.PI * 2 * i/n - Math.PI/2;
            var v = radarData[i].value/100;
            dataPts.push((cx + maxR*v*Math.cos(a)).toFixed(1) + ',' + (cy + maxR*v*Math.sin(a)).toFixed(1));
        }
        html += '<polygon points="' + dataPts.join(' ') + '" class="dz-radar-poly"/>';
        // Data dots
        for (var i=0;i<n;i++) {
            var a = Math.PI * 2 * i/n - Math.PI/2;
            var v = radarData[i].value/100;
            html += '<circle cx="' + (cx + maxR*v*Math.cos(a)).toFixed(1) + '" cy="' + (cy + maxR*v*Math.sin(a)).toFixed(1) + '" r="4" fill="#C9A227" stroke="#fff" stroke-width="2"/>';
        }
        // Labels
        for (var i=0;i<n;i++) {
            var a = Math.PI * 2 * i/n - Math.PI/2;
            var lx = cx + (maxR + 30)*Math.cos(a), ly = cy + (maxR + 30)*Math.sin(a);
            var anchor = Math.abs(Math.cos(a)) < .1 ? 'middle' : (Math.cos(a) > 0 ? 'start' : 'end');
            var label = radarData[i].label.length > 18 ? radarData[i].label.substring(0,16) + '…' : radarData[i].label;
            html += '<text x="' + lx.toFixed(1) + '" y="' + (ly+4).toFixed(1) + '" text-anchor="' + anchor + '" class="dz-radar-label">' + label + '</text>';
        }
        svg.innerHTML = html;
    }

    // ===== EXPANDABLE TIMELINE =====
    document.querySelectorAll('.dz-tl-item').forEach(function(item){
        item.querySelector('.dz-tl-head').addEventListener('click', function(){ item.classList.toggle('open'); });
    });

    // ===== STICKY TOC ACTIVE =====
    var tocLinks = document.querySelectorAll('.dz-toc-item');
    var sections = Array.from(tocLinks).map(function(l){ return document.getElementById(l.dataset.sec); }).filter(Boolean);
    window.addEventListener('scroll', function(){
        var y = window.scrollY + 150;
        var active = null;
        sections.forEach(function(s){ if (s.offsetTop <= y) active = s; });
        tocLinks.forEach(function(l){ l.classList.toggle('active', active && l.dataset.sec === active.id); });
    });

    // ===== SHARE =====
    var url = window.location.href;
    var fab = document.getElementById('shareFab');
    var panel = document.getElementById('sharePanel');
    fab.addEventListener('click', function(){ panel.classList.toggle('open'); });
    document.addEventListener('click', function(e){ if (!fab.contains(e.target) && !panel.contains(e.target)) panel.classList.remove('open'); });

    window.shareLinkedIn = function(){ window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url), '_blank'); panel.classList.remove('open'); };
    window.shareWhatsApp = function(){ window.open('https://wa.me/?text=' + encodeURIComponent('Profil Dosen: ' + name + ' — ' + url), '_blank'); panel.classList.remove('open'); };
    window.shareTwitter = function(){ window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(name) + '&url=' + encodeURIComponent(url), '_blank'); panel.classList.remove('open'); };
    window.shareTelegram = function(){ window.open('https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(name), '_blank'); panel.classList.remove('open'); };
    window.shareEmail = function(){ window.location.href = 'mailto:?subject=' + encodeURIComponent('Profil Dosen: ' + name) + '&body=' + encodeURIComponent(url); panel.classList.remove('open'); };
    window.copyLink = function(){ if (navigator.clipboard){ navigator.clipboard.writeText(url); showToast(EN ? 'Link copied!' : 'Link disalin!'); } panel.classList.remove('open'); };

    // ===== CITATION =====
    var citeData = { name: name, nidn: nidn, prodi: prodi, site: siteName, year: new Date().getFullYear(), url: url };
    var citations = {
        apa: citeData.name + ' (' + citeData.year + '). ' + citeData.site + ' — Faculty Profile. Retrieved from ' + citeData.url,
        bibtex: '@misc{' + citeData.name.replace(/\s+/g,'_') + citeData.year + ',\n  author = {' + citeData.name + '},\n  title = {Faculty Profile — ' + citeData.site + '},\n  year = {' + citeData.year + '},\n  note = {NIDN: ' + citeData.nidn + '},\n  howpublished = {' + citeData.url + '}\n}',
        ris: 'TY  - GEN\nAU  - ' + citeData.name + '\nTI  - Faculty Profile — ' + citeData.site + '\nPY  - ' + citeData.year + '\nUR  - ' + citeData.url + '\nN1  - NIDN: ' + citeData.nidn + '\nER  -'
    };
    var activeTab = 'apa';
    function renderCite(){ document.getElementById('citeContent').textContent = citations[activeTab]; }
    window.openCitation = function(){ renderCite(); document.getElementById('citeModal').classList.add('open'); };
    window.closeCite = function(){ document.getElementById('citeModal').classList.remove('open'); };
    document.querySelectorAll('.dz-cite-tab button').forEach(function(b){ b.addEventListener('click', function(){
        document.querySelectorAll('.dz-cite-tab button').forEach(function(x){ x.classList.remove('active'); });
        b.classList.add('active'); activeTab = b.dataset.tab; renderCite();
    }); });
    window.copyCitation = function(){ if (navigator.clipboard){ navigator.clipboard.writeText(citations[activeTab]); showToast(EN ? 'Citation copied!' : 'Kutipan disalin!'); } };
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeCite(); });

    // ===== PROFILE CARD PNG =====
    function wrapText(ctx, text, x, y, maxW, lh){
        var words = text.split(' '), line = '';
        for (var i=0;i<words.length;i++){
            var test = line + words[i] + ' ';
            if (ctx.measureText(test).width > maxW && i>0){ ctx.fillText(line, x, y); line = words[i]+' '; y += lh; } else line = test;
        }
        ctx.fillText(line, x, y); return y;
    }
    window.downloadProfileCard = function(){
        var c = document.createElement('canvas'); c.width = 1080; c.height = 1080;
        var x = c.getContext('2d');
        var g = x.createLinearGradient(0,0,0,1080); g.addColorStop(0,'#061420'); g.addColorStop(1,'#13334F');
        x.fillStyle = g; x.fillRect(0,0,1080,1080);
        x.strokeStyle = '#C9A227'; x.lineWidth = 6; x.strokeRect(40,40,1000,1000);
        x.strokeStyle = 'rgba(201,162,39,.3)'; x.lineWidth = 2; x.strokeRect(60,60,960,960);
        // Photo placeholder (gold circle)
        x.fillStyle = '#C9A227'; x.beginPath(); x.arc(540, 280, 130, 0, Math.PI*2); x.fill();
        x.fillStyle = '#0B2239'; x.font = 'bold 120px Georgia'; x.textAlign = 'center';
        x.fillText(name.charAt(0).toUpperCase(), 540, 330);
        // Label
        x.fillStyle = '#C9A227'; x.font = '600 28px Inter'; x.textAlign = 'center';
        x.fillText(EN ? 'FACULTY PROFILE' : 'PROFIL DOSEN', 540, 460);
        // Name
        x.fillStyle = '#F7F5F0'; x.font = '600 52px Georgia';
        var y = wrapText(x, name, 540, 540, 900, 64);
        // NIDN & prodi
        x.fillStyle = '#C9A227'; x.font = '400 28px Inter';
        if (nidn) x.fillText('NIDN: ' + nidn, 540, y + 70);
        x.fillStyle = 'rgba(247,245,240,.8)'; x.font = '400 24px Inter';
        if (prodi) x.fillText(prodi, 540, y + 110);
        // Footer
        x.fillStyle = 'rgba(247,245,240,.5)'; x.font = '26px Inter';
        x.fillText(siteName, 540, 980);
        // Download
        var a = document.createElement('a'); a.download = 'profile-' + name.replace(/\s+/g,'-') + '.png'; a.href = c.toDataURL('image/png'); a.click();
        showToast(EN ? 'Card downloaded!' : 'Kartu diunduh!');
        panel.classList.remove('open');
    };

    // ===== CV PLACEHOLDER =====
    window.downloadCV = function(){ showToast(EN ? 'CV download coming soon!' : 'Fitur unduh CV segera hadir!'); };

    function showToast(msg){
        var t = document.getElementById('dzToast');
        document.getElementById('dzToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }
})();
</script>