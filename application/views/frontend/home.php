<?php
if (!function_exists('hero_format')) {
    function hero_format($t) {
        return preg_replace('/\*(.+?)\*/', '<em class="italic text-gold">$1</em>', html_escape($t));
    }
}
$CI =& get_instance();
$CI->load->model('Setting_model');
// SINKRON: label hero default = nama fakultas (ID atau EN)
$default_label = get_site_lang() == 'en'
    ? site_tagline()   // EN pakai versi Inggris
    : site_name();     // ID pakai nama utama
$hero_label     = $CI->Setting_model->get('hero_label', '') ?: $default_label;

$hero_title     = $CI->Setting_model->get('hero_title', 'Shaping the *future* through science, code & *innovation*.');
$hero_subtitle  = $CI->Setting_model->get('hero_subtitle', '') ?: (site_name() . ' — pusat keunggulan akademik yang mencetak pemimpin teknologi dan ilmuwan kelas dunia.');
$hero_photo     = $CI->Setting_model->get('hero_photo', '');
$hero_anim      = $CI->Setting_model->get('hero_animation', 'robot');
$hero_particles = $CI->Setting_model->get('hero_particles', '1');

// FIX UTAMA: welcome_text otomatis pakai nama fakultas saat ini
$default_welcome = 'WELCOME TO ' . strtoupper(site_name());
$welcome_text    = $CI->Setting_model->get('welcome_text', '') ?: $default_welcome;
?>

<!-- ============ INTRO: SOFT VEIL OVERLAY ============ -->
<div id="introOverlay" class="intro-overlay">
    <canvas id="introCanvas" class="intro-canvas"></canvas>

    <div class="intro-line intro-line-left"></div>
    <div class="intro-line intro-line-right"></div>
    <div class="intro-shockwave"></div>

    <div class="intro-text-wrap">
        <?php
        // Sinkron: welcome_text dipecah jadi kata per kata untuk animasi
        $welcome_words = array_filter(array_map('trim', preg_split('/\s+/', strtoupper($welcome_text))));
        if (empty($welcome_words)) $welcome_words = ['WELCOME', 'TO', 'OUR', 'WEBSITE'];
        ?>
        <h1 class="intro-title">
            <?php foreach ($welcome_words as $w): ?>
            <span class="intro-word" data-word="<?= html_escape($w) ?>"><?= html_escape($w) ?></span>
            <?php endforeach; ?>
        </h1>
        <div class="intro-divider"></div>
        <p class="intro-subtitle">FOR MORE INFORMATION</p>
    </div>

    <div class="intro-skip-hint">click anywhere to skip</div>
</div>

<style>
/* ===== SOFT VEIL ===== */
.intro-overlay {
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(6, 20, 32, .55) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    overflow: hidden;
    transition: opacity 1s ease !important;
    cursor: pointer;
}
.intro-overlay.done { opacity: 0; pointer-events: none; }
.intro-canvas { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 2; pointer-events: none; }

.intro-line {
    position: absolute; top: 50%; height: 2px; width: 50vw;
    background: linear-gradient(90deg, transparent 0%, #C9A227 40%, #FFF6D6 50%, #C9A227 60%, transparent 100%);
    box-shadow: 0 0 18px rgba(201,162,39,.5), 0 0 40px rgba(201,162,39,.3);
    transform: translateY(-50%);
    opacity: 0;
    z-index: 3;
}
.intro-line-left { left: -50vw; animation: lineInLeft .55s cubic-bezier(.77,0,.18,1) .05s forwards; }
.intro-line-right { right: -50vw; animation: lineInRight .55s cubic-bezier(.77,0,.18,1) .05s forwards; }
@keyframes lineInLeft { 0% { left: -50vw; opacity: 0; } 30% { opacity: 1; } 100% { left: 0; opacity: 1; } }
@keyframes lineInRight { 0% { right: -50vw; opacity: 0; } 30% { opacity: 1; } 100% { right: 0; opacity: 1; } }

.intro-shockwave {
    position: absolute; top: 50%; left: 50%;
    width: 10px; height: 10px;
    border: 2px solid rgba(201,162,39,.8);
    border-radius: 50%;
    transform: translate(-50%,-50%);
    opacity: 0;
    z-index: 4;
    pointer-events: none;
}
.intro-shockwave.active { animation: shockwave 1.2s cubic-bezier(.22,1,.36,1) forwards; }
@keyframes shockwave {
    0% { width: 10px; height: 10px; opacity: 1; border-width: 3px; }
    100% { width: 200vw; height: 200vw; opacity: 0; border-width: 1px; }
}

.intro-text-wrap {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
    z-index: 5;
    opacity: 0;
    pointer-events: none;
    transition: opacity .6s ease, transform .6s ease;
}
.intro-text-wrap.show { opacity: 1; }
.intro-text-wrap.fly { opacity: 0; transform: translate(-50%,-58%); }

.intro-title {
    font-family: 'Fraunces', serif;
    font-weight: 300;
    font-size: clamp(24px, 5vw, 60px);
    letter-spacing: -.02em;
    color: #F7F5F0;
    line-height: 1.1;
    margin: 0;
    display: flex; flex-wrap: wrap; justify-content: center; gap: .3em;
    text-shadow: 0 4px 24px rgba(0,0,0,.4);
}
.intro-word { display: inline-block; opacity: 0; transform: translateY(30px); transition: all .6s cubic-bezier(.22,1,.36,1); }
.intro-word.in { opacity: 1; transform: none; }
.intro-word:nth-child(4) { color: #C9A227; font-style: italic; }

.intro-divider {
    width: 0; height: 2px; background: #C9A227;
    margin: 20px auto;
    transition: width .8s cubic-bezier(.22,1,.36,1) .3s;
    box-shadow: 0 0 10px rgba(201,162,39,.5);
}
.intro-text-wrap.show .intro-divider { width: 100px; }

.intro-subtitle {
    font-family: 'Inter', sans-serif;
    font-size: clamp(10px, 1.2vw, 13px);
    letter-spacing: .5em;
    text-transform: uppercase;
    color: #C9A227;
    margin: 0;
    opacity: 0;
    transition: opacity .6s ease .8s;
    text-shadow: 0 2px 12px rgba(0,0,0,.4);
}
.intro-text-wrap.show .intro-subtitle { opacity: 1; }

.intro-skip-hint {
    position: absolute;
    bottom: 40px; left: 50%;
    transform: translateX(-50%);
    font-family: 'Inter', sans-serif;
    font-size: 10px;
    letter-spacing: .4em;
    text-transform: uppercase;
    color: rgba(247,245,240,.5);
    z-index: 6;
    opacity: 0;
    animation: hintFade 1s ease 1.2s forwards;
}
@keyframes hintFade { to { opacity: 1; } }

/* ===== SCROLL PROGRESS BAR ===== */
#scrollProgress {
    position: fixed; top: 0; left: 0; height: 3px; width: 0;
    background: linear-gradient(90deg, #C9A227, #D4AF37, #C9A227);
    z-index: 9999;
    box-shadow: 0 0 10px rgba(201,162,39,.6);
    transition: width .1s linear;
}

/* ===== REVEAL ANIMATIONS ===== */
.reveal-up { opacity: 0; transform: translateY(50px); transition: opacity 1s cubic-bezier(.22,1,.36,1), transform 1s cubic-bezier(.22,1,.36,1); }
.reveal-up.in { opacity: 1; transform: none; }
.reveal-left { opacity: 0; transform: translateX(-60px); transition: opacity 1s cubic-bezier(.22,1,.36,1), transform 1s cubic-bezier(.22,1,.36,1); }
.reveal-left.in { opacity: 1; transform: none; }
.reveal-right { opacity: 0; transform: translateX(60px); transition: opacity 1s cubic-bezier(.22,1,.36,1), transform 1s cubic-bezier(.22,1,.36,1); }
.reveal-right.in { opacity: 1; transform: none; }
.reveal-scale { opacity: 0; transform: scale(.85); transition: opacity 1s cubic-bezier(.22,1,.36,1), transform 1s cubic-bezier(.22,1,.36,1); }
.reveal-scale.in { opacity: 1; transform: none; }

.btn-magnetic { transition: transform .3s cubic-bezier(.34,1.56,.64,1); }
.hero-parallax { transition: transform .2s ease-out; will-change: transform; }

/* =====  GLASS CARDS (kartu kaca) ===== */
.stats-strip { position: relative; overflow: hidden; background: transparent; }
.stats-bg {
    position: absolute; inset: 0;
    background:
        radial-gradient(420px 220px at 12% 15%, rgba(201,162,39,.20), transparent 60%),
        radial-gradient(520px 260px at 88% 85%, rgba(11,34,57,.14), transparent 60%),
        radial-gradient(320px 180px at 55% 45%, rgba(212,175,55,.14), transparent 60%);
}
.glass-card {
    position: relative;
    background: rgba(255,255,255,.42);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255,255,255,.65);
    box-shadow: 0 8px 32px rgba(11,34,57,.08), inset 0 1px 0 rgba(255,255,255,.85);
    transition: transform .4s cubic-bezier(.22,1,.36,1), background .4s ease, box-shadow .4s ease;
}
.glass-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.95), transparent);
}
.glass-card:hover {
    transform: translateY(-8px);
    background: rgba(11,34,57,.88);
    box-shadow: 0 16px 40px rgba(11,34,57,.25), inset 0 1px 0 rgba(255,255,255,.25);
}
.glass-card:hover .counter { color: #C9A227; }
.glass-card:hover .editorial-label { color: #F7F5F0; }

.counter { display: inline-block; min-width: 60px; text-align: left; }

.hero-text-animate { opacity: 0; transform: translateY(30px); animation: heroTextIn 1s cubic-bezier(.22,1,.36,1) forwards; }
@keyframes heroTextIn { to { opacity: 1; transform: none; } }
.hero-text-animate.d1 { animation-delay: .1s; }
.hero-text-animate.d2 { animation-delay: .3s; }
.hero-text-animate.d3 { animation-delay: .5s; }
.hero-text-animate.d4 { animation-delay: .7s; }

.gold-sweep { position: relative; overflow: hidden; }
.gold-sweep::after {
    content: '';
    position: absolute; top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(201,162,39,.15), transparent);
    transition: left .8s ease;
}
.gold-sweep:hover::after { left: 100%; }
</style>

<!-- ============ SCROLL PROGRESS BAR ============ -->
<div id="scrollProgress"></div>

<!-- ============ HERO: DYNAMIC APPEARANCE SYSTEM ============ -->
<section id="heroSection" class="relative bg-navy text-ivory overflow-hidden">

    <?php if ($hero_photo): ?>
        <div class="hero-photo hero-parallax" style="background-image:url('<?= base_url('assets/uploads/' . $hero_photo) ?>')"></div>
    <?php else: ?>
        <div class="absolute inset-0 hero-pattern"></div>
    <?php endif; ?>

    <div id="auroraLayer" class="absolute inset-0 pointer-events-none"></div>
    <div id="campusLayer" class="absolute inset-0 pointer-events-none overflow-hidden"></div>
    <canvas id="heroParticles" class="absolute inset-0 w-full h-full pointer-events-none <?= $hero_particles === '1' ? '' : 'hidden' ?>"></canvas>

    <div class="absolute top-0 right-0 w-64 h-64 border-l border-b border-ivory/10 hidden md:block"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 border-r border-t border-ivory/10 hidden md:block"></div>
    <div class="hidden md:block absolute right-10 top-1/2 -translate-y-1/2 text-right select-none pointer-events-none">
        <div class="oversize-num text-gold/25">FT</div>
        <div class="oversize-num text-gold/25 mt-2">IK</div>
    </div>

    <div class="container mx-auto px-6 pt-24 md:pt-32 pb-16 relative z-10">
        <p class="editorial-label text-gold mb-6 hero-text-animate d1"><?= html_escape($hero_label) ?></p>
        <h1 class="font-serif font-light text-5xl md:text-7xl lg:text-8xl leading-[1.08] tracking-tight max-w-4xl text-balance mb-8 hero-text-animate d2">
            <?= hero_format($hero_title) ?>
        </h1>
        <p class="text-lg md:text-xl text-ivory/80 max-w-2xl leading-relaxed mb-10 hero-text-animate d3"><?= html_escape($hero_subtitle) ?></p>
        <div class="flex flex-wrap gap-4 hero-text-animate d4">
            <a href="<?= base_url('akademik') ?>" class="btn-gold btn-magnetic px-8 py-4 font-semibold uppercase tracking-editorial text-xs gold-sweep"><?= t('btn_explore') ?></a>
            <a href="<?= base_url('berita') ?>" class="btn-outline btn-magnetic px-8 py-4 font-semibold uppercase tracking-editorial text-xs"><?= t('btn_news') ?></a>
        </div>
    </div>

    <div id="robotStrip" class="robot-strip relative z-10 <?= $hero_anim === 'robot' ? '' : 'hidden' ?>">
        <div id="robotText" class="robot-track-text"></div>
        <div id="robot" class="robot walking">
            <span class="antenna"></span><span class="head"></span><span class="body"></span>
            <span class="leg l"></span><span class="leg r"></span>
        </div>
    </div>
</section>

<script>
window.HERO_FX = {
    template: '<?= html_escape($hero_anim) ?>',
    particles: <?= $hero_particles === '1' ? 'true' : 'false' ?>,
    welcome: <?= json_encode($welcome_text) ?>
};
</script>
<script src="<?= base_url('assets/js/hero-fx.js') ?>"></script>

<!-- ============  STATS STRIP (GLASS CARDS) ============ -->
<section class="stats-strip border-b border-gray-200">
    <div class="stats-bg"></div>
    <div class="container mx-auto px-6 py-10 md:py-14 relative">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-5">
            <div class="stat-item glass-card p-6 md:p-8 reveal-up">
                <div class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight counter" data-target="<?= $stats['lecturers'] ?>"><?= $stats['lecturers'] ?></div>
                <div class="editorial-label text-slate mt-3"><?= t('home_stats_lecturers') ?></div>
            </div>
            <div class="stat-item glass-card p-6 md:p-8 reveal-up" style="transition-delay:.1s">
                <div class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight counter" data-target="<?= $stats['students'] ?>"><?= $stats['students'] ?></div>
                <div class="editorial-label text-slate mt-3"><?= t('home_stats_students') ?></div>
            </div>
            <div class="stat-item glass-card p-6 md:p-8 reveal-up" style="transition-delay:.2s">
                <div class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight counter" data-target="<?= $stats['programs'] ?>"><?= $stats['programs'] ?></div>
                <div class="editorial-label text-slate mt-3"><?= t('home_stats_programs') ?></div>
            </div>
            <div class="stat-item glass-card p-6 md:p-8 reveal-up" style="transition-delay:.3s">
                <div class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight"><span class="counter" data-target="<?= $stats['research'] ?>"><?= $stats['research'] ?></span>+</div>
                <div class="editorial-label text-slate mt-3"><?= t('home_stats_research') ?></div>
            </div>
            <div class="stat-item glass-card p-6 md:p-8 reveal-up col-span-2 md:col-span-1" style="transition-delay:.4s">
                <div class="font-serif text-4xl md:text-5xl font-light text-gold-muted tracking-tight counter" data-target="<?= $stats['alumni'] ?>"><?= $stats['alumni'] ?></div>
                <div class="editorial-label text-slate mt-3"><?= get_site_lang() == 'en' ? 'Alumni Network' : 'Jaringan Alumni' ?></div>
            </div>
        </div>
    </div>
</section>

<!-- ============ LATEST NEWS ============ -->
<section class="py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4 reveal-up">
            <div>
                <p class="editorial-label text-gold-muted mb-4"><?= t('home_news_label') ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight"><?= t('home_news_title') ?></h2>
            </div>
            <a href="<?= base_url('berita') ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-xs">
                <?= t('home_see_all') ?>
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
        </div>

        <?php if (empty($latest_news)): ?>
            <p class="text-slate text-center py-10"><?= get_site_lang() == 'en' ? 'No published news yet.' : 'Belum ada berita yang dipublikasikan.' ?></p>
        <?php else: ?>
            <?php $featured = $latest_news[0]; ?>
            <div class="grid md:grid-cols-2 gap-12 mb-16">
                <div class="overflow-hidden bg-gray-200 aspect-[4/3] relative news-card reveal-left">
                    <?php if ($featured->featured_image): ?>
                        <img src="<?= base_url('assets/uploads/' . $featured->featured_image) ?>" class="w-full h-full object-cover news-image" alt="">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center">
                            <i class="fas fa-newspaper text-6xl text-gold/30"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col justify-center reveal-right">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="editorial-label text-gold-muted"><?= html_escape($featured->category_name ?? 'Berita') ?></span>
                        <span class="w-12 h-px bg-gold"></span>
                        <span class="text-xs text-slate uppercase tracking-wider"><?= date('d F Y', strtotime($featured->published_at)) ?></span>
                    </div>
                    <h3 class="font-serif text-3xl md:text-4xl font-light text-navy leading-tight mb-4"><?= html_escape($featured->title) ?></h3>
                    <p class="text-slate leading-relaxed mb-6">
                        <?= html_escape(character_limiter(strip_tags((string)$featured->excerpt ?: (string)$featured->content), 200)) ?>
                    </p>
                    <a href="<?= base_url('berita/detail/' . $featured->slug) ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-xs self-start">
                        <?= t('home_read_more') ?>
                        <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                    </a>
                </div>
            </div>

            <?php if (count($latest_news) > 1): ?>
            <div class="grid md:grid-cols-3 gap-8">
                <?php for ($i = 1; $i < count($latest_news); $i++): $n = $latest_news[$i]; ?>
                <article class="news-card group cursor-pointer reveal-up" style="transition-delay:<?= $i * .1 ?>s">
                    <div class="overflow-hidden bg-gray-200 aspect-[4/3] mb-5">
                        <?php if ($n->featured_image): ?>
                            <img src="<?= base_url('assets/uploads/' . $n->featured_image) ?>" class="w-full h-full object-cover news-image" alt="">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-navy-light to-navy flex items-center justify-center">
                                <i class="fas fa-newspaper text-4xl text-gold/30"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="editorial-label text-gold-muted"><?= html_escape($n->category_name ?? 'Berita') ?></span>
                        <span class="text-[10px] text-slate uppercase tracking-wider"><?= date('d M Y', strtotime($n->published_at)) ?></span>
                    </div>
                    <h4 class="font-serif text-xl font-medium text-navy leading-snug group-hover:text-gold-muted transition"><?= html_escape($n->title) ?></h4>
                    <a href="<?= base_url('berita/detail/' . $n->slug) ?>" class="inline-block mt-3 text-xs uppercase tracking-editorial text-slate hover:text-gold transition font-semibold"><?= t('home_read_more') ?> &rarr;</a>
                </article>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ============ PROGRAMS ============ -->
<section class="py-24 bg-white border-t border-b border-gray-200">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-12 gap-12">
            <div class="md:col-span-4 md:sticky md:top-32 self-start reveal-left">
                <p class="editorial-label text-gold-muted mb-4"><?= t('home_prog_label') ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy leading-tight tracking-tight">
                    <?php if (get_site_lang() == 'en'): ?>
                        Study <em class="italic text-gold-muted">Programs</em> of Excellence
                    <?php else: ?>
                        Program Studi <em class="italic text-gold-muted">unggulan</em> kami
                    <?php endif; ?>
                </h2>
                <div class="w-16 h-px bg-gold my-8"></div>
                <p class="text-slate leading-relaxed mb-8"><?= t('home_prog_desc') ?></p>
                <a href="<?= base_url('akademik') ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-xs">
                    <?= t('home_prog_all') ?>
                    <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
                </a>
            </div>
            <div class="md:col-span-8">
                <?php if (empty($programs)): ?>
                    <p class="text-slate"><?= get_site_lang() == 'en' ? 'No study programs yet.' : 'Belum ada program studi.' ?></p>
                <?php else: foreach ($programs as $idx => $prog): ?>
                <a href="<?= base_url('akademik/detail/' . $prog->slug) ?>" class="block py-8 border-t border-gray-200 group hover-lift reveal-up gold-sweep" style="transition-delay:<?= $idx * .05 ?>s">
                    <div class="grid grid-cols-12 gap-4 items-baseline">
                        <div class="col-span-2 md:col-span-1">
                            <span class="font-serif text-2xl md:text-3xl font-light text-gold-muted"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <div class="col-span-10 md:col-span-7">
                            <h3 class="font-serif text-2xl md:text-3xl font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($prog->name) ?></h3>
                            <p class="text-sm text-slate mt-2 leading-relaxed hidden md:block"><?= html_escape(character_limiter(strip_tags((string)$prog->description), 150)) ?></p>
                        </div>
                        <div class="col-span-12 md:col-span-4 md:text-right">
                            <span class="inline-block bg-navy text-ivory text-xs uppercase tracking-editorial px-3 py-1.5 font-semibold"><?= html_escape($prog->degree) ?></span>
                            <div class="text-xs text-slate mt-2 uppercase tracking-wider">
                                <?= t('home_prog_accred') ?>: <span class="text-gold-muted font-semibold"><?= html_escape($prog->accreditation ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; endif; ?>
                <div class="border-t border-gray-200"></div>
            </div>
        </div>
    </div>
</section>

<!-- ============ WHY CHOOSE US ============ -->
<section class="py-24 bg-ivory relative overflow-hidden">
    <div class="absolute -top-16 -right-8 font-serif text-[18rem] leading-none text-navy/5 select-none pointer-events-none hidden lg:block">W</div>
    <div class="container mx-auto px-6 relative">
        <div class="text-center max-w-3xl mx-auto mb-16 reveal-up">
            <p class="editorial-label text-gold-muted mb-4"><?= get_site_lang() == 'en' ? 'Why Choose Us' : 'Mengapa Kami' ?></p>
            <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
                <?= get_site_lang() == 'en' ? 'Building <em class="italic text-gold-muted">tomorrow\'s leaders</em> today' : 'Membangun <em class="italic text-gold-muted">pemimpin masa depan</em> hari ini' ?>
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php
            $whyItems = [
                ['icon'=>'fa-microscope','num'=>'01','title_en'=>'Research-Driven','title_id'=>'Berbasis Riset','desc_en'=>'Every curriculum is enriched with active research and real-world industry collaboration.','desc_id'=>'Setiap kurikulum diperkaya dengan riset aktif dan kolaborasi industri nyata.'],
                ['icon'=>'fa-globe','num'=>'02','title_en'=>'Global Network','title_id'=>'Jaringan Global','desc_en'=>'Partnerships with universities and companies across 15+ countries for exchange and career.','desc_id'=>'Kemitraan dengan universitas dan perusahaan di 15+ negara untuk pertukaran dan karir.'],
                ['icon'=>'fa-laptop-code','num'=>'03','title_en'=>'Industry Ready','title_id'=>'Siap Industri','desc_en'=>'Modern labs, certification programs, and internship pathways that match market needs.','desc_id'=>'Lab modern, program sertifikasi, dan jalur magang yang sesuai kebutuhan pasar.'],
            ];
            foreach ($whyItems as $i => $item):
            ?>
            <div class="bg-white p-8 border border-gray-200 reveal-up hover-lift relative overflow-hidden group gold-sweep" style="transition-delay:<?= $i * .15 ?>s">
                <div class="absolute -top-4 -right-2 font-serif text-[6rem] leading-none text-navy/5 select-none pointer-events-none group-hover:text-gold/10 transition"><?= $item['num'] ?></div>
                <div class="w-14 h-14 bg-navy text-gold flex items-center justify-center text-xl mb-6 group-hover:bg-gold group-hover:text-navy transition">
                    <i class="fas <?= $item['icon'] ?>"></i>
                </div>
                <h3 class="font-serif text-xl font-medium text-navy mb-3"><?= get_site_lang() == 'en' ? $item['title_en'] : $item['title_id'] ?></h3>
                <p class="text-sm text-slate leading-relaxed"><?= get_site_lang() == 'en' ? $item['desc_en'] : $item['desc_id'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ DEAN'S QUOTE ============ -->
<?php if (!empty($dean) && !empty($dean->message)): ?>
<section class="py-24 bg-navy text-ivory relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full hero-pattern opacity-30"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center reveal-scale">
            <?php if (!empty($dean->photo)): ?>
            <div class="w-24 h-24 mx-auto mb-6 rounded-full overflow-hidden border-2 border-gold/60 shadow-lg">
                <img src="<?= base_url('assets/uploads/' . $dean->photo) ?>" class="w-full h-full object-cover" alt="<?= html_escape($dean->name) ?>">
            </div>
            <?php else: ?>
            <i class="fas fa-quote-left text-5xl text-gold mb-8"></i>
            <?php endif; ?>
            <p class="font-serif text-2xl md:text-4xl font-light italic leading-relaxed mb-10">"<?= nl2br(html_escape($dean->message)) ?>"</p>
            <div class="flex items-center justify-center gap-4">
                <div class="w-16 h-px bg-gold"></div>
                <div class="text-left">
                    <p class="font-serif text-lg font-medium text-ivory"><?= html_escape($dean->name) ?></p>
                    <p class="editorial-label text-gold mt-1"><?= t('home_dean_title') ?></p>
                </div>
                <div class="w-16 h-px bg-gold"></div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ RESEARCH SPOTLIGHT ============ -->
<section class="py-24 bg-ivory-warm">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 reveal-up">
            <p class="editorial-label text-gold-muted mb-4"><?= t('home_res_label') ?></p>
            <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight max-w-3xl mx-auto leading-tight">
                <?php if (get_site_lang() == 'en'): ?>
                    Our research shapes the <em class="italic">future</em>
                <?php else: ?>
                    Riset kami membentuk <em class="italic">masa depan</em>
                <?php endif; ?>
            </h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white p-8 border border-gray-200 reveal-up hover-lift" style="transition-delay:0s">
                <div class="font-serif text-5xl font-light text-gold-muted mb-6"><span class="counter" data-target="<?= $stats['research'] ?>"><?= $stats['research'] ?></span>+</div>
                <h3 class="font-serif text-xl font-semibold text-navy mb-3"><?= t('home_res_1') ?></h3>
                <p class="text-sm text-slate leading-relaxed"><?= t('home_res_1d') ?></p>
            </div>
            <div class="bg-white p-8 border border-gray-200 reveal-up hover-lift" style="transition-delay:.15s">
                <div class="font-serif text-5xl font-light text-gold-muted mb-6">120+</div>
                <h3 class="font-serif text-xl font-semibold text-navy mb-3"><?= t('home_res_2') ?></h3>
                <p class="text-sm text-slate leading-relaxed"><?= t('home_res_2d') ?></p>
            </div>
            <div class="bg-white p-8 border border-gray-200 reveal-up hover-lift" style="transition-delay:.3s">
                <div class="font-serif text-5xl font-light text-gold-muted mb-6">30+</div>
                <h3 class="font-serif text-xl font-semibold text-navy mb-3"><?= t('home_res_3') ?></h3>
                <p class="text-sm text-slate leading-relaxed"><?= t('home_res_3d') ?></p>
            </div>
        </div>

        <?php if (!empty($research_spotlight)): ?>
        <div class="mb-12">
            <h3 class="font-serif text-2xl font-light text-navy mb-8 text-center reveal-up"><?= t('home_res_current') ?></h3>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($research_spotlight as $i => $r): ?>
                <div class="bg-white p-6 border border-gray-200 reveal-up hover-lift" style="transition-delay:<?= $i * .1 ?>s">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="editorial-label <?= $r->type == 'research' ? 'text-purple-600' : 'text-teal-600' ?>">
                            <?= $r->type == 'research' ? (get_site_lang() == 'en' ? 'Research' : 'Penelitian') : (get_site_lang() == 'en' ? 'Community Service' : 'Pengabdian') ?>
                        </span>
                        <span class="w-8 h-px bg-gold"></span>
                        <span class="text-xs text-slate"><?= $r->year ?></span>
                    </div>
                    <h4 class="font-serif text-lg font-medium text-navy leading-snug mb-3 line-clamp-3"><?= html_escape($r->title) ?></h4>
                    <p class="text-xs text-slate"><i class="fas fa-user-tie text-gold-muted mr-1"></i><?= html_escape(trim(($r->title_front ?? '') . ' ' . ($r->lecturer_name ?? ''))) ?></p>
                    <?php if ($r->funding_source): ?>
                    <p class="text-xs text-gold-muted mt-2"><i class="fas fa-coins mr-1"></i><?= html_escape($r->funding_source) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-center reveal-up">
            <a href="<?= base_url('riset') ?>" class="btn-primary px-8 py-4 font-semibold uppercase tracking-editorial text-xs inline-block"><?= t('home_res_all') ?></a>
        </div>
    </div>
</section>

<!-- ============ FEATURED LECTURERS ============ -->
<?php if (!empty($featured_lecturers)): ?>
<section class="py-24 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4 reveal-up">
            <div>
                <p class="editorial-label text-gold-muted mb-4"><?= get_site_lang() == 'en' ? 'Our Faculty' : 'Tenaga Pengajar' ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight">
                    <?= get_site_lang() == 'en' ? 'Meet our <em class="italic text-gold-muted">experts</em>' : 'Bertemu dengan <em class="italic text-gold-muted">para ahli</em> kami' ?>
                </h2>
            </div>
            <a href="<?= base_url('dosen') ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-xs">
                <?= get_site_lang() == 'en' ? 'All Lecturers' : 'Semua Dosen' ?>
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($featured_lecturers as $idx => $lec): ?>
            <div class="bg-ivory p-8 border border-gray-200 reveal-up hover-lift relative overflow-hidden group text-center" style="transition-delay:<?= $idx * .15 ?>s">
                <div class="absolute -top-4 -right-2 font-serif text-[6rem] leading-none text-navy/5 select-none pointer-events-none"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="w-24 h-24 mx-auto mb-5 rounded-full overflow-hidden border-2 border-gold/50 shadow-md relative">
                    <img src="<?= base_url('assets/uploads/' . $lec->photo) ?>" class="w-full h-full object-cover" alt="<?= html_escape($lec->name) ?>">
                </div>
                <p class="text-xs text-gold-muted uppercase tracking-wider mb-1"><?= html_escape($lec->title_front ?? '') ?></p>
                <h3 class="font-serif text-xl font-medium text-navy leading-tight mb-2"><?= html_escape($lec->name) ?></h3>
                <p class="text-xs text-gold-muted uppercase tracking-wider mb-3"><?= html_escape($lec->title_back ?? '') ?></p>
                <p class="text-sm text-slate leading-relaxed line-clamp-2"><?= html_escape(character_limiter($lec->expertise ?? '-', 80)) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ LATEST BLOG DOSEN ============ -->
<?php if (!empty($latest_blogs)): ?>
<section class="py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4 reveal-up">
            <div>
                <p class="editorial-label text-gold-muted mb-4"><?= get_site_lang() == 'en' ? 'Faculty Blog' : 'Blog Dosen' ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight">
                    <?= get_site_lang() == 'en' ? 'Ideas & <em class="italic text-gold-muted">insights</em>' : 'Ide & <em class="italic text-gold-muted">wawasan</em>' ?>
                </h2>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($latest_blogs as $i => $blog): ?>
            <article class="bg-white p-8 border border-gray-200 reveal-up hover-lift group" style="transition-delay:<?= $i * .15 ?>s">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gold/40 flex-shrink-0 bg-navy text-gold flex items-center justify-center font-serif text-xl font-bold">
                        <?php if (!empty($blog->lecturer_photo)): ?>
                            <img src="<?= base_url('assets/uploads/' . $blog->lecturer_photo) ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <?= strtoupper(substr($blog->lecturer_name ?? '?', 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="editorial-label text-gold-muted"><?= get_site_lang() == 'en' ? 'Blog Post' : 'Artikel' ?></span>
                            <span class="w-8 h-px bg-gold"></span>
                            <span class="text-xs text-slate"><?= date('d M Y', strtotime($blog->published_at)) ?></span>
                        </div>
                        <h3 class="font-serif text-xl font-medium text-navy leading-snug mb-2 group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($blog->title) ?></h3>
                        <p class="text-xs text-slate mb-3"><i class="fas fa-user-tie text-gold-muted mr-1"></i><?= html_escape($blog->lecturer_name ?? '-') ?></p>
                        <p class="text-sm text-slate leading-relaxed line-clamp-3"><?= html_escape(character_limiter(strip_tags($blog->content ?? ''), 150)) ?></p>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ ACHIEVEMENTS ============ -->
<?php if (!empty($achievements)): ?>
<section class="py-24 bg-ivory-warm">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4 reveal-up">
            <div>
                <p class="editorial-label text-gold-muted mb-4"><?= t('home_ach_label') ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight">
                    <?php if (get_site_lang() == 'en'): ?>
                        Our <em class="italic text-gold-muted">students'</em> achievements
                    <?php else: ?>
                        Prestasi <em class="italic text-gold-muted">mahasiswa</em> kami
                    <?php endif; ?>
                </h2>
            </div>
            <a href="<?= base_url('prestasi') ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-xs">
                <?= t('home_ach_all') ?>
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach ($achievements as $i => $a):
                $level_label = [
                    'international' => get_site_lang() == 'en' ? 'International' : 'Internasional',
                    'national'      => get_site_lang() == 'en' ? 'National' : 'Nasional',
                    'regional'      => 'Regional',
                    'university'    => get_site_lang() == 'en' ? 'University' : 'Universitas',
                    'faculty'       => get_site_lang() == 'en' ? 'Faculty' : 'Fakultas',
                ][$a->level] ?? ucfirst($a->level);
            ?>
            <div class="bg-white p-8 border border-gray-200 reveal-up hover-lift relative overflow-hidden" style="transition-delay:<?= $i * .1 ?>s">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-gold/10 to-transparent"></div>
                <div class="relative">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-trophy text-gold text-2xl"></i>
                        <span class="editorial-label text-gold-muted"><?= $level_label ?></span>
                    </div>
                    <h3 class="font-serif text-xl font-medium text-navy leading-snug mb-3 line-clamp-3"><?= html_escape($a->achievement_title) ?></h3>
                    <p class="text-sm text-slate mb-2"><i class="fas fa-user-graduate text-gold-muted mr-1"></i><?= html_escape($a->student_name) ?></p>
                    <?php if ($a->prodi_name): ?>
                    <p class="text-xs text-slate"><?= html_escape($a->prodi_name) ?> &bull; <?= $a->year ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ FEATURED ALUMNI ============ -->
<?php if (!empty($featured_alumni)): ?>
<section class="py-24 bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute -bottom-16 -right-8 font-serif text-[16rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">A</div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4 reveal-up">
            <div>
                <p class="editorial-label text-gold mb-4"><?= get_site_lang() == 'en' ? 'Alumni Spotlight' : 'Sorotan Alumni' ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light tracking-tight">
                    <?= get_site_lang() == 'en' ? 'Our <em class="italic text-gold">graduates</em> lead the way' : '<em class="italic text-gold">Lulusan</em> kami memimpin perubahan' ?>
                </h2>
            </div>
            <a href="<?= base_url('alumni') ?>" class="link-arrow text-ivory font-semibold uppercase tracking-editorial text-xs">
                <?= get_site_lang() == 'en' ? 'View Directory' : 'Direktori Alumni' ?>
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <?php foreach ($featured_alumni as $idx => $a): ?>
            <div class="bg-white/5 backdrop-blur-sm border border-ivory/10 p-8 reveal-up hover-lift relative overflow-hidden group" style="transition-delay:<?= $idx * .15 ?>s">
                <div class="absolute -top-4 -right-2 font-serif text-[6rem] leading-none text-gold/10 select-none pointer-events-none group-hover:text-gold/20 transition"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gold/60 flex-shrink-0 bg-navy-light text-gold flex items-center justify-center font-serif text-xl font-bold">
                        <?php if (!empty($a->photo)): ?>
                            <img src="<?= base_url('assets/uploads/' . $a->photo) ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <?= strtoupper(substr($a->full_name, 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-serif text-lg font-medium text-ivory leading-tight mb-1"><?= html_escape($a->full_name) ?></h3>
                        <span class="bg-gold text-navy text-[9px] uppercase tracking-wider font-bold px-2 py-0.5">Class of <?= $a->graduation_year ?></span>
                    </div>
                </div>
                <p class="text-sm text-ivory/90 mb-1 leading-tight">
                    <?= html_escape($a->current_position ?? '-') ?>
                    <?php if ($a->company): ?>
                        <span class="text-gold font-semibold">@ <?= html_escape($a->company) ?></span>
                    <?php endif; ?>
                </p>
                <p class="text-xs text-ivory/60 mb-3">
                    <i class="fas fa-map-marker-alt text-gold-muted mr-1"></i><?= html_escape($a->city ?? '') ?><?= $a->country && $a->country !== 'Indonesia' ? ', ' . html_escape($a->country) : '' ?>
                </p>
                <p class="text-xs text-ivory/70 italic"><?= html_escape($a->prodi_name ?? '-') ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-gold text-navy p-8 md:p-10 text-center relative overflow-hidden reveal-scale">
            <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-navy/20"></div>
            <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-navy/20"></div>
            <div class="relative max-w-2xl mx-auto">
                <p class="editorial-label text-navy/60 mb-3"><?= get_site_lang() == 'en' ? 'Are you our alumni?' : 'Anda alumni kami?' ?></p>
                <h3 class="font-serif text-2xl md:text-3xl font-light tracking-tight mb-6">
                    <?= get_site_lang() == 'en' ? 'Join the directory & reconnect' : 'Bergabung dengan direktori & terhubung kembali' ?>
                </h3>
                <a href="<?= base_url('alumni/register') ?>" class="btn-primary inline-block px-10 py-4 font-semibold uppercase tracking-editorial text-xs">
                    <i class="fas fa-user-plus mr-2"></i><?= get_site_lang() == 'en' ? 'Register Now' : 'Daftar Sekarang' ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ UPCOMING EVENTS ============ -->
<?php if (!empty($upcoming_events)): ?>
<section class="py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4 reveal-up">
            <div>
                <p class="editorial-label text-gold-muted mb-4"><?= get_site_lang() == 'en' ? 'Academic Calendar' : 'Kalender Akademik' ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight">
                    <?= get_site_lang() == 'en' ? 'Upcoming <em class="italic text-gold-muted">events</em>' : 'Agenda <em class="italic text-gold-muted">mendatang</em>' ?>
                </h2>
            </div>
            <a href="<?= base_url('akademik/kalender') ?>" class="link-arrow text-navy font-semibold uppercase tracking-editorial text-xs">
                <?= get_site_lang() == 'en' ? 'Full Calendar' : 'Kalender Lengkap' ?>
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M0 5h18M14 1l4 4-4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
        </div>

        <div class="space-y-4">
            <?php foreach ($upcoming_events as $i => $ev):
                $ev_title = $ev->event_name ?? ($ev->title ?? 'Agenda');
                $ev_desc  = $ev->description ?? '';
                $ev_cat   = $ev->category ?? 'Agenda';
                $ev_start = $ev->start_date;
                $ev_end   = !empty($ev->end_date) ? $ev->end_date : $ev_start;
            ?>
            <div class="bg-white border border-gray-200 p-6 md:p-8 reveal-up hover-lift group flex items-center gap-6" style="transition-delay:<?= $i * .1 ?>s">
                <div class="w-20 h-20 bg-navy text-ivory flex flex-col items-center justify-center flex-shrink-0 group-hover:bg-gold group-hover:text-navy transition">
                    <div class="font-serif text-2xl leading-none"><?= date('d', strtotime($ev_start)) ?></div>
                    <div class="text-[9px] uppercase tracking-editorial text-gold mt-1 group-hover:text-navy transition"><?= date('M Y', strtotime($ev_start)) ?></div>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="editorial-label text-gold-muted"><?= html_escape($ev_cat) ?></span>
                    <h3 class="font-serif text-xl font-medium text-navy leading-snug mt-2 mb-1 group-hover:text-gold-muted transition"><?= html_escape($ev_title) ?></h3>
                    <?php if ($ev_desc !== ''): ?>
                    <p class="text-sm text-slate line-clamp-2"><?= html_escape(character_limiter($ev_desc, 150)) ?></p>
                    <?php endif; ?>
                </div>
                <div class="hidden md:flex items-center gap-2 text-xs text-slate flex-shrink-0">
                    <i class="fas fa-clock text-gold-muted"></i>
                    <span class="font-mono"><?= date('d M', strtotime($ev_start)) ?><?php if ($ev_end != $ev_start): ?> - <?= date('d M', strtotime($ev_end)) ?><?php endif; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ VIDEO TOUR ============ -->
<?php if (!empty($video_enabled) && ($video_yt_id ?? '')): ?>
<section class="py-24 bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern opacity-30"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-12 reveal-up">
            <p class="editorial-label text-gold mb-4"><?= t('home_video_label') ?></p>
            <h2 class="font-serif text-4xl md:text-6xl font-light tracking-tight max-w-3xl mx-auto leading-tight"><?= html_escape($video_title) ?></h2>
            <?php if ($video_subtitle): ?>
            <p class="text-ivory/70 mt-4 max-w-2xl mx-auto text-lg leading-relaxed"><?= html_escape($video_subtitle) ?></p>
            <?php endif; ?>
        </div>

        <div class="max-w-5xl mx-auto reveal-scale">
            <div class="relative aspect-video bg-navy-deep border border-gold/20 overflow-hidden group cursor-pointer" id="videoWrapper">
                <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-500 group-hover:opacity-60"
                    style="background-image: url('https://img.youtube.com/vi/<?= $video_yt_id ?>/maxresdefault.jpg')">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-deep/80 via-transparent to-transparent"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center z-10 transition-transform duration-500 group-hover:scale-110">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full bg-gold animate-ping opacity-40" style="width:100px;height:100px"></div>
                        <div class="relative w-20 h-20 md:w-24 md:h-24 rounded-full bg-gold flex items-center justify-center shadow-2xl">
                            <i class="fas fa-play text-navy text-2xl md:text-3xl ml-1"></i>
                        </div>
                    </div>
                </div>
                <div class="absolute top-4 left-4 w-12 h-12 border-t-2 border-l-2 border-gold"></div>
                <div class="absolute bottom-4 right-4 w-12 h-12 border-b-2 border-r-2 border-gold"></div>
                <div class="absolute bottom-6 left-6 bg-gold text-navy px-3 py-1 text-xs uppercase tracking-editorial font-bold">
                    <i class="fas fa-film mr-1"></i><?= get_site_lang() == 'en' ? 'Campus Tour' : 'Video Tour' ?>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mt-10 max-w-2xl mx-auto">
                <div class="text-center reveal-up">
                    <div class="font-serif text-3xl md:text-4xl font-light text-gold">24/7</div>
                    <p class="text-xs uppercase tracking-editorial text-ivory/60 mt-2"><?= get_site_lang() == 'en' ? 'Lab Access' : 'Akses Lab' ?></p>
                </div>
                <div class="text-center reveal-up" style="transition-delay:.15s">
                    <div class="font-serif text-3xl md:text-4xl font-light text-gold">15+</div>
                    <p class="text-xs uppercase tracking-editorial text-ivory/60 mt-2"><?= get_site_lang() == 'en' ? 'Modern Facilities' : 'Fasilitas Modern' ?></p>
                </div>
                <div class="text-center reveal-up" style="transition-delay:.3s">
                    <div class="font-serif text-3xl md:text-4xl font-light text-gold">50Ha</div>
                    <p class="text-xs uppercase tracking-editorial text-ivory/60 mt-2"><?= get_site_lang() == 'en' ? 'Campus Area' : 'Luas Kampus' ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="videoModal" class="fixed inset-0 bg-navy-deep/95 backdrop-blur-sm z-[9999] items-center justify-center p-6 transition-opacity duration-300" style="display:none;">
    <button id="closeVideo" class="absolute top-6 right-6 w-12 h-12 bg-gold text-navy rounded-full flex items-center justify-center hover:bg-gold-muted transition z-10">
        <i class="fas fa-times text-xl"></i>
    </button>
    <div class="w-full max-w-5xl aspect-video">
        <iframe id="videoFrame" class="w-full h-full border-0" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<script>
(function() {
    var ytId = '<?= $video_yt_id ?>';
    var wrapper = document.getElementById('videoWrapper');
    var modal = document.getElementById('videoModal');
    var frame = document.getElementById('videoFrame');
    var closeBtn = document.getElementById('closeVideo');
    if (!wrapper) return;

    wrapper.addEventListener('click', function() {
        if (ytId) {
            frame.src = 'https://www.youtube.com/embed/' + ytId + '?autoplay=1&rel=0';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    });
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        frame.src = '';
        document.body.style.overflow = '';
    });
    modal.addEventListener('click', function(e) { if (e.target === modal) closeBtn.click(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeBtn.click();
    });
})();
</script>
<?php endif; ?>

<!-- ============ SCRIPT UTAMA ============ -->
<script>
(function(){
    'use strict';

    // ===== 1. INTRO RINGAN: blur → garis → ledakan → tulisan → fade =====
    var overlay = document.getElementById('introOverlay');
    var canvas  = document.getElementById('introCanvas');
    var ctx     = canvas ? canvas.getContext('2d') : null;
    var reduce  = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function finishIntro(){
        if(!overlay || overlay.dataset.finished) return;
        overlay.dataset.finished = '1';
        overlay.classList.add('done');
        setTimeout(function(){ if(overlay.parentNode) overlay.parentNode.removeChild(overlay); }, 1100);
    }

    if(reduce || !overlay || !ctx){
        finishIntro();
    } else {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        var particles = [], running = true, doneFlag = false;
        var colors = ['#C9A227','#D4AF37','#F7F5F0','#FFE8A0'];

        function burst(x, y, n, power){
            for(var i=0;i<n;i++){
                var a = Math.random()*Math.PI*2, sp = (Math.random()*0.7+0.3)*power;
                particles.push({x:x,y:y,vx:Math.cos(a)*sp,vy:Math.sin(a)*sp,al:1,sz:Math.random()*2+1,c:colors[i%4]});
            }
        }
        function loop(){
            if(!running) return;
            ctx.clearRect(0,0,canvas.width,canvas.height);
            for(var i=particles.length-1;i>=0;i--){
                var p=particles[i];
                p.x+=p.vx; p.y+=p.vy; p.vy+=0.12; p.vx*=0.98; p.vy*=0.98; p.al-=0.02;
                if(p.al<=0){ particles.splice(i,1); continue; }
                ctx.globalAlpha=p.al; ctx.fillStyle=p.c;
                ctx.fillRect(p.x,p.y,p.sz,p.sz);
            }
            ctx.globalAlpha=1;
            if(particles.length>0 || !doneFlag){ requestAnimationFrame(loop); }
            else { running=false; ctx.clearRect(0,0,canvas.width,canvas.height); }
        }
        loop();

        var cx = window.innerWidth/2, cy = window.innerHeight/2;

        // 0.6s — garis bertemu → ledakan kembang api (TANPA getaran)
        setTimeout(function(){
            var shock=document.querySelector('.intro-shockwave'); if(shock) shock.classList.add('active');
            burst(cx,cy,60,7);
        },600);
        setTimeout(function(){ burst(cx-110,cy-45,20,5); },750);
        setTimeout(function(){ burst(cx+110,cy-45,20,5); },900);
        setTimeout(function(){ document.querySelectorAll('.intro-line').forEach(function(l){ l.style.opacity='0'; }); },650);

        // 1.0s — tulisan muncul
        setTimeout(function(){
            var w=document.querySelector('.intro-text-wrap'); if(w) w.classList.add('show');
            document.querySelectorAll('.intro-word').forEach(function(el,i){ setTimeout(function(){ el.classList.add('in'); }, i*100); });
        },1000);

        // 2.0s — tulisan melayang pergi
        setTimeout(function(){
            var w=document.querySelector('.intro-text-wrap');
            if(w) w.classList.add('fly');
        },2000);

        // 2.3s — veil memudar
        setTimeout(function(){ doneFlag=true; finishIntro(); },2300);

        overlay.addEventListener('click', function(){ doneFlag=true; finishIntro(); });
    }

    // ===== 2. SCROLL PROGRESS =====
    var progressBar = document.getElementById('scrollProgress');
    if(progressBar){
        window.addEventListener('scroll', function(){
            var scrolled = window.scrollY;
            var maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            var pct = maxScroll > 0 ? (scrolled / maxScroll) * 100 : 0;
            progressBar.style.width = pct + '%';
        });
    }

    // ===== 3. REVEAL ON SCROLL =====
    var revealObs = new IntersectionObserver(function(entries){
        entries.forEach(function(en){
            if(en.isIntersecting){ en.target.classList.add('in'); revealObs.unobserve(en.target); }
        });
    }, {threshold: 0.15, rootMargin: '0px 0px -80px 0px'});
    document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right, .reveal-scale').forEach(function(el){
        revealObs.observe(el);
    });

    // ===== 4. COUNTER ANIMATION =====
    var counterObs = new IntersectionObserver(function(entries){
        entries.forEach(function(en){
            if(!en.isIntersecting) return;
            var el = en.target;
            if(el.dataset.done) return;
            el.dataset.done = '1';

            var target = parseInt(el.dataset.target) || 0;
            if(target === 0){ el.textContent = '0'; counterObs.unobserve(el); return; }

            var duration = 1800, start = performance.now();
            function anim(now){
                var elapsed = now - start;
                var progress = Math.min(elapsed / duration, 1);
                var easeOut = 1 - Math.pow(1 - progress, 3);
                var current = Math.floor(easeOut * target);
                el.textContent = current.toLocaleString('id-ID');
                if(progress < 1) requestAnimationFrame(anim);
                else el.textContent = target.toLocaleString('id-ID');
            }
            requestAnimationFrame(anim);
            counterObs.unobserve(el);
        });
    }, {threshold: 0.3});
    document.querySelectorAll('.counter').forEach(function(c){ counterObs.observe(c); });

    // ===== 5. PARALLAX HERO =====
    var heroPhoto = document.querySelector('.hero-parallax');
    if(heroPhoto){
        window.addEventListener('scroll', function(){
            var y = window.scrollY;
            if(y < window.innerHeight) heroPhoto.style.transform = 'translateY(' + (y * 0.4) + 'px)';
        });
    }

    // ===== 6. MAGNETIC BUTTON =====
    document.querySelectorAll('.btn-magnetic').forEach(function(btn){
        btn.addEventListener('mousemove', function(e){
            var rect = btn.getBoundingClientRect();
            var x = e.clientX - rect.left - rect.width/2;
            var y = e.clientY - rect.top - rect.height/2;
            btn.style.transform = 'translate(' + (x * 0.2) + 'px, ' + (y * 0.3) + 'px)';
        });
        btn.addEventListener('mouseleave', function(){ btn.style.transform = ''; });
    });
})();
</script>