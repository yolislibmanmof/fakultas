<?php $EN = (get_site_lang() == 'en'); ?>

<style>
.lb-card { position: relative; overflow: hidden; background: #fff; border: 1px solid #e5e7eb; transition: all .4s cubic-bezier(.22,1,.36,1); }
.lb-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,34,57,.12); border-color: rgba(201,162,39,.4); }
.lb-card .lb-img { transition: transform .7s; }
.lb-card:hover .lb-img { transform: scale(1.05); }
.lb-card.hidden-search { display: none !important; }

/* ===== STATS BAND ===== */
.lb-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 2rem; }
.lb-stat { border-left: 2px solid #C9A227; padding-left: 1rem; }
.lb-stat-num { font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 300; color: #0B2239; line-height: 1; }
.lb-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .2em; color: #64748b; margin-top: 4px; font-weight: 600; }

/* ===== SEARCH ===== */
.lb-search-wrap { position: relative; max-width: 500px; }
.lb-search-wrap input { width: 100%; padding: 14px 20px 14px 50px; border: 2px solid #e5e7eb; background: #fff; font-size: 14px; transition: border-color .2s; }
.lb-search-wrap input:focus { outline: none; border-color: #C9A227; }
.lb-search-wrap i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #C9A227; }

/* ===== FEATURED AUTHOR ===== */
.featured-author {
    background: linear-gradient(135deg, #0B2239 0%, #13334F 100%);
    color: #F7F5F0; padding: 2.5rem; position: relative; overflow: hidden;
    border: 1px solid rgba(201,162,39,.3);
}
.featured-author::before {
    content: ''; position: absolute; top: -40%; right: -20%; width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(201,162,39,.2), transparent 70%);
    border-radius: 50%;
}
.featured-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
    background: #C9A227; color: #0B2239; font-size: 9px; letter-spacing: .25em;
    text-transform: uppercase; font-weight: 800;
}
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold-muted mb-5">Faculty Voices</p>
            <h1 class="font-serif text-5xl md:text-6xl font-light text-navy tracking-tight leading-[1.05]">
                Blog <em class="italic text-gold-muted">Dosen</em>
            </h1>
            <p class="text-slate mt-5 text-lg leading-relaxed">
                <?= $EN ? 'Thoughts, research insights, and academic reflections written directly by our faculty.' : 'Gagasan, wawasan riset, dan refleksi akademik yang ditulis langsung oleh dosen kami.' ?>
            </p>

            <!-- 🔥 STATS -->
            <?php if (!empty($lecturers)):
                $total_posts = array_sum(array_map(function($l){ return (int)($l->total_posts ?? 0); }, $lecturers));
                $total_authors = count(array_filter($lecturers, function($l){ return ($l->total_posts ?? 0) > 0; }));
            ?>
            <div class="lb-stats">
                <div class="lb-stat">
                    <div class="lb-stat-num"><?= count($lecturers) ?></div>
                    <div class="lb-stat-label"><?= $EN ? 'Authors' : 'Penulis' ?></div>
                </div>
                <div class="lb-stat">
                    <div class="lb-stat-num"><?= $total_posts ?></div>
                    <div class="lb-stat-label"><?= $EN ? 'Articles' : 'Artikel' ?></div>
                </div>
                <div class="lb-stat">
                    <div class="lb-stat-num"><?= $total_authors ?></div>
                    <div class="lb-stat-label"><?= $EN ? 'Active Writers' : 'Penulis Aktif' ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 🔥 SEARCH -->
            <?php if (!empty($lecturers)): ?>
            <div class="mt-8">
                <div class="lb-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="lbSearch" placeholder="<?= $EN ? 'Search lecturer name or expertise...' : 'Cari nama dosen atau keahlian...' ?>">
                </div>
                <p class="text-xs text-slate mt-2 italic" id="lbCount"><?= count($lecturers) ?> <?= $EN ? 'faculty members' : 'dosen' ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============ FEATURED AUTHOR (auto-pick most posts) ============ -->
<?php if (!empty($lecturers)):
    $featured = null;
    $max_posts = 0;
    foreach ($lecturers as $l) {
        if (($l->total_posts ?? 0) > $max_posts) {
            $max_posts = $l->total_posts;
            $featured = $l;
        }
    }
    if ($featured && $max_posts > 0):
?>
<section class="py-12 bg-white border-b border-gray-200">
    <div class="container mx-auto px-6">
        <div class="featured-author flex flex-col md:flex-row md:items-center gap-6">
            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gold flex-shrink-0 relative z-10">
                <?php if ($featured->photo): ?>
                    <img src="<?= base_url('assets/uploads/' . $featured->photo) ?>" class="w-full h-full object-cover" alt="">
                <?php else: ?>
                    <div class="w-full h-full bg-navy-light flex items-center justify-center font-serif text-3xl text-gold"><?= strtoupper(substr($featured->name, 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            <div class="relative z-10 flex-1">
                <span class="featured-badge"><i class="fas fa-feather-alt mr-1"></i><?= $EN ? 'Featured Author' : 'Penulis Unggulan' ?></span>
                <h3 class="font-serif text-2xl md:text-3xl font-light text-ivory mt-3 mb-2">
                    <?= html_escape(trim(($featured->title_front ?? '') . ' ' . $featured->name)) ?>
                </h3>
                <p class="text-ivory/70 text-sm"><?= html_escape(character_limiter($featured->expertise ?? '', 120)) ?></p>
                <p class="text-gold font-bold mt-2"><i class="fas fa-pen-nib mr-1"></i><?= $featured->total_posts ?> <?= $EN ? 'articles published' : 'artikel dipublikasikan' ?></p>
            </div>
            <a href="<?= base_url('lecturerblog/view/' . $featured->id) ?>" class="btn-gold px-8 py-3 text-xs uppercase tracking-editorial font-bold flex-shrink-0 relative z-10">
                <?= $EN ? 'Visit Blog' : 'Kunjungi Blog' ?>
            </a>
        </div>
    </div>
</section>
<?php endif; endif; ?>

<!-- ============ LECTURERS GRID ============ -->
<section class="py-20 bg-ivory">
    <div class="container mx-auto px-6">
        <?php if (empty($lecturers)): ?>
            <div class="text-center py-20 bg-white border border-gray-200">
                <i class="fas fa-pen-nib text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No faculty blogs yet.' : 'Belum ada blog dosen.' ?></p>
            </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="lbGrid">
            <?php foreach ($lecturers as $l): ?>
            <div class="lb-card group fade-in"
                 data-name="<?= strtolower(html_escape($l->name)) ?>"
                 data-expertise="<?= strtolower(html_escape($l->expertise ?? '')) ?>">
                <div class="aspect-[4/3] bg-gray-200 overflow-hidden relative">
                    <?php if ($l->photo): ?>
                        <img src="<?= base_url('assets/uploads/' . $l->photo) ?>" class="lb-img w-full h-full object-cover object-top" alt="" loading="lazy">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center">
                            <span class="font-serif text-6xl font-light text-gold/40"><?= strtoupper(substr($l->name, 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl font-medium text-navy leading-snug group-hover:text-gold-muted transition">
                        <?= html_escape(trim(($l->title_front ?? '') . ' ' . $l->name)) ?><?= $l->title_back ? ', ' . html_escape($l->title_back) : '' ?>
                    </h3>
                    <p class="text-xs text-slate mt-2 line-clamp-2"><?= html_escape($l->expertise ?? '-') ?></p>
                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100">
                        <span class="text-xs uppercase tracking-wider text-slate">
                            <i class="fas fa-pen-nib text-gold-muted mr-1"></i>
                            <?= (int)($l->total_posts ?? 0) ?> <?= $EN ? 'Articles' : 'Artikel' ?>
                        </span>
                        <a href="<?= base_url('lecturerblog/view/' . $l->id) ?>" class="text-xs uppercase tracking-editorial font-semibold text-navy hover:text-gold transition">
                            <?= $EN ? 'Visit Blog' : 'Kunjungi Blog' ?> &rarr;
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="lbEmpty" class="hidden text-center py-16 bg-white border border-gray-200">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light"><?= $EN ? 'No matching faculty found.' : 'Dosen tidak ditemukan.' ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function(){
    var search = document.getElementById('lbSearch');
    var grid = document.getElementById('lbGrid');
    var empty = document.getElementById('lbEmpty');
    var count = document.getElementById('lbCount');
    if (!search || !grid) return;

    var cards = Array.prototype.slice.call(grid.querySelectorAll('.lb-card'));

    search.addEventListener('input', function(){
        var q = this.value.toLowerCase().trim();
        var shown = 0;
        cards.forEach(function(card){
            var name = card.dataset.name || '';
            var exp = card.dataset.expertise || '';
            var hit = !q || name.indexOf(q) > -1 || exp.indexOf(q) > -1;
            card.classList.toggle('hidden-search', !hit);
            if (hit) shown++;
        });
        if (empty) empty.classList.toggle('hidden', shown > 0);
        if (count) count.textContent = shown + ' <?= $EN ? "faculty members" : "dosen" ?>';
    });
})();
</script>