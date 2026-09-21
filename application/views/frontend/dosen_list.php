<?php
$EN = (get_site_lang() == 'en');

// ===== STATS & META =====
$total_dzl = count($lecturers);
$prof_count = 0; $doktor_count = 0;
$cloud = []; $letters = []; $featured = [];
foreach ($lecturers as $l) {
    $tf = (string)($l->title_front ?? '');
    if (stripos($tf, 'Prof') !== false) $prof_count++;
    if (preg_match('/\bDr\b/i', $tf)) $doktor_count++;
    $tags = array_values(array_filter(array_map('trim', preg_split('/[,;|]/', (string)($l->expertise ?? '')))));
    foreach ($tags as $t) { $k = ucfirst(strtolower($t)); $cloud[$k] = ($cloud[$k] ?? 0) + 1; }
    $L = strtoupper(substr(trim($l->name), 0, 1));
    if (preg_match('/^[A-Z]$/', $L)) $letters[$L] = true;
    $featured[] = $l;   // SEMUA dosen masuk spotlight (tidak harus punya foto)
}
$unique_expertise = count($cloud);
arsort($cloud);
$cloud = array_slice($cloud, 0, 14, true);
ksort($letters);
// TIDAK ada slice — spotlight mengikuti jumlah REAL dosen
?>

<style>
/* ===== HERO ===== */
.dzl-hero { position: relative; overflow: hidden; background: linear-gradient(165deg, #061420 0%, #0B2239 60%, #13334F 100%); }
#dzlConstellation { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2; }
.dzl-hero-glow { position: absolute; top: -40%; left: 50%; transform: translateX(-50%); width: 160%; height: 120%;
    background: conic-gradient(from 0deg at 50% 0%, transparent 0deg, rgba(201,162,39,.12) 20deg, transparent 45deg, transparent 315deg, rgba(201,162,39,.08) 340deg, transparent 360deg);
    animation: dzlSway 10s ease-in-out infinite alternate; pointer-events: none; }
@keyframes dzlSway { from { transform: translateX(-50%) rotate(-6deg); } to { transform: translateX(-50%) rotate(6deg); } }

.dzl-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; }
.dzl-stat { background: rgba(247,245,240,.05); border: 1px solid rgba(201,162,39,.25); padding: 20px; text-align: center; }
.dzl-stat-num { font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 300; color: #C9A227; line-height: 1; }
.dzl-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .2em; color: rgba(247,245,240,.6); margin-top: 6px; font-weight: 600; }

/* ===== SPOTLIGHT ===== */
.dzl-spot-wrap { position: relative; max-width: 860px; margin: 0 auto; }
.dzl-spot-viewport { position: relative; min-height: 300px; }
.dzl-spot-slide { position: absolute; inset: 0; display: flex; gap: 2rem; align-items: center;
    background: #fff; border: 1px solid #e5e7eb; border-top: 4px solid #C9A227;
    opacity: 0; transform: translateX(60px); transition: opacity .6s, transform .6s cubic-bezier(.22,1,.36,1);
    pointer-events: none; padding: 2rem; }
.dzl-spot-slide.active { opacity: 1; transform: none; pointer-events: auto; }
.dzl-spot-photo { width: 200px; height: 240px; flex-shrink: 0; overflow: hidden; border: 3px solid #C9A227; }
.dzl-spot-photo img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
.dzl-spot-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 5; width: 42px; height: 42px; border-radius: 50%;
    background: #0B2239; color: #C9A227; border: 1px solid rgba(201,162,39,.4); cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; }
.dzl-spot-arrow:hover { background: #C9A227; color: #0B2239; }
.dzl-spot-arrow.left { left: -14px; } .dzl-spot-arrow.right { right: -14px; }
.dzl-spot-dots { display: flex; gap: .5rem; justify-content: center; margin-top: 1.25rem; }
.dzl-spot-dots button { width: 10px; height: 10px; border-radius: 50%; border: none; background: #d1d5db; cursor: pointer; transition: all .3s; padding: 0; }
.dzl-spot-dots button.active { background: #C9A227; transform: scale(1.3); }
@media (max-width: 640px) { .dzl-spot-slide { flex-direction: column; text-align: center; } .dzl-spot-viewport { min-height: 420px; } }

/* ===== TAG CLOUD ===== */
.dzl-tagcloud { display: flex; flex-wrap: wrap; gap: .6rem; align-items: center; justify-content: center; }
.tc-chip { padding: .4rem .9rem; border: 1px solid #e5e7eb; background: #fff; color: #475569; border-radius: 999px; cursor: pointer; transition: all .25s; }
.tc-chip:hover { border-color: #C9A227; color: #0B2239; transform: translateY(-2px); }
.tc-chip.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }
.tc-chip sup { color: #C9A227; font-weight: 700; }

/* ===== TOOLBAR / LETTERS ===== */
.dzl-letters { display: flex; flex-wrap: wrap; gap: 4px; }
.dzl-letters button { width: 30px; height: 30px; font-size: 11px; font-weight: 700; border: 1px solid #e5e7eb; background: #fff; color: #0B2239; cursor: pointer; transition: all .2s; }
.dzl-letters button:hover:not(:disabled) { background: #C9A227; border-color: #C9A227; }
.dzl-letters button.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }
.dzl-letters button:disabled { opacity: .25; cursor: not-allowed; }

/* ===== CARDS (3D TILT + GLARE) ===== */
.dzl-card { transition: box-shadow .4s; transform-style: preserve-3d; will-change: transform; position: relative; }
.dzl-card:hover { box-shadow: 0 24px 48px rgba(11,34,57,.18); }
.dzl-glare { position: absolute; inset: 0; z-index: 5; pointer-events: none; opacity: 0; transition: opacity .3s;
    background: radial-gradient(circle at var(--gx,50%) var(--gy,50%), rgba(255,255,255,.3), transparent 60%); }
.dzl-card:hover .dzl-glare { opacity: 1; }
.dzl-scan { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(201,162,39,.25) 50%, transparent 60%); transform: translateY(-100%); opacity: 0; pointer-events: none; }
.dzl-card:hover .dzl-scan { animation: dzlScan 1.2s ease; }
@keyframes dzlScan { 0% { transform: translateY(-100%); opacity: 1; } 100% { transform: translateY(100%); opacity: 1; } }
.dzl-quick { position: absolute; top: 10px; right: 10px; z-index: 6; width: 36px; height: 36px; border-radius: 50%;
    background: rgba(11,34,57,.75); color: #C9A227; border: 1px solid rgba(201,162,39,.5); cursor: pointer;
    opacity: 0; transform: scale(.7); transition: all .25s; display: flex; align-items: center; justify-content: center; }
.dzl-card:hover .dzl-quick { opacity: 1; transform: scale(1); }
.dzl-quick:hover { background: #C9A227; color: #0B2239; }
.dzl-tag { transition: all .25s; }
.dzl-tag:hover { background: #0B2239; color: #C9A227; }
.dzl-hidden { display: none !important; }

/* ===== LIST VIEW ===== */
#dzlGrid.view-list { grid-template-columns: 1fr !important; }
#dzlGrid.view-list .dzl-card { flex-direction: row !important; }
#dzlGrid.view-list .dzl-card .dzl-photo { width: 220px; flex-shrink: 0; aspect-ratio: 4/3; }
@media (max-width: 640px) { #dzlGrid.view-list .dzl-card { flex-direction: column !important; } #dzlGrid.view-list .dzl-card .dzl-photo { width: 100%; } }

/* ===== MODAL ===== */
.dzl-modal { position: fixed; inset: 0; z-index: 100; background: rgba(6,20,32,.85); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; padding: 1rem; }
.dzl-modal.open { display: flex; animation: dzlIn .3s ease; }
@keyframes dzlIn { from { opacity: 0; } to { opacity: 1; } }
.dzl-modal-card { background: #fff; width: 100%; max-width: 620px; max-height: 88vh; overflow-y: auto; border-top: 4px solid #C9A227; box-shadow: 0 24px 64px rgba(0,0,0,.4); position: relative; }
.dzl-modal-close { position: absolute; top: 12px; right: 12px; z-index: 5; width: 36px; height: 36px; border-radius: 50%; border: none; background: #0B2239; color: #C9A227; cursor: pointer; }
.dzl-modal-body { display: flex; gap: 1.5rem; padding: 2rem; }
.dzl-modal-photo { width: 180px; height: 220px; flex-shrink: 0; overflow: hidden; border: 3px solid #C9A227; }
.dzl-modal-photo img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
@media (max-width: 640px) { .dzl-modal-body { flex-direction: column; } .dzl-modal-photo { width: 100%; height: 260px; } }

@media print {
    .dzl-hero, .dzl-tagcloud, .dzl-toolbar, .dzl-quick, .dzl-modal, #dzlConstellation { display: none !important; }
    .dzl-card { box-shadow: none !important; break-inside: avoid; }
}
</style>

<!-- ============ CINEMATIC HERO ============ -->
<section class="dzl-hero text-ivory">
    <div class="dzl-hero-glow"></div>
    <canvas id="dzlConstellation"></canvas>
    <div class="container mx-auto px-6 py-20 md:py-24 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl">
                <p class="editorial-label text-gold mb-5"><i class="fas fa-chalkboard-teacher mr-2"></i><?= $EN ? 'Our Faculty' : 'Tenaga Pengajar' ?></p>
                <h1 class="font-serif text-5xl md:text-6xl font-light text-ivory tracking-tight leading-[1.05]">
                    <span class="dzl-scr" data-text="<?= $EN ? 'Faculty' : 'Direktori' ?>"><?= $EN ? 'Faculty' : 'Direktori' ?></span>
                    <em class="italic text-gold dzl-scr" data-text="<?= $EN ? 'Directory' : 'Dosen' ?>"><?= $EN ? 'Directory' : 'Dosen' ?></em>
                </h1>
                <p class="text-ivory/70 mt-5 text-lg leading-relaxed">
                    <?= $EN ? 'Our outstanding academics and researchers who guide your intellectual journey.' : 'Para akademisi dan peneliti unggulan yang membimbing perjalanan intelektual Anda.' ?>
                </p>
            </div>
        </div>

        <!-- STATS -->
        <div class="dzl-stats mt-10">
            <div class="dzl-stat"><div class="dzl-stat-num dzl-count" data-count="<?= $total_dzl ?>">0</div><div class="dzl-stat-label"><?= $EN ? 'Total Faculty' : 'Total Dosen' ?></div></div>
            <div class="dzl-stat"><div class="dzl-stat-num dzl-count" data-count="<?= $prof_count ?>">0</div><div class="dzl-stat-label"><?= $EN ? 'Professors' : 'Profesor' ?></div></div>
            <div class="dzl-stat"><div class="dzl-stat-num dzl-count" data-count="<?= $doktor_count ?>">0</div><div class="dzl-stat-label"><?= $EN ? 'Doctors' : 'Doktor' ?></div></div>
            <div class="dzl-stat"><div class="dzl-stat-num dzl-count" data-count="<?= $unique_expertise ?>">0</div><div class="dzl-stat-label"><?= $EN ? 'Expertise Fields' : 'Bidang Keahlian' ?></div></div>
        </div>
    </div>
</section>

<!-- ============ FACULTY SPOTLIGHT ============ -->
<?php if (!empty($featured)): ?>
<section class="py-16 bg-white border-b border-gray-200">
    <div class="container mx-auto px-6">
        <div class="text-center mb-10">
            <p class="editorial-label text-gold-muted mb-3"><i class="fas fa-star mr-2"></i><?= $EN ? 'Faculty Spotlight' : 'Sorotan Dosen' ?></p>
            <h2 class="font-serif text-3xl md:text-4xl font-light text-navy"><?= $EN ? 'Meet our <em class="italic text-gold-muted">distinguished</em> faculty' : 'Temui dosen <em class="italic text-gold-muted">terpilih</em> kami' ?></h2>
        </div>
        <div class="dzl-spot-wrap" id="dzlSpotWrap">
            <button class="dzl-spot-arrow left" id="dzlSpotPrev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <div class="dzl-spot-viewport">
                <?php foreach ($featured as $i => $f):
                    $ftags = array_values(array_filter(array_map('trim', preg_split('/[,;|]/', (string)($f->expertise ?? '')))));
                ?>
                <div class="dzl-spot-slide <?= $i === 0 ? 'active' : '' ?>">
                    <div class="dzl-spot-photo">
                        <?php if (!empty($f->photo)): ?>
                            <img src="<?= base_url('assets/uploads/' . $f->photo) ?>" alt="<?= html_escape($f->name) ?>">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center">
                                <span class="font-serif text-6xl font-light text-gold/60"><?= strtoupper(substr($f->name, 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <p class="editorial-label text-gold-muted mb-2"><?= html_escape($f->prodi_names ?? '') ?></p>
                        <h3 class="font-serif text-2xl md:text-3xl font-medium text-navy mb-2"><?= html_escape(trim(($f->title_front ?? '') . ' ' . $f->name)) ?><?= $f->title_back ? ', ' . html_escape($f->title_back) : '' ?></h3>
                        <p class="text-sm text-slate mb-4 leading-relaxed"><?= html_escape(implode(' • ', array_slice($ftags, 0, 3))) ?></p>
                        <a href="<?= base_url('dosen/detail/' . $f->id) ?>" class="inline-block bg-gold text-navy px-6 py-2.5 text-xs uppercase tracking-editorial font-bold hover:bg-navy hover:text-gold transition"><?= $EN ? 'View Profile' : 'Lihat Profil' ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="dzl-spot-arrow right" id="dzlSpotNext" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="dzl-spot-dots" id="dzlSpotDots"></div>
    </div>
</section>
<?php endif; ?>

<!-- ============ EXPERTISE TAG CLOUD ============ -->
<?php if (!empty($cloud)): ?>
<section class="py-14 bg-ivory border-b border-gray-200">
    <div class="container mx-auto px-6 text-center">
        <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Explore by expertise' : 'Telusuri per keahlian' ?></p>
        <div class="dzl-tagcloud" id="dzlCloud">
            <?php foreach ($cloud as $tag => $c): $size = 11 + min(10, $c * 3); ?>
            <button type="button" class="tc-chip" data-tag="<?= strtolower(html_escape($tag)) ?>" style="font-size:<?= $size ?>px"><?= html_escape($tag) ?> <sup><?= $c ?></sup></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ GRID ============ -->
<section class="py-16 bg-ivory">
    <div class="container mx-auto px-6">

        <!-- SEARCH + PRODI -->
        <?php if (!empty($lecturers)): ?>
        <div class="max-w-3xl space-y-4 mb-8">
            <div class="relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate"></i>
                <input type="text" id="dzlSearch" placeholder="<?= $EN ? 'Search name, expertise, or NIDN...' : 'Cari nama, keahlian, atau NIDN...' ?>"
                    class="w-full pl-12 pr-5 py-4 bg-white border border-navy/15 text-navy shadow-sm focus:border-gold outline-none transition">
            </div>
            <?php if (!empty($programs)): ?>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="dzl-filter-btn bg-navy text-ivory px-4 py-2 text-xs uppercase tracking-wider font-semibold" data-prodi=""><?= $EN ? 'All Programs' : 'Semua Prodi' ?></button>
                <?php foreach ($programs as $prog): ?>
                <button type="button" class="dzl-filter-btn bg-white border border-navy/20 text-navy px-4 py-2 text-xs uppercase tracking-wider font-semibold" data-prodi="<?= $prog->id ?>"><?= html_escape($prog->name) ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- TOOLBAR: LETTERS + SORT + VIEW -->
        <div class="dzl-toolbar flex flex-col lg:flex-row lg:items-center gap-4 justify-between mb-8 bg-white border border-gray-200 p-4">
            <div class="dzl-letters" id="dzlLetters">
                <?php foreach (range('A','Z') as $ch): $on = isset($letters[$ch]); ?>
                <button type="button" data-letter="<?= $ch ?>" <?= $on ? '' : 'disabled' ?>><?= $ch ?></button>
                <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-2">
                <select id="dzlSort" class="px-3 py-2 border border-navy/20 bg-white text-navy text-xs uppercase tracking-wider font-semibold">
                    <option value="default"><?= $EN ? 'Sort: Default' : 'Urut: Bawaan' ?></option>
                    <option value="name"><?= $EN ? 'Name A-Z' : 'Nama A-Z' ?></option>
                    <option value="rank"><?= $EN ? 'Seniority (Prof first)' : 'Senioritas (Prof dulu)' ?></option>
                </select>
                <button type="button" class="dzl-view-btn active px-3 py-2 border border-navy/20 bg-navy text-ivory" data-view="grid" title="Grid"><i class="fas fa-th"></i></button>
                <button type="button" class="dzl-view-btn px-3 py-2 border border-navy/20 bg-white text-navy" data-view="list" title="List"><i class="fas fa-list"></i></button>
            </div>
        </div>
        <p class="text-xs text-slate italic mb-6" id="dzlCount"><?= count($lecturers) ?> <?= $EN ? 'faculty members' : 'dosen ditampilkan' ?></p>
        <?php endif; ?>

        <?php if (empty($lecturers)): ?>
            <div class="text-center py-24 bg-white border border-gray-200">
                <i class="fas fa-user-tie text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'Faculty directory is not available yet.' : 'Direktori dosen belum tersedia.' ?></p>
            </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="dzlGrid">
            <?php foreach ($lecturers as $l):
                $tags = array_values(array_filter(array_map('trim', preg_split('/[,;|]/', (string)($l->expertise ?? '')))));
                $prodi_ids = isset($l->prodi_ids) ? explode(',', $l->prodi_ids) : [];
                $tf = (string)($l->title_front ?? '');
                $rank = (stripos($tf, 'Prof') !== false ? 3 : 0) + (preg_match('/\bDr\b/i', $tf) ? 2 : 0);
                $letter = strtoupper(substr(trim($l->name), 0, 1));
                if (!preg_match('/^[A-Z]$/', $letter)) $letter = '#';
                $display = trim(($l->title_front ?? '') . ' ' . $l->name);
            ?>
            <div class="dzl-card bg-white border border-gray-200 group fade-in overflow-hidden relative flex flex-col"
                 data-name="<?= strtolower(html_escape($l->name)) ?>"
                 data-displayname="<?= strtolower(html_escape($display)) ?>"
                 data-expertise="<?= strtolower(html_escape(implode(' ', $tags))) ?>"
                 data-nidn="<?= strtolower(html_escape($l->nidn ?? '')) ?>"
                 data-prodi="<?= html_escape(implode(',', $prodi_ids)) ?>"
                 data-letter="<?= $letter ?>"
                 data-rank="<?= $rank ?>"
                 data-fullname="<?= html_escape($display) ?><?= $l->title_back ? ', ' . html_escape($l->title_back) : '' ?>"
                 data-prodinames="<?= html_escape($l->prodi_names ?? '') ?>"
                 data-tags="<?= html_escape(implode(',', $tags)) ?>"
                 data-photo="<?= $l->photo ? base_url('assets/uploads/' . $l->photo) : '' ?>"
                 data-profile="<?= base_url('dosen/detail/' . $l->id) ?>"
                 data-scholar="<?= html_escape($l->google_scholar_url ?? '') ?>"
                 data-sinta="<?= html_escape($l->sinta_url ?? '') ?>"
                 data-blog="<?= base_url('lecturerblog/view/' . $l->id) ?>">
                <div class="dzl-glare"></div>
                <div class="absolute top-3 left-3 z-10 bg-navy/90 text-gold font-mono text-[10px] px-2.5 py-1 tracking-wider">ID · <?= str_pad($l->id, 4, '0', STR_PAD_LEFT) ?></div>

                <div class="dzl-photo aspect-[4/3] bg-gray-200 overflow-hidden relative">
                    <?php if ($l->photo): ?>
                        <img src="<?= base_url('assets/uploads/' . $l->photo) ?>" class="w-full h-full object-cover object-top transition duration-700 group-hover:scale-105" alt="<?= html_escape($l->name) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center">
                            <span class="font-serif text-7xl font-light text-gold/40"><?= strtoupper(substr($l->name, 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="dzl-scan"></div>
                    <button type="button" class="dzl-quick" data-quick title="Quick View"><i class="fas fa-eye"></i></button>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <h3 class="font-serif text-xl font-medium text-navy leading-snug group-hover:text-gold-muted transition"><?= html_escape($display) ?><?= $l->title_back ? ', ' . html_escape($l->title_back) : '' ?></h3>
                    <p class="text-[10px] uppercase tracking-editorial text-slate mt-2">NIDN <?= html_escape($l->nidn ?? '-') ?></p>
                    <div class="w-10 h-px bg-gold my-4"></div>
                    <?php if (!empty($tags)): ?>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <?php foreach (array_slice($tags, 0, 3) as $t): ?>
                            <span class="dzl-tag bg-ivory-warm border border-navy/10 px-2.5 py-1 text-[11px] text-navy"><?= html_escape($t) ?></span>
                        <?php endforeach; ?>
                        <?php if (count($tags) > 3): ?><span class="text-[11px] text-gold-muted font-semibold">+<?= count($tags) - 3 ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($l->prodi_names): ?><p class="text-xs text-gold-muted mt-1 font-semibold"><?= html_escape($l->prodi_names) ?></p><?php endif; ?>
                    <div class="flex gap-2 mt-auto pt-5 border-t border-gray-100">
                        <a href="<?= base_url('dosen/detail/' . $l->id) ?>" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-bold bg-gold text-navy py-2.5 hover:bg-navy hover:text-gold transition"><i class="fas fa-id-card mr-1"></i><?= $EN ? 'Profile' : 'Profil' ?></a>
                        <?php if ($l->google_scholar_url): ?><a href="<?= html_escape($l->google_scholar_url) ?>" target="_blank" rel="noopener" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition"><i class="fas fa-graduation-cap mr-1"></i>Scholar</a><?php endif; ?>
                        <?php if ($l->sinta_url): ?><a href="<?= html_escape($l->sinta_url) ?>" target="_blank" rel="noopener" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition"><i class="fas fa-chart-line mr-1"></i>SINTA</a><?php endif; ?>
                        <a href="<?= base_url('lecturerblog/view/' . $l->id) ?>" class="flex-1 text-center text-[10px] uppercase tracking-editorial font-semibold border border-navy/20 text-navy py-2.5 hover:bg-navy hover:text-ivory transition"><i class="fas fa-pen-nib mr-1"></i>Blog</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="dzlEmpty" class="dzl-hidden text-center py-16 bg-white border border-gray-200">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light"><?= $EN ? 'No matching faculty found.' : 'Dosen yang cocok tidak ditemukan.' ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ QUICK VIEW MODAL ============ -->
<div class="dzl-modal" id="dzlModal" onclick="if(event.target===this)dzlCloseModal()">
    <div class="dzl-modal-card">
        <button class="dzl-modal-close" onclick="dzlCloseModal()"><i class="fas fa-times"></i></button>
        <div class="dzl-modal-body">
            <div class="dzl-modal-photo"><img id="qmImg" src="" alt=""></div>
            <div class="flex-1">
                <p class="editorial-label text-gold-muted mb-1" id="qmProdi"></p>
                <h3 class="font-serif text-2xl font-medium text-navy mb-1" id="qmName"></h3>
                <p class="text-xs text-slate uppercase tracking-wider mb-3" id="qmNidn"></p>
                <div class="flex flex-wrap gap-2 mb-5" id="qmTags"></div>
                <div class="flex flex-wrap gap-2" id="qmLinks"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    // ===== CONSTELLATION =====
    var cc = document.getElementById('dzlConstellation');
    if (cc) {
        var ctx = cc.getContext('2d'), pts = [], mouse = {x:-999,y:-999};
        function rs(){ cc.width = cc.offsetWidth; cc.height = cc.offsetHeight; }
        rs(); window.addEventListener('resize', rs);
        cc.parentElement.addEventListener('mousemove', function(e){ var r = cc.getBoundingClientRect(); mouse.x = e.clientX-r.left; mouse.y = e.clientY-r.top; });
        cc.parentElement.addEventListener('mouseleave', function(){ mouse.x=-999; mouse.y=-999; });
        var N = Math.min(70, Math.floor(cc.offsetWidth/22));
        for (var i=0;i<N;i++) pts.push({x:Math.random()*cc.width,y:Math.random()*cc.height,vx:(Math.random()-.5)*.3,vy:(Math.random()-.5)*.3,s:Math.random()*1.5+.8});
        (function loop(){
            ctx.clearRect(0,0,cc.width,cc.height);
            pts.forEach(function(p){ p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>cc.width)p.vx*=-1; if(p.y<0||p.y>cc.height)p.vy*=-1;
                var dx=mouse.x-p.x, dy=mouse.y-p.y, d=Math.sqrt(dx*dx+dy*dy); if(d<120&&d>0){p.x+=dx/d*.3;p.y+=dy/d*.3;}
                ctx.beginPath(); ctx.arc(p.x,p.y,p.s,0,Math.PI*2); ctx.fillStyle='rgba(201,162,39,.7)'; ctx.fill(); });
            for (var a=0;a<pts.length;a++) for (var b=a+1;b<pts.length;b++){ var dx=pts[a].x-pts[b].x,dy=pts[a].y-pts[b].y,d=Math.sqrt(dx*dx+dy*dy);
                if(d<100){ ctx.beginPath(); ctx.moveTo(pts[a].x,pts[a].y); ctx.lineTo(pts[b].x,pts[b].y); ctx.strokeStyle='rgba(201,162,39,'+(.3*(1-d/100))+')'; ctx.lineWidth=.5; ctx.stroke(); } }
            requestAnimationFrame(loop);
        })();
    }

    // ===== SCRAMBLE DECODE =====
    document.querySelectorAll('.dzl-scr').forEach(function(el){
        var fin = el.dataset.text, glyphs = '#@%&$!?<>*+=', i = 0;
        var iv = setInterval(function(){
            el.textContent = fin.split('').map(function(c,idx){ return idx<i ? c : glyphs[Math.floor(Math.random()*glyphs.length)]; }).join('');
            i++; if (i > fin.length) { clearInterval(iv); el.textContent = fin; }
        }, 50);
    });

    // ===== COUNTERS =====
    var cio = new IntersectionObserver(function(es){ es.forEach(function(e){ if(!e.isIntersecting)return; var el=e.target; if(el.dataset.done)return; el.dataset.done='1';
        var t=+el.dataset.count, st=performance.now();
        (function f(n){ var p=Math.min(1,(n-st)/1400), ease=1-Math.pow(1-p,3); el.textContent=Math.round(t*ease).toLocaleString('id-ID'); if(p<1)requestAnimationFrame(f); })(st);
        cio.unobserve(el); }); }, {threshold:.3});
    document.querySelectorAll('.dzl-count').forEach(function(el){ cio.observe(el); });

    // ===== SPOTLIGHT CAROUSEL =====
    var slides = document.querySelectorAll('.dzl-spot-slide');
    if (slides.length) {
        var sIdx=0, sTimer=null, dotsWrap=document.getElementById('dzlSpotDots');
        slides.forEach(function(_,i){ var d=document.createElement('button'); if(i===0)d.classList.add('active');
            d.addEventListener('click',function(){ goSlide(i); restartAuto(); }); dotsWrap.appendChild(d); });
        function goSlide(n){ sIdx=(n+slides.length)%slides.length;
            slides.forEach(function(s,i){ s.classList.toggle('active', i===sIdx); });
            dotsWrap.querySelectorAll('button').forEach(function(d,i){ d.classList.toggle('active', i===sIdx); }); }
        function restartAuto(){ if(sTimer)clearInterval(sTimer); sTimer=setInterval(function(){ goSlide(sIdx+1); },5000); }
        document.getElementById('dzlSpotNext').addEventListener('click',function(){ goSlide(sIdx+1); restartAuto(); });
        document.getElementById('dzlSpotPrev').addEventListener('click',function(){ goSlide(sIdx-1); restartAuto(); });
        var wrap=document.getElementById('dzlSpotWrap');
        wrap.addEventListener('mouseenter',function(){ if(sTimer)clearInterval(sTimer); });
        wrap.addEventListener('mouseleave',restartAuto);
        restartAuto();
    }

    // ===== 3D TILT + GLARE =====
    document.querySelectorAll('.dzl-card').forEach(function(card){
        card.addEventListener('mousemove',function(e){ var r=card.getBoundingClientRect();
            var px=(e.clientX-r.left)/r.width-.5, py=(e.clientY-r.top)/r.height-.5;
            card.style.transform='perspective(900px) rotateY('+(px*8)+'deg) rotateX('+(-py*8)+'deg) translateY(-6px)';
            card.style.setProperty('--gx',((px+.5)*100)+'%'); card.style.setProperty('--gy',((py+.5)*100)+'%'); });
        card.addEventListener('mouseleave',function(){ card.style.transform=''; });
    });

    // ===== UNIFIED FILTER =====
    var input=document.getElementById('dzlSearch'), grid=document.getElementById('dzlGrid');
    var cards=grid?Array.prototype.slice.call(grid.querySelectorAll('.dzl-card')):[];
    var empty=document.getElementById('dzlEmpty'), count=document.getElementById('dzlCount');
    var state={q:'',prodi:'',letter:'',tag:''};
    function applyFilter(){ var shown=0;
        cards.forEach(function(c){
            var mQ=!state.q||(c.dataset.name||'').indexOf(state.q)>-1||(c.dataset.expertise||'').indexOf(state.q)>-1||(c.dataset.nidn||'').indexOf(state.q)>-1;
            var mP=!state.prodi||(c.dataset.prodi||'').split(',').indexOf(state.prodi)>-1;
            var mL=!state.letter||c.dataset.letter===state.letter;
            var mT=!state.tag||(c.dataset.expertise||'').indexOf(state.tag)>-1;
            var hit=mQ&&mP&&mL&&mT; c.classList.toggle('dzl-hidden',!hit); if(hit)shown++; });
        if(empty)empty.classList.toggle('dzl-hidden',shown>0);
        if(count)count.textContent=shown+' <?= $EN ? "faculty members" : "dosen ditampilkan" ?>'; }
    if(input)input.addEventListener('input',function(){ state.q=this.value.toLowerCase().trim(); applyFilter(); });

    document.querySelectorAll('.dzl-filter-btn').forEach(function(btn){ btn.addEventListener('click',function(){
        document.querySelectorAll('.dzl-filter-btn').forEach(function(b){ b.classList.remove('bg-navy','text-ivory'); b.classList.add('bg-white','border','border-navy/20','text-navy'); });
        btn.classList.remove('bg-white','border','border-navy/20','text-navy'); btn.classList.add('bg-navy','text-ivory');
        state.prodi=btn.dataset.prodi; applyFilter(); }); });

    document.querySelectorAll('#dzlLetters button').forEach(function(btn){ btn.addEventListener('click',function(){
        var on=btn.classList.contains('active');
        document.querySelectorAll('#dzlLetters button').forEach(function(b){ b.classList.remove('active'); });
        if(!on){ btn.classList.add('active'); state.letter=btn.dataset.letter; } else state.letter=''; applyFilter(); }); });

    document.querySelectorAll('.tc-chip').forEach(function(ch){ ch.addEventListener('click',function(){
        var on=ch.classList.contains('active');
        document.querySelectorAll('.tc-chip').forEach(function(c){ c.classList.remove('active'); });
        if(!on){ ch.classList.add('active'); state.tag=ch.dataset.tag; } else state.tag=''; applyFilter(); }); });

    // ===== SORT =====
    var sortSel=document.getElementById('dzlSort');
    if(sortSel)sortSel.addEventListener('change',function(){ var v=this.value; var arr=cards.slice();
        if(v==='name')arr.sort(function(a,b){return (a.dataset.displayname||'').localeCompare(b.dataset.displayname||'');});
        else if(v==='rank')arr.sort(function(a,b){return (+b.dataset.rank)-(+a.dataset.rank)||(a.dataset.displayname||'').localeCompare(b.dataset.displayname||'');});
        arr.forEach(function(c){ grid.appendChild(c); }); });

    // ===== VIEW TOGGLE =====
    document.querySelectorAll('.dzl-view-btn').forEach(function(btn){ btn.addEventListener('click',function(){
        document.querySelectorAll('.dzl-view-btn').forEach(function(b){ b.classList.remove('bg-navy','text-ivory','active'); b.classList.add('bg-white','text-navy'); });
        btn.classList.add('bg-navy','text-ivory','active'); btn.classList.remove('bg-white','text-navy');
        grid.classList.toggle('view-list', btn.dataset.view==='list'); }); });

    // ===== QUICK VIEW MODAL =====
    grid.addEventListener('click',function(e){ var q=e.target.closest('[data-quick]'); if(!q)return;
        var card=q.closest('.dzl-card'), d=card.dataset;
        document.getElementById('qmImg').src=d.photo||'';
        document.getElementById('qmName').textContent=d.fullname;
        document.getElementById('qmNidn').textContent='NIDN '+(d.nidn||'-').toUpperCase();
        document.getElementById('qmProdi').textContent=d.prodinames||'';
        var tagsWrap=document.getElementById('qmTags'); tagsWrap.innerHTML='';
        (d.tags?d.tags.split(','):[]).forEach(function(t){ if(!t)return; var s=document.createElement('span'); s.className='dzl-tag bg-ivory-warm border border-navy/10 px-2.5 py-1 text-[11px] text-navy'; s.textContent=t; tagsWrap.appendChild(s); });
        var links=document.getElementById('qmLinks'); links.innerHTML='';
        function addLink(href,label,icon,ext){ var a=document.createElement('a'); a.href=href; a.className='bg-gold text-navy px-4 py-2 text-[10px] uppercase tracking-editorial font-bold hover:bg-navy hover:text-gold transition'; if(ext){a.target='_blank';a.rel='noopener';} a.innerHTML='<i class="fas '+icon+' mr-1"></i>'+label; links.appendChild(a); }
        addLink(d.profile,'<?= $EN ? "Profile" : "Profil" ?>','fa-id-card',false);
        if(d.scholar)addLink(d.scholar,'Scholar','fa-graduation-cap',true);
        if(d.sinta)addLink(d.sinta,'SINTA','fa-chart-line',true);
        addLink(d.blog,'Blog','fa-pen-nib',false);
        document.getElementById('dzlModal').classList.add('open'); });
    window.dzlCloseModal=function(){ document.getElementById('dzlModal').classList.remove('open'); };
    document.addEventListener('keydown',function(e){ if(e.key==='Escape')dzlCloseModal(); });
})();
</script>