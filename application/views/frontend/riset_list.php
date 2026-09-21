<?php $EN = (get_site_lang() == 'en'); ?>

<?php
// ===== 🔥 Top lecturers untuk filter chips =====
$lec_counts = [];
foreach ($items as $r) {
    $n = trim(($r->title_front ?? '') . ' ' . ($r->lecturer_name ?? ''));
    if ($n !== '') $lec_counts[$n] = ($lec_counts[$n] ?? 0) + 1;
}
arsort($lec_counts);
$top_lecs = array_slice($lec_counts, 0, 5, true);
?>

<style>
/* ===== LAB HERO ===== */
.rs-hero { position: relative; overflow: hidden; background: linear-gradient(160deg, #061420 0%, #0B2239 55%, #1a1033 100%); }
.rs-hex {
    position: absolute; inset: 0; opacity: .8; pointer-events: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='64' viewBox='0 0 56 64'%3E%3Cpath d='M28 2 54 17v30L28 62 2 47V17z' fill='none' stroke='rgba(168,85,247,0.10)' stroke-width='1'/%3E%3C/svg%3E");
}
#rsCanvas { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }
.rs-flask { position: absolute; color: rgba(168,85,247,.16); animation: rsFloat 7s ease-in-out infinite alternate; pointer-events: none; }
@keyframes rsFloat { from { transform: translateY(0) rotate(-6deg); } to { transform: translateY(-22px) rotate(6deg); } }

/* ===== RATIO BAR ===== */
.rs-ratio { display: flex; height: 10px; border-radius: 99px; overflow: hidden; background: rgba(247,245,240,.1); }
.rs-ratio > div { width: 0; transition: width 1.4s cubic-bezier(.22,1,.36,1); }
.rs-ratio .rs-r { background: linear-gradient(90deg, #a855f7, #8b5cf6); }
.rs-ratio .rs-c { background: linear-gradient(90deg, #14b8a6, #0d9488); }

/* ===== YEAR CHART ===== */
.rs-stack { display: flex; flex-direction: column-reverse; justify-content: flex-start; }
.rs-seg { width: 100%; height: 0%; transition: height 1.2s cubic-bezier(.22,1,.36,1); }
.rs-seg-r { background: linear-gradient(180deg, #a855f7, #7c3aed); }
.rs-seg-c { background: linear-gradient(180deg, #2dd4bf, #0d9488); }
.rs-col:hover .rs-seg { filter: brightness(1.2); }

/* ===== CARDS ===== */
.rs-item { position: relative; overflow: hidden; background: #fff; border: 1px solid #e5e7eb; transition: all .4s cubic-bezier(.22,1,.36,1); }
.rs-item::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--tp, #a855f7), transparent);
    transform: scaleX(0); transform-origin: left; transition: transform .5s;
}
.rs-item:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,34,57,.14); }
.rs-item:hover::before { transform: scaleX(1); }
.rs-abs { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.rs-item.expanded .rs-abs { display: block; -webkit-line-clamp: unset; }
.rs-more { cursor: pointer; }
.rs-hidden { display: none !important; }

/* ===== 🔥 CITE / DOI ===== */
.rs-doi { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #64748b; background: #F1F5F9; padding: 3px 8px; border: 1px solid #e2e8f0; }
.rs-cite-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; background: transparent; border: 1px solid #a855f7;
    color: #7c3aed; font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; cursor: pointer; transition: all .2s;
}
.rs-cite-btn:hover { background: #a855f7; color: #fff; }

/* ===== 🔥 LECTURER CHIPS ===== */
.rs-lec-chip {
    padding: .45rem .9rem; font-size: 10px; letter-spacing: .12em; text-transform: uppercase;
    font-weight: 700; border: 1px solid #e5e7eb; background: #fff; color: #64748b;
    cursor: pointer; transition: all .25s;
}
.rs-lec-chip:hover { border-color: #a855f7; color: #7c3aed; transform: translateY(-2px); }
.rs-lec-chip.active { background: #7c3aed; color: #fff; border-color: #7c3aed; }

/* ===== TOAST ===== */
.rs-toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 100;
    background: #0B2239; color: #F7F5F0; padding: 12px 20px;
    font-size: 13px; box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(120%); transition: transform .3s;
}
.rs-toast.show { transform: translateX(0); }
.rs-toast i { color: #C9A227; margin-right: 8px; }

/* ===== REVEAL ===== */
.rs-rv { opacity: 0; transform: translateY(26px); transition: all .9s cubic-bezier(.22,1,.36,1); transition-delay: var(--d, 0s); }
.rs-rv.in { opacity: 1; transform: none; }

@media print {
    #rsCanvas, .rs-flask, .rs-cite-btn, .rs-toast { display: none !important; }
    .rs-abs { display: block !important; -webkit-line-clamp: unset !important; }
}
</style>

<!-- ============ LAB HERO ============ -->
<section class="rs-hero text-ivory">
    <div class="rs-hex"></div>
    <canvas id="rsCanvas"></canvas>
    <i class="fas fa-flask rs-flask text-4xl" style="top:20%; left:7%;"></i>
    <i class="fas fa-atom rs-flask text-3xl" style="top:64%; left:12%; animation-delay:1.4s;"></i>
    <i class="fas fa-microscope rs-flask text-4xl" style="top:28%; right:9%; animation-delay:.7s;"></i>
    <i class="fas fa-dna rs-flask text-3xl" style="top:70%; right:14%; animation-delay:2.1s;"></i>

    <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold mb-6"><i class="fas fa-vial mr-2"></i><?= $EN ? 'Research & Community Service' : 'Penelitian & Pengabdian' ?></p>
            <h1 class="font-serif font-light tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                <?= $EN ? 'Research <em class="italic text-gold">& Impact</em>' : 'Riset <em class="italic text-gold">& Pengabdian</em>' ?>
            </h1>
            <p class="text-ivory/70 mt-6 text-lg leading-relaxed max-w-2xl">
                <?= $EN ? 'Our track record of research and real contribution to society.' : 'Rekam jejak penelitian dan kontribusi nyata kami kepada masyarakat.' ?>
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mt-14 max-w-3xl">
            <div class="border-l-2 border-purple-400 pl-4">
                <div class="font-serif text-5xl font-light text-purple-300 rs-count" data-count="<?= $stats['research'] ?>">0</div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Research' : 'Penelitian' ?></p>
            </div>
            <div class="border-l-2 border-teal-400 pl-4">
                <div class="font-serif text-5xl font-light text-teal-300 rs-count" data-count="<?= $stats['community'] ?>">0</div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Community Service' : 'Pengabdian' ?></p>
            </div>
            <div class="border-l-2 border-gold pl-4">
                <div class="font-serif text-5xl font-light text-gold rs-count" data-count="<?= $stats['research'] + $stats['community'] ?>">0</div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Total Projects' : 'Total Proyek' ?></p>
            </div>
        </div>

        <?php $tot_all = $stats['research'] + $stats['community']; if ($tot_all > 0): ?>
        <div class="max-w-3xl mt-8 rs-rv">
            <div class="rs-ratio" id="rsRatio" data-r="<?= round(($stats['research'] / $tot_all) * 100) ?>" data-c="<?= round(($stats['community'] / $tot_all) * 100) ?>">
                <div class="rs-r"></div><div class="rs-c"></div>
            </div>
            <div class="flex justify-between mt-2 text-[10px] uppercase tracking-wider text-ivory/60 font-mono">
                <span><i class="fas fa-flask text-purple-300 mr-1"></i><?= round(($stats['research'] / $tot_all) * 100) ?>% <?= $EN ? 'Research' : 'Riset' ?></span>
                <span><?= round(($stats['community'] / $tot_all) * 100) ?>% <?= $EN ? 'Service' : 'Pengabdian' ?> <i class="fas fa-hands-helping text-teal-300 ml-1"></i></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ YEAR CHART ============ -->
<?php if (!empty($year_chart)): 
    $max_total = 1;
    foreach ($year_chart as $yc) $max_total = max($max_total, $yc['research'] + $yc['community']);
?>
<section class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-6 py-12">
        <div class="flex items-end justify-between mb-8 rs-rv">
            <div>
                <p class="editorial-label text-gold-muted mb-2"><?= $EN ? 'Activity' : 'Aktivitas' ?></p>
                <h2 class="font-serif text-2xl md:text-3xl font-light text-navy"><?= $EN ? 'Projects per <em class="italic text-gold-muted">year</em>' : 'Proyek per <em class="italic text-gold-muted">tahun</em>' ?></h2>
            </div>
            <!-- 🔥 FIX: typo </i> → </span> -->
            <div class="flex items-center gap-4 text-[10px] uppercase tracking-wider font-mono text-slate">
                <span><span class="inline-block w-3 h-3 bg-purple-500 mr-1"></span><?= $EN ? 'Research' : 'Riset' ?></span>
                <span><span class="inline-block w-3 h-3 bg-teal-500 mr-1"></span><?= $EN ? 'Service' : 'Pengabdian' ?></span>
            </div>
        </div>
        <div class="flex items-end gap-3 md:gap-6 rs-rv" id="rsChart">
            <?php foreach ($year_chart as $yc):
                $hR = round(($yc['research'] / $max_total) * 100);
                $hC = round(($yc['community'] / $max_total) * 100);
            ?>
            <div class="rs-col flex-1 flex flex-col items-center gap-2" title="<?= $yc['year'] ?> — <?= $yc['research'] ?> <?= $EN ? 'research' : 'riset' ?>, <?= $yc['community'] ?> <?= $EN ? 'service' : 'pengabdian' ?>">
                <div class="rs-stack w-full max-w-[56px]" style="height:150px">
                    <div class="rs-seg rs-seg-r" data-h="<?= $hR ?>%"></div>
                    <div class="rs-seg rs-seg-c" data-h="<?= $hC ?>%"></div>
                </div>
                <span class="font-mono text-[10px] text-slate"><?= $yc['year'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ FILTER + LIST ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">

        <!-- Filter Bar -->
        <form action="<?= base_url('riset') ?>" method="GET" class="flex flex-wrap items-center gap-3 mb-6 pb-6 border-b border-gray-200">
            <span class="editorial-label text-slate"><?= $EN ? 'Filter' : 'Filter' ?>:</span>
            <a href="<?= base_url('riset') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$type ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $EN ? 'All' : 'Semua' ?></a>
            <a href="<?= base_url('riset?type=research') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $type == 'research' ? 'bg-purple-600 text-ivory border-purple-600' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $EN ? 'Research' : 'Penelitian' ?></a>
            <a href="<?= base_url('riset?type=community_service') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $type == 'community_service' ? 'bg-teal-600 text-ivory border-teal-600' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $EN ? 'Community Service' : 'Pengabdian' ?></a>
            <span class="w-px h-6 bg-gray-300 mx-2"></span>
            <select name="year" onchange="this.form.submit()" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 border border-navy/20 bg-white text-navy">
                <option value=""><?= $EN ? 'All Years' : 'Semua Tahun' ?></option>
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
            <!-- 🔥 PRINT -->
            <button type="button" onclick="window.print()" class="ml-auto text-xs uppercase tracking-editorial font-semibold px-4 py-2 border border-navy/20 text-navy hover:bg-navy hover:text-ivory transition">
                <i class="fas fa-print mr-1"></i><?= $EN ? 'Print' : 'Cetak' ?>
            </button>
        </form>

        <!-- 🔥 LECTURER CHIPS -->
        <?php if (!empty($top_lecs)): ?>
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <span class="editorial-label text-slate mr-1"><i class="fas fa-user-tie text-gold-muted mr-1"></i><?= $EN ? 'Researcher' : 'Peneliti' ?>:</span>
            <button type="button" class="rs-lec-chip active" data-lec=""><?= $EN ? 'All' : 'Semua' ?></button>
            <?php foreach ($top_lecs as $lname => $lcount): ?>
            <button type="button" class="rs-lec-chip" data-lec="<?= strtolower(html_escape($lname)) ?>"><?= html_escape($lname) ?> (<?= $lcount ?>)</button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Live search -->
        <div class="relative max-w-xl mb-12">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gold-muted"></i>
            <input type="text" id="rsSearch" placeholder="<?= $EN ? 'Type to filter title, researcher, or funding...' : 'Ketik untuk menyaring judul / peneliti / pendanaan...' ?>"
                class="w-full pl-12 pr-5 py-3 bg-white border-2 border-navy/15 text-navy placeholder:text-slate focus:border-gold outline-none transition">
        </div>

        <!-- Research List -->
        <?php if (empty($items)): ?>
            <div class="text-center py-24 bg-white border border-gray-200 rs-rv in">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-ivory-warm flex items-center justify-center">
                    <i class="fas fa-flask text-3xl text-gold-muted"></i>
                </div>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No research data yet.' : 'Belum ada data riset.' ?></p>
            </div>
        <?php else: ?>
        <div class="space-y-6 max-w-5xl mx-auto" id="rsList">
            <?php foreach ($items as $idx => $r):
                $is_res = ($r->type == 'research');
                $author_full = trim(($r->title_front ?? '') . ' ' . ($r->lecturer_name ?? ''));
                $search_str = strtolower(html_escape($r->title . ' ' . $author_full . ' ' . ($r->funding_source ?? '')));
            ?>
            <div class="rs-item rs-rv p-8 md:p-10 md:pl-12 group"
                 data-search="<?= $search_str ?>"
                 data-lec="<?= strtolower(html_escape($author_full)) ?>"
                 data-title="<?= html_escape($r->title) ?>"
                 data-author="<?= html_escape($author_full) ?>"
                 data-year="<?= $r->year ?>"
                 data-kind="<?= $is_res ? 'research' : 'community' ?>"
                 data-funding="<?= html_escape($r->funding_source ?? '') ?>"
                 style="--d:<?= ($idx % 5) * .06 ?>s; --tp:<?= $is_res ? '#a855f7' : '#14b8a6' ?>">
                <span class="absolute left-0 top-0 bottom-0 w-1.5 <?= $is_res ? 'bg-purple-500' : 'bg-teal-500' ?>"></span>
                <div class="absolute -top-7 -right-3 font-serif text-[7rem] leading-none text-navy/5 group-hover:text-gold/10 transition-colors select-none pointer-events-none"><?= $r->year ?></div>

                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <span class="editorial-label text-gold-muted"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="w-8 h-px bg-gold"></span>
                    <span class="<?= $is_res ? 'bg-purple-500/10 text-purple-700 border-purple-500/20' : 'bg-teal-500/10 text-teal-700 border-teal-500/20' ?> border text-[10px] uppercase tracking-editorial font-bold px-3 py-1">
                        <?= $is_res ? ($EN ? 'Research' : 'Penelitian') : ($EN ? 'Community Service' : 'Pengabdian') ?>
                    </span>
                    <span class="text-xs font-mono text-slate"><?= $r->year ?></span>
                    <?php if ($r->funding_source): ?>
                        <span class="text-[10px] uppercase tracking-editorial font-semibold bg-gold/10 text-gold-muted px-3 py-1 border border-gold/20">
                            <i class="fas fa-coins mr-1"></i><?= html_escape($r->funding_source) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($r->doi)): ?>
                        <span class="rs-doi">DOI: <?= html_escape($r->doi) ?></span>
                    <?php endif; ?>
                </div>

                <h3 class="font-serif text-2xl md:text-[1.65rem] font-medium text-navy leading-snug tracking-[-0.01em] mb-4 group-hover:text-gold-muted transition">
                    <?= html_escape($r->title) ?>
                </h3>
                <p class="text-sm text-slate mb-4">
                    <i class="fas fa-user-tie text-gold-muted mr-2"></i><?= html_escape($author_full) ?>
                </p>
                <?php if ($r->abstract): ?>
                    <p class="rs-abs text-sm text-gray-600 leading-relaxed mb-2"><?= html_escape($r->abstract) ?></p>
                    <button type="button" class="rs-more text-[10px] uppercase tracking-editorial font-bold text-gold-muted hover:text-navy transition mb-4">
                        <i class="fas fa-chevron-down mr-1"></i><?= $EN ? 'Read abstract' : 'Baca abstrak' ?>
                    </button>
                <?php endif; ?>

                <div class="flex flex-wrap items-center gap-3">
                    <?php if ($r->document_file): ?>
                    <a href="<?= base_url('assets/uploads/research/' . $r->document_file) ?>" target="_blank" rel="noopener"
                       class="link-arrow inline-flex text-navy font-semibold uppercase tracking-editorial text-xs">
                        <i class="fas fa-file-pdf mr-1"></i><?= $EN ? 'View Document' : 'Lihat Dokumen' ?>
                        <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                    </a>
                    <?php endif; ?>
                    <!-- 🔥 CITE BIBTEX -->
                    <button type="button" class="rs-cite-btn" onclick="citeBibtex(this)">
                        <i class="fas fa-quote-left"></i><?= $EN ? 'Cite (BibTeX)' : 'Sitasi (BibTeX)' ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="hidden text-center py-16 text-slate font-serif text-xl" id="rsEmpty">
            <?= $EN ? 'No project matches your search.' : 'Tidak ada proyek yang cocok dengan pencarian Anda.' ?>
        </p>
        <?php endif; ?>

        <!-- CTA -->
        <div class="mt-16 bg-navy text-ivory p-8 md:p-10 relative overflow-hidden rs-rv">
            <div class="absolute -top-8 -right-8 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <p class="editorial-label text-gold mb-2"><?= $EN ? 'Collaboration' : 'Kolaborasi' ?></p>
                    <h3 class="font-serif text-2xl md:text-3xl font-light"><?= $EN ? 'Have a research or partnership idea?' : 'Punya ide riset atau kemitraan?' ?></h3>
                </div>
                <a href="<?= base_url('kontak') ?>" class="btn-gold px-8 py-4 text-xs uppercase tracking-editorial font-bold flex-shrink-0">
                    <i class="fas fa-handshake mr-2"></i><?= $EN ? 'Contact Us' : 'Hubungi Kami' ?>
                </a>
            </div>
        </div>
    </div>
</section>

<div class="rs-toast" id="rsToast"><i class="fas fa-check-circle"></i><span id="rsToastMsg"></span></div>

<script>
(function(){
    // ===== MOLECULE CANVAS =====
    var cv = document.getElementById('rsCanvas');
    if (cv) {
        var ctx = cv.getContext('2d'), pts = [], vis = true;
        function rs(){ cv.width = cv.offsetWidth; cv.height = cv.offsetHeight; }
        rs(); window.addEventListener('resize', rs);
        var cols = ['rgba(168,85,247,.5)', 'rgba(45,212,191,.45)', 'rgba(201,162,39,.4)'];
        for (var i = 0; i < 34; i++) pts.push({
            x: Math.random() * cv.width, y: Math.random() * cv.height,
            vx: (Math.random() - .5) * .3, vy: (Math.random() - .5) * .3,
            r: Math.random() * 1.6 + .6, c: cols[i % 3]
        });
        new IntersectionObserver(function(e){ vis = e[0].isIntersecting; }).observe(cv);
        var fr = 0;
        (function anim(){
            requestAnimationFrame(anim);
            if (!vis || document.hidden || ++fr % 2) return;
            ctx.clearRect(0, 0, cv.width, cv.height);
            pts.forEach(function(p){
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > cv.width) p.vx *= -1;
                if (p.y < 0 || p.y > cv.height) p.vy *= -1;
                ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.28); ctx.fillStyle = p.c; ctx.fill();
            });
            for (var i = 0; i < pts.length; i++) for (var j = i + 1; j < pts.length; j++) {
                var dx = pts[i].x - pts[j].x, dy = pts[i].y - pts[j].y, d = Math.sqrt(dx*dx + dy*dy);
                if (d < 120) {
                    ctx.beginPath(); ctx.moveTo(pts[i].x, pts[i].y); ctx.lineTo(pts[j].x, pts[j].y);
                    ctx.strokeStyle = 'rgba(168,85,247,' + (0.14 * (1 - d/120)) + ')'; ctx.lineWidth = .5; ctx.stroke();
                }
            }
        })();
    }

    // ===== COUNTERS =====
    var cio = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            var el = e.target;
            if (el.dataset.done) return; el.dataset.done = '1';
            var t = +el.dataset.count, st = performance.now();
            (function f(n){
                var p = Math.min(1, (n - st) / 1400), ease = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(t * ease).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(f);
            })(st);
            cio.unobserve(el);
        });
    }, { threshold: .3 });
    document.querySelectorAll('.rs-count').forEach(function(el){ cio.observe(el); });

    // ===== REVEAL + CHART =====
    var rio = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            e.target.classList.add('in');
            e.target.querySelectorAll('.rs-seg').forEach(function(s){
                setTimeout(function(){ s.style.height = s.dataset.h; }, 250);
            });
            rio.unobserve(e.target);
        });
    }, { threshold: .15 });
    document.querySelectorAll('.rs-rv').forEach(function(el){ rio.observe(el); });

    // ===== RATIO =====
    var ratio = document.getElementById('rsRatio');
    if (ratio) {
        var ro = new IntersectionObserver(function(e){
            if(!e[0].isIntersecting) return;
            var bars = ratio.querySelectorAll('div');
            setTimeout(function(){
                bars[0].style.width = ratio.dataset.r + '%';
                bars[1].style.width = ratio.dataset.c + '%';
            }, 300);
            ro.unobserve(ratio);
        }, { threshold: .4 });
        ro.observe(ratio);
    }

    // ===== TOAST =====
    function showToast(msg){
        var t = document.getElementById('rsToast');
        document.getElementById('rsToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }

    // ===== 🔥 COMBO FILTER (search + lecturer) =====
    var search = document.getElementById('rsSearch');
    var list = document.getElementById('rsList');
    var emptyMsg = document.getElementById('rsEmpty');
    var lecChips = document.querySelectorAll('.rs-lec-chip');
    var activeLec = '';

    function applyFilter(){
        if (!list) return;
        var q = search ? search.value.toLowerCase() : '';
        var shown = 0;
        var cards = list.querySelectorAll('.rs-item');
        cards.forEach(function(c){
            var hitQ = !q || (c.dataset.search || '').indexOf(q) > -1;
            var hitL = !activeLec || (c.dataset.lec || '') === activeLec;
            var hit = hitQ && hitL;
            c.classList.toggle('rs-hidden', !hit);
            if (hit) shown++;
        });
        if (emptyMsg) emptyMsg.classList.toggle('hidden', shown > 0);
    }

    if (search) search.addEventListener('input', applyFilter);
    lecChips.forEach(function(chip){
        chip.addEventListener('click', function(){
            lecChips.forEach(function(c){ c.classList.remove('active'); });
            chip.classList.add('active');
            activeLec = chip.dataset.lec;
            applyFilter();
        });
    });

    // ===== ABSTRACT TOGGLE =====
    document.querySelectorAll('.rs-more').forEach(function(btn){
        btn.addEventListener('click', function(){
            var item = btn.closest('.rs-item');
            var open = item.classList.toggle('expanded');
            btn.innerHTML = open
                ? '<i class="fas fa-chevron-up mr-1"></i><?= $EN ? "Close" : "Tutup" ?>'
                : '<i class="fas fa-chevron-down mr-1"></i><?= $EN ? "Read abstract" : "Baca abstrak" ?>';
        });
    });

    // ===== 🔥 CITE BIBTEX =====
    window.citeBibtex = function(btn){
        var item = btn.closest('.rs-item');
        var d = item.dataset;
        var last = (d.author || 'anonymous').trim().split(' ').pop().toLowerCase();
        var key = last + d.year;
        var type = d.kind === 'research' ? 'article' : 'misc';
        var lines = [
            '@' + type + '{' + key + ',',
            '  title   = {' + (d.title || '') + '},',
            '  author  = {' + (d.author || '') + '},',
            '  year    = {' + (d.year || '') + '},',
            '  note    = {' + (d.funding || 'Faculty Research Repository') + '}',
            '}'
        ];
        if (navigator.clipboard) {
            navigator.clipboard.writeText(lines.join('\n'));
            showToast('<?= $EN ? "BibTeX copied to clipboard!" : "BibTeX disalin!" ?>');
        }
    };
})();
</script>