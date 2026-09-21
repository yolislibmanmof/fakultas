<?php
$EN = (get_site_lang() == 'en');
// 🔥 Parse content dengan aman (preserve HTML tags dari rich editor)
$raw_content = $post->content ?? '';
$parsed_content = $raw_content; // asumsi dari editor sudah di-sanitize server-side

// 🔥 Estimate reading time (200 kata/menit)
$word_count = str_word_count(strip_tags($parsed_content));
$read_time = max(1, ceil($word_count / 200));

// 🔥 Auto-generate TOC dari h2/h3
$toc_items = [];
if (preg_match_all('/<h([23])[^>]*>(.*?)<\/h[23]>/is', $parsed_content, $matches)) {
    foreach ($matches[0] as $i => $full) {
        $level = (int)$matches[1][$i];
        $text = strip_tags($matches[2][$i]);
        $id = 'section-' . ($i + 1);
        $toc_items[] = ['level' => $level, 'text' => $text, 'id' => $id];
        // Inject id ke heading
        $parsed_content = preg_replace(
            '/<h' . $level . '([^>]*)>' . preg_quote($matches[2][$i], '/') . '/i',
            '<h' . $level . '$1 id="' . $id . '">' . $matches[2][$i],
            $parsed_content,
            1
        );
    }
}
?>

<!-- 🔥 JSON-LD Article Schema (SEO) -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'datePublished' => $post->published_at,
    'author' => [
        '@type' => 'Person',
        'name' => trim(($post->title_front ?? '') . ' ' . $post->lecturer_name),
    ],
    'image' => $post->featured_image ? base_url('assets/uploads/' . $post->featured_image) : '',
    'publisher' => ['@type' => 'Organization', 'name' => site_name()],
]) ?>
</script>

<style>
.lb-reveal { opacity: 0; transform: translateY(24px); transition: all .9s cubic-bezier(.22,1,.36,1); }
.lb-reveal.in { opacity: 1; transform: none; }

/* ===== READING TIME BADGE ===== */
.read-time-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; background: rgba(201,162,39,.15);
    color: #C9A227; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.15em;
}

/* ===== TOC ===== */
.lb-toc {
    position: sticky; top: 110px;
    background: #fff; border: 1px solid #e5e7eb;
    padding: 1.5rem; max-height: 60vh; overflow-y: auto;
}
.lb-toc-title { font-family: 'Fraunces', serif; font-size: 1.1rem; color: #0B2239; margin-bottom: 1rem; padding-bottom: .75rem; border-bottom: 1px solid #e5e7eb; }
.lb-toc ul { list-style: none; padding: 0; margin: 0; }
.lb-toc li { padding: 6px 0; border-left: 2px solid transparent; padding-left: 12px; margin-left: -14px; transition: all .2s; }
.lb-toc li.active { border-left-color: #C9A227; }
.lb-toc li.h3 { padding-left: 24px; }
.lb-toc a { color: #475569; font-size: 13px; text-decoration: none; transition: color .2s; display: block; }
.lb-toc a:hover, .lb-toc li.active a { color: #C9A227; }

/* ===== SHARE BUTTONS (working) ===== */
.lb-share-btn {
    width: 40px; height: 40px; border: 1px solid #e5e7eb;
    display: flex; align-items: center; justify-content: center;
    color: #64748b; background: #fff; cursor: pointer;
    transition: all .3s; text-decoration: none;
}
.lb-share-btn:hover { background: #0B2239; color: #C9A227; border-color: #0B2239; transform: translateY(-2px); }

/* ===== PROGRESS BAR (reading progress) ===== */
.reading-progress {
    position: fixed; top: 73px; left: 0; right: 0; height: 3px;
    background: rgba(201,162,39,.15); z-index: 999;
}
.reading-progress-bar {
    height: 100%; background: linear-gradient(90deg, #C9A227, #D4AF37);
    width: 0%; transition: width .1s ease;
}

/* ===== TOAST ===== */
.lb-toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 1000;
    padding: 12px 20px; background: #0B2239; color: #F7F5F0;
    font-size: 13px; box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(120%); transition: transform .3s;
}
.lb-toast.show { transform: translateX(0); }
.lb-toast i { color: #C9A227; margin-right: 8px; }

/* ===== CONTENT TYPOGRAPHY ===== */
.lb-content h2 { font-family: 'Fraunces', serif; font-size: 1.75rem; font-weight: 500; color: #0B2239; margin: 2.5rem 0 1rem; line-height: 1.2; }
.lb-content h3 { font-family: 'Fraunces', serif; font-size: 1.35rem; font-weight: 500; color: #0B2239; margin: 2rem 0 .75rem; line-height: 1.3; }
.lb-content p { margin-bottom: 1.25rem; }
.lb-content ul, .lb-content ol { margin: 1.25rem 0; padding-left: 1.5rem; }
.lb-content li { margin-bottom: .5rem; }
.lb-content a { color: #C9A227; text-decoration: underline; }
.lb-content blockquote {
    border-left: 3px solid #C9A227; padding: 1rem 1.5rem;
    background: #FAF8F3; margin: 1.5rem 0; font-style: italic;
}
.lb-content img { max-width: 100%; height: auto; margin: 1.5rem 0; border-radius: 2px; }
.lb-content code { background: #F1F5F9; padding: 2px 8px; border-radius: 2px; font-family: 'JetBrains Mono', monospace; font-size: .9em; }
.lb-content pre { background: #0B2239; color: #F7F5F0; padding: 1.5rem; overflow-x: auto; margin: 1.5rem 0; border-radius: 4px; }
.lb-content pre code { background: transparent; color: inherit; padding: 0; }

/* ===== BREADCRUMB ===== */
.lb-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 11px; color: #64748b; margin-bottom: 1.5rem; flex-wrap: wrap; }
.lb-breadcrumb a { color: #64748b; text-decoration: none; transition: color .2s; }
.lb-breadcrumb a:hover { color: #C9A227; }
.lb-breadcrumb .sep { color: #cbd5e1; }

/* ===== RELATED ARTICLE CARD ===== */
.related-card {
    display: block; padding: 1.25rem; background: #fff;
    border: 1px solid #e5e7eb; transition: all .3s;
    text-decoration: none;
}
.related-card:hover { transform: translateY(-4px); border-color: rgba(201,162,39,.4); box-shadow: 0 12px 24px rgba(11,34,57,.1); }

/* ===== PRINT ===== */
@media print {
    .reading-progress, .lb-share-btn, .lb-toc, .lb-toast, .lb-breadcrumb a[href*="javascript"] { display: none !important; }
    .lb-content { max-width: 100% !important; }
}
</style>

<!-- 🔥 READING PROGRESS BAR -->
<div class="reading-progress" id="readingProgress">
    <div class="reading-progress-bar" id="readingProgressBar"></div>
</div>

<!-- ============ ARTICLE HEADER ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <div class="lb-breadcrumb text-ivory/50">
                <a href="<?= base_url() ?>" class="text-ivory/60 hover:text-gold">Home</a>
                <span class="sep">/</span>
                <a href="<?= base_url('lecturerblog') ?>" class="text-ivory/60 hover:text-gold">Blog Dosen</a>
                <span class="sep">/</span>
                <a href="<?= base_url('lecturerblog/view/' . $post->lecturer_id) ?>" class="text-ivory/60 hover:text-gold"><?= html_escape($post->lecturer_name) ?></a>
                <span class="sep">/</span>
                <span class="text-ivory/80 truncate max-w-[200px]"><?= html_escape(character_limiter($post->title, 40)) ?></span>
            </div>

            <a href="<?= base_url('lecturerblog/view/' . $post->lecturer_id) ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-ivory/60 hover:text-gold transition mb-8">
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                <?= $EN ? 'Back to Blog' : 'Kembali ke Blog' ?>
            </a>
            <div class="flex items-center gap-4 mb-6 flex-wrap">
                <span class="text-xs text-ivory/60 uppercase tracking-wider"><?= date('d F Y', strtotime($post->published_at)) ?></span>
                <span class="w-12 h-px bg-gold"></span>
                <span class="text-xs text-ivory/60"><i class="fas fa-eye mr-1"></i><?= number_format($post->views) ?> <?= $EN ? 'views' : 'dibaca' ?></span>
                <span class="read-time-badge"><i class="fas fa-clock"></i><?= $read_time ?> <?= $EN ? 'min read' : 'menit baca' ?></span>
            </div>
            <h1 class="font-serif text-4xl md:text-6xl font-light leading-[1.1] tracking-tight text-balance mb-8">
                <?= html_escape($post->title) ?>
            </h1>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full overflow-hidden border border-gold flex-shrink-0">
                    <?php if ($post->lecturer_photo): ?>
                        <img src="<?= base_url('assets/uploads/' . $post->lecturer_photo) ?>" class="w-full h-full object-cover" alt="">
                    <?php else: ?>
                        <div class="w-full h-full bg-navy-light flex items-center justify-center font-serif text-gold"><?= strtoupper(substr($post->lecturer_name, 0, 1)) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="font-serif font-medium"><?= html_escape(trim(($post->title_front ?? '') . ' ' . $post->lecturer_name)) ?><?= $post->title_back ? ', ' . html_escape($post->title_back) : '' ?></p>
                    <p class="text-xs text-ivory/60"><?= html_escape($post->expertise ?? '') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ARTICLE BODY ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <?php if ($post->featured_image): ?>
                <figure class="mb-10">
                    <img src="<?= base_url('assets/uploads/' . $post->featured_image) ?>" class="w-full aspect-[16/9] object-cover" alt="<?= html_escape($post->title) ?>" loading="lazy">
                </figure>
                <?php endif; ?>

                <div class="bg-white border border-gray-200 p-8 md:p-12">
                    <div class="lb-content text-gray-700 leading-loose text-[17px]">
                        <?= $parsed_content ?>
                    </div>

                    <!-- 🔥 Tags -->
                    <?php if (!empty($post->tags)): ?>
                    <div class="mt-10 pt-8 border-t border-gray-200">
                        <p class="editorial-label text-slate mb-3"><?= $EN ? 'Tags' : 'Tag' ?></p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (explode(',', $post->tags) as $tag): ?>
                            <span class="bg-ivory-warm text-navy text-xs px-3 py-1.5 font-semibold">#<?= html_escape(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- 🔥 SHARE (working) -->
                    <div class="mt-12 pt-8 border-t border-gray-200 flex flex-wrap items-center justify-between gap-4">
                        <span class="editorial-label text-slate"><?= $EN ? 'Share this article' : 'Bagikan artikel ini' ?></span>
                        <div class="flex gap-2">
                            <button type="button" class="lb-share-btn" onclick="shareFB()" title="Facebook"><i class="fab fa-facebook-f text-sm"></i></button>
                            <button type="button" class="lb-share-btn" onclick="shareLinkedIn()" title="LinkedIn"><i class="fab fa-linkedin-in text-sm"></i></button>
                            <button type="button" class="lb-share-btn" onclick="shareTwitter()" title="Twitter"><i class="fab fa-twitter text-sm"></i></button>
                            <button type="button" class="lb-share-btn" onclick="shareWA()" title="WhatsApp"><i class="fab fa-whatsapp text-sm"></i></button>
                            <button type="button" class="lb-share-btn" onclick="copyLink()" title="Copy Link"><i class="fas fa-link text-sm"></i></button>
                            <button type="button" class="lb-share-btn" onclick="window.print()" title="Print"><i class="fas fa-print text-sm"></i></button>
                        </div>
                    </div>
                </div>

                <!-- 🔥 RELATED ARTICLES -->
                <?php if (!empty($related)): ?>
                <div class="mt-12">
                    <p class="editorial-label text-gold-muted mb-5"><?= $EN ? 'Continue Reading' : 'Baca Juga' ?></p>
                    <div class="grid md:grid-cols-2 gap-5">
                        <?php foreach (array_slice($related, 0, 2) as $r): ?>
                        <a href="<?= base_url('lecturerblog/post/' . $r->slug) ?>" class="related-card">
                            <p class="text-[10px] text-slate uppercase tracking-wider mb-1"><?= date('d M Y', strtotime($r->published_at)) ?></p>
                            <h4 class="font-serif text-base font-medium text-navy leading-snug group-hover:text-gold-muted transition"><?= html_escape($r->title) ?></h4>
                            <span class="inline-block mt-2 text-xs text-gold-muted font-semibold"><?= $EN ? 'Read more' : 'Baca' ?> →</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside>
                <div class="space-y-6">
                    <!-- 🔥 TABLE OF CONTENTS -->
                    <?php if (count($toc_items) >= 2): ?>
                    <div class="lb-toc lb-reveal">
                        <p class="lb-toc-title"><?= $EN ? 'In this article' : 'Daftar isi' ?></p>
                        <ul id="tocList">
                            <?php foreach ($toc_items as $toc): ?>
                            <li class="<?= $toc['level'] == 3 ? 'h3' : '' ?>">
                                <a href="#<?= $toc['id'] ?>"><?= html_escape($toc['text']) ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Author Card -->
                    <div class="bg-white border border-gray-200 p-6 lb-reveal">
                        <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'About the Author' : 'Tentang Penulis' ?></p>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 rounded-full overflow-hidden border border-gold flex-shrink-0">
                                <?php if ($post->lecturer_photo): ?>
                                    <img src="<?= base_url('assets/uploads/' . $post->lecturer_photo) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <div class="w-full h-full bg-navy flex items-center justify-center font-serif text-xl text-gold"><?= strtoupper(substr($post->lecturer_name, 0, 1)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy text-sm truncate"><?= html_escape(trim(($post->title_front ?? '') . ' ' . $post->lecturer_name)) ?></p>
                                <p class="text-xs text-slate truncate"><?= html_escape(character_limiter($post->expertise ?? '', 60)) ?></p>
                            </div>
                        </div>
                        <a href="<?= base_url('lecturerblog/view/' . $post->lecturer_id) ?>" class="block w-full text-center btn-navy py-2.5 text-xs uppercase tracking-editorial font-semibold">
                            <?= $EN ? 'View All Articles' : 'Lihat Semua Artikel' ?>
                        </a>
                    </div>

                    <!-- More from author -->
                    <?php if (!empty($related)): ?>
                    <div class="lb-reveal">
                        <p class="editorial-label text-gold-muted mb-5"><?= $EN ? 'More from this author' : 'Artikel Lainnya' ?></p>
                        <div class="space-y-4">
                            <?php foreach (array_slice($related, 0, 3) as $r): ?>
                            <a href="<?= base_url('lecturerblog/post/' . $r->slug) ?>" class="block group pb-4 border-b border-gray-100 last:border-0">
                                <p class="text-[10px] text-slate uppercase tracking-wider mb-1"><?= date('d M Y', strtotime($r->published_at)) ?></p>
                                <h4 class="font-serif text-sm font-medium text-navy leading-snug group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($r->title) ?></h4>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="bg-navy text-ivory p-8 relative overflow-hidden lb-reveal">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gold/10 rounded-full blur-2xl"></div>
                        <p class="editorial-label text-gold mb-4"><?= $EN ? 'Faculty Directory' : 'Direktori Dosen' ?></p>
                        <a href="<?= base_url('dosen') ?>" class="btn-gold inline-block px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
                            <?= $EN ? 'All Faculty' : 'Semua Dosen' ?>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- Toast -->
<div class="lb-toast" id="lbToast"><i class="fas fa-check-circle"></i><span id="lbToastMsg"></span></div>

<script>
(function(){
    var url = window.location.href;
    var title = <?= json_encode($post->title) ?>;

    // ===== REVEAL =====
    var obs = new IntersectionObserver(function(es){
        es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.lb-reveal').forEach(function(el){ obs.observe(el); });

    // ===== TOAST =====
    function showToast(msg){
        var t = document.getElementById('lbToast');
        document.getElementById('lbToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }

    // ===== 🔥 SHARE (working) =====
    window.shareFB = function(){ window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank'); };
    window.shareLinkedIn = function(){ window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url), '_blank'); };
    window.shareTwitter = function(){ window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url), '_blank'); };
    window.shareWA = function(){ window.open('https://wa.me/?text=' + encodeURIComponent(title + ' — ' + url), '_blank'); };
    window.copyLink = function(){
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url);
            showToast('<?= $EN ? "Link copied!" : "Link disalin!" ?>');
        }
    };

    // ===== 🔥 READING PROGRESS =====
    var progressBar = document.getElementById('readingProgressBar');
    window.addEventListener('scroll', function(){
        var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var scrolled = height > 0 ? (winScroll / height) * 100 : 0;
        progressBar.style.width = scrolled + '%';
    });

    // ===== 🔥 TOC ACTIVE HIGHLIGHT =====
    var tocList = document.getElementById('tocList');
    if (tocList) {
        var tocLinks = tocList.querySelectorAll('a');
        var sections = Array.from(tocLinks).map(function(a){
            return document.getElementById(a.getAttribute('href').substring(1));
        }).filter(Boolean);

        function updateActiveToc(){
            var scrollPos = window.scrollY + 150;
            var activeIdx = -1;
            sections.forEach(function(sec, i){
                if (sec.offsetTop <= scrollPos) activeIdx = i;
            });
            tocLinks.forEach(function(a, i){
                a.parentElement.classList.toggle('active', i === activeIdx);
            });
        }
        window.addEventListener('scroll', updateActiveToc);
        updateActiveToc();
    }
})();
</script>