<?php
$EN = (get_site_lang() == 'en');

// 🔥 Stats untuk author
$total_views = array_sum(array_map(function($p){ return (int)($p->views ?? 0); }, $posts));
$total_posts = count($posts);
$avg_read_time = 0;
if ($total_posts > 0) {
    $total_words = 0;
    foreach ($posts as $p) {
        $total_words += str_word_count(strip_tags($p->content ?? ''));
    }
    $avg_read_time = max(1, ceil(($total_words / $total_posts) / 200));
}

// 🔥 Most popular post
$most_popular = null;
$max_views = 0;
foreach ($posts as $p) {
    if (($p->views ?? 0) > $max_views) {
        $max_views = $p->views;
        $most_popular = $p;
    }
}
?>

<style>
.lb-post-card { position: relative; overflow: hidden; background: #fff; border: 1px solid #e5e7eb; transition: all .4s cubic-bezier(.22,1,.36,1); }
.lb-post-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,34,57,.12); border-color: rgba(201,162,39,.4); }
.lb-post-card .lb-img { transition: transform .7s; }
.lb-post-card:hover .lb-img { transform: scale(1.05); }
.lb-post-card.hidden-search { display: none !important; }

/* ===== POPULAR BADGE ===== */
.popular-badge {
    position: absolute; top: 12px; left: 12px; z-index: 10;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; background: #C9A227; color: #0B2239;
    font-size: 9px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.15em;
}

/* ===== STATS ===== */
.author-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 1.5rem; }
@media (max-width: 640px) { .author-stats { grid-template-columns: repeat(2, 1fr); } }
.author-stat { border-left: 2px solid #C9A227; padding-left: 1rem; }
.author-stat-num { font-family: 'Fraunces', serif; font-size: 2rem; font-weight: 300; color: #F7F5F0; line-height: 1; }
.author-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .15em; color: rgba(247,245,240,.6); margin-top: 4px; }
</style>

<!-- ============ AUTHOR HEADER ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="container mx-auto px-6 py-16 md:py-20 relative z-10">
        <a href="<?= base_url('lecturerblog') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-ivory/60 hover:text-gold transition mb-8">
            <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            <?= $EN ? 'All Faculty Blogs' : 'Semua Blog Dosen' ?>
        </a>
        <div class="flex flex-col md:flex-row md:items-center gap-8">
            <div class="w-28 h-28 rounded-full overflow-hidden border-2 border-gold flex-shrink-0">
                <?php if ($lecturer->photo): ?>
                    <img src="<?= base_url('assets/uploads/' . $lecturer->photo) ?>" class="w-full h-full object-cover" alt="">
                <?php else: ?>
                    <div class="w-full h-full bg-navy-light flex items-center justify-center font-serif text-4xl text-gold"><?= strtoupper(substr($lecturer->name, 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <p class="editorial-label text-gold mb-3">Faculty Blog</p>
                <h1 class="font-serif text-4xl md:text-5xl font-light tracking-tight leading-tight">
                    <?= html_escape(trim(($lecturer->title_front ?? '') . ' ' . $lecturer->name)) ?><?= $lecturer->title_back ? ', ' . html_escape($lecturer->title_back) : '' ?>
                </h1>
                <p class="text-ivory/70 mt-3 max-w-2xl"><?= html_escape($lecturer->expertise ?? '') ?></p>
                <div class="flex gap-3 mt-5 flex-wrap">
                    <?php if ($lecturer->email): ?>
                    <a href="mailto:<?= html_escape($lecturer->email) ?>" class="text-xs uppercase tracking-editorial border border-ivory/30 px-4 py-2 hover:bg-gold hover:text-navy hover:border-gold transition">
                        <i class="fas fa-envelope mr-1"></i>Email
                    </a>
                    <?php endif; ?>
                    <?php if ($lecturer->google_scholar_url): ?>
                    <a href="<?= html_escape($lecturer->google_scholar_url) ?>" target="_blank" rel="noopener" class="text-xs uppercase tracking-editorial border border-ivory/30 px-4 py-2 hover:bg-gold hover:text-navy hover:border-gold transition">
                        <i class="fas fa-graduation-cap mr-1"></i>Scholar
                    </a>
                    <?php endif; ?>
                    <?php if ($lecturer->sinta_url): ?>
                    <a href="<?= html_escape($lecturer->sinta_url) ?>" target="_blank" rel="noopener" class="text-xs uppercase tracking-editorial border border-ivory/30 px-4 py-2 hover:bg-gold hover:text-navy hover:border-gold transition">
                        <i class="fas fa-chart-line mr-1"></i>SINTA
                    </a>
                    <?php endif; ?>
                    <a href="<?= base_url('dosen/detail/' . $lecturer->id) ?>" class="text-xs uppercase tracking-editorial border border-ivory/30 px-4 py-2 hover:bg-gold hover:text-navy hover:border-gold transition">
                        <i class="fas fa-id-card mr-1"></i><?= $EN ? 'Full Profile' : 'Profil Lengkap' ?>
                    </a>
                </div>

                <!-- 🔥 STATS -->
                <?php if ($total_posts > 0): ?>
                <div class="author-stats">
                    <div class="author-stat">
                        <div class="author-stat-num"><?= $total_posts ?></div>
                        <div class="author-stat-label"><?= $EN ? 'Articles' : 'Artikel' ?></div>
                    </div>
                    <div class="author-stat">
                        <div class="author-stat-num"><?= number_format($total_views) ?></div>
                        <div class="author-stat-label"><?= $EN ? 'Total Views' : 'Total Dibaca' ?></div>
                    </div>
                    <div class="author-stat">
                        <div class="author-stat-num"><?= $avg_read_time ?></div>
                        <div class="author-stat-label"><?= $EN ? 'Avg Min Read' : 'Menit Baca' ?></div>
                    </div>
                    <div class="author-stat">
                        <div class="author-stat-num"><?= $max_views ?></div>
                        <div class="author-stat-label"><?= $EN ? 'Peak Views' : 'Puncak Views' ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ POSTS ============ -->
<section class="py-20 bg-ivory">
    <div class="container mx-auto px-6">

        <!-- 🔥 SEARCH (jika ada posts) -->
        <?php if (!empty($posts)): ?>
        <div class="max-w-xl mb-10">
            <div class="relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gold-muted"></i>
                <input type="text" id="postSearch" placeholder="<?= $EN ? 'Search articles...' : 'Cari artikel...' ?>"
                    class="w-full pl-14 pr-5 py-3.5 bg-white border-2 border-navy/15 text-navy focus:border-gold outline-none transition">
            </div>
            <p class="text-xs text-slate mt-2 italic" id="postCount"><?= count($posts) ?> <?= $EN ? 'articles' : 'artikel' ?></p>
        </div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="text-center py-20 bg-white border border-gray-200">
                <i class="fas fa-pen-nib text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No articles published yet.' : 'Belum ada artikel yang dipublikasikan.' ?></p>
            </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="postGrid">
            <?php foreach ($posts as $p):
                $is_popular = ($most_popular && $most_popular->id === $p->id && $p->views > 100);
            ?>
            <article class="lb-post-card group fade-in relative"
                     data-title="<?= strtolower(html_escape($p->title)) ?>"
                     data-excerpt="<?= strtolower(html_escape(strip_tags($p->excerpt ?? $p->content))) ?>">
                <?php if ($is_popular): ?>
                <div class="popular-badge"><i class="fas fa-fire"></i><?= $EN ? 'Popular' : 'Populer' ?></div>
                <?php endif; ?>
                <a href="<?= base_url('lecturerblog/post/' . $p->slug) ?>" class="block">
                    <div class="overflow-hidden bg-gray-200 aspect-[16/9]">
                        <?php if ($p->featured_image): ?>
                            <img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="lb-img w-full h-full object-cover" alt="" loading="lazy">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-navy-light to-navy flex items-center justify-center">
                                <i class="fas fa-pen-nib text-4xl text-gold/30"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <span class="text-[10px] text-slate uppercase tracking-wider"><?= date('d M Y', strtotime($p->published_at)) ?></span>
                            <span class="w-8 h-px bg-gold"></span>
                            <span class="text-[10px] text-slate"><i class="fas fa-eye mr-1"></i><?= number_format($p->views) ?></span>
                            <span class="text-[10px] text-gold-muted"><i class="fas fa-clock mr-1"></i><?= max(1, ceil(str_word_count(strip_tags($p->content ?? '')) / 200)) ?> min</span>
                        </div>
                        <h3 class="font-serif text-xl font-medium text-navy leading-snug group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($p->title) ?></h3>
                        <p class="text-sm text-slate mt-3 line-clamp-3"><?= html_escape(character_limiter($p->excerpt ?: strip_tags($p->content ?? ''), 120)) ?></p>
                        <span class="inline-block mt-4 text-xs uppercase tracking-editorial font-semibold text-navy group-hover:text-gold transition"><?= $EN ? 'Read Article' : 'Baca Artikel' ?> &rarr;</span>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>

        <div id="postEmpty" class="hidden text-center py-16 bg-white border border-gray-200">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light"><?= $EN ? 'No matching articles.' : 'Artikel tidak ditemukan.' ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function(){
    var search = document.getElementById('postSearch');
    var grid = document.getElementById('postGrid');
    var empty = document.getElementById('postEmpty');
    var count = document.getElementById('postCount');
    if (!search || !grid) return;

    var cards = Array.prototype.slice.call(grid.querySelectorAll('.lb-post-card'));

    search.addEventListener('input', function(){
        var q = this.value.toLowerCase().trim();
        var shown = 0;
        cards.forEach(function(card){
            var title = card.dataset.title || '';
            var excerpt = card.dataset.excerpt || '';
            var hit = !q || title.indexOf(q) > -1 || excerpt.indexOf(q) > -1;
            card.classList.toggle('hidden-search', !hit);
            if (hit) shown++;
        });
        if (empty) empty.classList.toggle('hidden', shown > 0);
        if (count) count.textContent = shown + ' <?= $EN ? "articles" : "artikel" ?>';
    });
})();
</script>