<?php 
$EN = (get_site_lang() == 'en');
$degrees = array_unique(array_map(function($p){ return $p->degree; }, $programs));
sort($degrees);
?>

<style>
/* ===== 🔥 FILTER BAR ===== */
.prog-filter-bar {
    display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 32px;
    padding: 16px 20px; background: #fff; border: 1px solid #e5e7eb;
}
.prog-filter-group { display: flex; align-items: center; gap: 8px; }
.prog-filter-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; font-weight: 600; }
.prog-filter-select {
    padding: 8px 12px; border: 1px solid #e5e7eb; background: #fff;
    font-size: 12px; font-weight: 500; color: #0B2239;
    cursor: pointer; transition: border-color .2s;
}
.prog-filter-select:focus { outline: none; border-color: #C9A227; }

/* ===== 🔥 SEARCH ===== */
.prog-search {
    flex: 1; min-width: 200px; padding: 10px 14px 10px 40px;
    border: 2px solid #e5e7eb; background: #fff; font-size: 14px;
    transition: border-color .2s;
}
.prog-search:focus { outline: none; border-color: #C9A227; }

/* ===== 🔥 VIEW COUNT ===== */
.view-count {
    font-size: 12px; color: #64748b; font-family: 'JetBrains Mono', monospace;
}

/* ===== 🔥 CARD ENHANCEMENTS ===== */
.prog-card { position: relative; transition: all .3s cubic-bezier(.22,1,.36,1); }
.prog-card.hidden-card { display: none; }

/* 🔥 Favorite button */
.fav-btn {
    position: absolute; top: 16px; right: 16px; z-index: 10;
    width: 36px; height: 36px; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px);
    border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .2s; color: #64748b;
}
.fav-btn:hover { border-color: #C9A227; color: #C9A227; transform: scale(1.1); }
.fav-btn.active { background: #C9A227; color: #fff; border-color: #C9A227; }

/* 🔥 Quick View Overlay */
.quick-view-overlay {
    position: absolute; inset: 0; background: rgba(11,34,57,0.95);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .3s; pointer-events: none; z-index: 20;
}
.prog-card:hover .quick-view-overlay { opacity: 1; pointer-events: all; }
.quick-view-btn {
    padding: 12px 24px; background: #C9A227; color: #0B2239;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
    border: none; cursor: pointer; transition: all .2s;
}
.quick-view-btn:hover { background: #fff; }

/* ===== 🔥 QUICK VIEW MODAL ===== */
.qv-modal {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(6,20,32,0.85); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; padding: 1rem;
}
.qv-modal.open { display: flex; animation: fadeIn .3s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.qv-modal-card {
    background: #fff; width: 100%; max-width: 640px; max-height: 85vh; overflow-y: auto;
    box-shadow: 0 32px 64px rgba(0,0,0,.3);
}
.qv-modal-head {
    padding: 24px; background: #0B2239; color: #F7F5F0;
    display: flex; justify-content: space-between; align-items-start;
    border-bottom: 3px solid #C9A227;
}
.qv-modal-close {
    background: none; border: none; color: #C9A227; font-size: 24px;
    cursor: pointer; padding: 0; width: 32px; height: 32px;
}
.qv-modal-body { padding: 24px; }
.qv-stat { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.qv-stat:last-child { border: 0; }
.qv-stat-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.qv-stat-value { font-family: 'Fraunces', serif; font-size: 18px; color: #0B2239; font-weight: 500; }

/* ===== 🔥 COMPARE BAR ===== */
.compare-bar {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 90;
    background: #0B2239; color: #F7F5F0; padding: 16px 24px;
    display: none; align-items: center; justify-content: space-between;
    box-shadow: 0 -8px 32px rgba(0,0,0,.2);
    transform: translateY(100%); transition: transform .3s;
}
.compare-bar.visible { display: flex; transform: translateY(0); }
.compare-items { display: flex; gap: 8px; flex-wrap: wrap; }
.compare-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; background: rgba(201,162,39,0.15); border: 1px solid rgba(201,162,39,0.3);
    font-size: 11px; font-weight: 600;
}
.compare-chip button { background: none; border: none; color: #C9A227; cursor: pointer; padding: 0; }
.compare-btn {
    padding: 10px 24px; background: #C9A227; color: #0B2239;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
    border: none; cursor: pointer;
}

/* ===== 🔥 COMPARE MODAL ===== */
.cmp-modal {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(6,20,32,0.9); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; padding: 1rem;
}
.cmp-modal.open { display: flex; }
.cmp-modal-card {
    background: #fff; width: 100%; max-width: 900px; max-height: 85vh; overflow-y: auto;
}
.cmp-modal-head {
    padding: 20px 24px; background: #0B2239; color: #F7F5F0;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 3px solid #C9A227;
}
.cmp-table { width: 100%; border-collapse: collapse; }
.cmp-table th, .cmp-table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
.cmp-table th { background: #F7F5F0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; font-weight: 600; }
.cmp-table td { font-size: 14px; color: #0B2239; }
.cmp-table .cmp-winner { background: #fef3c7; font-weight: 600; }

/* ===== 🔥 MARQUEE ===== */
.marquee { overflow: hidden; white-space: nowrap; }
.marquee-content { display: inline-block; animation: marquee 40s linear infinite; }
@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

/* ===== 🔥 TOAST ===== */
.toast {
    position: fixed; bottom: 80px; right: 24px; z-index: 100;
    padding: 14px 20px; background: #0B2239; color: #F7F5F0;
    font-size: 13px; font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(120%); transition: transform .3s cubic-bezier(.22,1,.36,1);
}
.toast.show { transform: translateX(0); }
.toast i { color: #C9A227; margin-right: 8px; }
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="absolute -bottom-16 -left-8 font-serif text-[18rem] leading-none text-navy/5 select-none pointer-events-none hidden lg:block">PS</div>
    <div class="container mx-auto px-6 py-20 md:py-28 relative">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="max-w-3xl">
                <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Academic Programs' : 'Program Akademik' ?></p>
                <h1 class="font-serif font-light text-navy tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                    <?= $EN ? 'Study <em class="italic text-gold-muted">Programs</em>' : 'Program <em class="italic text-gold-muted">Studi</em>' ?>
                </h1>
                <p class="text-slate mt-6 text-lg leading-relaxed max-w-2xl">
                    <?= $EN ? 'Accredited programs designed to shape your future, guided by world-class faculty.' : 'Pilihan program studi terakreditasi yang dirancang untuk membentuk masa depan Anda, dipandu dosen kelas dunia.' ?>
                </p>
            </div>
            <div class="hidden md:block text-right">
                <div class="font-serif text-8xl font-light text-navy/10 leading-none"><?= count($programs) ?></div>
                <p class="editorial-label text-slate mt-2"><?= $EN ? 'Active Programs' : 'Program Aktif' ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============ MARQUEE TICKER ============ -->
<?php if (!empty($programs)): ?>
<div class="marquee bg-navy text-ivory/80 py-3">
    <div class="marquee-content text-[11px] uppercase tracking-editorial">
        <?php 
        $ticker = '';
        foreach ($programs as $p) {
            $ticker .= '<span class="mx-4">' . html_escape($p->name) . ' <span class="text-gold mx-3">✦</span> ' . html_escape($p->degree) . '</span>';
        }
        echo $ticker . $ticker; // duplicate for seamless loop
        ?>
    </div>
</div>
<?php endif; ?>

<!-- ============ PROGRAM GRID ============ -->
<section class="py-24 bg-ivory">
    <div class="container mx-auto px-6">

        <?php if (empty($programs)): ?>
            <div class="text-center py-24 bg-white border border-gray-200">
                <i class="fas fa-graduation-cap text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No study programs yet.' : 'Belum ada program studi.' ?></p>
            </div>
        <?php else: ?>
        
        <!-- 🔥 Filter Bar -->
        <div class="prog-filter-bar rv">
            <div class="prog-filter-group">
                <span class="prog-filter-label"><?= $EN ? 'Degree' : 'Jenjang' ?>:</span>
                <select class="prog-filter-select" id="filterDegree">
                    <option value="all"><?= $EN ? 'All' : 'Semua' ?></option>
                    <?php foreach ($degrees as $d): ?>
                    <option value="<?= html_escape($d) ?>"><?= html_escape($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="prog-filter-group">
                <span class="prog-filter-label"><?= $EN ? 'Accreditation' : 'Akreditasi' ?>:</span>
                <select class="prog-filter-select" id="filterAcc">
                    <option value="all"><?= $EN ? 'All' : 'Semua' ?></option>
                    <option value="Unggul">Unggul</option>
                    <option value="Baik Sekali">Baik Sekali</option>
                    <option value="Baik">Baik</option>
                </select>
            </div>
            <div class="prog-filter-group">
                <span class="prog-filter-label"><?= $EN ? 'Sort' : 'Urutkan' ?>:</span>
                <select class="prog-filter-select" id="sortBy">
                    <option value="name"><?= $EN ? 'Name A-Z' : 'Nama A-Z' ?></option>
                    <option value="name-desc"><?= $EN ? 'Name Z-A' : 'Nama Z-A' ?></option>
                    <option value="sks"><?= $EN ? 'Credits (High)' : 'SKS (Tinggi)' ?></option>
                    <option value="courses"><?= $EN ? 'Courses (Most)' : 'MK (Terbanyak)' ?></option>
                </select>
            </div>
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate/50"></i>
                <input type="text" class="prog-search" id="progSearch" placeholder="<?= $EN ? 'Search programs...' : 'Cari program studi...' ?>">
            </div>
            <span class="view-count" id="viewCount"><?= count($programs) ?> programs</span>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="programGrid">
            <?php foreach ($programs as $idx => $p): 
                $prog_id = 'prog-' . $p->slug;
            ?>
            <div class="prog-card relative bg-white border border-gray-200 group rv flex flex-col"
                 data-id="<?= html_escape($prog_id) ?>"
                 data-name="<?= html_escape(strtolower($p->name)) ?>"
                 data-degree="<?= html_escape($p->degree) ?>"
                 data-acc="<?= html_escape($p->accreditation ?? '') ?>"
                 data-sks="<?= (int)$p->total_sks ?>"
                 data-courses="<?= (int)$p->total_courses ?>">
                
                <!-- 🔥 Favorite Button -->
                <button type="button" class="fav-btn" data-id="<?= html_escape($prog_id) ?>" title="<?= $EN ? 'Add to favorites' : 'Tambah ke favorit' ?>">
                    <i class="fas fa-heart"></i>
                </button>
                
                <!-- 🔥 Compare Checkbox -->
                <label class="absolute top-16 right-4 z-10 flex items-center gap-1 cursor-pointer opacity-0 group-hover:opacity-100 transition" title="<?= $EN ? 'Compare' : 'Bandingkan' ?>">
                    <input type="checkbox" class="compare-check accent-gold" data-id="<?= html_escape($prog_id) ?>" data-name="<?= html_escape($p->name) ?>">
                    <span class="text-[10px] text-slate uppercase tracking-wider"><?= $EN ? 'Compare' : 'Banding' ?></span>
                </label>
                
                <!-- 🔥 Quick View Overlay -->
                <div class="quick-view-overlay">
                    <button type="button" class="quick-view-btn" onclick="openQuickView('<?= html_escape($prog_id) ?>')">
                        <i class="fas fa-eye mr-2"></i><?= $EN ? 'Quick View' : 'Lihat Cepat' ?>
                    </button>
                </div>
                
                <div class="absolute top-0 left-0 right-0 h-[3px] bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-10"></div>
                <div class="absolute -top-5 -right-3 font-serif text-[7rem] leading-none text-navy/5 group-hover:text-gold/10 transition-colors duration-500 select-none pointer-events-none">
                    <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?>
                </div>

                <div class="p-8 md:p-9 flex flex-col flex-1 relative">
                    <div class="flex items-center justify-between mb-7">
                        <span class="editorial-label text-gold-muted"><?= $EN ? 'Program' : 'Prodi' ?> <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="bg-navy text-ivory text-[10px] uppercase tracking-editorial px-3 py-1.5 font-semibold"><?= html_escape($p->degree) ?></span>
                    </div>

                    <h3 class="font-serif text-2xl md:text-[1.7rem] font-medium text-navy leading-tight tracking-[-0.01em] group-hover:text-gold-muted transition mb-4">
                        <?= html_escape($p->name) ?>
                    </h3>
                    <p class="text-sm text-slate leading-relaxed mb-8 line-clamp-3">
                        <?= html_escape(character_limiter($p->description ?? '', 120)) ?>
                    </p>

                    <div class="mt-auto">
                        <div class="grid grid-cols-2 divide-x divide-gray-100 border-y border-gray-100 mb-5">
                            <div class="py-3 pr-4">
                                <div class="font-serif text-2xl font-light text-navy leading-none"><?= $p->total_courses ?></div>
                                <div class="text-[10px] uppercase tracking-wider text-slate mt-1"><?= $EN ? 'Courses' : 'Mata Kuliah' ?></div>
                            </div>
                            <div class="py-3 pl-4">
                                <div class="font-serif text-2xl font-light text-navy leading-none"><?= $p->total_sks ?></div>
                                <div class="text-[10px] uppercase tracking-wider text-slate mt-1">SKS</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] uppercase tracking-wider text-slate">
                                <?= $EN ? 'Accreditation' : 'Akreditasi' ?>:
                                <span class="<?= ($p->accreditation ?? '') == 'Unggul' ? 'text-gold-muted' : 'text-navy' ?> font-bold"><?= html_escape($p->accreditation ?? '-') ?></span>
                            </span>
                            <a href="<?= base_url('akademik/detail/' . $p->slug) ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-[10px]">
                                <?= $EN ? 'Detail' : 'Detail' ?>
                                <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden data for quick view -->
                <script type="application/json" class="qv-data">
                <?= json_encode([
                    'name' => $p->name,
                    'degree' => $p->degree,
                    'accreditation' => $p->accreditation ?? '-',
                    'accreditation_until' => $p->accreditation_until ?? '-',
                    'description' => $p->description ?? '',
                    'total_sks' => $p->total_sks,
                    'total_courses' => $p->total_courses,
                    'slug' => $p->slug,
                    'head' => $p->head_of_study_program ?? '-',
                ]) ?>
                </script>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-16">
            <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
            <p class="text-slate"><?= $EN ? 'No programs match your filter.' : 'Tidak ada program yang cocok.' ?></p>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- 🔥 Quick View Modal -->
<div class="qv-modal" id="qvModal" onclick="if(event.target===this)closeQuickView()">
    <div class="qv-modal-card">
        <div class="qv-modal-head">
            <div>
                <p class="editorial-label text-gold mb-2" id="qvDegree">-</p>
                <h3 class="font-serif text-2xl font-light" id="qvName">-</h3>
            </div>
            <button class="qv-modal-close" onclick="closeQuickView()"><i class="fas fa-times"></i></button>
        </div>
        <div class="qv-modal-body">
            <p class="text-slate mb-6" id="qvDesc">-</p>
            <div class="qv-stat"><span class="qv-stat-label"><?= $EN ? 'Accreditation' : 'Akreditasi' ?></span><span class="qv-stat-value" id="qvAcc">-</span></div>
            <div class="qv-stat"><span class="qv-stat-label"><?= $EN ? 'Valid Until' : 'Berlaku Sampai' ?></span><span class="qv-stat-value" id="qvUntil">-</span></div>
            <div class="qv-stat"><span class="qv-stat-label"><?= $EN ? 'Total Credits' : 'Total SKS' ?></span><span class="qv-stat-value" id="qvSks">-</span></div>
            <div class="qv-stat"><span class="qv-stat-label"><?= $EN ? 'Courses' : 'Mata Kuliah' ?></span><span class="qv-stat-value" id="qvCourses">-</span></div>
            <div class="qv-stat"><span class="qv-stat-label"><?= $EN ? 'Head of Program' : 'Ketua Prodi' ?></span><span class="qv-stat-value" id="qvHead">-</span></div>
            <div class="mt-6 flex gap-3">
                <a href="#" id="qvDetailLink" class="flex-1 text-center px-4 py-3 bg-navy text-ivory font-semibold uppercase tracking-editorial text-[10px]"><i class="fas fa-arrow-right mr-2"></i><?= $EN ? 'View Details' : 'Lihat Detail' ?></a>
                <button type="button" onclick="shareProgram()" class="px-4 py-3 border border-navy text-navy font-semibold uppercase tracking-editorial text-[10px]"><i class="fas fa-share-alt mr-2"></i>Share</button>
            </div>
        </div>
    </div>
</div>

<!-- 🔥 Compare Modal -->
<div class="cmp-modal" id="cmpModal" onclick="if(event.target===this)closeCompare()">
    <div class="cmp-modal-card">
        <div class="cmp-modal-head">
            <h3 class="font-serif text-xl font-light"><?= $EN ? 'Program Comparison' : 'Perbandingan Program' ?></h3>
            <button class="qv-modal-close" onclick="closeCompare()"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6">
            <table class="cmp-table" id="cmpTable"></table>
        </div>
    </div>
</div>

<!-- 🔥 Compare Bar (fixed bottom) -->
<div class="compare-bar" id="compareBar">
    <div class="compare-items" id="compareItems"></div>
    <div class="flex gap-3">
        <button type="button" onclick="clearCompare()" class="px-4 py-2 text-xs uppercase tracking-editorial font-semibold text-ivory/70 hover:text-ivory">Clear</button>
        <button type="button" onclick="openCompare()" class="compare-btn"><i class="fas fa-balance-scale mr-2"></i><?= $EN ? 'Compare' : 'Bandingkan' ?></button>
    </div>
</div>

<!-- 🔥 Toast -->
<div id="toast" class="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<script>
(function(){
    var programs = {};
    document.querySelectorAll('.prog-card').forEach(function(card){
        var data = JSON.parse(card.querySelector('.qv-data').textContent);
        programs[card.dataset.id] = data;
    });
    
    // ===== 🔥 REVEAL ANIMATION =====
    var io = new IntersectionObserver(function(es){
        es.forEach(function(en){ if(!en.isIntersecting) return;
            en.target.classList.add('rv-in');
            io.unobserve(en.target);
        });
    }, {threshold:.1});
    document.querySelectorAll('.rv').forEach(function(el){ io.observe(el); });
    
    // ===== 🔥 FILTER & SEARCH =====
    var filterDegree = document.getElementById('filterDegree');
    var filterAcc = document.getElementById('filterAcc');
    var sortBy = document.getElementById('sortBy');
    var searchInput = document.getElementById('progSearch');
    var cards = document.querySelectorAll('.prog-card');
    var grid = document.getElementById('programGrid');
    var emptyState = document.getElementById('emptyState');
    var viewCount = document.getElementById('viewCount');
    
    function applyFilters(){
        var degree = filterDegree.value;
        var acc = filterAcc.value;
        var query = (searchInput.value || '').toLowerCase();
        var shown = 0;
        
        cards.forEach(function(card){
            var matchDegree = degree === 'all' || card.dataset.degree === degree;
            var matchAcc = acc === 'all' || card.dataset.acc === acc;
            var matchSearch = !query || card.dataset.name.indexOf(query) > -1;
            var show = matchDegree && matchAcc && matchSearch;
            card.classList.toggle('hidden-card', !show);
            if(show) shown++;
        });
        
        viewCount.textContent = shown + ' / ' + cards.length + ' programs';
        emptyState.classList.toggle('hidden', shown > 0);
        
        // Sort
        if (sortBy.value !== 'name') {
            var sorted = Array.from(cards).sort(function(a, b){
                switch(sortBy.value){
                    case 'name-desc': return b.dataset.name.localeCompare(a.dataset.name);
                    case 'sks': return (+b.dataset.sks) - (+a.dataset.sks);
                    case 'courses': return (+b.dataset.courses) - (+a.dataset.courses);
                    default: return a.dataset.name.localeCompare(b.dataset.name);
                }
            });
            sorted.forEach(function(card){ grid.appendChild(card); });
        }
    }
    
    filterDegree.addEventListener('change', applyFilters);
    filterAcc.addEventListener('change', applyFilters);
    sortBy.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
    
    // ===== 🔥 FAVORITES =====
    var favorites = JSON.parse(localStorage.getItem('program_favorites') || '[]');
    
    function updateFavUI(){
        document.querySelectorAll('.fav-btn').forEach(function(btn){
            btn.classList.toggle('active', favorites.indexOf(btn.dataset.id) > -1);
        });
    }
    
    document.querySelectorAll('.fav-btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            var id = btn.dataset.id;
            var idx = favorites.indexOf(id);
            if (idx > -1) {
                favorites.splice(idx, 1);
                showToast('<?= $EN ? "Removed from favorites" : "Dihapus dari favorit" ?>');
            } else {
                favorites.push(id);
                showToast('<?= $EN ? "Added to favorites!" : "Ditambahkan ke favorit!" ?>');
            }
            localStorage.setItem('program_favorites', JSON.stringify(favorites));
            updateFavUI();
        });
    });
    
    updateFavUI();
    
    // ===== 🔥 TOAST =====
    function showToast(msg){
        var toast = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        toast.classList.add('show');
        setTimeout(function(){ toast.classList.remove('show'); }, 2500);
    }
    
    // ===== 🔥 QUICK VIEW =====
    window.openQuickView = function(id){
        var data = programs[id];
        if (!data) return;
        document.getElementById('qvDegree').textContent = data.degree;
        document.getElementById('qvName').textContent = data.name;
        document.getElementById('qvDesc').textContent = data.description || '-';
        document.getElementById('qvAcc').textContent = data.accreditation;
        document.getElementById('qvUntil').textContent = data.accreditation_until || '-';
        document.getElementById('qvSks').textContent = data.total_sks + ' SKS';
        document.getElementById('qvCourses').textContent = data.total_courses;
        document.getElementById('qvHead').textContent = data.head;
        document.getElementById('qvDetailLink').href = '<?= base_url("akademik/detail/") ?>' + data.slug;
        document.getElementById('qvModal').classList.add('open');
    };
    
    window.closeQuickView = function(){
        document.getElementById('qvModal').classList.remove('open');
    };
    
    window.shareProgram = function(){
        var name = document.getElementById('qvName').textContent;
        var url = document.getElementById('qvDetailLink').href;
        if (navigator.share) {
            navigator.share({ title: name, url: url });
        } else {
            navigator.clipboard.writeText(url);
            showToast('<?= $EN ? "Link copied!" : "Link disalin!" ?>');
        }
    };
    
    // ===== 🔥 COMPARE =====
    var compareList = JSON.parse(localStorage.getItem('program_compare') || '[]');
    var compareBar = document.getElementById('compareBar');
    var compareItems = document.getElementById('compareItems');
    
    function updateCompareUI(){
        compareItems.innerHTML = '';
        compareList.forEach(function(id){
            var data = programs[id];
            if (!data) return;
            var chip = document.createElement('span');
            chip.className = 'compare-chip';
            chip.innerHTML = data.name + ' <button onclick="removeCompare(\'' + id + '\')" title="Remove">&times;</button>';
            compareItems.appendChild(chip);
        });
        compareBar.classList.toggle('visible', compareList.length > 0);
        
        // Update checkboxes
        document.querySelectorAll('.compare-check').forEach(function(cb){
            cb.checked = compareList.indexOf(cb.dataset.id) > -1;
        });
    }
    
    document.querySelectorAll('.compare-check').forEach(function(cb){
        cb.addEventListener('change', function(){
            var id = cb.dataset.id;
            if (cb.checked) {
                if (compareList.length >= 3) {
                    showToast('<?= $EN ? "Max 3 programs to compare" : "Maksimal 3 program untuk dibandingkan" ?>');
                    cb.checked = false;
                    return;
                }
                compareList.push(id);
            } else {
                compareList = compareList.filter(function(c){ return c !== id; });
            }
            localStorage.setItem('program_compare', JSON.stringify(compareList));
            updateCompareUI();
        });
    });
    
    window.removeCompare = function(id){
        compareList = compareList.filter(function(c){ return c !== id; });
        localStorage.setItem('program_compare', JSON.stringify(compareList));
        updateCompareUI();
    };
    
    window.clearCompare = function(){
        compareList = [];
        localStorage.setItem('program_compare', JSON.stringify(compareList));
        updateCompareUI();
    };
    
    window.openCompare = function(){
        if (compareList.length < 2) {
            showToast('<?= $EN ? "Select at least 2 programs" : "Pilih minimal 2 program" ?>');
            return;
        }
        
        var table = document.getElementById('cmpTable');
        var headers = ['<?= $EN ? "Attribute" : "Atribut" ?>'];
        compareList.forEach(function(id){
            headers.push(programs[id].name);
        });
        
        var rows = [
            ['<?= $EN ? "Degree" : "Jenjang" ?>', function(p){ return p.degree; }],
            ['<?= $EN ? "Accreditation" : "Akreditasi" ?>', function(p){ return p.accreditation; }],
            ['<?= $EN ? "Total SKS" : "Total SKS" ?>', function(p){ return p.total_sks; }],
            ['<?= $EN ? "Courses" : "Mata Kuliah" ?>', function(p){ return p.total_courses; }],
            ['<?= $EN ? "Head" : "Kaprodi" ?>', function(p){ return p.head; }],
        ];
        
        // Find winners for numeric comparison
        var maxSks = Math.max.apply(null, compareList.map(function(id){ return programs[id].total_sks; }));
        var maxCourses = Math.max.apply(null, compareList.map(function(id){ return programs[id].total_courses; }));
        
        var html = '<thead><tr>' + headers.map(function(h){ return '<th>' + h + '</th>'; }).join('') + '</tr></thead><tbody>';
        
        rows.forEach(function(row){
            html += '<tr><td class="font-semibold">' + row[0] + '</td>';
            compareList.forEach(function(id){
                var val = row[1](programs[id]);
                var isWinner = false;
                if (row[0].indexOf('SKS') > -1 && val === maxSks) isWinner = true;
                if (row[0].indexOf('Courses') > -1 && val === maxCourses) isWinner = true;
                html += '<td class="' + (isWinner ? 'cmp-winner' : '') + '">' + val + (isWinner ? ' 🏆' : '') + '</td>';
            });
            html += '</tr>';
        });
        
        html += '</tbody>';
        table.innerHTML = html;
        document.getElementById('cmpModal').classList.add('open');
    };
    
    window.closeCompare = function(){
        document.getElementById('cmpModal').classList.remove('open');
    };
    
    updateCompareUI();
    
    // ===== ESC to close modals =====
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            closeQuickView();
            closeCompare();
        }
    });
})();
</script>