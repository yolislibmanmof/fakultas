<?php
$EN = (get_site_lang() == 'en');

// ===== 🔥 HIGHLIGHT HELPER =====
if (!function_exists('sr_hl')) {
    function sr_hl($text, $q) {
        $esc = html_escape($text);
        $q = trim((string)$q);
        if ($q === '') return $esc;
        $eq = html_escape($q);
        $out = preg_replace('/(' . preg_quote($eq, '/') . ')/iu', '<mark class="sr-hl">$1</mark>', $esc);
        return $out === null ? $esc : $out;
    }
}
?>

<style>
/* ===== 🔥 HIGHLIGHT ===== */
.sr-hl { background: rgba(201,162,39,.4); color: inherit; padding: 0 3px; border-radius: 2px; }

/* ===== RECENT SEARCHES ===== */
.sr-recent { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 1.25rem; }
.sr-recent-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; background: rgba(247,245,240,.1);
    border: 1px solid rgba(247,245,240,.2); color: #F7F5F0;
    font-size: 11px; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.sr-recent-chip:hover { background: #C9A227; color: #0B2239; border-color: #C9A227; }
.sr-recent-chip i { font-size: 9px; opacity: .6; }

/* ===== KBD HINT ===== */
.kbd-hint {
    display: inline-block; padding: 2px 8px; margin-left: 8px;
    background: rgba(247,245,240,.12); border: 1px solid rgba(247,245,240,.25);
    font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #C9A227;
}

/* ===== TIPS CARD ===== */
.sr-tips { background: #fff; border: 1px solid #e5e7eb; border-left: 4px solid #C9A227; padding: 1.5rem 2rem; }
.sr-tips li { font-size: 13px; color: #64748b; margin-bottom: 6px; padding-left: 4px; }
.sr-tips li i { color: #C9A227; margin-right: 8px; }
</style>

<!-- ============ SEARCH HEADER ============ -->
<section class="bg-navy text-ivory py-20 md:py-24 relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute -bottom-24 -right-8 font-serif text-[16rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">?</div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <p class="editorial-label text-gold mb-6"><?= $EN ? 'Smart Search' : 'Pencarian Cerdas' ?></p>
            <h1 class="font-serif font-light text-4xl md:text-6xl tracking-[-0.02em] leading-[1.05] mb-10">
                <?php if ($q !== ''): ?>
                    <?= $EN ? 'Found' : 'Ditemukan' ?> <em class="italic text-gold"><?= $total ?></em> <?= $EN ? 'results' : 'hasil' ?>
                <?php else: ?>
                    <?= $EN ? 'Search across <em class="italic text-gold">all</em> faculty content' : 'Cari di <em class="italic text-gold">seluruh</em> konten fakultas' ?>
                <?php endif; ?>
            </h1>
            <form action="<?= base_url('search') ?>" method="GET" id="srForm">
                <div class="relative max-w-xl mx-auto">
                    <input type="text" name="q" id="srInput" value="<?= html_escape($q) ?>" placeholder="<?= $EN ? 'Search news, faculty, programs, forms...' : 'Cari berita, dosen, program studi, formulir...' ?>"
                        class="w-full px-6 py-4 pr-14 border-2 border-gold text-navy placeholder:text-slate font-serif text-lg outline-none bg-ivory focus:border-gold-soft transition">
                    <button type="submit" class="absolute right-0 top-0 bottom-0 w-14 bg-gold text-navy hover:bg-gold-muted transition flex items-center justify-center">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <p class="text-ivory/40 text-[10px] uppercase tracking-wider mt-3">
                    <?= $EN ? 'Press' : 'Tekan' ?> <span class="kbd-hint">/</span> <?= $EN ? 'to focus search' : 'untuk fokus ke pencarian' ?>
                </p>
            </form>
            <?php if ($q !== ''): ?>
                <p class="text-ivory/60 mt-5 text-sm"><?= $EN ? 'for keyword' : 'untuk kata kunci' ?> "<span class="text-gold font-semibold"><?= html_escape($q) ?></span>"</p>
            <?php else: ?>
                <!-- 🔥 RECENT SEARCHES -->
                <div class="sr-recent" id="srRecent"></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============ RESULTS ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6 max-w-5xl space-y-14">

        <?php if ($q !== '' && $total == 0): ?>
            <div class="text-center py-24 bg-white border border-gray-200">
                <i class="fas fa-search-minus text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No matching results.' : 'Tidak ada hasil yang cocok.' ?></p>
                <p class="text-slate mt-2"><?= $EN ? 'Try another keyword or check your spelling.' : 'Coba kata kunci lain atau periksa ejaan Anda.' ?></p>
            </div>
        <?php endif; ?>

        <!-- 01 News -->
        <?php if (!empty($results['posts'])): ?>
        <div class="fade-in">
            <div class="flex items-end justify-between mb-7 pb-4 border-b-2 border-navy">
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl font-light text-gold-muted leading-none">01</span>
                    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-[-0.01em]"><?= $EN ? 'News & Announcements' : 'Berita & Pengumuman' ?></h2>
                </div>
                <span class="font-serif text-2xl font-light text-gold-muted"><?= count($results['posts']) ?></span>
            </div>
            <div class="space-y-3">
                <?php foreach ($results['posts'] as $p): ?>
                <a href="<?= base_url('berita/detail/' . $p->slug) ?>" class="block bg-white border border-gray-200 p-6 hover-lift hover:border-gold/40 transition group">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="editorial-label text-gold-muted"><?= html_escape($p->category_name ?? 'Berita') ?></span>
                        <span class="w-8 h-px bg-gold"></span>
                        <span class="text-xs font-mono text-slate"><?= date('d M Y', strtotime($p->published_at)) ?></span>
                    </div>
                    <h3 class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition tracking-[-0.01em]"><?= sr_hl($p->title, $q) ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 02 Lecturers -->
        <?php if (!empty($results['lecturers'])): ?>
        <div class="fade-in">
            <div class="flex items-end justify-between mb-7 pb-4 border-b-2 border-navy">
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl font-light text-gold-muted leading-none">02</span>
                    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-[-0.01em]"><?= $EN ? 'Faculty' : 'Dosen' ?></h2>
                </div>
                <span class="font-serif text-2xl font-light text-gold-muted"><?= count($results['lecturers']) ?></span>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                <?php foreach ($results['lecturers'] as $l): ?>
                <!-- 🔥 FIX: link ke detail dosen -->
                <a href="<?= base_url('dosen/detail/' . $l->id) ?>" class="bg-white border border-gray-200 p-6 hover-lift hover:border-gold/40 transition group">
                    <h3 class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition tracking-[-0.01em]">
                        <?= sr_hl(trim(($l->title_front ?? '') . ' ' . $l->name), $q) ?><?= $l->title_back ? ', ' . html_escape($l->title_back) : '' ?>
                    </h3>
                    <p class="text-sm text-slate mt-2 line-clamp-2"><?= sr_hl(character_limiter($l->expertise ?? '-', 60), $q) ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 03 Programs -->
        <?php if (!empty($results['programs'])): ?>
        <div class="fade-in">
            <div class="flex items-end justify-between mb-7 pb-4 border-b-2 border-navy">
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl font-light text-gold-muted leading-none">03</span>
                    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-[-0.01em]"><?= $EN ? 'Study Programs' : 'Program Studi' ?></h2>
                </div>
                <span class="font-serif text-2xl font-light text-gold-muted"><?= count($results['programs']) ?></span>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                <?php foreach ($results['programs'] as $p): ?>
                <a href="<?= base_url('akademik/detail/' . $p->slug) ?>" class="bg-white border border-gray-200 p-6 hover-lift hover:border-gold/40 transition group">
                    <h3 class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition tracking-[-0.01em]">
                        <?= sr_hl($p->name, $q) ?>
                        <span class="text-xs bg-navy text-ivory px-2 py-0.5 ml-2 align-middle"><?= html_escape($p->degree) ?></span>
                    </h3>
                    <p class="text-sm text-slate mt-2"><?= $EN ? 'Accreditation' : 'Akreditasi' ?>: <span class="text-gold-muted font-semibold"><?= html_escape($p->accreditation ?? '-') ?></span></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 04 Courses -->
        <?php if (!empty($results['courses'])): ?>
        <div class="fade-in">
            <div class="flex items-end justify-between mb-7 pb-4 border-b-2 border-navy">
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl font-light text-gold-muted leading-none">04</span>
                    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-[-0.01em]"><?= $EN ? 'Courses' : 'Mata Kuliah' ?></h2>
                </div>
                <span class="font-serif text-2xl font-light text-gold-muted"><?= count($results['courses']) ?></span>
            </div>
            <div class="bg-white border border-gray-200 divide-y divide-gray-100">
                <?php foreach ($results['courses'] as $c): ?>
                <a href="<?= base_url('akademik/kurikulum') ?>" class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-ivory-warm/50 hover:pl-8 transition-all duration-300 group">
                    <div class="flex items-center gap-4 min-w-0">
                        <span class="font-mono text-xs text-gold-muted flex-shrink-0"><?= html_escape($c->course_code) ?></span>
                        <span class="font-medium text-navy group-hover:text-gold-muted transition"><?= sr_hl($c->name, $q) ?></span>
                    </div>
                    <span class="bg-navy text-ivory text-xs px-2.5 py-1 font-semibold flex-shrink-0"><?= $c->sks ?> SKS</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 05 Documents -->
        <?php if (!empty($results['documents'])): ?>
        <div class="fade-in">
            <div class="flex items-end justify-between mb-7 pb-4 border-b-2 border-navy">
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl font-light text-gold-muted leading-none">05</span>
                    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-[-0.01em]"><?= $EN ? 'Documents & Forms' : 'Dokumen & Formulir' ?></h2>
                </div>
                <span class="font-serif text-2xl font-light text-gold-muted"><?= count($results['documents']) ?></span>
            </div>
            <div class="bg-white border border-gray-200 divide-y divide-gray-100">
                <?php foreach ($results['documents'] as $d): ?>
                <!-- 🔥 FIX: link langsung ke file download -->
                <a href="<?= base_url('download/file/' . $d->id) ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-ivory-warm/50 hover:pl-8 transition-all duration-300 group">
                    <i class="fas fa-download text-gold-muted"></i>
                    <span class="font-medium text-navy group-hover:text-gold-muted transition"><?= sr_hl($d->title, $q) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 🔥 06 Alumni (defensive) -->
        <?php if (!empty($results['alumni'])): ?>
        <div class="fade-in">
            <div class="flex items-end justify-between mb-7 pb-4 border-b-2 border-navy">
                <div class="flex items-baseline gap-4">
                    <span class="font-serif text-3xl font-light text-gold-muted leading-none">06</span>
                    <h2 class="font-serif text-2xl md:text-3xl font-light text-navy tracking-[-0.01em]"><?= $EN ? 'Alumni' : 'Alumni' ?></h2>
                </div>
                <span class="font-serif text-2xl font-light text-gold-muted"><?= count($results['alumni']) ?></span>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                <?php foreach ($results['alumni'] as $al): ?>
                <a href="<?= base_url('alumni/view/' . $al->id) ?>" class="bg-white border border-gray-200 p-6 hover-lift hover:border-gold/40 transition group">
                    <h3 class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition"><?= sr_hl($al->full_name, $q) ?></h3>
                    <p class="text-sm text-slate mt-2"><?= html_escape($al->current_position ?? '') ?> <?= $al->company ? '@ ' . html_escape($al->company) : '' ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 🔥 SEARCH TIPS -->
        <?php if ($q === ''): ?>
        <div class="sr-tips fade-in">
            <p class="editorial-label text-gold-muted mb-4"><i class="fas fa-lightbulb mr-1"></i><?= $EN ? 'Search Tips' : 'Tips Pencarian' ?></p>
            <ul class="list-none">
                <li><i class="fas fa-check"></i><?= $EN ? 'Use specific keywords, e.g.' : 'Gunakan kata kunci spesifik, mis.' ?> <em>"beasiswa"</em>, <em>"kurikulum"</em>, <em>"skripsi"</em></li>
                <li><i class="fas fa-check"></i><?= $EN ? 'Search lecturer names to find their profile & research.' : 'Cari nama dosen untuk menemukan profil & risetnya.' ?></li>
                <li><i class="fas fa-check"></i><?= $EN ? 'Course codes (e.g. IF101) work too.' : 'Kode mata kuliah (mis. IF101) juga bisa dicari.' ?></li>
            </ul>
        </div>
        <?php endif; ?>

    </div>
</section>

<script>
(function(){
    var input = document.getElementById('srInput');
    var form = document.getElementById('srForm');
    var q = <?= json_encode($q ?? '') ?>;

    // ===== 🔥 KEYBOARD SHORTCUT "/" =====
    document.addEventListener('keydown', function(e){
        if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
            var tag = (document.activeElement.tagName || '').toLowerCase();
            if (tag !== 'input' && tag !== 'textarea') {
                e.preventDefault();
                input.focus();
                input.select();
            }
        }
    });

    // ===== 🔥 RECENT SEARCHES (localStorage) =====
    var KEY = 'site_recent_searches';
    function getRecent(){
        try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch(e){ return []; }
    }
    function saveRecent(term){
        term = (term || '').trim();
        if (!term) return;
        var list = getRecent().filter(function(t){ return t.toLowerCase() !== term.toLowerCase(); });
        list.unshift(term);
        localStorage.setItem(KEY, JSON.stringify(list.slice(0, 5)));
    }
    if (form) form.addEventListener('submit', function(){ saveRecent(input.value); });

    // Render chips saat q kosong
    var recentWrap = document.getElementById('srRecent');
    if (recentWrap && !q) {
        var recents = getRecent();
        recents.forEach(function(t){
            var a = document.createElement('a');
            a.href = '<?= base_url('search?q=') ?>' + encodeURIComponent(t);
            a.className = 'sr-recent-chip';
            a.innerHTML = '<i class="fas fa-history"></i>' + t.replace(/</g, '&lt;');
            recentWrap.appendChild(a);
        });
        if (recents.length) {
            var lbl = document.createElement('span');
            lbl.className = 'sr-recent-chip';
            lbl.style.cursor = 'default';
            lbl.innerHTML = '<i class="fas fa-clock"></i><?= $EN ? 'Recent' : 'Terakhir' ?>:';
            recentWrap.insertBefore(lbl, recentWrap.firstChild);
        }
    }
})();
</script>