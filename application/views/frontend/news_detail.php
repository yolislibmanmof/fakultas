<?php
$EN = (get_site_lang() == 'en');
$p = $post;

$content_text = strip_tags($p->content ?? '');
$word_count   = str_word_count($content_text);
$read_time    = max(1, ceil($word_count / 200));
$views        = (int)($p->views ?? 0);
$date_pub     = strtotime($p->published_at);
$date_human   = date('d F Y', $date_pub);
$iso_date     = date('c', $date_pub);

// Drop cap
$content_html = trim((string)($p->content ?? ''));
$has_drop     = false;
if ($content_html !== '') {
    if (preg_match('/<p[^>]*>(.+?)<\/p>/is', $content_html, $m)) {
        $first_p = strip_tags($m[1]);
        if (mb_strlen($first_p) > 0) {
            $drop_char = mb_substr($first_p, 0, 1);
            $drop_rest = mb_substr($first_p, 1);
            $content_html = preg_replace(
                '/<p[^>]*>(.+?)<\/p>/is',
                '<p class="article-lead"><span class="article-dropcap">' . html_escape($drop_char) . '</span>' . html_escape($drop_rest) . '</p>',
                $content_html,
                1
            );
            $has_drop = true;
        }
    }
}

// 🔥 Extract H2/H3 for Table of Contents
$toc_items = [];
if (preg_match_all('/<(h[23])[^>]*id="([^"]*)"[^>]*>(.*?)<\/\1>/is', $content_html, $m)) {
    foreach ($m[0] as $i => $match) {
        $toc_items[] = [
            'level' => strtolower($m[1][$i]),
            'id' => $m[2][$i],
            'text' => strip_tags($m[3][$i]),
        ];
    }
}

// 🔥 Extract quotes for smart highlight
$smart_quotes = [];
if (preg_match_all('/<blockquote[^>]*>(.*?)<\/blockquote>/is', $content_html, $qm)) {
    foreach ($qm[1] as $q) {
        $qt = trim(strip_tags($q));
        if (strlen($qt) > 20) $smart_quotes[] = $qt;
    }
}

$current_url = current_url();
$share_fb    = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($current_url);
$share_tw    = 'https://twitter.com/intent/tweet?url=' . urlencode($current_url) . '&text=' . urlencode($p->title);
$share_wa    = 'https://wa.me/?text=' . urlencode($p->title . ' ' . $current_url);
$share_tg    = 'https://t.me/share/url?url=' . urlencode($current_url) . '&text=' . urlencode($p->title);
$share_copy  = $current_url;

// 🔥 Estimated audience (dummy live stat)
$est_audience = rand(12, 89);
?>

<!-- JSON-LD Schema -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $p->title,
    'datePublished' => $iso_date,
    'dateModified' => $iso_date,
    'author' => ['@type' => 'Person', 'name' => $p->author ?? site_name()],
    'publisher' => ['@type' => 'Organization', 'name' => site_name()],
    'image' => !empty($p->featured_image) ? base_url('assets/uploads/' . $p->featured_image) : '',
    'description' => character_limiter(strip_tags($p->excerpt ?? $p->content ?? ''), 160),
    'wordCount' => $word_count,
]) ?>
</script>

<style>
/* ===== PROGRESS + TOC ===== */
.read-progress { position: fixed; top: 0; left: 0; height: 3px; width: 0; background: linear-gradient(90deg, #C9A227, #D4AF37, #C9A227); z-index: 9998; box-shadow: 0 0 12px rgba(201,162,39,.7); transition: width .1s linear; }
.toc-sidebar { position: fixed; left: max(2rem, calc(50vw - 740px)); top: 50%; transform: translateY(-50%); z-index: 50; display: none; flex-direction: column; gap: .4rem; max-height: 60vh; overflow-y: auto; }
@media (min-width: 1400px) { .toc-sidebar { display: flex; } }
.toc-item { font-size: 11px; color: #64748b; padding: .4rem .8rem; border-left: 2px solid #e5e7eb; transition: all .3s; text-decoration: none; max-width: 180px; }
.toc-item:hover { color: #C9A227; border-color: #C9A227; }
.toc-item.active { color: #0B2239; border-color: #C9A227; font-weight: 600; }
.toc-item.h3 { padding-left: 1.5rem; font-size: 10px; }

/* ===== 🎧 FLOATING TTS PLAYER ===== */
.tts-player {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    background: #0B2239; color: #F7F5F0; padding: 16px 20px;
    border-radius: 12px; box-shadow: 0 12px 40px rgba(0,0,0,.3);
    display: none; align-items: center; gap: 16px; min-width: 320px;
    border-top: 3px solid #C9A227;
}
.tts-player.active { display: flex; animation: slideUp .4s ease; }
@keyframes slideUp { from { transform: translateY(100px); opacity: 0; } to { transform: none; opacity: 1; } }
.tts-waveform { display: flex; gap: 2px; align-items: center; height: 30px; }
.tts-waveform span { width: 3px; background: #C9A227; animation: wave 1s ease-in-out infinite; }
.tts-waveform span:nth-child(1) { animation-delay: 0s; height: 10px; }
.tts-waveform span:nth-child(2) { animation-delay: .1s; height: 18px; }
.tts-waveform span:nth-child(3) { animation-delay: .2s; height: 14px; }
.tts-waveform span:nth-child(4) { animation-delay: .3s; height: 22px; }
.tts-waveform span:nth-child(5) { animation-delay: .4s; height: 16px; }
@keyframes wave { 0%,100% { transform: scaleY(1); } 50% { transform: scaleY(.3); } }
.tts-player.paused .tts-waveform span { animation-play-state: paused; }
.tts-progress { flex: 1; height: 3px; background: rgba(247,245,240,.2); border-radius: 2px; overflow: hidden; }
.tts-progress-bar { height: 100%; background: #C9A227; width: 0; transition: width .3s; }

/* ===== 🖍️ HIGHLIGHTER ===== */
.user-highlight { background: rgba(201,162,39,.3); padding: 2px 0; cursor: pointer; position: relative; }
.user-highlight:hover { background: rgba(201,162,39,.5); }
.highlight-toolbar {
    position: absolute; background: #0B2239; color: #C9A227;
    padding: 4px 8px; border-radius: 4px; font-size: 10px;
    display: none; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
.highlight-toolbar.show { display: block; }

/* ===== 👥 AUDIENCE STAT ===== */
.audience-stat { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(247,245,240,.7); }
.audience-dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

/* ===== 📊 READING ANALYTICS ===== */
.reading-stats { display: flex; gap: 2rem; padding: 1rem; background: #F7F5F0; border: 1px solid #e5e7eb; margin-bottom: 2rem; }
.reading-stat { text-align: center; }
.reading-stat .num { font-family: 'Fraunces', serif; font-size: 1.5rem; color: #C9A227; }
.reading-stat .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #64748b; margin-top: 2px; }

/* ===== KEYBOARD HELP ===== */
.kb-modal { position: fixed; inset: 0; z-index: 10000; background: rgba(6,20,32,.9); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; padding: 2rem; }
.kb-modal.open { display: flex; }
.kb-modal-card { background: #fff; max-width: 500px; width: 100%; padding: 2rem; }
.kb-shortcut { display: flex; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid #e5e7eb; }
.kb-shortcut:last-child { border-bottom: 0; }
.kb-key { background: #0B2239; color: #C9A227; padding: 2px 8px; font-family: 'JetBrains Mono', monospace; font-size: 11px; border-radius: 3px; }

.article-hero { position: relative; overflow: hidden; min-height: 60vh; }
.article-hero .img-fit-wrap { position: absolute; inset: 0; }
.article-body { font-size: 17px; line-height: 1.85; color: #334155; }
.article-body p { margin-bottom: 1.5rem; }
.article-body h2, .article-body h3, .article-body h4 { font-family: 'Fraunces', serif; font-weight: 500; color: #0B2239; margin: 2.5rem 0 1rem; line-height: 1.2; }
.article-body h2 { font-size: 2rem; }
.article-body h3 { font-size: 1.5rem; }
.article-body h4 { font-size: 1.2rem; }
.article-body a { color: #B8941F; text-decoration: underline; text-underline-offset: 3px; }
.article-body a:hover { color: #C9A227; }
.article-body ul, .article-body ol { margin: 1.5rem 0 1.5rem 1.5rem; }
.article-body li { margin-bottom: .5rem; }
.article-body img { display: block; width: 100%; max-width: 100%; height: auto; margin: 2.5rem 0; border-radius: 4px; box-shadow: 0 8px 24px rgba(11,34,57,.12); object-fit: contain; background: linear-gradient(135deg, #061420 0%, #0B2239 100%); }
.article-body blockquote { position: relative; font-family: 'Fraunces', serif; font-style: italic; font-weight: 400; font-size: 1.5rem; line-height: 1.5; color: #0B2239; padding: 2rem 2rem 2rem 4rem; margin: 2.5rem 0; border-left: 4px solid #C9A227; background: linear-gradient(135deg, #FAF8F3 0%, #F5EFE0 100%); box-shadow: 0 4px 16px rgba(11,34,57,.05); }
.article-body blockquote::before { content: '"'; position: absolute; top: -20px; left: 15px; font-family: 'Fraunces', serif; font-size: 7rem; line-height: 1; color: #C9A227; opacity: .25; font-weight: 700; }
.article-body blockquote p { margin-bottom: 0; }
.article-lead::first-letter { float: left; font-family: 'Fraunces', serif; font-size: 5.5rem; font-weight: 700; line-height: 0.85; padding: 0.4rem 0.8rem 0 0; color: #C9A227; font-style: italic; }
.article-dropcap { float: left; font-family: 'Fraunces', serif; font-size: 5.5rem; font-weight: 700; line-height: 0.85; padding: 0.4rem 0.8rem 0 0; color: #C9A227; font-style: italic; }

.share-rail { position: fixed; left: max(2rem, calc(50vw - 740px)); top: 50%; transform: translateY(-50%); z-index: 50; display: none; flex-direction: column; gap: .5rem; }
@media (min-width: 1400px) { .share-rail { display: flex; } }
.share-rail a, .share-rail button { width: 44px; height: 44px; background: #fff; border: 1px solid #e5e7eb; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all .3s; box-shadow: 0 4px 12px rgba(11,34,57,.05); cursor: pointer; text-decoration: none; }
.share-rail a:hover, .share-rail button:hover { background: #C9A227; color: #0B2239; border-color: #C9A227; transform: translateX(4px); }
.share-rail button.bookmarked { background: #C9A227; color: #0B2239; border-color: #C9A227; }

.author-card { background: linear-gradient(135deg, #FAF8F3 0%, #fff 100%); border: 1px solid #e5e7eb; border-left: 4px solid #C9A227; }
.rel-card { transition: all .4s cubic-bezier(.22,1,.36,1); display: block; }
.rel-card:hover { transform: translateY(-6px); box-shadow: 0 16px 32px rgba(11,34,57,.12); }
.rel-card .img-fit-wrap { position: relative; aspect-ratio: 4/3; overflow: hidden; background: #061420; }
.rel-card .img-fit-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: blur(18px) brightness(.5) saturate(1.1); transform: scale(1.1); transition: transform .6s; }
.rel-card .img-fit-fg { position: relative; z-index: 1; width: 100%; height: 100%; object-fit: contain; transition: transform .6s; }
.rel-card:hover .img-fit-bg { transform: scale(1.15); }
.rel-card:hover .img-fit-fg { transform: scale(1.03); }

.article-reveal { opacity: 0; transform: translateY(30px); transition: all .9s cubic-bezier(.22,1,.36,1); }
.article-reveal.in { opacity: 1; transform: none; }
.meta-pill { background: rgba(247,245,240,.12); border: 1px solid rgba(247,245,240,.2); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

.copy-toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(100px); background: #0B2239; color: #F7F5F0; padding: .75rem 1.5rem; box-shadow: 0 12px 24px rgba(0,0,0,.25); transition: transform .4s cubic-bezier(.22,1,.36,1); z-index: 9999; }
.copy-toast.show { transform: translateX(-50%) translateY(0); }

.img-fit-wrap { position: relative; background: #061420; overflow: hidden; }
.img-fit-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: blur(22px) brightness(.5) saturate(1.15); transform: scale(1.12); }
.img-fit-fg { position: relative; z-index: 1; width: 100%; height: 100%; object-fit: contain; }
.hero-fit-fg { filter: drop-shadow(0 24px 48px rgba(0,0,0,.5)); }

/* ===== READING TOOLBAR ===== */
.read-toolbar { display: flex; flex-wrap: wrap; gap: 8px; padding: 12px 16px; background: #F7F5F0; border: 1px solid #e5e7eb; margin-bottom: 2rem; align-items: center; }
.read-tool { padding: 6px 12px; background: #fff; border: 1px solid #e5e7eb; color: #0B2239; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; }
.read-tool:hover { background: #0B2239; color: #F7F5F0; border-color: #0B2239; }
.read-tool.active { background: #C9A227; color: #0B2239; border-color: #C9A227; }
.font-size-ctrl { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e5e7eb; color: #0B2239; font-weight: 700; cursor: pointer; transition: all .2s; }
.font-size-ctrl:hover { background: #0B2239; color: #F7F5F0; }

/* ===== DARK READING MODE ===== */
body.dark-reading { background: #0B2239 !important; }
body.dark-reading .article-body { color: #F7F5F0; }
body.dark-reading .article-body h2, body.dark-reading .article-body h3, body.dark-reading .article-body h4 { color: #C9A227; }
body.dark-reading .article-body blockquote { background: linear-gradient(135deg, #13334F 0%, #0B2239 100%); color: #F7F5F0; border-left-color: #C9A227; }
body.dark-reading .read-toolbar { background: #13334F; border-color: #0B2239; }
body.dark-reading .reading-stats { background: #13334F; border-color: #0B2239; }

/* ===== TTS HIGHLIGHT ===== */
.article-body p.tts-active { background: rgba(201,162,39,.15); padding: 4px 8px; margin-left: -8px; margin-right: -8px; border-radius: 2px; transition: background .3s; }

/* ===== PRINT ===== */
@media print {
    .read-progress, .share-rail, .read-toolbar, .copy-toast, .mobile-share, .author-card, .rel-card, .toc-sidebar, .tts-player, .reading-stats, .kb-modal { display: none !important; }
    .article-hero { min-height: auto !important; }
    .article-body { font-size: 12pt; }
    .article-body img { break-inside: avoid; }
    body { background: #fff !important; }
}
</style>

<div class="read-progress" id="readProgress"></div>

<!-- ============ 📑 TABLE OF CONTENTS SIDEBAR ===== -->
<?php if (!empty($toc_items)): ?>
<aside class="toc-sidebar" id="tocSidebar">
    <?php foreach ($toc_items as $ti): ?>
    <a href="#<?= $ti['id'] ?>" class="toc-item <?= $ti['level'] ?>"><?= html_escape($ti['text']) ?></a>
    <?php endforeach; ?>
</aside>
<?php endif; ?>

<!-- ============ HERO ===== -->
<section class="article-hero bg-navy text-ivory relative">
    <?php if (!empty($p->featured_image)): ?>
        <div class="img-fit-wrap">
            <img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-bg" alt="" loading="lazy">
            <img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="img-fit-fg hero-fit-fg" alt="<?= html_escape($p->title) ?>">
        </div>
    <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-br from-navy via-navy-light to-navy-deep"></div>
    <?php endif; ?>

    <div class="container mx-auto px-6 pt-24 md:pt-32 pb-20 relative z-10">
        <div class="max-w-3xl">
            <a href="<?= base_url('berita') ?>" class="inline-flex items-center gap-2 text-[10px] uppercase tracking-editorial text-ivory/60 hover:text-gold transition mb-6">
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                <?= $EN ? 'Back to News' : 'Kembali ke Berita' ?>
            </a>

            <div class="flex flex-wrap items-center gap-3 mb-6">
                <?php if (!empty($p->category_name)): ?>
                    <a href="<?= base_url('berita?cat=' . ($p->category_slug ?? '')) ?>" class="meta-pill px-3 py-1 text-[10px] uppercase tracking-wider font-bold text-gold hover:bg-gold hover:text-navy transition"><?= html_escape($p->category_name) ?></a>
                <?php endif; ?>
                <div class="meta-pill px-3 py-1 text-[10px] uppercase tracking-wider text-ivory/80"><i class="fas fa-calendar mr-1"></i><?= $date_human ?></div>
                <div class="meta-pill px-3 py-1 text-[10px] uppercase tracking-wider text-ivory/80"><i class="fas fa-clock mr-1"></i><?= $read_time ?> min <?= $EN ? 'read' : 'baca' ?></div>
                <div class="meta-pill px-3 py-1 text-[10px] uppercase tracking-wider text-ivory/80"><i class="fas fa-eye mr-1"></i><span id="viewsCounter">0</span> views</div>
                <!-- 👥 AUDIENCE STAT -->
                <div class="audience-stat"><span class="audience-dot"></span><span id="audienceCount"><?= $est_audience ?></span> <?= $EN ? 'reading now' : 'sedang membaca' ?></div>
            </div>

            <h1 class="font-serif font-light text-4xl md:text-6xl leading-[1.08] tracking-tight mb-8 text-balance"><?= html_escape($p->title) ?></h1>

            <?php if (!empty($p->excerpt)): ?>
                <p class="text-lg md:text-xl text-ivory/80 leading-relaxed max-w-2xl"><?= html_escape(character_limiter($p->excerpt, 200)) ?></p>
            <?php endif; ?>

            <?php if (!empty($p->author)): ?>
            <div class="flex items-center gap-4 mt-10 pt-8 border-t border-ivory/10 max-w-md">
                <div class="w-12 h-12 rounded-full bg-gold text-navy flex items-center justify-center font-serif font-bold text-lg flex-shrink-0"><?= strtoupper(substr($p->author, 0, 1)) ?></div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-ivory/50"><?= $EN ? 'Written by' : 'Ditulis oleh' ?></p>
                    <p class="font-semibold text-ivory"><?= html_escape($p->author) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="absolute top-6 right-6 hidden md:flex items-center gap-2 z-10">
        <span class="font-mono text-[10px] tracking-widest text-ivory/40"><?= date('d/m/Y', $date_pub) ?></span>
    </div>
</section>

<!-- ============ SHARE RAIL ===== -->
<aside class="share-rail" id="shareRail">
    <a href="<?= $share_fb ?>" target="_blank" rel="noopener" title="Facebook"><i class="fab fa-facebook-f"></i></a>
    <a href="<?= $share_tw ?>" target="_blank" rel="noopener" title="Twitter"><i class="fab fa-x-twitter"></i></a>
    <a href="<?= $share_wa ?>" target="_blank" rel="noopener" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
    <a href="<?= $share_tg ?>" target="_blank" rel="noopener" title="Telegram"><i class="fab fa-telegram-plane"></i></a>
    <button type="button" onclick="copyLink(event)" title="Copy link"><i class="fas fa-link"></i></button>
    <button type="button" id="bookmarkBtn" onclick="toggleBookmark()" title="Bookmark"><i class="fas fa-bookmark"></i></button>
</aside>

<!-- ============ ARTICLE BODY ===== -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto">

            <!-- 📊 READING ANALYTICS -->
            <div class="reading-stats article-reveal">
                <div class="reading-stat"><div class="num" id="wordCount"><?= number_format($word_count) ?></div><div class="lbl"><?= $EN ? 'Words' : 'Kata' ?></div></div>
                <div class="reading-stat"><div class="num"><?= $read_time ?></div><div class="lbl"><?= $EN ? 'Min Read' : 'Menit' ?></div></div>
                <div class="reading-stat"><div class="num" id="wpmStat">--</div><div class="lbl">WPM</div></div>
                <div class="reading-stat"><div class="num" id="timeLeft"><?= $read_time ?></div><div class="lbl"><?= $EN ? 'Left' : 'Tersisa' ?></div></div>
            </div>

            <!-- READING TOOLBAR -->
            <div class="read-toolbar article-reveal">
                <span class="text-[10px] uppercase tracking-wider text-slate font-semibold mr-2"><?= $EN ? 'Reading tools' : 'Alat baca' ?>:</span>
                <button type="button" class="font-size-ctrl" onclick="adjustFont(-1)" title="<?= $EN ? 'Smaller text' : 'Perkecil teks' ?>">A-</button>
                <button type="button" class="font-size-ctrl" onclick="adjustFont(1)" title="<?= $EN ? 'Larger text' : 'Perbesar teks' ?>">A+</button>
                <button type="button" class="read-tool" id="ttsBtn" onclick="toggleTTS()"><i class="fas fa-volume-up"></i><?= $EN ? 'Listen' : 'Dengarkan' ?></button>
                <button type="button" class="read-tool" id="darkBtn" onclick="toggleDarkMode()"><i class="fas fa-moon"></i><?= $EN ? 'Dark Mode' : 'Mode Gelap' ?></button>
                <button type="button" class="read-tool" onclick="window.print()"><i class="fas fa-print"></i><?= $EN ? 'Print' : 'Cetak' ?></button>
                <button type="button" class="read-tool" onclick="openKBHelp()"><i class="fas fa-keyboard"></i>Shortcuts</button>
            </div>

            <article class="article-body article-reveal" id="articleBody"><?= $content_html ?></article>

            <!-- Mobile share -->
            <div class="md:hidden flex flex-wrap gap-2 mt-10 pt-8 border-t border-gray-200">
                <span class="text-[10px] uppercase tracking-wider text-slate font-semibold mr-2 self-center">SHARE:</span>
                <a href="<?= $share_fb ?>" target="_blank" rel="noopener" class="px-4 py-2 bg-navy text-ivory text-xs uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition"><i class="fab fa-facebook-f mr-1"></i>FB</a>
                <a href="<?= $share_tw ?>" target="_blank" rel="noopener" class="px-4 py-2 bg-navy text-ivory text-xs uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition"><i class="fab fa-x-twitter mr-1"></i>X</a>
                <a href="<?= $share_wa ?>" target="_blank" rel="noopener" class="px-4 py-2 bg-navy text-ivory text-xs uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition"><i class="fab fa-whatsapp mr-1"></i>WA</a>
                <a href="<?= $share_tg ?>" target="_blank" rel="noopener" class="px-4 py-2 bg-navy text-ivory text-xs uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition"><i class="fab fa-telegram-plane mr-1"></i>TG</a>
                <button type="button" onclick="copyLink(event)" class="px-4 py-2 bg-gold text-navy text-xs uppercase tracking-wider font-semibold hover:bg-navy hover:text-gold transition"><i class="fas fa-link mr-1"></i>Copy</button>
            </div>

            <!-- Author bio -->
            <?php if (!empty($p->author)): ?>
            <div class="author-card p-6 md:p-8 mt-12 article-reveal">
                <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'About the Author' : 'Tentang Penulis' ?></p>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-navy text-gold flex items-center justify-center font-serif font-bold text-2xl flex-shrink-0"><?= strtoupper(substr($p->author, 0, 1)) ?></div>
                    <div>
                        <h3 class="font-serif text-xl font-medium text-navy"><?= html_escape($p->author) ?></h3>
                        <p class="text-sm text-slate mt-1"><?= $EN ? 'Editorial Staff at' : 'Tim Redaksi di' ?> <?= site_name() ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Back / Next -->
            <div class="grid md:grid-cols-2 gap-4 mt-12 article-reveal">
                <a href="<?= base_url('berita') ?>" class="bg-white border border-gray-200 p-6 hover-lift group block">
                    <p class="text-[10px] uppercase tracking-wider text-slate mb-2"><i class="fas fa-arrow-left mr-1"></i> <?= $EN ? 'All News' : 'Semua Berita' ?></p>
                    <p class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition"><?= $EN ? 'Back to Directory' : 'Kembali ke Direktori' ?></p>
                </a>
                <?php if (!empty($related)): ?>
                <a href="<?= base_url('berita/detail/' . $related[0]->slug) ?>" class="bg-white border border-gray-200 p-6 hover-lift group text-right block">
                    <p class="text-[10px] uppercase tracking-wider text-slate mb-2"><?= $EN ? 'Next Read' : 'Baca Selanjutnya' ?> <i class="fas fa-arrow-right ml-1"></i></p>
                    <p class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($related[0]->title) ?></p>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ RELATED ARTICLES ===== -->
<?php if (!empty($related)): ?>
<section class="py-16 md:py-20 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6"><div class="max-w-6xl mx-auto">
        <div class="flex items-end justify-between mb-10 article-reveal">
            <div><p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Continue Reading' : 'Baca Juga' ?></p><h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-tight"><?= $EN ? 'Related <em class="italic text-gold-muted">stories</em>' : 'Cerita <em class="italic text-gold-muted">terkait</em>' ?></h2></div>
            <a href="<?= base_url('berita') ?>" class="hidden md:inline-flex items-center gap-2 text-[10px] uppercase tracking-editorial text-navy font-semibold hover:text-gold transition"><?= $EN ? 'See all news' : 'Lihat semua' ?><svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg></a>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach ($related as $r): ?>
            <a href="<?= base_url('berita/detail/' . $r->slug) ?>" class="rel-card bg-ivory overflow-hidden article-reveal">
                <div class="img-fit-wrap"><?php if (!empty($r->featured_image)): ?><img src="<?= base_url('assets/uploads/' . $r->featured_image) ?>" class="img-fit-bg" alt="" loading="lazy"><img src="<?= base_url('assets/uploads/' . $r->featured_image) ?>" class="img-fit-fg" alt="<?= html_escape($r->title) ?>"><?php else: ?><div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center"><i class="fas fa-newspaper text-4xl text-gold/30"></i></div><?php endif; ?></div>
                <div class="p-6"><div class="flex items-center gap-3 mb-3"><?php if (!empty($r->category_name)): ?><span class="text-[10px] uppercase tracking-wider font-bold text-gold-muted"><?= html_escape($r->category_name) ?></span><?php endif; ?><span class="text-[10px] uppercase tracking-wider text-slate"><?= date('d M Y', strtotime($r->published_at)) ?></span></div><h3 class="font-serif text-lg font-medium text-navy leading-snug line-clamp-2"><?= html_escape($r->title) ?></h3></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div></div>
</section>
<?php endif; ?>

<!-- 🎧 FLOATING TTS PLAYER -->
<div class="tts-player" id="ttsPlayer">
    <button onclick="toggleTTSPlay()" id="ttsPlayBtn"><i class="fas fa-pause"></i></button>
    <div class="tts-waveform"><span></span><span></span><span></span><span></span><span></span></div>
    <div class="tts-progress"><div class="tts-progress-bar" id="ttsProgressBar"></div></div>
    <button onclick="stopTTS()"><i class="fas fa-times"></i></button>
</div>

<!-- ⌨️ KEYBOARD HELP MODAL -->
<div class="kb-modal" id="kbModal" onclick="if(event.target===this)closeKBHelp()">
    <div class="kb-modal-card">
        <div class="flex justify-between items-center mb-4"><h3 class="font-serif text-2xl font-light text-navy">Keyboard Shortcuts</h3><button onclick="closeKBHelp()" class="text-2xl text-slate hover:text-navy">&times;</button></div>
        <div class="kb-shortcut"><span>Increase font size</span><span class="kb-key">+</span></div>
        <div class="kb-shortcut"><span>Decrease font size</span><span class="kb-key">-</span></div>
        <div class="kb-shortcut"><span>Toggle dark mode</span><span class="kb-key">D</span></div>
        <div class="kb-shortcut"><span>Toggle bookmark</span><span class="kb-key">B</span></div>
        <div class="kb-shortcut"><span>Toggle text-to-speech</span><span class="kb-key">L</span></div>
        <div class="kb-shortcut"><span>Show this help</span><span class="kb-key">?</span></div>
        <div class="kb-shortcut"><span>Jump to top</span><span class="kb-key">T</span></div>
    </div>
</div>

<div class="copy-toast" id="copyToast"><i class="fas fa-check-circle mr-2"></i>Link copied to clipboard!</div>

<script>
(function(){
    var hero = document.querySelector('.article-hero');
    var heroH = hero ? hero.offsetHeight : 0;
    var prog = document.getElementById('readProgress');
    var originalTitle = document.title;
    var readTime = <?= $read_time ?>;
    var articleSlug = <?= json_encode($p->slug ?? '') ?>;
    var wordCount = <?= $word_count ?>;
    var startReadTime = Date.now();

    // ===== 🌓 AUTO DARK MODE =====
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        if (!localStorage.getItem('articleDarkMode')) {
            document.body.classList.add('dark-reading');
            var db = document.getElementById('darkBtn');
            if (db) { db.classList.add('active'); db.innerHTML = '<i class="fas fa-sun"></i><?= $EN ? "Light Mode" : "Mode Terang" ?>'; }
        }
    }

    // ===== 🔖 READING POSITION SAVER =====
    var savedPos = localStorage.getItem('readpos_' + articleSlug);
    if (savedPos) {
        setTimeout(function(){ window.scrollTo(0, parseInt(savedPos)); }, 500);
    }
    window.addEventListener('scroll', function(){ localStorage.setItem('readpos_' + articleSlug, window.scrollY); });

    // ===== PROGRESS BAR + TITLE UPDATE + TIME LEFT =====
    window.addEventListener('scroll', function(){
        var scrolled = window.scrollY;
        var max = document.documentElement.scrollHeight - window.innerHeight;
        if (scrolled < heroH) { prog.style.width = '0%'; document.title = originalTitle; return; }
        var pct = max > 0 ? (scrolled / max) * 100 : 0;
        prog.style.width = Math.min(pct, 100) + '%';
        var remaining = Math.max(0, Math.ceil(readTime * (1 - pct/100)));
        document.title = remaining + ' min · ' + originalTitle;
        var tl = document.getElementById('timeLeft');
        if (tl) tl.textContent = remaining;
        // WPM calculation
        var elapsed = (Date.now() - startReadTime) / 60000; // minutes
        var wordsRead = Math.floor(wordCount * pct / 100);
        var wpm = elapsed > 0.1 ? Math.round(wordsRead / elapsed) : 0;
        var ws = document.getElementById('wpmStat');
        if (ws) ws.textContent = wpm;
    });

    // ===== VIEWS COUNTER =====
    var vc = document.getElementById('viewsCounter');
    if (vc) { var target = <?= $views ?>; var duration = 1500, start = performance.now();
        function anim(now){ var p = Math.min((now - start) / duration, 1); var ease = 1 - Math.pow(1 - p, 3); vc.textContent = Math.floor(ease * target).toLocaleString('id-ID'); if (p < 1) requestAnimationFrame(anim); else vc.textContent = target.toLocaleString('id-ID'); }
        requestAnimationFrame(anim); }

    // ===== 👥 AUDIENCE COUNTER (live update) =====
    var ac = document.getElementById('audienceCount');
    if (ac) { setInterval(function(){ var c = parseInt(ac.textContent); var delta = Math.random() > .5 ? 1 : -1; c = Math.max(5, c + delta); ac.textContent = c; }, 3000); }

    // ===== REVEAL =====
    var obs = new IntersectionObserver(function(es){ es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } }); }, { threshold: .1, rootMargin: '0px 0px -80px 0px' });
    document.querySelectorAll('.article-reveal').forEach(function(el){ obs.observe(el); });

    // ===== 📑 TOC ACTIVE STATE =====
    var tocItems = document.querySelectorAll('.toc-item');
    var headings = [];
    tocItems.forEach(function(ti){ var h = document.getElementById(ti.getAttribute('href').substring(1)); if (h) headings.push({el: h, link: ti}); });
    if (headings.length) {
        window.addEventListener('scroll', function(){
            var y = window.scrollY + 150;
            var active = null;
            headings.forEach(function(h){ if (h.el.offsetTop <= y) active = h; });
            tocItems.forEach(function(ti){ ti.classList.remove('active'); });
            if (active) active.link.classList.add('active');
        });
    }

    // ===== FONT SIZE =====
    var body = document.getElementById('articleBody');
    var baseSize = 17;
    var currentSize = parseInt(localStorage.getItem('articleFontSize')) || baseSize;
    if (body) body.style.fontSize = currentSize + 'px';
    window.adjustFont = function(delta){ currentSize = Math.max(14, Math.min(24, currentSize + delta)); if (body) body.style.fontSize = currentSize + 'px'; localStorage.setItem('articleFontSize', currentSize); };

    // ===== DARK MODE =====
    var darkBtn = document.getElementById('darkBtn');
    var isDark = localStorage.getItem('articleDarkMode') === '1' || document.body.classList.contains('dark-reading');
    if (isDark) { document.body.classList.add('dark-reading'); darkBtn.classList.add('active'); darkBtn.innerHTML = '<i class="fas fa-sun"></i><?= $EN ? "Light Mode" : "Mode Terang" ?>'; }
    window.toggleDarkMode = function(){ isDark = !isDark; document.body.classList.toggle('dark-reading', isDark); darkBtn.classList.toggle('active', isDark); darkBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i><?= $EN ? "Light Mode" : "Mode Terang" ?>' : '<i class="fas fa-moon"></i><?= $EN ? "Dark Mode" : "Mode Gelap" ?>'; localStorage.setItem('articleDarkMode', isDark ? '1' : '0'); };

    // ===== 🎧 TEXT-TO-SPEECH WITH PLAYER =====
    var ttsBtn = document.getElementById('ttsBtn');
    var ttsPlayer = document.getElementById('ttsPlayer');
    var ttsProgressBar = document.getElementById('ttsProgressBar');
    var isTTSPlaying = false;
    var currentParagraph = 0;
    var paragraphs = body ? Array.from(body.querySelectorAll('p')) : [];
    var ttsUtterance = null;

    window.toggleTTS = function(){
        if (!('speechSynthesis' in window)) { alert('<?= $EN ? "Text-to-speech not supported" : "TTS tidak didukung" ?>'); return; }
        if (isTTSPlaying) { window.speechSynthesis.cancel(); isTTSPlaying = false; ttsBtn.classList.remove('active'); ttsBtn.innerHTML = '<i class="fas fa-volume-up"></i><?= $EN ? "Listen" : "Dengarkan" ?>'; ttsPlayer.classList.remove('active'); paragraphs.forEach(function(p){ p.classList.remove('tts-active'); }); return; }
        if (paragraphs.length === 0) return;
        isTTSPlaying = true; ttsBtn.classList.add('active'); ttsBtn.innerHTML = '<i class="fas fa-stop"></i><?= $EN ? "Stop" : "Berhenti" ?>'; ttsPlayer.classList.add('active'); currentParagraph = 0; speakNext();
    };
    window.toggleTTSPlay = function(){
        if (window.speechSynthesis.paused) { window.speechSynthesis.resume(); ttsPlayer.classList.remove('paused'); }
        else if (window.speechSynthesis.speaking) { window.speechSynthesis.pause(); ttsPlayer.classList.add('paused'); }
    };
    window.stopTTS = function(){ toggleTTS(); };

    function speakNext(){
        if (!isTTSPlaying || currentParagraph >= paragraphs.length) { isTTSPlaying = false; ttsBtn.classList.remove('active'); ttsBtn.innerHTML = '<i class="fas fa-volume-up"></i><?= $EN ? "Listen" : "Dengarkan" ?>'; ttsPlayer.classList.remove('active'); paragraphs.forEach(function(p){ p.classList.remove('tts-active'); }); return; }
        paragraphs.forEach(function(p){ p.classList.remove('tts-active'); });
        paragraphs[currentParagraph].classList.add('tts-active');
        paragraphs[currentParagraph].scrollIntoView({ behavior: 'smooth', block: 'center' });
        ttsProgressBar.style.width = ((currentParagraph / paragraphs.length) * 100) + '%';
        ttsUtterance = new SpeechSynthesisUtterance(paragraphs[currentParagraph].textContent);
        ttsUtterance.lang = '<?= $EN ? "en-US" : "id-ID" ?>';
        ttsUtterance.rate = 0.95;
        ttsUtterance.onend = function(){ currentParagraph++; speakNext(); };
        window.speechSynthesis.speak(ttsUtterance);
    }

    // ===== BOOKMARK =====
    var bookmarks = JSON.parse(localStorage.getItem('news_bookmarks') || '[]');
    var bookmarkBtn = document.getElementById('bookmarkBtn');
    if (bookmarkBtn && bookmarks.indexOf(articleSlug) > -1) { bookmarkBtn.classList.add('bookmarked'); }
    window.toggleBookmark = function(){ var idx = bookmarks.indexOf(articleSlug); if (idx > -1) { bookmarks.splice(idx, 1); bookmarkBtn.classList.remove('bookmarked'); } else { bookmarks.push(articleSlug); bookmarkBtn.classList.add('bookmarked'); } localStorage.setItem('news_bookmarks', JSON.stringify(bookmarks)); };

    // ===== ⌨️ KEYBOARD SHORTCUTS =====
    document.addEventListener('keydown', function(e){
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === '?') { openKBHelp(); }
        else if (e.key === '+' || e.key === '=') { adjustFont(1); }
        else if (e.key === '-') { adjustFont(-1); }
        else if (e.key.toLowerCase() === 'd') { toggleDarkMode(); }
        else if (e.key.toLowerCase() === 'b') { toggleBookmark(); }
        else if (e.key.toLowerCase() === 'l') { toggleTTS(); }
        else if (e.key.toLowerCase() === 't') { window.scrollTo({top: 0, behavior: 'smooth'}); }
    });
    window.openKBHelp = function(){ document.getElementById('kbModal').classList.add('open'); };
    window.closeKBHelp = function(){ document.getElementById('kbModal').classList.remove('open'); };
})();

function copyLink(e) { e.preventDefault(); navigator.clipboard.writeText(<?= json_encode($share_copy) ?>).then(function(){ var t = document.getElementById('copyToast'); t.classList.add('show'); setTimeout(function(){ t.classList.remove('show'); }, 2500); }); }
</script>