<?php
$EN = (get_site_lang() == 'en');
$total_dl = array_sum(array_map(function ($d) { return (int)$d->download_count; }, $documents));

// 🔥 Recent downloads (top 5)
$recent = array_slice($documents, 0, 5);
usort($recent, function($a, $b){ return $b->download_count - $a->download_count; });
$recent = array_slice($recent, 0, 5);

if (!function_exists('dv_size')) {
    function dv_size($kb) {
        $kb = (float)$kb;
        return $kb >= 1024 ? round($kb / 1024, 1) . ' MB' : round($kb) . ' KB';
    }
}

$ext_map = [
    'pdf'  => ['icon' => 'fa-file-pdf',        'cls' => 'dv-pdf'],
    'doc'  => ['icon' => 'fa-file-word',       'cls' => 'dv-doc'],
    'docx' => ['icon' => 'fa-file-word',       'cls' => 'dv-doc'],
    'xls'  => ['icon' => 'fa-file-excel',      'cls' => 'dv-xls'],
    'xlsx' => ['icon' => 'fa-file-excel',      'cls' => 'dv-xls'],
    'csv'  => ['icon' => 'fa-file-csv',        'cls' => 'dv-xls'],
    'ppt'  => ['icon' => 'fa-file-powerpoint', 'cls' => 'dv-ppt'],
    'pptx' => ['icon' => 'fa-file-powerpoint', 'cls' => 'dv-ppt'],
    'zip'  => ['icon' => 'fa-file-zipper',     'cls' => 'dv-zip'],
    'rar'  => ['icon' => 'fa-file-zipper',     'cls' => 'dv-zip'],
    'jpg'  => ['icon' => 'fa-file-image',      'cls' => 'dv-img', 'preview' => true],
    'jpeg' => ['icon' => 'fa-file-image',      'cls' => 'dv-img', 'preview' => true],
    'png'  => ['icon' => 'fa-file-image',      'cls' => 'dv-img', 'preview' => true],
];
?>

<style>
/* ===== VAULT HERO ===== */
.vault-hero { position: relative; overflow: hidden; background: linear-gradient(160deg, #061420 0%, #0B2239 55%, #13334F 100%); }
.vault-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(201,162,39,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(201,162,39,.05) 1px, transparent 1px); background-size: 44px 44px; }
.vault-ring { position: absolute; border: 1px dashed rgba(201,162,39,.25); border-radius: 50%; pointer-events: none; }
.vault-ring.r1 { width: 480px; height: 480px; right: -120px; top: -140px; animation: dvSpin 40s linear infinite; }
.vault-ring.r2 { width: 320px; height: 320px; right: -40px; top: -60px; border-color: rgba(201,162,39,.4); animation: dvSpin 28s linear infinite reverse; }
.vault-ring.r3 { width: 620px; height: 620px; left: -260px; bottom: -320px; animation: dvSpin 55s linear infinite; }
@keyframes dvSpin { to { transform: rotate(360deg); } }
.vault-lock { position: absolute; right: 8%; top: 50%; transform: translateY(-50%); width: 120px; height: 120px; border: 2px solid rgba(201,162,39,.35); border-radius: 50%; display: none; align-items: center; justify-content: center; }
@media (min-width: 1024px) { .vault-lock { display: flex; } }
.vault-lock::before { content: ''; position: absolute; inset: 10px; border: 1px dashed rgba(201,162,39,.5); border-radius: 50%; animation: dvSpin 20s linear infinite; }
.vault-lock i { font-size: 2.2rem; color: rgba(201,162,39,.8); }

/* ===== TOOLBAR STICKY ===== */
.dv-toolbar { position: sticky; top: 73px; z-index: 30; background: rgba(247,245,240,.92); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid #e5e7eb; }

/* ===== FILE MEDALLION COLORS ===== */
.dv-medal { width: 64px; height: 64px; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0; color: #fff; transition: transform .4s cubic-bezier(.34,1.56,.64,1); }
.dv-card:hover .dv-medal { transform: rotate(-6deg) scale(1.08); }
.dv-pdf  { background: linear-gradient(135deg, #dc2626, #991b1b); }
.dv-doc  { background: linear-gradient(135deg, #2563eb, #1e40af); }
.dv-xls  { background: linear-gradient(135deg, #16a34a, #166534); }
.dv-ppt  { background: linear-gradient(135deg, #ea580c, #9a3412); }
.dv-zip  { background: linear-gradient(135deg, #d97706, #92400e); }
.dv-img  { background: linear-gradient(135deg, #9333ea, #6b21a8); }
.dv-def  { background: linear-gradient(135deg, #475569, #334155); }

/* ===== CARD ===== */
.dv-card { position: relative; overflow: hidden; background: #fff; border: 1px solid #e5e7eb; transition: all .4s cubic-bezier(.22,1,.36,1); }
.dv-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #C9A227, #D4AF37); transform: scaleX(0); transform-origin: left; transition: transform .5s; z-index: 2; }
.dv-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,34,57,.14); border-color: rgba(201,162,39,.4); }
.dv-card:hover::before { transform: scaleX(1); }
.dv-dl-btn { position: relative; overflow: hidden; }
.dv-dl-btn::after { content: ''; position: absolute; top: 0; left: -80%; width: 50%; height: 100%; background: linear-gradient(105deg, transparent, rgba(255,255,255,.4), transparent); transform: skewX(-20deg); transition: left .5s; }
.dv-dl-btn:hover::after { left: 130%; }

/* ===== SORT BUTTONS ===== */
.dv-sort { padding: .45rem .9rem; font-size: 10px; letter-spacing: .18em; text-transform: uppercase; font-weight: 700; border: 1px solid #e5e7eb; background: #fff; color: #64748b; cursor: pointer; transition: all .25s; }
.dv-sort:hover { border-color: #C9A227; color: #0B2239; }
.dv-sort.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }

/* ===== 🔥 VIEW TOGGLE ===== */
.dv-view-btn { width: 38px; height: 38px; border: 1px solid #e5e7eb; background: #fff; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s; }
.dv-view-btn:hover { border-color: #C9A227; color: #0B2239; }
.dv-view-btn.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }

/* ===== 🔥 LIST VIEW ===== */
#dvGrid.list-view { display: flex; flex-direction: column; gap: 0.5rem; }
#dvGrid.list-view .dv-card { flex-direction: row; padding: 1rem 1.5rem; }
#dvGrid.list-view .dv-medal { width: 48px; height: 48px; }
#dvGrid.list-view .dv-medal i { font-size: 1rem; }
#dvGrid.list-view .dv-medal span { font-size: 7px; }

/* ===== 🔥 PREVIEW MODAL ===== */
.dv-preview-modal { position: fixed; inset: 0; z-index: 100; background: rgba(6,20,32,.95); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; padding: 2rem; }
.dv-preview-modal.open { display: flex; animation: fadeIn .3s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.dv-preview-card { background: #fff; max-width: 900px; width: 100%; max-height: 90vh; overflow: hidden; box-shadow: 0 32px 64px rgba(0,0,0,.5); }
.dv-preview-head { padding: 1.25rem 1.5rem; background: #0B2239; color: #F7F5F0; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #C9A227; }
.dv-preview-close { background: none; border: none; color: #C9A227; font-size: 24px; cursor: pointer; }
.dv-preview-body { padding: 1.5rem; max-height: 70vh; overflow-y: auto; text-align: center; }
.dv-preview-body img { max-width: 100%; max-height: 60vh; object-fit: contain; }
.dv-preview-foot { padding: 1rem 1.5rem; background: #F7F5F0; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e5e7eb; }

/* ===== 🔥 RECENT DOWNLOADS ===== */
.recent-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px dashed #e5e7eb; }
.recent-item:last-child { border: 0; }
.recent-medal { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; font-size: 10px; }

/* ===== REVEAL ===== */
.dv-rv { opacity: 0; transform: translateY(24px); transition: all .8s cubic-bezier(.22,1,.36,1); transition-delay: var(--d, 0s); }
.dv-rv.in { opacity: 1; transform: none; }
.dv-hidden { display: none !important; }

/* ===== 🔥 KBD SHORTCUT HINT ===== */
.kbd-hint { display: inline-block; padding: 2px 6px; background: rgba(247,245,240,.15); border: 1px solid rgba(247,245,240,.25); font-family: 'JetBrains Mono', monospace; font-size: 9px; color: #C9A227; margin-left: 6px; }
</style>

<!-- ============ VAULT HERO ============ -->
<section class="vault-hero text-ivory">
    <div class="vault-grid"></div>
    <div class="vault-ring r1"></div>
    <div class="vault-ring r2"></div>
    <div class="vault-ring r3"></div>
    <div class="vault-lock"><i class="fas fa-vault"></i></div>

    <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold mb-6"><i class="fas fa-lock-open mr-2"></i><?= $EN ? 'Secure Resource Vault' : 'Brankas Dokumen Resmi' ?></p>
            <h1 class="font-serif font-light tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                <?= $EN ? 'Document <em class="italic text-gold">Vault</em>' : 'Pusat <em class="italic text-gold">Unduhan</em>' ?>
            </h1>
            <p class="text-ivory/70 mt-6 text-lg leading-relaxed max-w-2xl">
                <?= $EN ? 'Academic forms, official decrees, guidelines, and templates — verified and ready to download.' : 'Formulir akademik, surat keputusan, panduan, dan templat — terverifikasi dan siap diunduh.' ?>
            </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-6 mt-14 max-w-xl">
            <div class="border-l-2 border-gold pl-4">
                <div class="font-serif text-4xl md:text-5xl font-light text-gold dv-count" data-count="<?= count($documents) ?>">0</div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Documents' : 'Dokumen' ?></p>
            </div>
            <div class="border-l-2 border-gold pl-4">
                <div class="font-serif text-4xl md:text-5xl font-light text-ivory dv-count" data-count="<?= $total_dl ?>">0</div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Downloads' : 'Unduhan' ?></p>
            </div>
            <div class="border-l-2 border-gold pl-4">
                <div class="font-serif text-4xl md:text-5xl font-light text-ivory/70 dv-count" data-count="<?= count($categories) ?>">0</div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Categories' : 'Kategori' ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============ 🔥 RECENT DOWNLOADS ============ -->
<?php if (!empty($recent)): ?>
<section class="py-10 bg-white border-b border-gray-200">
    <div class="container mx-auto px-6">
        <div class="flex items-center gap-3 mb-5">
            <span class="editorial-label text-gold-muted"><i class="fas fa-fire text-orange-500 mr-1"></i><?= $EN ? 'Most Downloaded' : 'Paling Banyak Diunduh' ?></span>
            <span class="flex-1 h-px bg-gray-200"></span>
        </div>
        <div class="grid md:grid-cols-5 gap-4">
            <?php foreach ($recent as $rd):
                $rext = strtolower(pathinfo($rd->file_name, PATHINFO_EXTENSION));
                $rem = $ext_map[$rext] ?? ['icon' => 'fa-file', 'cls' => 'dv-def'];
            ?>
            <a href="<?= base_url('download/file/' . $rd->id) ?>" class="recent-item group">
                <div class="recent-medal <?= $rem['cls'] ?>"><i class="fas <?= $rem['icon'] ?>"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-navy truncate group-hover:text-gold-muted transition"><?= html_escape($rd->title) ?></p>
                    <p class="text-[10px] text-gold-muted font-mono"><i class="fas fa-download mr-1"></i><?= number_format($rd->download_count) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ STICKY TOOLBAR ============ -->
<div class="dv-toolbar">
    <div class="container mx-auto px-6 py-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <!-- Live search -->
            <div class="relative flex-1 max-w-xl">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gold-muted"></i>
                <input type="text" id="dvSearch" value="<?= html_escape($q) ?>" placeholder="<?= $EN ? 'Type to filter instantly...' : 'Ketik untuk menyaring instan...' ?> (<?= $EN ? 'press' : 'tekan' ?> /)"
                    class="w-full pl-12 pr-5 py-3 bg-white border-2 border-navy/15 text-navy placeholder:text-slate focus:border-gold outline-none transition">
            </div>
            <!-- Sort + View Toggle -->
            <div class="flex items-center gap-2">
                <span class="editorial-label text-slate mr-1 hidden md:inline"><?= $EN ? 'Sort' : 'Urutkan' ?>:</span>
                <button class="dv-sort active" data-sort="default"><?= $EN ? 'Default' : 'Bawaan' ?></button>
                <button class="dv-sort" data-sort="dl"><i class="fas fa-download mr-1"></i><?= $EN ? 'Popular' : 'Terpopuler' ?></button>
                <button class="dv-sort" data-sort="az">A–Z</button>
                <span class="w-px h-6 bg-gray-200 mx-2"></span>
                <!-- 🔥 VIEW TOGGLE -->
                <button class="dv-view-btn active" data-view="grid" title="<?= $EN ? 'Grid view' : 'Tampilan grid' ?>"><i class="fas fa-th"></i></button>
                <button class="dv-view-btn" data-view="list" title="<?= $EN ? 'List view' : 'Tampilan list' ?>"><i class="fas fa-list"></i></button>
            </div>
        </div>
        <!-- Category chips -->
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="<?= base_url('download') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$cat ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $EN ? 'All' : 'Semua' ?></a>
            <?php foreach ($categories as $key => $label): ?>
            <a href="<?= base_url('download?cat=' . $key) ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $cat == $key ? 'bg-gold text-navy border-gold' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
                <?= html_escape($label) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ============ DOCUMENT GRID ============ -->
<section class="py-16 md:py-20 bg-ivory">
    <div class="container mx-auto px-6">

        <?php if (empty($documents)): ?>
            <div class="text-center py-24 bg-white border border-gray-200 dv-rv in">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-ivory-warm flex items-center justify-center">
                    <i class="fas fa-folder-open text-3xl text-gold-muted"></i>
                </div>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'The vault is empty for this query.' : 'Brankas kosong untuk pencarian ini.' ?></p>
                <a href="<?= base_url('download') ?>" class="inline-block mt-6 text-xs uppercase tracking-editorial font-bold text-gold-muted hover:text-navy transition"><?= $EN ? 'Reset filters' : 'Atur ulang filter' ?> →</a>
            </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 gap-5" id="dvGrid">
            <?php foreach ($documents as $i => $d):
                $ext = strtolower(pathinfo($d->file_name, PATHINFO_EXTENSION));
                $em  = $ext_map[$ext] ?? ['icon' => 'fa-file', 'cls' => 'dv-def'];
                $can_preview = !empty($em['preview']);
            ?>
            <div class="dv-card dv-rv p-6 md:p-7 flex items-center gap-5"
                 style="--d:<?= ($i % 6) * .06 ?>s"
                 data-id="<?= $d->id ?>"
                 data-title="<?= html_escape($d->title) ?>"
                 data-title-lower="<?= strtolower(html_escape($d->title)) ?>"
                 data-dl="<?= (int)$d->download_count ?>"
                 data-ext="<?= html_escape($ext) ?>"
                 data-file="<?= html_escape($d->file_name) ?>"
                 data-preview="<?= $can_preview ? '1' : '0' ?>">
                <div class="dv-medal <?= $em['cls'] ?>">
                    <i class="fas <?= $em['icon'] ?> text-xl"></i>
                    <span class="text-[8px] uppercase tracking-widest mt-1 font-bold"><?= strtoupper($ext) ?></span>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-serif font-medium text-navy leading-snug line-clamp-2 group-hover:text-gold-muted transition">
                        <?= html_escape($d->title) ?>
                    </h3>
                    <p class="text-xs text-slate mt-2 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="bg-ivory-warm px-2 py-0.5 uppercase tracking-wider font-semibold"><?= html_escape($categories[$d->category] ?? ucfirst($d->category)) ?></span>
                        <span class="font-mono"><?= dv_size($d->file_size) ?></span>
                        <span class="text-gold-muted font-semibold dl-count-display"><i class="fas fa-download mr-1"></i><?= number_format($d->download_count) ?></span>
                    </p>
                </div>

                <!-- 🔥 Preview button (jika gambar) -->
                <?php if ($can_preview): ?>
                <button type="button" class="dv-preview-btn btn-outline px-4 py-2.5 text-xs uppercase tracking-editorial font-semibold flex-shrink-0 mr-2" title="<?= $EN ? 'Preview' : 'Pratinjau' ?>">
                    <i class="fas fa-eye"></i>
                </button>
                <?php endif; ?>

                <a href="<?= base_url('download/file/' . $d->id) ?>" class="dv-dl-btn btn-navy px-5 py-2.5 text-xs uppercase tracking-editorial font-semibold flex-shrink-0">
                    <i class="fas fa-download mr-1"></i><?= $EN ? 'Get' : 'Unduh' ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="dv-hidden text-center py-16 text-slate font-serif text-xl" id="dvEmpty">
            <?= $EN ? 'No document matches your filter.' : 'Tidak ada dokumen yang cocok dengan saringan Anda.' ?>
        </p>
        <?php endif; ?>

        <!-- CTA -->
        <div class="mt-16 bg-navy text-ivory p-8 md:p-10 relative overflow-hidden dv-rv">
            <div class="absolute -top-8 -right-8 w-40 h-40 bg-gold/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <p class="editorial-label text-gold mb-2"><?= $EN ? 'Missing document?' : 'Dokumen tidak ditemukan?' ?></p>
                    <h3 class="font-serif text-2xl md:text-3xl font-light"><?= $EN ? 'Request it from the faculty office.' : 'Ajukan permintaan ke tata usaha fakultas.' ?></h3>
                </div>
                <a href="<?= base_url('kontak') ?>" class="btn-gold px-8 py-4 text-xs uppercase tracking-editorial font-bold flex-shrink-0">
                    <i class="fas fa-envelope mr-2"></i><?= $EN ? 'Contact Us' : 'Hubungi Kami' ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 🔥 PREVIEW MODAL -->
<div class="dv-preview-modal" id="previewModal" onclick="if(event.target===this)closePreview()">
    <div class="dv-preview-card">
        <div class="dv-preview-head">
            <div>
                <p class="editorial-label text-gold mb-1" id="pvMeta">-</p>
                <h3 class="font-serif text-lg font-light" id="pvTitle">-</h3>
            </div>
            <button class="dv-preview-close" onclick="closePreview()"><i class="fas fa-times"></i></button>
        </div>
        <div class="dv-preview-body">
            <img id="pvImage" src="" alt="Preview">
        </div>
        <div class="dv-preview-foot">
            <p class="text-xs text-slate"><i class="fas fa-info-circle text-gold-muted mr-1"></i><?= $EN ? 'Preview only' : 'Hanya pratinjau' ?></p>
            <a id="pvDownload" href="#" class="btn-gold px-5 py-2 text-xs uppercase tracking-editorial font-bold"><i class="fas fa-download mr-1"></i><?= $EN ? 'Download' : 'Unduh' ?></a>
        </div>
    </div>
</div>

<script>
(function(){
    // ===== COUNTERS =====
    var cio = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            var el = e.target;
            if (el.dataset.done) return; el.dataset.done = '1';
            var t = +el.dataset.count, st = performance.now();
            (function f(n){
                var p = Math.min(1, (n - st) / 1500), ease = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(t * ease).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(f);
            })(st);
            cio.unobserve(el);
        });
    }, { threshold: .3 });
    document.querySelectorAll('.dv-count').forEach(function(el){ cio.observe(el); });

    // ===== REVEAL =====
    var rio = new IntersectionObserver(function(es){
        es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); rio.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.dv-rv').forEach(function(el){ rio.observe(el); });

    var grid = document.getElementById('dvGrid');
    if (!grid) return;
    var cards = Array.prototype.slice.call(grid.querySelectorAll('.dv-card'));
    var emptyMsg = document.getElementById('dvEmpty');
    var search = document.getElementById('dvSearch');

    // ===== LIVE SEARCH =====
    function applyFilter(){
        var q = (search.value || '').toLowerCase();
        var shown = 0;
        cards.forEach(function(c){
            var hit = !q || c.dataset.titleLower.indexOf(q) > -1;
            c.classList.toggle('dv-hidden', !hit);
            if (hit) shown++;
        });
        if (emptyMsg) emptyMsg.classList.toggle('dv-hidden', shown > 0);
    }
    search.addEventListener('input', applyFilter);

    // ===== SORT =====
    var sortBtns = document.querySelectorAll('.dv-sort');
    sortBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            sortBtns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            var mode = btn.dataset.sort;
            var sorted = cards.slice();
            if (mode === 'dl') sorted.sort(function(a,b){ return (+b.dataset.dl) - (+a.dataset.dl); });
            else if (mode === 'az') sorted.sort(function(a,b){ return a.dataset.title.localeCompare(b.dataset.title, 'id', {sensitivity: 'base'}); });
            sorted.forEach(function(c){ grid.appendChild(c); });
        });
    });

    // ===== 🔥 VIEW TOGGLE =====
    var viewBtns = document.querySelectorAll('.dv-view-btn');
    viewBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            viewBtns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            grid.classList.toggle('list-view', btn.dataset.view === 'list');
        });
    });

    // ===== 🔥 KEYBOARD SHORTCUT =====
    document.addEventListener('keydown', function(e){
        if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
            var tag = (document.activeElement.tagName || '').toLowerCase();
            if (tag !== 'input' && tag !== 'textarea') {
                e.preventDefault();
                search.focus();
                search.select();
            }
        }
        if (e.key === 'Escape') closePreview();
    });

    // ===== 🔥 PREVIEW MODAL =====
    window.closePreview = function(){
        document.getElementById('previewModal').classList.remove('open');
    };
    document.querySelectorAll('.dv-preview-btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var card = btn.closest('.dv-card');
            var id = card.dataset.id;
            var title = card.dataset.title;
            var ext = card.dataset.ext;
            var file = card.dataset.file;
            document.getElementById('pvTitle').textContent = title;
            document.getElementById('pvMeta').textContent = ext.toUpperCase() + ' · ' + file;
            document.getElementById('pvImage').src = '<?= base_url("assets/uploads/") ?>' + file;
            document.getElementById('pvDownload').href = '<?= base_url("download/file/") ?>' + id;
            document.getElementById('previewModal').classList.add('open');
        });
    });

    // ===== 🔥 AJAX DOWNLOAD TRACKER =====
    document.querySelectorAll('.dv-dl-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var card = btn.closest('.dv-card');
            var id = card.dataset.id;
            var display = card.querySelector('.dl-count-display');
            // Increment lokal
            if (display) {
                var cur = parseInt((display.textContent || '0').replace(/[^\d]/g, '')) || 0;
                display.innerHTML = '<i class="fas fa-download mr-1"></i>' + (cur + 1).toLocaleString('id-ID');
            }
            // Track di background
            fetch('<?= base_url("download/track/") ?>' + id, { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest'} }).catch(function(){});
        });
    });
})();
</script>