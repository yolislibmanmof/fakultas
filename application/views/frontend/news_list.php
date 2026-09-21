<?php
$EN = (get_site_lang() == 'en');
if (!function_exists('read_min')) {
    function read_min($t) { return max(1, (int)round(str_word_count(strip_tags((string)$t)) / 200)); }
}
$first     = !empty($posts) ? $posts[0] : NULL;
$secondary = array_slice($posts, 1, 2);
$tertiary  = array_slice($posts, 3, 3);
$rail      = array_slice($posts, 6);
$total_views = 0;
foreach ($posts as $pv) $total_views += (int)($pv->views ?? 0);

$trending = array_slice($posts, 0);
usort($trending, function($a, $b){ return ($b->views ?? 0) - ($a->views ?? 0); });
$trending = array_slice($trending, 0, 3);
$max_trend = (!empty($trending) && $trending[0]->views) ? $trending[0]->views : 1;

// Archive by month
$by_month = [];
foreach ($posts as $p) { $k = date('Y-m', strtotime($p->published_at)); $by_month[$k][] = $p; }
krsort($by_month);

// Category leaderboard
$cat_board = [];
foreach ($posts as $p) { $cn = $p->category_name ?? 'Umum'; $cat_board[$cn] = ($cat_board[$cn] ?? 0) + 1; }
arsort($cat_board);
$cat_board = array_slice($cat_board, 0, 5, true);
$max_cat = max($cat_board) ?: 1;
?>

<style>
.scrollbar-hide::-webkit-scrollbar{display:none}.scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}
.marquee{overflow:hidden}.marquee-content{display:inline-block;white-space:nowrap;animation:marquee 55s linear infinite}
.marquee:hover .marquee-content{animation-play-state:paused}
@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.kenburns .img-fit-bg,.kenburns>div{animation:kb 22s ease-in-out infinite alternate}
@keyframes kb{from{transform:scale(1.12) translate(0,0)}to{transform:scale(1.22) translate(2%,-2%)}}
.rv{opacity:0;transform:translateY(28px);transition:opacity .9s cubic-bezier(.22,1,.36,1),transform .9s cubic-bezier(.22,1,.36,1);transition-delay:var(--d,0s)}
.rv-in{opacity:1;transform:none}
#rail.dragging{cursor:grabbing;scroll-snap-type:none}

.img-fit-wrap { position: relative; background: #061420; overflow: hidden; }
.img-fit-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: blur(22px) brightness(.5) saturate(1.15); transform: scale(1.12); }
.img-fit-fg { position: relative; z-index: 1; width: 100%; height: 100%; object-fit: contain; }
.hero-fit-fg { filter: drop-shadow(0 24px 48px rgba(0,0,0,.5)); }

/* Constellation */
#nzConstellation { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }

/* SEARCH */
.news-search-wrap { position: relative; max-width: 600px; margin: 0 auto; }
.news-search-wrap input { width: 100%; padding: 14px 20px 14px 50px; border: 2px solid #e5e7eb; background: #fff; font-size: 14px; transition: border-color .2s; }
.news-search-wrap input:focus { outline: none; border-color: #C9A227; }
.news-search-wrap i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #C9A227; }
.cat-count { display: inline-block; padding: 1px 6px; background: rgba(201,162,39,.15); color: #C9A227; font-family: 'JetBrains Mono', monospace; font-size: 9px; font-weight: 700; margin-left: 4px; border-radius: 2px; }

/* 3D TILT + GLARE */
.tilt-card { transform-style: preserve-3d; will-change: transform; }
.tilt-card .glare { position: absolute; inset: 0; z-index: 5; pointer-events: none; opacity: 0; transition: opacity .3s; background: radial-gradient(circle at var(--gx,50%) var(--gy,50%), rgba(255,255,255,.25), transparent 60%); }
.tilt-card:hover .glare { opacity: 1; }

/* BOOKMARK */
.bm-btn { position: absolute; top: 10px; right: 10px; z-index: 8; width: 34px; height: 34px; border-radius: 50%; background: rgba(11,34,57,.75); color: #C9A227; border: 1px solid rgba(201,162,39,.5); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .25s; }
.bm-btn:hover { background: #C9A227; color: #0B2239; transform: scale(1.1); }
.bm-btn.saved { background: #C9A227; color: #0B2239; }
.bm-btn.saved i { font-weight: 900; }

/* LIST VIEW */
.list-row { display: flex; gap: 1rem; align-items: center; background: #fff; border: 1px solid #e5e7eb; padding: 1rem; transition: all .3s; position: relative; }
.list-row:hover { border-color: #C9A227; transform: translateX(4px); box-shadow: 0 8px 24px rgba(11,34,57,.1); }
.list-date { width: 64px; flex-shrink: 0; text-align: center; background: #0B2239; color: #F7F5F0; padding: .5rem 0; }
.list-date .d { font-family: 'Fraunces', serif; font-size: 1.5rem; line-height: 1; }
.list-date .m { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #C9A227; }

/* ARCHIVE TIMELINE */
.nz-month-head { display: flex; align-items: center; gap: 1rem; cursor: pointer; padding: .8rem 0; user-select: none; }
.nz-month-head .chev { transition: transform .3s; color: #C9A227; }
.nz-month.open .chev { transform: rotate(90deg); }
.nz-month-body { max-height: 0; overflow: hidden; transition: max-height .5s cubic-bezier(.22,1,.36,1); }
.nz-month.open .nz-month-body { max-height: 1200px; }

/* LEADERBOARD */
.nz-bar { height: 8px; background: #EDE8DE; overflow: hidden; }
.nz-bar-fill { height: 100%; width: 0; background: linear-gradient(90deg, #B8941F, #D4AF37); transition: width 1.2s cubic-bezier(.22,1,.36,1); }

/* TRENDING BARS */
.trend-bar { height: 4px; background: #EDE8DE; margin-top: 8px; overflow: hidden; }
.trend-bar-fill { height: 100%; width: 0; background: linear-gradient(90deg, #f97316, #C9A227); transition: width 1.2s cubic-bezier(.22,1,.36,1); }

.trending-card { display: flex; gap: 16px; padding: 16px; background: #fff; border: 1px solid #e5e7eb; transition: all .3s; text-decoration: none; }
.trending-card:hover { transform: translateY(-2px); border-color: rgba(201,162,39,.4); box-shadow: 0 8px 16px rgba(11,34,57,.08); }
.trending-rank { font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 300; color: #C9A227; line-height: 1; flex-shrink: 0; width: 40px; }

.news-card.hidden-search { display: none !important; }

/* NEWSLETTER */
.nz-newsletter { background: linear-gradient(135deg, #0B2239, #13334F); }

@media print {
    .marquee, #rail, .news-search-wrap, .bm-btn, #nzConstellation, .nz-newsletter, .nz-toolbar-extra { display: none !important; }
    .rv { opacity: 1 !important; transform: none !important; }
}
</style>

<!-- ============ MASTHEAD ============ -->
<div class="bg-navy text-ivory">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between py-3 text-[10px] uppercase tracking-editorial text-ivory/60">
            <span><?= date('l, d F Y') ?></span>
            <span class="hidden md:block font-serif text-lg normal-case tracking-normal text-gold italic">— The Faculty Gazette —</span>
            <span><?= $EN ? 'Edition' : 'Edisi' ?> №<?= date('Y') ?></span>
        </div>
    </div>
</div>

<!-- ============ FRONT PAGE ============ -->
<?php if ($first): ?>
<section class="relative bg-navy text-ivory overflow-hidden">
    <canvas id="nzConstellation"></canvas>
    <a href="<?= base_url('berita/detail/' . $first->slug) ?>" class="block group relative z-10">
        <div class="relative min-h-[78vh] flex items-end overflow-hidden">
            <?php if ($first->featured_image): ?>
                <div class="absolute inset-0 kenburns"><img src="<?= base_url('assets/uploads/' . $first->featured_image) ?>" class="img-fit-bg" alt="" loading="lazy"></div>
                <div class="absolute inset-0 z-[1] flex items-center justify-center"><img src="<?= base_url('assets/uploads/' . $first->featured_image) ?>" class="img-fit-fg hero-fit-fg" alt="<?= html_escape($first->title) ?>"></div>
            <?php else: ?>
                <div class="absolute inset-0 hero-pattern bg-gradient-to-br from-navy via-navy-light to-navy-deep"></div>
            <?php endif; ?>
            <div class="absolute inset-0 z-[2] bg-gradient-to-t from-navy via-navy/40 to-navy/10"></div>
            <div class="absolute inset-0 z-[2] bg-gradient-to-r from-navy/60 via-transparent to-transparent"></div>
            <div class="absolute top-6 left-6 w-16 h-16 border-t-2 border-l-2 border-gold/60 z-[3]"></div>
            <div class="absolute bottom-6 right-6 w-16 h-16 border-b-2 border-r-2 border-gold/60 z-[3]"></div>
            <div class="container mx-auto px-6 pb-16 md:pb-24 relative z-10">
                <div class="max-w-4xl">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="bg-gold text-navy text-[10px] uppercase tracking-editorial font-bold px-4 py-2" data-scramble><?= $EN ? 'Front Page' : 'Halaman Depan' ?></span>
                        <span class="editorial-label text-gold"><?= html_escape($first->category_name ?? 'Berita') ?></span>
                    </div>
                    <h1 class="font-serif font-light text-4xl md:text-6xl lg:text-7xl leading-[1.05] tracking-[-0.02em] text-balance mb-8 group-hover:text-gold-soft transition-colors duration-500"><?= html_escape($first->title) ?></h1>
                    <p class="text-ivory/80 text-lg md:text-xl leading-relaxed max-w-2xl mb-10"><?= html_escape(character_limiter($first->excerpt ?: strip_tags($first->content ?? ''), 200)) ?></p>
                    <div class="flex flex-wrap items-center gap-6 text-xs uppercase tracking-editorial">
                        <span class="flex items-center gap-2"><i class="fas fa-calendar-alt text-gold"></i><?= date('d F Y', strtotime($first->published_at)) ?></span>
                        <span class="flex items-center gap-2"><i class="fas fa-clock text-gold"></i><?= read_min($first->content) ?> <?= $EN ? 'min read' : 'mnt baca' ?></span>
                        <span class="flex items-center gap-2"><i class="fas fa-eye text-gold"></i><?= number_format($first->views ?? 0) ?></span>
                        <span class="link-arrow text-gold font-bold"><?= $EN ? 'Read the story' : 'Baca ceritanya' ?>
                            <svg width="24" height="12" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</section>
<?php endif; ?>

<!-- ============ TICKER ============ -->
<?php if (!empty($posts)): ?>
<div class="bg-gold text-navy overflow-hidden relative z-20">
    <div class="flex items-stretch">
        <div class="bg-navy text-gold px-5 py-3 text-[10px] uppercase tracking-editorial font-bold flex items-center gap-2 flex-shrink-0 z-10">
            <span class="w-2 h-2 rounded-full bg-gold animate-ping"></span><?= $EN ? 'Breaking' : 'Terbaru' ?>
        </div>
        <div class="marquee py-3 flex-1">
            <div class="marquee-content text-[11px] uppercase tracking-editorial font-semibold">
                <?php foreach ($posts as $t): ?><span class="mx-5"><?= html_escape($t->title) ?> <span class="mx-3">✦</span></span><?php endforeach; ?>
                <?php foreach (array_slice($posts, 0, 4) as $t): ?><span class="mx-5"><?= html_escape($t->title) ?> <span class="mx-3">✦</span></span><?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ============ STICKY FILTER + SEARCH + SORT + VIEW ============ -->
<section class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-[73px] z-30">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide mb-3">
            <span class="editorial-label text-slate whitespace-nowrap mr-1"><i class="fas fa-filter text-gold-muted mr-1"></i><?= $EN ? 'Section' : 'Rubrik' ?>:</span>
            <a href="<?= base_url('berita') ?>" class="whitespace-nowrap text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$cat ? 'bg-navy text-ivory shadow-lg' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $EN ? 'All' : 'Semua' ?><span class="cat-count"><?= count($posts) ?></span></a>
            <?php foreach ($categories as $c):
                $cslug = $c->slug ?? url_title(strtolower($c->name), '-', TRUE);
                $cat_count = count(array_filter($posts, function($p) use ($c) { return isset($p->category_id) && $p->category_id == $c->id; }));
            ?>
            <a href="<?= base_url('berita?cat=' . $cslug) ?>" class="whitespace-nowrap text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $cat == $cslug ? 'bg-gold text-navy shadow-lg' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= html_escape($c->name) ?><?php if ($cat_count > 0): ?><span class="cat-count"><?= $cat_count ?></span><?php endif; ?></a>
            <?php endforeach; ?>
            <!-- Saved chip -->
            <button type="button" id="savedChip" class="whitespace-nowrap text-xs uppercase tracking-editorial font-semibold px-4 py-2 border border-navy/20 text-navy hover:bg-navy hover:text-ivory transition"><i class="fas fa-bookmark mr-1"></i><?= $EN ? 'Saved' : 'Disimpan' ?><span class="cat-count" id="savedCount">0</span></button>
        </div>

        <!-- SEARCH always-on + SORT + VIEW -->
        <div class="grid md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-6 news-search-wrap !mx-0">
                <i class="fas fa-search"></i>
                <input type="text" id="newsSearch" placeholder="<?= $EN ? 'Search articles... ( press / )' : 'Cari artikel... ( tekan / )' ?>">
            </div>
            <div class="md:col-span-3 nz-toolbar-extra">
                <select id="newsSort" class="w-full px-3 py-2 border-2 border-e5e7eb border-gray-200 bg-white text-navy text-xs uppercase tracking-wider font-semibold">
                    <option value="newest"><?= $EN ? 'Sort: Newest' : 'Urut: Terbaru' ?></option>
                    <option value="views"><?= $EN ? 'Sort: Most Read' : 'Urut: Terbanyak Dibaca' ?></option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2 justify-end nz-toolbar-extra">
                <button type="button" class="nz-view-btn active px-3 py-2 border border-navy/20 bg-navy text-ivory" data-view="grid" title="Bento"><i class="fas fa-th"></i></button>
                <button type="button" class="nz-view-btn px-3 py-2 border border-navy/20 bg-white text-navy" data-view="list" title="List"><i class="fas fa-list"></i></button>
            </div>
        </div>
        <p class="text-xs text-slate text-center mt-2 italic" id="newsCount"><?= count($posts) ?> <?= $EN ? 'articles' : 'artikel' ?></p>
    </div>
</section>

<!-- ============ BENTO (GRID VIEW) ============ -->
<section class="py-20 md:py-28 bg-ivory" id="gridView">
    <div class="container mx-auto px-6">
        <?php if (empty($posts)): ?>
            <div class="text-center py-24 bg-white border border-gray-200 rv"><i class="fas fa-newspaper text-6xl text-gray-300 mb-6"></i><p class="font-serif text-3xl text-navy font-light"><?= $EN ? 'The press is silent today.' : 'Belum ada berita.' ?></p></div>
        <?php else: ?>
        <div id="bentoWrap">
            <div class="flex items-end justify-between mb-12 rv">
                <div><p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'In this edition' : 'Dalam edisi ini' ?></p><h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight"><?= $EN ? 'The <em class="italic text-gold-muted">Stories</em>' : 'Kabar <em class="italic text-gold-muted">Pilihan</em>' ?></h2></div>
                <span class="hidden md:block font-serif text-7xl font-light text-navy/10 leading-none"><?= count($posts) ?></span>
            </div>
            <div class="grid md:grid-cols-12 gap-6 mb-6">
                <?php if (isset($secondary[0])): $p = $secondary[0]; ?>
                <article class="md:col-span-7 rv news-card tilt-card group bg-white border border-gray-200 overflow-hidden relative hover-lift" style="--d:.05s" data-title="<?= strtolower(html_escape($p->title)) ?>" data-views="<?= (int)($p->views ?? 0) ?>" data-ts="<?= strtotime($p->published_at) ?>" data-slug="<?= html_escape($p->slug) ?>">
                    <div class="glare"></div>
                    <button type="button" class="bm-btn" data-slug="<?= html_escape($p->slug) ?>" title="<?= $EN ? 'Save' : 'Simpan' ?>"><i class="far fa-bookmark"></i></button>
                    <a href="<?= base_url('berita/detail/' . $p->slug) ?>" class="block h-full">
                        <div class="relative overflow-hidden aspect-[16/9] img-fit-wrap">
                            <?php if ($p->featured_image): ?><img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-bg" alt="" loading="lazy"><img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-fg news-image" alt="<?= html_escape($p->title) ?>"><?php else: ?><div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center"><i class="fas fa-newspaper text-6xl text-gold/30"></i></div><?php endif; ?>
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-10"></div>
                            <div class="absolute bottom-4 left-4 z-10 bg-navy/90 backdrop-blur text-ivory text-[10px] uppercase tracking-editorial px-3 py-1.5"><i class="fas fa-clock text-gold mr-1"></i><?= read_min($p->content) ?> <?= $EN ? 'min' : 'mnt' ?></div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-4"><span class="editorial-label text-gold-muted"><?= html_escape($p->category_name ?? 'Umum') ?></span><span class="w-10 h-px bg-gold"></span><span class="text-xs text-slate uppercase tracking-wider"><?= date('d M Y', strtotime($p->published_at)) ?></span></div>
                            <h2 class="font-serif text-2xl md:text-3xl font-medium text-navy leading-tight mb-3 group-hover:text-gold-muted transition"><?= html_escape($p->title) ?></h2>
                            <p class="text-slate leading-relaxed line-clamp-2"><?= html_escape(character_limiter($p->excerpt ?: strip_tags($p->content ?? ''), 160)) ?></p>
                        </div>
                    </a>
                </article>
                <?php endif; ?>
                <?php if (isset($secondary[1])): $p = $secondary[1]; ?>
                <article class="md:col-span-5 rv news-card tilt-card bg-navy text-ivory p-8 md:p-10 relative overflow-hidden group hover-lift" style="--d:.15s" data-title="<?= strtolower(html_escape($p->title)) ?>" data-views="<?= (int)($p->views ?? 0) ?>" data-ts="<?= strtotime($p->published_at) ?>" data-slug="<?= html_escape($p->slug) ?>">
                    <div class="glare"></div>
                    <button type="button" class="bm-btn" data-slug="<?= html_escape($p->slug) ?>" title="<?= $EN ? 'Save' : 'Simpan' ?>"><i class="far fa-bookmark"></i></button>
                    <div class="absolute -top-6 -right-4 font-serif text-[10rem] leading-none text-gold/10 select-none pointer-events-none group-hover:text-gold/20 transition">02</div>
                    <a href="<?= base_url('berita/detail/' . $p->slug) ?>" class="block h-full relative">
                        <p class="editorial-label text-gold mb-6"><?= html_escape($p->category_name ?? 'Umum') ?></p>
                        <h2 class="font-serif text-2xl md:text-3xl font-light leading-tight mb-6 group-hover:text-gold-soft transition text-balance">"<?= html_escape($p->title) ?>"</h2>
                        <p class="text-ivory/70 leading-relaxed line-clamp-4 mb-8"><?= html_escape(character_limiter($p->excerpt ?: strip_tags($p->content ?? ''), 180)) ?></p>
                        <div class="flex items-center justify-between mt-auto pt-6 border-t border-ivory/10"><span class="text-xs uppercase tracking-editorial text-ivory/60"><?= date('d M Y', strtotime($p->published_at)) ?></span><span class="link-arrow text-gold font-bold text-xs uppercase tracking-editorial"><?= $EN ? 'Read' : 'Baca' ?><svg width="18" height="9" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg></span></div>
                    </a>
                </article>
                <?php endif; ?>
            </div>
            <?php if (count($tertiary) > 0): ?>
            <div class="grid md:grid-cols-3 gap-6 mb-16">
                <?php foreach ($tertiary as $i => $p): ?>
                <article class="rv news-card tilt-card group bg-white border border-gray-200 overflow-hidden hover-lift relative" style="--d:.<?= $i + 1 ?>5s" data-title="<?= strtolower(html_escape($p->title)) ?>" data-views="<?= (int)($p->views ?? 0) ?>" data-ts="<?= strtotime($p->published_at) ?>" data-slug="<?= html_escape($p->slug) ?>">
                    <div class="glare"></div>
                    <button type="button" class="bm-btn" data-slug="<?= html_escape($p->slug) ?>" title="<?= $EN ? 'Save' : 'Simpan' ?>"><i class="far fa-bookmark"></i></button>
                    <a href="<?= base_url('berita/detail/' . $p->slug) ?>" class="block">
                        <div class="relative overflow-hidden aspect-[4/3] img-fit-wrap">
                            <?php if ($p->featured_image): ?><img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-bg" alt="" loading="lazy"><img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-fg news-image" alt="<?= html_escape($p->title) ?>"><?php else: ?><div class="w-full h-full bg-gradient-to-br from-navy-light to-navy flex items-center justify-center"><i class="fas fa-newspaper text-4xl text-gold/30"></i></div><?php endif; ?>
                            <div class="absolute top-4 left-4 z-10 bg-gold text-navy text-center px-3 py-1.5"><div class="font-serif text-lg leading-none"><?= date('d', strtotime($p->published_at)) ?></div><div class="text-[8px] uppercase tracking-editorial font-bold"><?= date('M', strtotime($p->published_at)) ?></div></div>
                        </div>
                        <div class="p-6"><span class="editorial-label text-gold-muted"><?= html_escape($p->category_name ?? 'Umum') ?></span><h3 class="font-serif text-lg font-medium text-navy leading-snug mt-3 group-hover:text-gold-muted transition"><?= html_escape($p->title) ?></h3></div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if (count($rail) > 0): ?>
            <div class="rv mb-4 flex items-end justify-between"><div><p class="editorial-label text-gold-muted mb-2"><?= $EN ? 'Archive rail' : 'Rel arsip' ?></p><h3 class="font-serif text-2xl md:text-3xl font-light text-navy"><?= $EN ? 'Keep <em class="italic text-gold-muted">exploring</em>' : 'Terus <em class="italic text-gold-muted">jelajahi</em>' ?></h3></div><span class="hidden md:flex items-center gap-2 text-xs text-slate"><i class="fas fa-hand-pointer text-gold-muted"></i><?= $EN ? 'drag to scroll' : 'geser untuk gulir' ?></span></div>
            <div id="rail" class="rv flex gap-5 overflow-x-auto pb-6 snap-x snap-mandatory cursor-grab select-none">
                <?php foreach ($rail as $p): ?>
                <a href="<?= base_url('berita/detail/' . $p->slug) ?>" class="news-card snap-start flex-shrink-0 w-72 bg-white border border-gray-200 overflow-hidden group hover-lift relative" data-title="<?= strtolower(html_escape($p->title)) ?>" data-views="<?= (int)($p->views ?? 0) ?>" data-ts="<?= strtotime($p->published_at) ?>" data-slug="<?= html_escape($p->slug) ?>">
                    <div class="aspect-video img-fit-wrap overflow-hidden"><?php if ($p->featured_image): ?><img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-bg" alt="" loading="lazy"><img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-fg news-image" alt="<?= html_escape($p->title) ?>"><?php else: ?><div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center"><i class="fas fa-newspaper text-3xl text-gold/30"></i></div><?php endif; ?></div>
                    <div class="p-5"><span class="text-[10px] uppercase tracking-editorial text-gold-muted font-semibold"><?= html_escape($p->category_name ?? 'Umum') ?></span><h4 class="font-serif text-base font-medium text-navy leading-snug mt-2 line-clamp-2 group-hover:text-gold-muted transition"><?= html_escape($p->title) ?></h4></div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- LIST VIEW (hidden default) -->
        <div id="newsList" class="hidden space-y-3"></div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ ARCHIVE + LEADERBOARD ============ -->
<?php if (!empty($posts)): ?>
<section class="py-20 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-14">
            <!-- Archive -->
            <div class="rv">
                <p class="editorial-label text-gold-muted mb-3"><i class="fas fa-archive mr-1"></i><?= $EN ? 'Archive' : 'Arsip' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy mb-8"><?= $EN ? 'Browse by <em class="italic text-gold-muted">month</em>' : 'Telusuri per <em class="italic text-gold-muted">bulan</em>' ?></h2>
                <div class="space-y-2">
                    <?php foreach ($by_month as $mk => $mp):
                        $ts = strtotime($mk . '-01');
                    ?>
                    <div class="nz-month bg-ivory border border-gray-200">
                        <div class="nz-month-head px-5">
                            <i class="fas fa-chevron-right chev"></i>
                            <span class="font-serif text-lg text-navy"><?= date('F Y', $ts) ?></span>
                            <span class="cat-count ml-auto"><?= count($mp) ?></span>
                        </div>
                        <div class="nz-month-body px-5 pb-4 space-y-2">
                            <?php foreach ($mp as $p): ?>
                            <a href="<?= base_url('berita/detail/' . $p->slug) ?>" class="block text-sm text-slate hover:text-gold-muted transition py-1 border-b border-gray-100 last:border-0"><i class="fas fa-newspaper text-[10px] text-gold-muted mr-2"></i><?= html_escape($p->title) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Leaderboard -->
            <div class="rv" style="--d:.1s">
                <p class="editorial-label text-gold-muted mb-3"><i class="fas fa-chart-bar mr-1"></i><?= $EN ? 'Sections' : 'Rubrik' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy mb-8"><?= $EN ? 'Most active <em class="italic text-gold-muted">sections</em>' : 'Rubrik paling <em class="italic text-gold-muted">aktif</em>' ?></h2>
                <div class="space-y-5">
                    <?php foreach ($cat_board as $cn => $cc): ?>
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span class="font-medium text-navy"><?= html_escape($cn) ?></span><span class="font-mono text-xs text-gold-muted"><?= $cc ?></span></div>
                        <div class="nz-bar"><div class="nz-bar-fill" data-w="<?= round($cc / $max_cat * 100) ?>%"></div></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ TRENDING ============ -->
<?php if (!empty($trending) && count($trending) >= 3): ?>
<section class="py-16 bg-ivory border-t border-gray-200">
    <div class="container mx-auto px-6"><div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-8 rv"><i class="fas fa-fire text-orange-500 text-2xl"></i><div><p class="editorial-label text-gold-muted mb-1"><?= $EN ? 'Most Read' : 'Paling Dibaca' ?></p><h2 class="font-serif text-3xl md:text-4xl font-light text-navy"><?= $EN ? 'Trending <em class="italic text-gold-muted">now</em>' : 'Sedang <em class="italic text-gold-muted">trending</em>' ?></h2></div></div>
        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach ($trending as $idx => $t): ?>
            <a href="<?= base_url('berita/detail/' . $t->slug) ?>" class="trending-card rv">
                <span class="trending-rank"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></span>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-slate uppercase tracking-wider mb-1"><?= html_escape($t->category_name ?? 'News') ?></p>
                    <h3 class="font-serif text-sm font-medium text-navy leading-snug line-clamp-2"><?= html_escape($t->title) ?></h3>
                    <div class="trend-bar"><div class="trend-bar-fill" data-w="<?= round((($t->views ?? 0) / $max_trend) * 100) ?>%"></div></div>
                    <p class="text-[10px] text-gold-muted font-semibold mt-2"><i class="fas fa-eye mr-1"></i><?= number_format($t->views ?? 0) ?> views</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div></div>
</section>
<?php endif; ?>

<!-- ============ QUOTE ============ -->
<section class="py-24 bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern opacity-30"></div>
    <div class="absolute top-8 left-8 font-serif text-[10rem] leading-none text-gold/10 select-none">"</div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <p class="editorial-label text-gold mb-6"><?= $EN ? 'What we stand for' : 'Nilai yang kami junjung' ?></p>
        <h2 class="font-serif text-4xl md:text-6xl font-light tracking-tight"><?= $EN ? 'Where' : 'Tempat' ?> <em id="wordRot" class="italic text-gold transition-opacity duration-300">Inspirasi</em><br><?= $EN ? 'becomes impact.' : 'menjadi dampak.' ?></h2>
    </div>
</section>

<!-- ============ NEWSLETTER ============ -->
<section class="nz-newsletter py-16 text-ivory">
    <div class="container mx-auto px-6"><div class="max-w-3xl mx-auto text-center rv">
        <i class="fas fa-envelope-open-text text-4xl text-gold mb-4"></i>
        <h2 class="font-serif text-3xl md:text-4xl font-light mb-3"><?= $EN ? 'Never miss a story' : 'Jangan lewatkan kabar' ?></h2>
        <p class="text-ivory/70 mb-8"><?= $EN ? 'Get the latest faculty news delivered to your inbox.' : 'Dapatkan kabar terbaru fakultas langsung ke inbox Anda.' ?></p>
        <form id="nzNewsForm" class="flex flex-col sm:flex-row gap-3 max-w-xl mx-auto">
            <input type="email" required placeholder="<?= $EN ? 'Your email' : 'Email Anda' ?>" class="flex-1 px-5 py-4 bg-transparent border border-ivory/30 text-ivory placeholder:text-ivory/50 focus:border-gold outline-none transition">
            <button type="submit" class="btn-gold px-8 py-4 text-xs uppercase tracking-editorial font-bold"><?= $EN ? 'Subscribe' : 'Berlangganan' ?></button>
        </form>
    </div></div>
</section>

<!-- ============ STATS ============ -->
<section class="bg-ivory border-b border-gray-200">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-3 divide-x divide-gray-200 rv">
            <div class="py-10 px-4 text-center"><div class="font-serif text-5xl md:text-6xl font-light text-navy" data-count="<?= count($posts) ?>">0</div><p class="editorial-label text-slate mt-3"><?= $EN ? 'Articles' : 'Artikel' ?></p></div>
            <div class="py-10 px-4 text-center"><div class="font-serif text-5xl md:text-6xl font-light text-gold-muted" data-count="<?= $total_views ?>">0</div><p class="editorial-label text-slate mt-3"><?= $EN ? 'Total Reads' : 'Total Bacaan' ?></p></div>
            <div class="py-10 px-4 text-center"><div class="font-serif text-5xl md:text-6xl font-light text-navy" data-count="<?= count($categories) ?>">0</div><p class="editorial-label text-slate mt-3"><?= $EN ? 'Sections' : 'Rubrik' ?></p></div>
        </div>
    </div>
</section>

<script>
(function(){
    // ===== CONSTELLATION =====
    var cc = document.getElementById('nzConstellation');
    if (cc) {
        var ctx = cc.getContext('2d'), pts = [], mouse = {x:-999,y:-999};
        function rs(){ cc.width = cc.offsetWidth; cc.height = cc.offsetHeight; }
        rs(); window.addEventListener('resize', rs);
        cc.parentElement.addEventListener('mousemove', function(e){ var r = cc.getBoundingClientRect(); mouse.x = e.clientX-r.left; mouse.y = e.clientY-r.top; });
        cc.parentElement.addEventListener('mouseleave', function(){ mouse.x=-999; mouse.y=-999; });
        for (var i=0;i<60;i++) pts.push({x:Math.random()*cc.width,y:Math.random()*cc.height,vx:(Math.random()-.5)*.3,vy:(Math.random()-.5)*.3,s:Math.random()*1.5+.8});
        (function loop(){
            ctx.clearRect(0,0,cc.width,cc.height);
            pts.forEach(function(p){ p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>cc.width)p.vx*=-1; if(p.y<0||p.y>cc.height)p.vy*=-1;
                var dx=mouse.x-p.x,dy=mouse.y-p.y,d=Math.sqrt(dx*dx+dy*dy); if(d<120&&d>0){p.x+=dx/d*.3;p.y+=dy/d*.3;}
                ctx.beginPath(); ctx.arc(p.x,p.y,p.s,0,Math.PI*2); ctx.fillStyle='rgba(201,162,39,.7)'; ctx.fill(); });
            for (var a=0;a<pts.length;a++) for (var b=a+1;b<pts.length;b++){ var dx=pts[a].x-pts[b].x,dy=pts[a].y-pts[b].y,d=Math.sqrt(dx*dx+dy*dy);
                if(d<100){ ctx.beginPath(); ctx.moveTo(pts[a].x,pts[a].y); ctx.lineTo(pts[b].x,pts[b].y); ctx.strokeStyle='rgba(201,162,39,'+(.3*(1-d/100))+')'; ctx.lineWidth=.5; ctx.stroke(); } }
            requestAnimationFrame(loop);
        })();
    }

    // ===== REVEAL + BARS + COUNT =====
    var io = new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add('rv-in');
        en.target.querySelectorAll('[data-count]').forEach(runCount);
        en.target.querySelectorAll('.nz-bar-fill, .trend-bar-fill').forEach(function(b){ setTimeout(function(){ b.style.width = b.dataset.w; }, 200); });
        io.unobserve(en.target); } }); }, {threshold:.15});
    document.querySelectorAll('.rv').forEach(function(el){ io.observe(el); });
    function runCount(el){ if(el.dataset.done)return; el.dataset.done=1; var t=+el.dataset.count, st=performance.now();
        (function f(n){ var p=Math.min(1,(n-st)/1400), e=1-Math.pow(1-p,3); el.textContent=Math.round(t*e).toLocaleString('id-ID'); if(p<1)requestAnimationFrame(f); })(st); }

    // ===== SCRAMBLE =====
    document.querySelectorAll('[data-scramble]').forEach(function(el){ var fin=el.textContent, chars='#@%&$!?<>*', i=0;
        var iv=setInterval(function(){ el.textContent=fin.split('').map(function(c,idx){ return idx<i?c:chars[Math.floor(Math.random()*chars.length)]; }).join(''); i++; if(i>fin.length){clearInterval(iv); el.textContent=fin;} },45); });

    // ===== WORD ROT =====
    var words=['Inspirasi','Inovasi','Prestasi','Kolaborasi','Riset','Karya']; var wr=document.getElementById('wordRot'), wi=0;
    if(wr) setInterval(function(){ wr.style.opacity=0; setTimeout(function(){ wi=(wi+1)%words.length; wr.textContent=words[wi]; wr.style.opacity=1; },300); },2600);

    // ===== RAIL DRAG =====
    var rail=document.getElementById('rail');
    if(rail){ var down=false,sx=0,sl=0;
        rail.addEventListener('pointerdown',function(e){down=true;sx=e.clientX;sl=rail.scrollLeft;rail.classList.add('dragging');});
        window.addEventListener('pointerup',function(){down=false;rail.classList.remove('dragging');});
        rail.addEventListener('pointermove',function(e){ if(!down)return; e.preventDefault(); rail.scrollLeft=sl-(e.clientX-sx); }); }

    // ===== 3D TILT =====
    document.querySelectorAll('.tilt-card').forEach(function(card){
        card.addEventListener('mousemove',function(e){ var r=card.getBoundingClientRect(); var px=(e.clientX-r.left)/r.width-.5, py=(e.clientY-r.top)/r.height-.5;
            card.style.transform='perspective(900px) rotateY('+(px*6)+'deg) rotateX('+(-py*6)+'deg) translateY(-6px)';
            card.style.setProperty('--gx',((px+.5)*100)+'%'); card.style.setProperty('--gy',((py+.5)*100)+'%'); });
        card.addEventListener('mouseleave',function(){ card.style.transform=''; });
    });

    // ===== BOOKMARKS =====
    var bookmarks = JSON.parse(localStorage.getItem('news_bookmarks') || '[]');
    var savedCount = document.getElementById('savedCount');
    var savedActive = false;
    function refreshSavedUI(){ savedCount.textContent = bookmarks.length;
        document.querySelectorAll('.bm-btn').forEach(function(b){ var on = bookmarks.indexOf(b.dataset.slug) > -1; b.classList.toggle('saved', on); b.querySelector('i').className = on ? 'fas fa-bookmark' : 'far fa-bookmark'; }); }
    refreshSavedUI();
    document.addEventListener('click', function(e){ var b = e.target.closest('.bm-btn'); if(!b) return; e.preventDefault(); e.stopPropagation();
        var s = b.dataset.slug; var i = bookmarks.indexOf(s); if(i>-1) bookmarks.splice(i,1); else bookmarks.push(s);
        localStorage.setItem('news_bookmarks', JSON.stringify(bookmarks)); refreshSavedUI(); applyFilter(); });
    document.getElementById('savedChip').addEventListener('click', function(){ savedActive = !savedActive; this.classList.toggle('bg-gold', savedActive); this.classList.toggle('text-navy', savedActive); applyFilter(); });

    // ===== VIEW TOGGLE + BUILD LIST =====
    var gridView = document.getElementById('gridView');
    var bentoWrap = document.getElementById('bentoWrap');
    var listWrap = document.getElementById('newsList');
    var allCards = Array.prototype.slice.call(document.querySelectorAll('.news-card'));
    // Build list rows from all cards data
    var seen = {};
    allCards.forEach(function(c){ var s=c.dataset.slug; if(seen[s])return; seen[s]=1;
        var a = document.createElement('div'); a.className = 'list-row news-card-item'; a.dataset.slug = s; a.dataset.title = c.dataset.title; a.dataset.views = c.dataset.views; a.dataset.ts = c.dataset.ts;
        var d = new Date(+c.dataset.ts*1000);
        a.innerHTML = '<div class="list-date"><div class="d">'+d.getDate()+'</div><div class="m">'+d.toLocaleString('id',{month:'short'})+'</div></div>'
            + '<a href="<?= base_url('berita/detail') ?>/'+s+'" class="flex-1 min-w-0"><h4 class="font-serif text-base font-medium text-navy leading-snug line-clamp-1">'+ (c.querySelector('h2,h3,h4')?c.querySelector('h2,h3,h4').textContent:'') +'</h4><p class="text-xs text-slate mt-1"><i class="fas fa-eye mr-1"></i>'+ (+c.dataset.views).toLocaleString('id') +' views</p></a>'
            + '<button type="button" class="bm-btn !static" data-slug="'+s+'"><i class="far fa-bookmark"></i></button>';
        listWrap.appendChild(a); });
    document.querySelectorAll('.nz-view-btn').forEach(function(btn){ btn.addEventListener('click', function(){
        document.querySelectorAll('.nz-view-btn').forEach(function(b){ b.classList.remove('bg-navy','text-ivory','active'); b.classList.add('bg-white','text-navy'); });
        btn.classList.add('bg-navy','text-ivory','active'); btn.classList.remove('bg-white','text-navy');
        var isList = btn.dataset.view === 'list';
        bentoWrap.classList.toggle('hidden', isList);
        listWrap.classList.toggle('hidden', !isList);
        applyFilter(); }); });

    // ===== SORT (list view) =====
    document.getElementById('newsSort').addEventListener('change', function(){
        var v = this.value; var rows = Array.prototype.slice.call(listWrap.children);
        rows.sort(function(a,b){ return v==='views' ? (+b.dataset.views)-(+a.dataset.views) : (+b.dataset.ts)-(+a.dataset.ts); });
        rows.forEach(function(r){ listWrap.appendChild(r); }); });

    // ===== SEARCH + SAVED FILTER =====
    var search = document.getElementById('newsSearch');
    var count = document.getElementById('newsCount');
    function applyFilter(){
        var q = (search?search.value:'').toLowerCase().trim();
        var shown = 0;
        allCards.forEach(function(card){ var hit = (!q || (card.dataset.title||'').indexOf(q)>-1) && (!savedActive || bookmarks.indexOf(card.dataset.slug)>-1);
            card.classList.toggle('hidden-search', !hit); if(hit)shown++; });
        Array.prototype.forEach.call(listWrap.children, function(row){ var hit = (!q || (row.dataset.title||'').indexOf(q)>-1) && (!savedActive || bookmarks.indexOf(row.dataset.slug)>-1);
            row.style.display = hit ? '' : 'none'; });
        if (count) count.textContent = shown + ' <?= $EN ? "articles" : "artikel" ?>';
    }
    window.applyFilter = applyFilter;
    if (search) search.addEventListener('input', applyFilter);

    // ===== ⌨️ "/" focus =====
    document.addEventListener('keydown', function(e){ if(e.key==='/' && document.activeElement.tagName!=='INPUT' && document.activeElement.tagName!=='TEXTAREA'){ e.preventDefault(); search && search.focus(); } });

    // ===== ARCHIVE expand =====
    document.querySelectorAll('.nz-month-head').forEach(function(h){ h.addEventListener('click', function(){ h.parentElement.classList.toggle('open'); }); });

    // ===== NEWSLETTER =====
    var nf = document.getElementById('nzNewsForm');
    if (nf) nf.addEventListener('submit', function(e){ e.preventDefault(); showToast('<?= $EN ? "Subscribed! Welcome aboard." : "Berlangganan berhasil!" ?>'); nf.reset(); });

    function showToast(msg){ var t=document.createElement('div'); t.textContent=msg; t.style.cssText='position:fixed;bottom:24px;right:24px;z-index:200;background:#0B2239;color:#F7F5F0;padding:12px 20px;font-size:13px;border-left:3px solid #C9A227;box-shadow:0 8px 24px rgba(0,0,0,.3);'; document.body.appendChild(t); setTimeout(function(){ t.remove(); },2500); }
})();
</script>