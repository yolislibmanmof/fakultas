<?php
$EN = (get_site_lang() == 'en');
$total_prodi = count($programs);
$unggul_count = 0;
$baik_sekali_count = 0;
$baik_count = 0;
foreach ($programs as $p) {
    $acc = $p->accreditation ?? '';
    if ($acc === 'Unggul') $unggul_count++;
    elseif ($acc === 'Baik Sekali') $baik_sekali_count++;
    elseif ($acc === 'Baik') $baik_count++;
}
// Ambil akreditasi fakultas dari settings
$faculty_acc = $faculty_accreditation ?? 'Unggul';
$faculty_until = $faculty_accreditation_until ?? '2030-12-31';
?>

<style>
/* ===== 🔥 PROGRESS BAR ===== */
.acc-progress { height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; margin-top: 8px; }
.acc-progress-bar { height: 100%; transition: width 1.2s cubic-bezier(.22,1,.36,1); border-radius: 3px; }
.acc-progress-bar.unggul { background: linear-gradient(90deg, #C9A227, #D4AF37); }
.acc-progress-bar.baik-sekali { background: linear-gradient(90deg, #10b981, #34d399); }
.acc-progress-bar.baik { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

/* ===== 🔥 TOOLTIP ===== */
.acc-tooltip { position: relative; cursor: help; }
.acc-tooltip::after {
    content: attr(data-tooltip);
    position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%) translateY(-8px);
    background: #0B2239; color: #F7F5F0; padding: 8px 12px; border-radius: 4px;
    font-size: 11px; white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity .2s, transform .2s; z-index: 10;
}
.acc-tooltip:hover::after { opacity: 1; transform: translateX(-50%) translateY(-4px); }

/* ===== 🔥 FILTER BAR ===== */
.acc-filter-bar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; }
.acc-filter-btn {
    padding: 8px 16px; border: 1px solid #e5e7eb; background: #fff;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
    cursor: pointer; transition: all .2s;
}
.acc-filter-btn:hover { border-color: #C9A227; color: #C9A227; }
.acc-filter-btn.active { background: #0B2239; color: #F7F5F0; border-color: #0B2239; }

/* ===== 🔥 SEARCH ===== */
.acc-search {
    width: 100%; max-width: 400px; padding: 12px 16px 12px 44px;
    border: 2px solid #e5e7eb; background: #fff; font-size: 14px;
    transition: border-color .2s;
}
.acc-search:focus { outline: none; border-color: #C9A227; }

/* ===== 🔥 EXPORT BTN ===== */
.export-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px; background: #0B2239; color: #F7F5F0;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
    text-decoration: none; transition: all .2s;
}
.export-btn:hover { background: #C9A227; color: #0B2239; }

/* ===== 🔥 COUNTER ANIMATION ===== */
.counter-animated { display: inline-block; }

/* ===== 🔥 MEDALLION PULSE ===== */
.pulse-ring {
    position: relative;
}
.pulse-ring::before {
    content: ''; position: absolute; inset: -8px;
    border: 2px solid #C9A227; border-radius: 50%;
    animation: pulseRing 2s ease-out infinite;
}
@keyframes pulseRing {
    0% { transform: scale(0.9); opacity: 1; }
    100% { transform: scale(1.3); opacity: 0; }
}

/* ===== 🔥 TABLE ROW HOVER ===== */
.acc-row { transition: all .2s; }
.acc-row:hover { background: #F7F5F0; }
.acc-row.highlighted { background: #fef3c7; }

/* ===== 🔥 SORT INDICATOR ===== */
.sort-btn { cursor: pointer; user-select: none; }
.sort-btn:hover { color: #C9A227; }
.sort-icon { font-size: 10px; margin-left: 4px; opacity: 0.5; }
.sort-btn.sorted .sort-icon { opacity: 1; color: #C9A227; }

/* ===== 🔥 DOWNLOAD CERT BTN ===== */
.cert-download {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; background: transparent; border: 1px solid #e5e7eb;
    font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
    color: #64748b; transition: all .2s; text-decoration: none;
}
.cert-download:hover { border-color: #C9A227; color: #C9A227; }
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="absolute -bottom-16 -right-8 font-serif text-[18rem] leading-none text-navy/5 select-none pointer-events-none hidden lg:block">A</div>
    <div class="container mx-auto px-6 py-20 md:py-28 relative">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Quality Assurance' : 'Penjaminan Mutu' ?></p>
            <h1 class="font-serif font-light text-navy tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                <?= $EN ? 'Accreditation <em class="italic text-gold-muted">& Reputation</em>' : 'Akreditasi <em class="italic text-gold-muted">& Reputasi</em>' ?>
            </h1>
            <p class="text-slate mt-6 text-lg leading-relaxed">
                <?= $EN ? 'A commitment to quality, nationally recognized by BAN-PT.' : 'Komitmen mutu yang diakui secara nasional oleh BAN-PT.' ?>
            </p>
        </div>
    </div>
</section>

<!-- ============ ACCREDITATION ============ -->
<section class="py-24 bg-ivory">
    <div class="container mx-auto px-6 max-w-5xl">

        <!-- 🔥 Stats Row dengan Counter Animation -->
        <div class="grid grid-cols-2 md:grid-cols-3 divide-x divide-gray-200 border-y border-gray-200 mb-16 rv">
            <div class="py-8 px-6 text-center">
                <div class="font-serif text-5xl md:text-6xl font-light text-navy leading-none counter-animated" data-count="<?= $total_prodi ?>">0</div>
                <div class="editorial-label text-slate mt-3"><?= $EN ? 'Study Programs' : 'Program Studi' ?></div>
            </div>
            <div class="py-8 px-6 text-center">
                <div class="font-serif text-5xl md:text-6xl font-light text-gold-muted leading-none counter-animated" data-count="<?= $unggul_count ?>">0</div>
                <div class="editorial-label text-slate mt-3"><?= $EN ? 'Excellent (Unggul)' : 'Peringkat Unggul' ?></div>
            </div>
            <div class="py-8 px-6 text-center">
                <div class="font-serif text-5xl md:text-6xl font-light text-navy leading-none counter-animated" data-count="<?= $baik_sekali_count ?>">0</div>
                <div class="editorial-label text-slate mt-3"><?= $EN ? 'Very Good' : 'Baik Sekali' ?></div>
            </div>
        </div>

        <!-- Faculty Medallion -->
        <div class="bg-navy text-ivory p-10 md:p-16 text-center relative overflow-hidden mb-16 rv">
            <div class="absolute inset-0 hero-pattern"></div>
            <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-gold/40"></div>
            <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-gold/40"></div>
            <div class="relative">
                <div class="relative inline-block mb-8">
                    <div class="pulse-ring w-28 h-28 md:w-32 md:h-32 rounded-full border-2 border-gold flex items-center justify-center">
                        <i class="fas fa-certificate text-4xl md:text-5xl text-gold"></i>
                    </div>
                </div>
                <p class="editorial-label text-ivory/60 mb-4"><?= $EN ? 'Faculty Accreditation' : 'Akreditasi Fakultas' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light tracking-[-0.02em] mb-8"><?= $EN ? 'Faculty of Engineering & Computer Science' : 'Fakultas Teknik & Ilmu Komputer' ?></h2>
                <div class="inline-block bg-gold text-navy font-serif font-bold text-3xl md:text-4xl px-12 py-4 tracking-tight"><?= html_escape($faculty_acc) ?></div>
                <p class="text-sm mt-8 text-ivory/60"><?= $EN ? 'Based on BAN-PT Decree • Valid until ' . date('Y', strtotime($faculty_until)) : 'Berdasarkan SK BAN-PT • Berlaku s/d ' . date('Y', strtotime($faculty_until)) ?></p>
                
                <!-- 🔥 Download Sertifikat Fakultas -->
                <?php if (!empty($faculty_certificate_file)): ?>
                <div class="mt-6">
                    <a href="<?= base_url('assets/uploads/certificates/' . $faculty_certificate_file) ?>" target="_blank" class="export-btn">
                        <i class="fas fa-download"></i>
                        <?= $EN ? 'Download Certificate' : 'Unduh Sertifikat' ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 🔥 Filter + Search Bar -->
        <div class="flex flex-col md:flex-row gap-4 mb-8 items-start md:items-center justify-between">
            <div class="acc-filter-bar">
                <button class="acc-filter-btn active" data-filter="all"><?= $EN ? 'All' : 'Semua' ?></button>
                <button class="acc-filter-btn" data-filter="Unggul"><?= $EN ? 'Excellent' : 'Unggul' ?></button>
                <button class="acc-filter-btn" data-filter="Baik Sekali">Baik Sekali</button>
                <button class="acc-filter-btn" data-filter="Baik">Baik</button>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate/50"></i>
                <input type="text" class="acc-search" id="accSearch" placeholder="<?= $EN ? 'Search program...' : 'Cari program studi...' ?>">
            </div>
        </div>

        <!-- Programs Table -->
        <div class="bg-white border border-gray-200 overflow-hidden rv">
            <div class="px-8 py-6 border-b border-gray-200 bg-ivory-warm/50 flex items-center justify-between">
                <p class="editorial-label text-gold-muted"><?= $EN ? 'Program Accreditation' : 'Akreditasi Program Studi' ?></p>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate" id="visibleCount"><?= $total_prodi ?> programs</span>
                    <button type="button" class="export-btn" onclick="exportAccreditation()">
                        <i class="fas fa-file-pdf"></i>
                        <?= $EN ? 'Export' : 'Export' ?>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full refined-table" id="accTable">
                    <thead>
                        <tr>
                            <th class="px-8 py-4 text-left sort-btn" data-sort="name">
                                <?= $EN ? 'Study Program' : 'Program Studi' ?>
                                <i class="fas fa-sort sort-icon"></i>
                            </th>
                            <th class="px-4 py-4 text-left"><?= $EN ? 'Level' : 'Jenjang' ?></th>
                            <th class="px-4 py-4 text-center sort-btn" data-sort="rank">
                                <?= $EN ? 'Rank' : 'Peringkat' ?>
                                <i class="fas fa-sort sort-icon"></i>
                            </th>
                            <th class="px-8 py-4 text-right"><?= $EN ? 'Valid Until' : 'Berlaku Sampai' ?></th>
                            <th class="px-4 py-4 text-center"><?= $EN ? 'Certificate' : 'Sertifikat' ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($programs as $p): 
                            $rank_order = ['Unggul' => 1, 'Baik Sekali' => 2, 'Baik' => 3, 'Cukup' => 4, 'Kurang' => 5];
                            $acc_rank = $rank_order[$p->accreditation ?? ''] ?? 99;
                        ?>
                        <tr class="acc-row hover:bg-ivory-warm/40 transition group"
                            data-name="<?= html_escape(strtolower($p->name)) ?>"
                            data-rank="<?= html_escape($p->accreditation ?? '') ?>"
                            data-rank-order="<?= $acc_rank ?>">
                            <td class="px-8 py-5">
                                <span class="font-serif text-base font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($p->name) ?></span>
                                <!-- 🔥 Progress Bar (untuk yang belum Unggul) -->
                                <?php if (($p->accreditation ?? '') !== 'Unggul' && !empty($p->accreditation)): ?>
                                <div class="acc-progress">
                                    <?php 
                                    $progress = $acc_rank == 2 ? 75 : ($acc_rank == 3 ? 50 : 25);
                                    $bar_class = $acc_rank == 2 ? 'baik-sekali' : 'baik';
                                    ?>
                                    <div class="acc-progress-bar <?= $bar_class ?>" style="width: 0%" data-w="<?= $progress ?>%"></div>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-5 text-slate"><?= html_escape($p->degree) ?></td>
                            <td class="px-4 py-5 text-center">
                                <?php if (($p->accreditation ?? '') === 'Unggul'): ?>
                                    <span class="acc-tooltip inline-flex items-center gap-2 bg-gold text-navy px-4 py-1.5 text-[10px] uppercase tracking-editorial font-bold" data-tooltip="<?= $EN ? 'Highest rank by BAN-PT' : 'Peringkat tertinggi dari BAN-PT' ?>">
                                        <i class="fas fa-certificate"></i><?= html_escape($p->accreditation) ?>
                                    </span>
                                <?php elseif (($p->accreditation ?? '') === 'Baik Sekali'): ?>
                                    <span class="acc-tooltip inline-block bg-green-600 text-ivory px-4 py-1.5 text-[10px] uppercase tracking-editorial font-bold" data-tooltip="<?= $EN ? 'Very Good rank' : 'Peringkat Baik Sekali' ?>">
                                        <?= html_escape($p->accreditation) ?>
                                    </span>
                                <?php elseif (!empty($p->accreditation)): ?>
                                    <span class="inline-block bg-navy text-ivory px-4 py-1.5 text-[10px] uppercase tracking-editorial font-bold">
                                        <?= html_escape($p->accreditation) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-5 text-right font-mono text-xs text-slate">
                                <?= $p->accreditation_until ? date('d M Y', strtotime($p->accreditation_until)) : '-' ?>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <?php if (!empty($p->certificate_file)): ?>
                                <a href="<?= base_url('assets/uploads/certificates/' . $p->certificate_file) ?>" target="_blank" class="cert-download">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                                <?php else: ?>
                                <span class="text-xs text-slate">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- 🔥 Empty State -->
            <div id="emptyState" class="hidden p-12 text-center">
                <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                <p class="text-slate"><?= $EN ? 'No programs match your search.' : 'Tidak ada program yang cocok.' ?></p>
            </div>
        </div>

        <p class="text-xs text-slate mt-8 text-center">
            <i class="fas fa-info-circle text-gold-muted mr-1"></i>
            <?= $EN ? 'Official certificates can be requested through the faculty administration office.' : 'Sertifikat resmi dapat diminta melalui kantor tata usaha fakultas.' ?>
        </p>

    </div>
</section>

<script>
(function(){
    // ===== 🔥 COUNTER ANIMATION =====
    var io = new IntersectionObserver(function(es){
        es.forEach(function(en){
            if(!en.isIntersecting) return;
            en.target.classList.add('rv-in');
            en.target.querySelectorAll('.counter-animated').forEach(function(el){
                if(el.dataset.done) return;
                el.dataset.done = '1';
                var target = +el.dataset.count;
                var start = performance.now();
                (function animate(now){
                    var progress = Math.min((now - start) / 1400, 1);
                    var ease = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.round(target * ease);
                    if(progress < 1) requestAnimationFrame(animate);
                })(start);
            });
            // Progress bars
            en.target.querySelectorAll('.acc-progress-bar').forEach(function(bar){
                setTimeout(function(){ bar.style.width = bar.dataset.w; }, 300);
            });
            io.unobserve(en.target);
        });
    }, {threshold: 0.15});
    document.querySelectorAll('.rv').forEach(function(el){ io.observe(el); });

    // ===== 🔥 FILTER BUTTONS =====
    var filterBtns = document.querySelectorAll('.acc-filter-btn');
    var rows = document.querySelectorAll('.acc-row');
    var searchInput = document.getElementById('accSearch');
    var emptyState = document.getElementById('emptyState');
    var visibleCount = document.getElementById('visibleCount');
    var currentFilter = 'all';

    function applyFilters(){
        var query = (searchInput.value || '').toLowerCase();
        var shown = 0;
        rows.forEach(function(row){
            var matchFilter = currentFilter === 'all' || row.dataset.rank === currentFilter;
            var matchSearch = !query || row.dataset.name.indexOf(query) > -1;
            var show = matchFilter && matchSearch;
            row.style.display = show ? '' : 'none';
            if(show) shown++;
        });
        visibleCount.textContent = shown + ' / ' + rows.length + ' programs';
        emptyState.classList.toggle('hidden', shown > 0);
    }

    filterBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            filterBtns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);

    // ===== 🔥 SORT =====
    var sortBtns = document.querySelectorAll('.sort-btn');
    sortBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            var col = btn.dataset.sort;
            var tbody = document.querySelector('#accTable tbody');
            var isAsc = !btn.classList.contains('sorted-asc');
            
            sortBtns.forEach(function(b){
                b.classList.remove('sorted', 'sorted-asc', 'sorted-desc');
                b.querySelector('.sort-icon').className = 'fas fa-sort sort-icon';
            });
            
            btn.classList.add('sorted', isAsc ? 'sorted-asc' : 'sorted-desc');
            btn.querySelector('.sort-icon').className = 'fas fa-sort-' + (isAsc ? 'up' : 'down') + ' sort-icon';
            
            var sortedRows = Array.from(rows).sort(function(a, b){
                var aVal, bVal;
                if(col === 'name'){
                    aVal = a.dataset.name;
                    bVal = b.dataset.name;
                    return isAsc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                } else if(col === 'rank'){
                    aVal = +a.dataset.rankOrder;
                    bVal = +b.dataset.rankOrder;
                    return isAsc ? aVal - bVal : bVal - aVal;
                }
                return 0;
            });
            
            sortedRows.forEach(function(row){ tbody.appendChild(row); });
        });
    });

    // ===== 🔥 EXPORT PDF =====
    window.exportAccreditation = function(){
        window.print();
    };
})();
</script>

<style>
/* Print styles */
@media print {
    .acc-filter-bar, .acc-search, .export-btn, .cert-download { display: none !important; }
    .acc-row { page-break-inside: avoid; }
}
</style>