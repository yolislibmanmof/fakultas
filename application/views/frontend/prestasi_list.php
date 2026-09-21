<?php
$EN = (get_site_lang() == 'en');
$level_counts = ['international' => 0, 'national' => 0, 'regional' => 0, 'university' => 0, 'faculty' => 0];
foreach ($achievements as $a) { if (isset($level_counts[$a->level])) $level_counts[$a->level]++; }

$level_rank = ['international' => 5, 'national' => 4, 'regional' => 3, 'university' => 2, 'faculty' => 1];
$podium_pool = $achievements;
usort($podium_pool, function ($x, $y) use ($level_rank) {
    $rx = $level_rank[$x->level] ?? 0; $ry = $level_rank[$y->level] ?? 0;
    if ($rx !== $ry) return $ry - $rx;
    return ((int)$y->year) - ((int)$x->year);
});
$top3 = array_slice($podium_pool, 0, 3);
$pod_slots = [null, null, null];
if (isset($top3[0])) $pod_slots[0] = $top3[0];
if (isset($top3[1])) $pod_slots[1] = $top3[1];
if (isset($top3[2])) $pod_slots[2] = $top3[2];

$years = [];
foreach ($achievements as $a) { if (!in_array($a->year, $years)) $years[] = $a->year; }
rsort($years);

$year_counts = [];
foreach ($achievements as $a) { $year_counts[$a->year] = ($year_counts[$a->year] ?? 0) + 1; }
ksort($year_counts);
$max_year = max($year_counts) ?: 1;

// Champion Spotlight: mahasiswa dengan prestasi terbanyak
$champ_map = [];
foreach ($achievements as $a) {
    $k = strtolower(trim($a->student_name));
    if (!isset($champ_map[$k])) $champ_map[$k] = ['name' => $a->student_name, 'count' => 0, 'top_level' => 0, 'prodi' => $a->prodi_name ?? ''];
    $champ_map[$k]['count']++;
    $champ_map[$k]['top_level'] = max($champ_map[$k]['top_level'], $level_rank[$a->level] ?? 0);
}
usort($champ_map, function($a,$b){ return $b['count'] - $a['count'] ?: $b['top_level'] - $a['top_level']; });
$champions = array_slice($champ_map, 0, 5);

$level_meta_map = [
    'international' => ['label' => $EN ? 'International' : 'Internasional', 'badge' => 'bg-gold text-navy', 'dot' => '#C9A227'],
    'national'      => ['label' => $EN ? 'National' : 'Nasional', 'badge' => 'bg-blue-500 text-ivory', 'dot' => '#3b82f6'],
    'regional'      => ['label' => 'Regional', 'badge' => 'bg-green-500 text-ivory', 'dot' => '#22c55e'],
    'university'    => ['label' => $EN ? 'University' : 'Universitas', 'badge' => 'bg-purple-500 text-ivory', 'dot' => '#a855f7'],
    'faculty'       => ['label' => $EN ? 'Faculty' : 'Fakultas', 'badge' => 'bg-gray-500 text-ivory', 'dot' => '#6b7280'],
];

// 🇮 Kota-kota Indonesia: fokus Maumere/Flores/NTT + kota besar nasional
$intl_cities = [
    ['city'=>'Maumere','flag'=>'🇮🇩'], ['city'=>'Jakarta','flag'=>'🇮🇩'], ['city'=>'Surabaya','flag'=>'🇮🇩'],
    ['city'=>'Ende','flag'=>'🇮🇩'], ['city'=>'Kupang','flag'=>'🇮🇩'], ['city'=>'Yogyakarta','flag'=>'🇮🇩'],
    ['city'=>'Labuan Bajo','flag'=>'🇮🇩'], ['city'=>'Denpasar','flag'=>'🇮🇩'], ['city'=>'Larantuka','flag'=>'🇮🇩'],
    ['city'=>'Makassar','flag'=>'🇮🇩'], ['city'=>'Ruteng','flag'=>'🇮'], ['city'=>'Bandung','flag'=>'🇮🇩'],
];
$intl_count = 0;
foreach ($achievements as $a) {
    if (true) { // tampilkan semua prestasi di Panggung Nusantara
        $c = $intl_cities[$intl_count % count($intl_cities)];
        $intl_points[] = [
            'city' => $c['city'], 'flag' => $c['flag'],
            'title' => $a->achievement_title, 'student' => $a->student_name,
            'year' => $a->year, 'organizer' => $a->organizer ?? '',
        ];
        $intl_count++;
    }
}
?>
<style>
/* ===== THEME VARIABLES (3 modes) ===== */
.tr-page {
    --tr-bg: #F7F5F0; --tr-surface: #fff; --tr-text: #0B2239; --tr-muted: #64748b;
    --tr-border: #e5e7eb; --tr-gold: #C9A227; --tr-navy: #0B2239; --tr-ivory: #F7F5F0;
}
.tr-page.theme-gold {
    --tr-bg: #0B2239; --tr-surface: #13334F; --tr-text: #F7F5F0; --tr-muted: #cbd5e1;
    --tr-border: rgba(201,162,39,.25); --tr-gold: #D4AF37; --tr-navy: #061420; --tr-ivory: #F7F5F0;
}
.tr-page.theme-midnight {
    --tr-bg: #0a0a0a; --tr-surface: #1a1a1a; --tr-text: #fff; --tr-muted: #888;
    --tr-border: rgba(201,162,39,.15); --tr-gold: #FFD700; --tr-navy: #000; --tr-ivory: #fff;
}
.tr-page { background: var(--tr-bg); color: var(--tr-text); transition: background .5s, color .5s; }
.tr-page .tr-surface { background: var(--tr-surface); }
.tr-page .tr-border { border-color: var(--tr-border); }
.tr-page .tr-muted-text { color: var(--tr-muted); }

/* ===== CINEMATIC CURTAIN INTRO ===== */
.tr-curtain { position: fixed; inset: 0; z-index: 9999; pointer-events: none; display: flex; }
.tr-curtain-half {
    flex: 1; background: linear-gradient(180deg, #061420 0%, #0B2239 50%, #13334F 100%);
    position: relative; overflow: hidden;
    transition: transform 1.2s cubic-bezier(.77,0,.18,1);
}
.tr-curtain-half::before {
    content: ''; position: absolute; inset: 0;
    background: repeating-linear-gradient(90deg,
        transparent 0, transparent 18px,
        rgba(201,162,39,.08) 18px, rgba(201,162,39,.08) 20px);
}
.tr-curtain-half::after {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse at top, rgba(201,162,39,.2), transparent 60%);
}
.tr-curtain-left { transform: translateX(0); }
.tr-curtain-right { transform: translateX(0); }
.tr-curtain.done .tr-curtain-left { transform: translateX(-100%); }
.tr-curtain.done .tr-curtain-right { transform: translateX(100%); }
.tr-curtain-center {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    z-index: 2; text-align: center; opacity: 1;
    transition: opacity .3s .4s;
}
.tr-curtain.done .tr-curtain-center { opacity: 0; }
.tr-curtain-title {
    font-family: 'Fraunces', serif; color: #C9A227;
    font-size: clamp(2rem, 6vw, 4.5rem); font-weight: 300; letter-spacing: -.03em;
    text-shadow: 0 4px 30px rgba(201,162,39,.5);
}
.tr-curtain-sub {
    color: rgba(247,245,240,.7); font-size: .8rem; letter-spacing: .4em; text-transform: uppercase;
    margin-top: 1rem;
}

/* ===== HERO ===== */
.tr-hero { position: relative; overflow: hidden; background: linear-gradient(165deg, #061420 0%, #0B2239 60%, #13334F 100%); }
.tr-spot {
    position: absolute; top: -30%; left: 50%; width: 220%; height: 150%;
    transform: translateX(-50%); transform-origin: 50% 0;
    background: conic-gradient(from 0deg at 50% 0%,
        transparent 0deg, rgba(201,162,39,.14) 18deg, transparent 38deg,
        transparent 142deg, rgba(201,162,39,.09) 160deg, transparent 180deg);
    animation: trSway 9s ease-in-out infinite alternate; pointer-events: none;
}
@keyframes trSway { from { transform: translateX(-50%) rotate(-7deg); } to { transform: translateX(-50%) rotate(7deg); } }
#trConstellation { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2; }
#trConfetti { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 5; }
.tr-shooting-star {
    position: absolute; width: 80px; height: 1px;
    background: linear-gradient(90deg, transparent, #C9A227, transparent);
    opacity: 0; pointer-events: none; z-index: 1;
}

/* ===== 3D ROTATING TROPHY ===== */
.tr-trophy-3d {
    position: relative; width: 180px; height: 220px; margin: 2rem auto 0;
    perspective: 800px;
}
.tr-trophy-inner {
    position: relative; width: 100%; height: 100%;
    transform-style: preserve-3d;
    animation: trophyRotate 12s linear infinite;
}
@keyframes trophyRotate {
    0%   { transform: rotateY(0deg)   rotateX(5deg); }
    100% { transform: rotateY(360deg) rotateX(5deg); }
}
.tr-trophy-face {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Fraunces', serif; font-size: 5rem;
    background: linear-gradient(135deg, #D4AF37 0%, #C9A227 50%, #B8941F 100%);
    border: 3px solid #FFE8A0;
    box-shadow: 0 0 60px rgba(201,162,39,.6), inset 0 0 30px rgba(0,0,0,.2);
    border-radius: 10px;
    color: #0B2239;
}
.tr-trophy-face:nth-child(1) { transform: translateZ(90px); }
.tr-trophy-face:nth-child(2) { transform: rotateY(90deg) translateZ(90px); }
.tr-trophy-face:nth-child(3) { transform: rotateY(180deg) translateZ(90px); }
.tr-trophy-face:nth-child(4) { transform: rotateY(270deg) translateZ(90px); }
.tr-trophy-face i { filter: drop-shadow(0 4px 10px rgba(0,0,0,.3)); }
@media (max-width: 767px) {
    .tr-trophy-3d { width: 120px; height: 150px; }
    .tr-trophy-face { font-size: 3.5rem; }
    .tr-trophy-face:nth-child(1) { transform: translateZ(60px); }
    .tr-trophy-face:nth-child(2) { transform: rotateY(90deg) translateZ(60px); }
    .tr-trophy-face:nth-child(3) { transform: rotateY(180deg) translateZ(60px); }
    .tr-trophy-face:nth-child(4) { transform: rotateY(270deg) translateZ(60px); }
}

/* ===== FLOAT ICONS ===== */
.tr-float { position: absolute; color: rgba(201,162,39,.14); animation: trFloat 7s ease-in-out infinite alternate; pointer-events: none; }
@keyframes trFloat { from { transform: translateY(0) rotate(-8deg); } to { transform: translateY(-26px) rotate(8deg); } }

/* ===== PODIUM ===== */
.tr-podium { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; align-items: end; max-width: 860px; margin: 0 auto; }
.tr-pod { text-align: center; }
.tr-pod-avatar {
    width: 76px; height: 76px; margin: 0 auto .75rem; border-radius: 50%;
    background: #13334F; color: #C9A227; display: flex; align-items: center; justify-content: center;
    font-family: 'Fraunces', serif; font-size: 1.6rem; font-weight: 700;
    border: 3px solid rgba(201,162,39,.4); position: relative; transition: transform .3s ease;
}
.tr-pod-avatar:hover { transform: scale(1.1) rotate(-4deg); }
.tr-pod[data-rank="1"] .tr-pod-avatar { width: 96px; height: 96px; font-size: 2rem; border-color: #C9A227; box-shadow: 0 0 34px rgba(201,162,39,.45); }
.tr-pod-crown { position: absolute; top: -26px; left: 50%; transform: translateX(-50%); color: #C9A227; font-size: 1.2rem; animation: trFloat 3s ease-in-out infinite alternate; }
.tr-pod-name { font-family: 'Fraunces', serif; font-weight: 600; color: #F7F5F0; font-size: .95rem; line-height: 1.25; }
.tr-pod-title { font-size: .68rem; color: rgba(247,245,240,.6); margin-top: .25rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.tr-pod-bar {
    margin-top: .9rem; display: flex; align-items: flex-start; justify-content: center; padding-top: .8rem;
    font-family: 'Fraunces', serif; font-weight: 700; font-size: 2rem; color: rgba(6,20,32,.55);
    transform: scaleY(0); transform-origin: bottom; transition: transform 1s cubic-bezier(.22,1,.36,1);
}
.tr-podium.in .tr-pod-bar { transform: scaleY(1); }
.tr-pod[data-rank="1"] .tr-pod-bar { height: 130px; background: linear-gradient(180deg, #D4AF37, #B8941F); transition-delay: .2s; }
.tr-pod[data-rank="2"] .tr-pod-bar { height: 96px;  background: linear-gradient(180deg, #cbd5e1, #94a3b8); transition-delay: .45s; }
.tr-pod[data-rank="3"] .tr-pod-bar { height: 72px;  background: linear-gradient(180deg, #d6a06b, #b45309); transition-delay: .7s; }
@media (max-width: 767px) {
    .tr-podium { grid-template-columns: 1fr; align-items: stretch; }
    .tr-pod { display: flex; align-items: center; gap: 1rem; text-align: left; }
    .tr-pod-avatar { margin: 0; flex-shrink: 0; }
    .tr-pod-bar { display: none; }
    .tr-pod[data-rank="1"] { order: 1; } .tr-pod[data-rank="2"] { order: 2; } .tr-pod[data-rank="3"] { order: 3; }
}

/* ===== MARQUEE ===== */
.tr-marquee { overflow: hidden; background: #0B2239; border-top: 1px solid rgba(201,162,39,.25); border-bottom: 1px solid rgba(201,162,39,.25); }
.tr-marquee-track { display: flex; gap: 3rem; padding: .9rem 0; white-space: nowrap; width: max-content; animation: trMarquee 45s linear infinite; }
.tr-marquee:hover .tr-marquee-track { animation-play-state: paused; }
@keyframes trMarquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.tr-mq-item { display: inline-flex; align-items: center; gap: .6rem; color: rgba(247,245,240,.75); font-size: .8rem; }
.tr-mq-item i { color: #C9A227; }
.tr-mq-item b { color: #F7F5F0; font-weight: 600; }

/* ===== CHAMPION SPOTLIGHT ===== */
.tr-champ-section { position: relative; overflow: hidden; }
.tr-champ-section::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse at top, rgba(201,162,39,.15), transparent 70%);
    pointer-events: none;
}
.tr-champ-card {
    position: relative; padding: 2rem; border: 2px solid rgba(201,162,39,.3);
    background: linear-gradient(135deg, rgba(201,162,39,.05), transparent);
    transition: all .4s; overflow: hidden;
}
.tr-champ-card::before {
    content: ''; position: absolute; inset: -2px;
    background: linear-gradient(45deg, transparent, #C9A227, transparent);
    opacity: 0; transition: opacity .4s; z-index: -1;
    animation: champShine 3s linear infinite;
}
.tr-champ-card:hover { transform: translateY(-8px) scale(1.02); border-color: #C9A227; box-shadow: 0 20px 60px rgba(201,162,39,.3); }
.tr-champ-card:hover::before { opacity: .5; }
@keyframes champShine {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.tr-champ-rank {
    position: absolute; top: 10px; right: 10px; z-index: 2;
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, #D4AF37, #B8941F);
    color: #0B2239; font-family: 'Fraunces', serif; font-weight: 700; font-size: .95rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(201,162,39,.5);
    border: 2px solid rgba(255,255,255,.35);
}
.tr-champ-avatar {
    width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 50%;
    background: linear-gradient(135deg, #D4AF37, #B8941F); color: #0B2239;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Fraunces', serif; font-size: 2rem; font-weight: 700;
    box-shadow: 0 8px 24px rgba(201,162,39,.4);
    border: 3px solid rgba(255,255,255,.2);
}
.tr-champ-count {
    font-family: 'Fraunces', serif; font-size: 3.5rem; font-weight: 300;
    color: #C9A227; line-height: 1; margin: .5rem 0;
}
.tr-champ-label {
    font-size: 10px; text-transform: uppercase; letter-spacing: .2em; color: #64748b; font-weight: 700;
}

/* ===== WORLD MAP ===== */
.tr-map-wrap { position: relative; max-width: 900px; margin: 0 auto; }
.tr-map-svg { width: 100%; height: auto; }
.tr-map-dot { fill: #C9A227; stroke: #fff; stroke-width: 1.5; filter: drop-shadow(0 2px 4px rgba(0,0,0,.3)); cursor: pointer; }
.tr-map-pulse { fill: #C9A227; animation: trMapPulse 2s ease-out infinite; transform-origin: center; transform-box: fill-box; }
@keyframes trMapPulse {
    0% { opacity: .8; transform: scale(1); }
    100% { opacity: 0; transform: scale(4); }
}
.tr-map-tooltip {
    position: absolute; background: #0B2239; color: #F7F5F0;
    padding: 8px 12px; font-size: 11px; pointer-events: none;
    opacity: 0; transition: opacity .2s; white-space: nowrap; z-index: 10;
    border-left: 2px solid #C9A227;
}
.tr-map-tooltip.show { opacity: 1; }

.chip, .tr-chip {
    padding: .5rem 1rem; font-size: 10px; letter-spacing: .18em; text-transform: uppercase;
    font-weight: 700; border: 1px solid #e5e7eb; background: #fff; color: #64748b;
    cursor: pointer; transition: all .25s; display: inline-flex; align-items: center; gap: .4rem;
}
.tr-chip:hover { border-color: #C9A227; color: #0B2239; transform: translateY(-2px); }
.tr-chip.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }
.tr-chip .tr-n { font-family: 'Fraunces', serif; font-size: .8rem; }

.tr-dist-row { display: flex; align-items: center; gap: .75rem; }
.tr-dist-bar { flex: 1; height: 6px; background: #EDE8DE; overflow: hidden; }
.tr-dist-fill { height: 100%; width: 0; transition: width 1.2s cubic-bezier(.22,1,.36,1); }
.tr-yearbar { width: 100%; background: linear-gradient(to top, #B8941F, #D4AF37); height: 0%; transition: height 1.2s cubic-bezier(.22,1,.36,1); border-radius: 2px 2px 0 0; }

.tr-item { transition: opacity .4s, transform .4s; }
.tr-item.tr-hidden { display: none; }
.tr-card {
    background: var(--tr-surface, #fff); border: 1px solid var(--tr-border, #e5e7eb); position: relative; overflow: hidden;
    transition: all .4s cubic-bezier(.22,1,.36,1); cursor: pointer;
    transform-style: preserve-3d;
}
.tr-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--lv, #C9A227), transparent);
    transform: scaleX(0); transform-origin: left; transition: transform .5s;
}
.tr-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,34,57,.14); }
.tr-card:hover::before { transform: scaleX(1); }
.tr-card.tr-intl { border-color: rgba(201,162,39,.45); box-shadow: 0 0 0 1px rgba(201,162,39,.15), 0 8px 24px rgba(201,162,39,.12); }
.tr-card.tr-intl::after {
    content: ''; position: absolute; top: 0; left: -80%; width: 50%; height: 100%;
    background: linear-gradient(105deg, transparent, rgba(201,162,39,.12), transparent);
    transform: skewX(-20deg); transition: left .6s; pointer-events: none;
}
.tr-card.tr-intl:hover::after { left: 130%; }
.tr-dot-pulse { animation: trPulse 2s ease-out infinite; }
@keyframes trPulse { 0% { box-shadow: 0 0 0 0 rgba(201,162,39,.5); } 100% { box-shadow: 0 0 0 12px rgba(201,162,39,0); } }

/* Firework effect for international reveal */
.tr-card.tr-intl.tr-firework { animation: cardFirework .8s ease-out; }
@keyframes cardFirework {
    0%   { box-shadow: 0 0 0 0 rgba(201,162,39,.9), 0 8px 24px rgba(201,162,39,.12); }
    50%  { box-shadow: 0 0 0 30px rgba(201,162,39,0), 0 8px 24px rgba(201,162,39,.3); }
    100% { box-shadow: 0 0 0 1px rgba(201,162,39,.15), 0 8px 24px rgba(201,162,39,.12); }
}

/* ===== 🌍 WORLD STAGE SPOTLIGHT (pengganti peta) ===== */
.tr-world-stage { background: linear-gradient(165deg, #061420 0%, #0B2239 60%, #13334F 100%); }
.tr-stage-glow {
    position: absolute; top: -40%; left: 50%; transform: translateX(-50%);
    width: 160%; height: 120%;
    background: conic-gradient(from 0deg at 50% 0%, transparent 0deg, rgba(201,162,39,.12) 20deg, transparent 45deg, transparent 315deg, rgba(201,162,39,.08) 340deg, transparent 360deg);
    animation: trSway 10s ease-in-out infinite alternate; pointer-events: none;
}
.tr-stage-wrap { position: relative; max-width: 760px; margin: 0 auto; }
.tr-stage-viewport { position: relative; min-height: 340px; }
.tr-stage-slide {
    position: absolute; inset: 0; text-align: center;
    opacity: 0; transform: translateX(60px) scale(.96);
    transition: opacity .6s ease, transform .6s cubic-bezier(.22,1,.36,1);
    pointer-events: none;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    background: rgba(247,245,240,.04); border: 1px solid rgba(201,162,39,.25);
    backdrop-filter: blur(6px); padding: 2.5rem 2rem;
}
.tr-stage-slide.active { opacity: 1; transform: none; pointer-events: auto; }
.tr-stage-flag { font-size: 3.5rem; margin-bottom: .75rem; filter: drop-shadow(0 6px 16px rgba(0,0,0,.4)); animation: trFloat 4s ease-in-out infinite alternate; }
.tr-stage-city { color: #C9A227; font-size: .8rem; letter-spacing: .4em; text-transform: uppercase; font-weight: 700; margin-bottom: 1rem; }
.tr-stage-title { font-family: 'Fraunces', serif; color: #F7F5F0; font-size: clamp(1.4rem, 3.5vw, 2.4rem); font-weight: 500; line-height: 1.25; max-width: 560px; margin: 0 auto 1rem; }
.tr-stage-student { color: rgba(247,245,240,.85); font-size: 1rem; }
.tr-stage-meta { display: flex; gap: .75rem; justify-content: center; align-items: center; margin-top: 1rem; color: rgba(247,245,240,.55); font-size: .8rem; flex-wrap: wrap; }
.tr-stage-dotsep { color: #C9A227; }
.tr-stage-arrow {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 5;
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(11,34,57,.6); color: #C9A227; border: 1px solid rgba(201,162,39,.4);
    cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center;
}
.tr-stage-arrow:hover { background: #C9A227; color: #0B2239; }
.tr-stage-arrow.left { left: -10px; } .tr-stage-arrow.right { right: -10px; }
@media (min-width: 768px) { .tr-stage-arrow.left { left: -56px; } .tr-stage-arrow.right { right: -56px; } }
.tr-stage-dots { display: flex; gap: .5rem; justify-content: center; margin-top: 1.5rem; }
.tr-stage-dots button { width: 10px; height: 10px; border-radius: 50%; border: none; background: rgba(247,245,240,.25); cursor: pointer; transition: all .3s; padding: 0; }
.tr-stage-dots button.active { background: #C9A227; transform: scale(1.3); }
.tr-stage-chips { display: flex; flex-wrap: wrap; gap: .6rem; justify-content: center; margin-top: 2rem; }
.tr-stage-chip {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .5rem 1rem; background: rgba(247,245,240,.06);
    border: 1px solid rgba(201,162,39,.3); color: rgba(247,245,240,.8);
    font-size: .8rem; border-radius: 999px;
}

/* ===== GRID VIEW ===== */
#trTimeline.view-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
#trTimeline.view-grid .tr-item { display: block; }
#trTimeline.view-grid .tr-item .tr-spine { display: none; }
#trTimeline.view-grid .tr-item .tr-body { padding-bottom: 0; }

.tr-rv { opacity: 0; transform: translateY(26px); transition: all .9s cubic-bezier(.22,1,.36,1); transition-delay: var(--d, 0s); }
.tr-rv.in { opacity: 1; transform: none; }

.tr-search-wrap { position: relative; max-width: 400px; }
.tr-search-wrap input { width: 100%; padding: 12px 16px 12px 44px; border: 2px solid #e5e7eb; background: var(--tr-surface, #fff); color: var(--tr-text); font-size: 13px; transition: border-color .2s; }
.tr-search-wrap input:focus { outline: none; border-color: #C9A227; }
.tr-search-wrap i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #C9A227; }
.tr-year-select { padding: 12px 16px; border: 2px solid #e5e7eb; background: var(--tr-surface, #fff); color: var(--tr-text); font-size: 13px; cursor: pointer; min-width: 120px; }
.tr-year-select:focus { outline: none; border-color: #C9A227; }

.tr-share-btn {
    width: 32px; height: 32px; background: transparent; border: 1px solid var(--tr-border, #e5e7eb);
    color: var(--tr-muted, #64748b); cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; justify-content: center;
}
.tr-share-btn:hover { background: #C9A227; color: #0B2239; border-color: #C9A227; }

.tr-stats-band { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
.tr-stat { background: var(--tr-surface, #fff); border: 1px solid var(--tr-border, #e5e7eb); padding: 20px; text-align: center; transition: all .3s; }
.tr-stat:hover { transform: translateY(-4px); border-color: rgba(201,162,39,.4); box-shadow: 0 12px 24px rgba(11,34,57,.08); }
.tr-stat-num { font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 300; color: #C9A227; line-height: 1; }
.tr-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .2em; color: var(--tr-muted, #64748b); margin-top: 6px; font-weight: 600; }

/* ===== MODAL ===== */
.tr-modal { position: fixed; inset: 0; z-index: 100; background: rgba(6,20,32,.85); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; padding: 1rem; }
.tr-modal.open { display: flex; animation: trIn .3s ease; }
@keyframes trIn { from { opacity: 0; } to { opacity: 1; } }
.tr-modal-card { background: #fff; width: 100%; max-width: 560px; max-height: 85vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,.4); border-top: 4px solid #C9A227; }
.tr-modal-head { padding: 1.5rem; background: #0B2239; color: #F7F5F0; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
.tr-modal-head h3 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 1.25rem; line-height: 1.3; }
.tr-modal-close { background: none; border: none; color: #C9A227; font-size: 20px; cursor: pointer; flex-shrink: 0; }
.tr-modal-body { padding: 1.5rem; }
.tr-modal-row { display: flex; gap: 1rem; padding: .6rem 0; border-bottom: 1px dashed #e5e7eb; font-size: .875rem; }
.tr-modal-row:last-of-type { border-bottom: 0; }
.tr-modal-row .lbl { width: 110px; flex-shrink: 0; color: #64748b; text-transform: uppercase; font-size: 10px; letter-spacing: .1em; font-weight: 700; padding-top: 2px; }
.tr-modal-row .val { color: #0B2239; font-weight: 500; }
.tr-modal-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.25rem; }
.tr-modal-actions button {
    flex: 1; min-width: 110px; padding: .7rem; font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; cursor: pointer; border: 1px solid #e5e7eb; background: #fff; color: #0B2239; transition: all .2s;
    display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
}
.tr-modal-actions button:hover { background: #C9A227; color: #0B2239; border-color: #C9A227; }
.tr-modal-actions button.primary { background: #0B2239; color: #C9A227; border-color: #0B2239; }

/* ===== 🔥 THEME SWITCHER FLOAT ===== */
.tr-theme-switch {
    position: fixed; bottom: 24px; left: 24px; z-index: 50;
    background: #0B2239; color: #C9A227;
    padding: 8px; border: 1px solid rgba(201,162,39,.4);
    display: flex; flex-direction: column; gap: 4px;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
}
.tr-theme-btn {
    width: 32px; height: 32px; border: 2px solid transparent; background: transparent; color: #C9A227;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all .2s; font-size: 11px;
}
.tr-theme-btn:hover { background: rgba(201,162,39,.15); }
.tr-theme-btn.active { background: #C9A227; color: #0B2239; border-color: #C9A227; }

/* ===== 🔥 SOUND TOGGLE ===== */
.tr-sound-toggle {
    position: fixed; bottom: 24px; right: 80px; z-index: 50;
    width: 40px; height: 40px; border-radius: 50%;
    background: #0B2239; color: #C9A227;
    border: 1px solid rgba(201,162,39,.4);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(0,0,0,.2); transition: all .2s;
}
.tr-sound-toggle:hover { background: #C9A227; color: #0B2239; }
.tr-sound-toggle.muted { opacity: .4; }

.tr-toast {
    position: fixed; bottom: 24px; right: 140px; z-index: 100;
    background: #0B2239; color: #F7F5F0; padding: 12px 20px;
    font-size: 13px; box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(-120%); transition: transform .3s;
    border-left: 3px solid #C9A227;
}
.tr-toast.show { transform: translateX(0); }
.tr-toast i { color: #C9A227; margin-right: 8px; }

@media print {
    .tr-marquee, .tr-toast, .tr-share-btn, .tr-search-wrap, .tr-year-select, #trViewBar, .tr-modal, #trConfetti, .tr-float, .tr-spot, .tr-curtain, .tr-theme-switch, .tr-sound-toggle, #trConstellation, .tr-trophy-3d { display: none !important; }
    .tr-hero { background: #fff !important; color: #0B2239 !important; }
    .tr-rv { opacity: 1 !important; transform: none !important; }
    .tr-card { box-shadow: none !important; break-inside: avoid; cursor: default; }
    body { background: #fff !important; }
}
</style>

<!-- ============ CINEMATIC CURTAIN INTRO ============ -->
<div class="tr-curtain" id="trCurtain">
    <div class="tr-curtain-half tr-curtain-left"></div>
    <div class="tr-curtain-half tr-curtain-right"></div>
    <div class="tr-curtain-center">
        <div class="tr-curtain-title">
            <i class="fas fa-trophy" style="color:#C9A227"></i> <?= $EN ? 'HALL OF FAME' : 'AULA KEHORMATAN' ?>
        </div>
        <div class="tr-curtain-sub"><?= html_escape(site_name()) ?></div>
    </div>
</div>

<div class="tr-page">

<!-- ============ FLOATING CONTROLS ============ -->
<div class="tr-theme-switch" title="<?= $EN ? 'Page theme' : 'Tema halaman' ?>">
    <button class="tr-theme-btn active" data-theme="default" title="Default"><i class="fas fa-sun"></i></button>
    <button class="tr-theme-btn" data-theme="gold" title="Gold Luxe"><i class="fas fa-gem"></i></button>
    <button class="tr-theme-btn" data-theme="midnight" title="Midnight"><i class="fas fa-moon"></i></button>
</div>

<button class="tr-sound-toggle" id="trSoundToggle" title="<?= $EN ? 'Toggle sound effects' : 'Efek suara' ?>">
    <i class="fas fa-volume-up" id="trSoundIcon"></i>
</button>

<!-- ============ HERO ============ -->
<section class="tr-hero text-ivory">
    <div class="tr-spot"></div>
    <canvas id="trConstellation"></canvas>
    <canvas id="trConfetti"></canvas>
    <i class="fas fa-trophy tr-float text-4xl" style="top:18%; left:8%;"></i>
    <i class="fas fa-medal tr-float text-3xl" style="top:60%; left:14%; animation-delay:1.2s;"></i>
    <i class="fas fa-award tr-float text-4xl" style="top:30%; right:10%; animation-delay:.6s;"></i>
    <i class="fas fa-star tr-float text-2xl" style="top:70%; right:16%; animation-delay:2s;"></i>

    <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <p class="editorial-label text-gold mb-6"><i class="fas fa-crown mr-2"></i><?= $EN ? 'Hall of Fame' : 'Aula Kehormatan' ?></p>
            <h1 class="font-serif font-light tracking-[-0.03em] leading-[1.05] text-5xl md:text-7xl">
                <?= $EN ? 'Pride & <em class="italic text-gold">Achievements</em>' : 'Prestasi <em class="italic text-gold">Mahasiswa</em>' ?>
            </h1>
            <p class="text-ivory/70 mt-6 text-lg leading-relaxed">
                <?= $EN ? 'The faculty\'s pride — outstanding student achievements on national and international stages.' : 'Kebanggaan fakultas — pencapaian gemilang mahasiswa di kancah nasional dan internasional.' ?>
            </p>

            <!-- 3D ROTATING TROPHY -->
            <div class="tr-trophy-3d tr-rv">
                <div class="tr-trophy-inner">
                    <div class="tr-trophy-face"><i class="fas fa-trophy"></i></div>
                    <div class="tr-trophy-face"><i class="fas fa-crown"></i></div>
                    <div class="tr-trophy-face"><i class="fas fa-medal"></i></div>
                    <div class="tr-trophy-face"><i class="fas fa-award"></i></div>
                </div>
            </div>

            <div class="inline-flex items-center gap-3 mt-8 bg-ivory/5 border border-ivory/15 px-6 py-3">
                <span class="font-serif text-4xl font-light text-gold tr-count" data-count="<?= count($achievements) ?>">0</span>
                <span class="editorial-label text-ivory/60 text-left"><?= $EN ? 'Recorded<br>Achievements' : 'Prestasi<br>Tercatat' ?></span>
            </div>
        </div>

        <?php if ($pod_slots[0]): ?>
        <div class="tr-podium tr-rv mt-16" id="trPodium">
            <div class="tr-pod" data-rank="2">
                <?php if ($pod_slots[1]): ?>
                <div>
                    <div class="tr-pod-avatar"><?= strtoupper(substr($pod_slots[1]->student_name, 0, 1)) ?></div>
                    <p class="tr-pod-name"><?= html_escape($pod_slots[1]->student_name) ?></p>
                    <p class="tr-pod-title"><?= html_escape($pod_slots[1]->achievement_title) ?></p>
                    <div class="tr-pod-bar">2</div>
                </div>
                <?php endif; ?>
            </div>
            <div class="tr-pod" data-rank="1">
                <div>
                    <div class="tr-pod-avatar">
                        <span class="tr-pod-crown"><i class="fas fa-crown"></i></span>
                        <?= strtoupper(substr($pod_slots[0]->student_name, 0, 1)) ?>
                    </div>
                    <p class="tr-pod-name"><?= html_escape($pod_slots[0]->student_name) ?></p>
                    <p class="tr-pod-title"><?= html_escape($pod_slots[0]->achievement_title) ?></p>
                    <div class="tr-pod-bar">1</div>
                </div>
            </div>
            <div class="tr-pod" data-rank="3">
                <?php if ($pod_slots[2]): ?>
                <div>
                    <div class="tr-pod-avatar"><?= strtoupper(substr($pod_slots[2]->student_name, 0, 1)) ?></div>
                    <p class="tr-pod-name"><?= html_escape($pod_slots[2]->student_name) ?></p>
                    <p class="tr-pod-title"><?= html_escape($pod_slots[2]->achievement_title) ?></p>
                    <div class="tr-pod-bar">3</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ MARQUEE ============ -->
<?php if (!empty($achievements)): ?>
<div class="tr-marquee">
    <div class="tr-marquee-track">
        <?php for ($rep = 0; $rep < 2; $rep++): ?>
            <?php foreach ($podium_pool as $a): ?>
            <span class="tr-mq-item"><i class="fas fa-trophy"></i><b><?= html_escape($a->achievement_title) ?></b> — <?= html_escape($a->student_name) ?> (<?= $a->year ?>)</span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<!-- ============ CHAMPION SPOTLIGHT ============ -->
<?php if (!empty($champions)): ?>
<section class="tr-champ-section py-20 tr-surface tr-border border-b">
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-12 tr-rv">
            <p class="editorial-label text-gold mb-4"><i class="fas fa-crown mr-2"></i><?= $EN ? 'Champions Spotlight' : 'Sorotan Juara' ?></p>
            <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight">
                <?= $EN ? 'Our most <em class="italic text-gold">decorated</em> students' : 'Mahasiswa paling <em class="italic text-gold">berprestasi</em>' ?>
            </h2>
            <p class="text-slate mt-4 max-w-2xl mx-auto"><?= $EN ? 'The pride of our faculty — students who consistently shone across multiple competitions.' : 'Kebanggaan fakultas — mahasiswa yang konsisten bersinar di berbagai kompetisi.' ?></p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
            <?php foreach ($champions as $idx => $c): ?>
            <div class="tr-champ-card tr-rv text-center" style="--d:<?= $idx * .08 ?>s">
                <div class="tr-champ-rank">#<?= $idx + 1 ?></div>
                <div class="tr-champ-avatar"><?= strtoupper(substr($c['name'], 0, 1)) ?></div>
                <p class="font-serif font-semibold text-navy text-sm leading-tight"><?= html_escape($c['name']) ?></p>
                <?php if ($c['prodi']): ?>
                <p class="text-[10px] text-slate mt-1 uppercase tracking-wider"><?= html_escape($c['prodi']) ?></p>
                <?php endif; ?>
                <div class="tr-champ-count"><?= $c['count'] ?></div>
                <div class="tr-champ-label"><?= $EN ? 'Achievements' : 'Prestasi' ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ WORLD STAGE SPOTLIGHT (pengganti peta) ============ -->
<?php if (!empty($intl_points)): ?>
<section class="tr-world-stage py-20 relative overflow-hidden">
    <div class="tr-stage-glow"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-10 tr-rv">
            <p class="editorial-label text-gold mb-4"><i class="fas fa-globe mr-2"></i><?= $EN ? 'World Stage' : 'Panggung Juara' ?></p>
            <h2 class="font-serif text-4xl md:text-5xl font-light text-ivory tracking-tight">
                <?= $EN ? 'Shining across <em class="italic text-gold">the globe</em>' : 'Bersinar di <em class="italic text-gold">seluruh dunia</em>' ?>
            </h2>            <p class="editorial-label text-gold mb-4"><i class="fas fa-map-marker-alt mr-2"></i><?= $EN ? 'Achievement Stage' : 'Panggung Prestasi' ?></p>
            <h2 class="font-serif text-4xl md:text-5xl font-light text-ivory tracking-tight">
                <?= $EN ? 'From Maumere to <em class="italic text-gold">the Nation</em>' : 'Dari Maumere untuk <em class="italic text-gold">Indonesia</em>' ?>
            </h2>
            <p class="text-ivory/60 mt-4"><?= count($intl_points) ?> <?= $EN ? 'outstanding achievements across' : 'prestasi gemilang di' ?> <?= count(array_unique(array_column($intl_points, 'city'))) ?> <?= $EN ? 'cities' : 'kota seluruh Indonesia' ?></p>
            <p class="text-ivory/60 mt-4"><?= count($intl_points) ?> <?= $EN ? 'international achievements in' : 'prestasi internasional di' ?> <?= count(array_unique(array_column($intl_points, 'city'))) ?> <?= $EN ? 'cities worldwide' : 'kota di seluruh dunia' ?></p>
        </div>

        <!-- Spotlight Carousel -->
        <div class="tr-stage-wrap tr-rv" id="stageWrap">
            <button class="tr-stage-arrow left" id="stagePrev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <div class="tr-stage-viewport" id="stageViewport">
                <?php foreach ($intl_points as $i => $p): ?>
                <div class="tr-stage-slide <?= $i === 0 ? 'active' : '' ?>">
                    <div class="tr-stage-flag"><?= $p['flag'] ?></div>
                    <p class="tr-stage-city"><?= html_escape($p['city']) ?></p>
                    <h3 class="tr-stage-title"><?= html_escape($p['title']) ?></h3>
                    <p class="tr-stage-student"><i class="fas fa-user-graduate mr-2"></i><?= html_escape($p['student']) ?></p>
                    <div class="tr-stage-meta">
                        <span><i class="fas fa-calendar mr-1"></i><?= $p['year'] ?></span>
                        <?php if ($p['organizer']): ?>
                        <span class="tr-stage-dotsep">•</span>
                        <span><i class="fas fa-trophy mr-1"></i><?= html_escape($p['organizer']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="tr-stage-arrow right" id="stageNext" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="tr-stage-dots" id="stageDots"></div>

        <!-- City chips -->
        <div class="tr-stage-chips tr-rv">
            <?php $seen = []; foreach ($intl_points as $p): if (isset($seen[$p['city']])) continue; $seen[$p['city']] = 1; ?>
            <span class="tr-stage-chip"><?= $p['flag'] ?> <?= html_escape($p['city']) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ STATS BAND ============ -->
<?php if (!empty($achievements)): ?>
<section class="py-10 tr-surface tr-border border-b">
    <div class="container mx-auto px-6">
        <div class="tr-stats-band">
            <div class="tr-stat tr-rv">
                <div class="tr-stat-num tr-count" data-count="<?= $level_counts['international'] ?>">0</div>
                <div class="tr-stat-label"><i class="fas fa-globe mr-1"></i><?= $EN ? 'International' : 'Internasional' ?></div>
            </div>
            <div class="tr-stat tr-rv" style="--d:.05s">
                <div class="tr-stat-num tr-count" data-count="<?= $level_counts['national'] ?>">0</div>
                <div class="tr-stat-label"><i class="fas fa-flag mr-1"></i><?= $EN ? 'National' : 'Nasional' ?></div>
            </div>
            <div class="tr-stat tr-rv" style="--d:.1s">
                <div class="tr-stat-num tr-count" data-count="<?= $level_counts['regional'] ?>">0</div>
                <div class="tr-stat-label"><i class="fas fa-map mr-1"></i>Regional</div>
            </div>
            <div class="tr-stat tr-rv" style="--d:.15s">
                <div class="tr-stat-num tr-count" data-count="<?= count($years) ?>">0</div>
                <div class="tr-stat-label"><i class="fas fa-calendar mr-1"></i><?= $EN ? 'Active Years' : 'Tahun Aktif' ?></div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ LEVEL STATS + YEAR TREND + FILTER ============ -->
<section class="tr-surface tr-border border-b">
    <div class="container mx-auto px-6 py-10">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-3 tr-rv">
                <?php
                $max_lv = max($level_counts) ?: 1;
                foreach ($level_counts as $lv => $cnt):
                    $m = $level_meta_map[$lv] ?? ['label' => ucfirst($lv), 'dot' => '#6b7280'];
                    $w = round(($cnt / $max_lv) * 100);
                ?>
                <div class="tr-dist-row">
                    <span class="w-28 text-[10px] uppercase tracking-wider font-bold tr-muted-text"><?= $m['label'] ?></span>
                    <div class="tr-dist-bar"><div class="tr-dist-fill" data-w="<?= $w ?>%" style="background:<?= $m['dot'] ?>"></div></div>
                    <span class="w-8 text-right font-serif font-semibold text-navy tr-count" data-count="<?= $cnt ?>">0</span>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="tr-rv" style="--d:.15s">
                <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Filter by level' : 'Saring per level' ?></p>
                <div class="flex flex-wrap gap-2" id="trChips">
                    <button class="tr-chip active" data-level="all"><?= $EN ? 'All' : 'Semua' ?> <span class="tr-n"><?= count($achievements) ?></span></button>
                    <?php foreach ($level_counts as $lv => $cnt):
                        $m = $level_meta_map[$lv] ?? ['label' => ucfirst($lv)];
                    ?>
                    <button class="tr-chip" data-level="<?= $lv ?>"><?= $m['label'] ?> <span class="tr-n"><?= $cnt ?></span></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (count($year_counts) > 1): ?>
        <div class="mt-10 tr-rv">
            <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Year-over-year trend' : 'Tren per tahun' ?></p>
            <div class="flex items-end gap-3" style="height:120px">
                <?php foreach ($year_counts as $y => $c): ?>
                <div class="flex-1 flex flex-col items-center justify-end gap-2 h-full" title="<?= $y ?>: <?= $c ?> <?= $EN ? 'achievements' : 'prestasi' ?>">
                    <span class="text-[10px] font-mono font-bold text-gold-muted"><?= $c ?></span>
                    <div class="tr-yearbar" data-h="<?= round($c / $max_year * 100) ?>%"></div>
                    <span class="text-[10px] font-mono tr-muted-text"><?= $y ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ TIMELINE ============ -->
<section class="py-16 md:py-24" style="background: var(--tr-bg)">
    <div class="container mx-auto px-6 max-w-6xl">

        <?php if (empty($achievements)): ?>
            <div class="text-center py-24 tr-surface tr-border tr-rv in">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-ivory-warm flex items-center justify-center">
                    <i class="fas fa-trophy text-3xl text-gold-muted"></i>
                </div>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No achievement data yet.' : 'Belum ada data prestasi.' ?></p>
            </div>
        <?php else: ?>

        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6 tr-rv" id="trViewBar">
            <div class="tr-search-wrap flex-1">
                <i class="fas fa-search"></i>
                <input type="text" id="trSearch" placeholder="<?= $EN ? 'Search by title, student, or organizer...' : 'Cari judul, mahasiswa, atau penyelenggara...' ?>">
            </div>
            <select id="trYear" class="tr-year-select">
                <option value=""><?= $EN ? 'All Years' : 'Semua Tahun' ?></option>
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
                <?php endforeach; ?>
            </select>
            <select id="trSort" class="tr-year-select">
                <option value="default"><?= $EN ? 'Sort' : 'Urutkan' ?></option>
                <option value="newest"><?= $EN ? 'Newest' : 'Terbaru' ?></option>
                <option value="level"><?= $EN ? 'Top Level' : 'Level Tertinggi' ?></option>
            </select>
            <div class="flex gap-2">
                <button class="tr-chip active" data-view="timeline" title="Timeline"><i class="fas fa-stream"></i></button>
                <button class="tr-chip" data-view="grid" title="Grid"><i class="fas fa-th"></i></button>
            </div>
            <button type="button" onclick="window.print()" class="tr-chip" style="background:#0B2239;color:#C9A227;border-color:#0B2239;">
                <i class="fas fa-print"></i><?= $EN ? 'Print' : 'Cetak' ?>
            </button>
        </div>
        <p class="text-xs tr-muted-text mb-6 italic tr-rv" id="trCount"><?= count($achievements) ?> <?= $EN ? 'achievements shown' : 'prestasi ditampilkan' ?></p>

        <div class="space-y-0" id="trTimeline">
            <?php foreach ($achievements as $i => $a):
                $m = $level_meta_map[$a->level] ?? ['label' => ucfirst($a->level), 'badge' => 'bg-gray-500 text-ivory', 'dot' => '#6b7280'];
                $is_last = ($i === count($achievements) - 1);
                $is_intl = ($a->level === 'international');
            ?>
            <div class="tr-item flex gap-6 md:gap-10 tr-rv"
                 data-level="<?= html_escape($a->level) ?>"
                 data-rank="<?= $level_rank[$a->level] ?? 0 ?>"
                 data-year="<?= $a->year ?>"
                 data-title="<?= strtolower(html_escape($a->achievement_title)) ?>"
                 data-student="<?= strtolower(html_escape($a->student_name)) ?>"
                 data-organizer="<?= strtolower(html_escape($a->organizer ?? '')) ?>"
                 style="--d:<?= ($i % 5) * .07 ?>s">
                <div class="tr-spine flex flex-col items-center flex-shrink-0">
                    <div class="w-20 md:w-24 bg-navy text-ivory text-center px-3 py-4 relative z-10">
                        <div class="font-serif text-2xl md:text-3xl font-light leading-none"><?= $a->year ?></div>
                        <div class="text-[9px] uppercase tracking-editorial text-gold mt-1"><?= $m['label'] ?></div>
                    </div>
                    <div class="w-px flex-1 bg-gray-200 relative">
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-3 h-3 rounded-full -mt-1.5 <?= $is_intl ? 'tr-dot-pulse' : '' ?>" style="background:<?= $m['dot'] ?>"></span>
                    </div>
                </div>

                <div class="tr-body flex-1 min-w-0 pb-12 <?= $is_last ? 'pb-0' : '' ?>">
                    <div class="tr-card <?= $is_intl ? 'tr-intl' : '' ?> p-6 md:p-8" style="--lv:<?= $m['dot'] ?>"
                         data-full-title="<?= html_escape($a->achievement_title) ?>"
                         data-full-student="<?= html_escape($a->student_name) ?>"
                         data-nim="<?= html_escape($a->nim ?? '') ?>"
                         data-organizer-full="<?= html_escape($a->organizer ?? '') ?>"
                         data-prodi="<?= html_escape($a->prodi_name ?? '') ?>"
                         data-level-label="<?= html_escape($m['label']) ?>"
                         data-year-full="<?= $a->year ?>">
                        <div class="absolute -top-5 -right-3 font-serif text-[6rem] leading-none text-navy/5 select-none pointer-events-none"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></div>
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="<?= $m['badge'] ?> text-[10px] uppercase tracking-editorial font-bold px-3 py-1"><?= $m['label'] ?></span>
                            <?php if ($is_intl): ?>
                                <span class="text-[10px] uppercase tracking-editorial font-bold text-gold-muted"><i class="fas fa-globe mr-1"></i><?= $EN ? 'World Stage' : 'Kancah Dunia' ?></span>
                            <?php endif; ?>
                            <span class="w-8 h-px bg-gold"></span>
                            <?php if ($a->prodi_name): ?>
                                <span class="text-[10px] uppercase tracking-wider bg-ivory-warm text-navy px-2 py-1 font-semibold"><?= html_escape($a->prodi_name) ?></span>
                            <?php endif; ?>
                            <button type="button" class="tr-share-btn ml-auto" onclick="event.stopPropagation(); shareAchievement(this, '<?= html_escape(addslashes($a->achievement_title)) ?>', '<?= html_escape(addslashes($a->student_name)) ?>')" title="<?= $EN ? 'Share achievement' : 'Bagikan prestasi' ?>">
                                <i class="fas fa-share-alt text-xs"></i>
                            </button>
                        </div>
                        <h3 class="font-serif text-xl md:text-2xl font-medium text-navy leading-snug tracking-[-0.01em] mb-4">
                            <i class="fas fa-trophy text-gold mr-2"></i><?= html_escape($a->achievement_title) ?>
                        </h3>
                        <div class="space-y-1.5 text-sm tr-muted-text">
                            <p><i class="fas fa-user-graduate text-gold-muted mr-2"></i><?= html_escape($a->student_name) ?><?php if ($a->nim): ?> <span class="text-gray-400 font-mono text-xs">(<?= html_escape($a->nim) ?>)</span><?php endif; ?></p>
                            <?php if ($a->organizer): ?>
                                <p><i class="fas fa-building text-gold-muted mr-2"></i><?= $EN ? 'Organizer' : 'Penyelenggara' ?>: <?= html_escape($a->organizer) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="hidden text-center py-16 tr-muted-text font-serif text-xl" id="trEmpty">
            <?= $EN ? 'No achievements match your filter.' : 'Tidak ada prestasi yang cocok dengan filter Anda.' ?>
        </p>
        <?php endif; ?>

    </div>
</section>

</div><!-- end .tr-page -->

<!-- MODAL -->
<div class="tr-modal" id="trModal" onclick="if(event.target===this)closeTrModal()">
    <div class="tr-modal-card">
        <div class="tr-modal-head">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-gold mb-1" id="tmLevel"></p>
                <h3 id="tmTitle"></h3>
            </div>
            <button class="tr-modal-close" onclick="closeTrModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="tr-modal-body">
            <div class="tr-modal-row"><span class="lbl"><?= $EN ? 'Student' : 'Mahasiswa' ?></span><span class="val" id="tmStudent"></span></div>
            <div class="tr-modal-row"><span class="lbl">NIM</span><span class="val" id="tmNim"></span></div>
            <div class="tr-modal-row"><span class="lbl"><?= $EN ? 'Program' : 'Prodi' ?></span><span class="val" id="tmProdi"></span></div>
            <div class="tr-modal-row"><span class="lbl"><?= $EN ? 'Organizer' : 'Penyelenggara' ?></span><span class="val" id="tmOrganizer"></span></div>
            <div class="tr-modal-row"><span class="lbl"><?= $EN ? 'Year' : 'Tahun' ?></span><span class="val" id="tmYear"></span></div>
            <div class="tr-modal-actions">
                <button onclick="modalShareWA()"><i class="fab fa-whatsapp"></i>WhatsApp</button>
                <button onclick="modalShareFB()"><i class="fab fa-facebook-f"></i>Facebook</button>
                <button onclick="modalCopy()"><i class="fas fa-link"></i><?= $EN ? 'Copy' : 'Salin' ?></button>
                <button class="primary" onclick="downloadShareCard()"><i class="fas fa-image"></i><?= $EN ? 'Download Card' : 'Unduh Kartu' ?></button>
            </div>
        </div>
    </div>
</div>

<div class="tr-toast" id="trToast"><i class="fas fa-check-circle"></i><span id="trToastMsg"></span></div>

<script>
(function(){
    var currentAch = null;
    var soundOn = localStorage.getItem('tr_sound') !== 'off';

    // ===== CINEMATIC CURTAIN =====
    var curtain = document.getElementById('trCurtain');
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (curtain) {
        if (reduceMotion) {
            curtain.parentNode && curtain.parentNode.removeChild(curtain);
        } else {
            setTimeout(function(){ curtain.classList.add('done'); }, 700);    // judul tampil 0.7 dtk
            setTimeout(function(){ if (curtain.parentNode) curtain.parentNode.removeChild(curtain); }, 2000); // TOTAL 2 dtk
        }
    }

    // ===== THEME SWITCHER =====
    var page = document.querySelector('.tr-page');
    document.querySelectorAll('.tr-theme-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tr-theme-btn').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            page.classList.remove('theme-gold', 'theme-midnight');
            if (btn.dataset.theme !== 'default') page.classList.add('theme-' + btn.dataset.theme);
        });
    });

    // ===== SOUND TOGGLE =====
    var soundBtn = document.getElementById('trSoundToggle');
    var soundIcon = document.getElementById('trSoundIcon');
    function updateSoundUI(){
        soundIcon.className = soundOn ? 'fas fa-volume-up' : 'fas fa-volume-mute';
        soundBtn.classList.toggle('muted', !soundOn);
    }
    updateSoundUI();
    soundBtn.addEventListener('click', function(){
        soundOn = !soundOn;
        localStorage.setItem('tr_sound', soundOn ? 'on' : 'off');
        updateSoundUI();
    });
    function playChime(){
        if (!soundOn) return;
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + .1);
            gain.gain.setValueAtTime(.05, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .3);
            osc.start(); osc.stop(ctx.currentTime + .3);
        } catch(e) {}
    }

    // ===== INTERACTIVE CONSTELLATION (mouse-aware) =====
    var cc = document.getElementById('trConstellation');
    if (cc) {
        var ctx = cc.getContext('2d'), pts = [], mouse = {x: -999, y: -999};
        function resize(){ cc.width = cc.offsetWidth; cc.height = cc.offsetHeight; }
        resize(); window.addEventListener('resize', resize);
        cc.parentElement.addEventListener('mousemove', function(e){
            var r = cc.getBoundingClientRect();
            mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top;
        });
        cc.parentElement.addEventListener('mouseleave', function(){ mouse.x = -999; mouse.y = -999; });
        var N = Math.min(80, Math.floor(cc.offsetWidth / 20));
        for (var i=0;i<N;i++) pts.push({ x: Math.random()*cc.width, y: Math.random()*cc.height, vx:(Math.random()-.5)*.3, vy:(Math.random()-.5)*.3, s: Math.random()*1.5+.8 });
        (function loop(){
            ctx.clearRect(0,0,cc.width,cc.height);
            pts.forEach(function(p){
                p.x += p.vx; p.y += p.vy;
                if (p.x<0||p.x>cc.width) p.vx *= -1;
                if (p.y<0||p.y>cc.height) p.vy *= -1;
                // Mouse attraction
                var dx = mouse.x - p.x, dy = mouse.y - p.y;
                var d = Math.sqrt(dx*dx + dy*dy);
                if (d < 120 && d > 0) { p.x += dx/d * .3; p.y += dy/d * .3; }
                ctx.beginPath(); ctx.arc(p.x, p.y, p.s, 0, Math.PI*2);
                ctx.fillStyle = 'rgba(201,162,39,.7)'; ctx.fill();
            });
            // Connect nearby
            for (var i=0;i<pts.length;i++) for (var j=i+1;j<pts.length;j++){
                var dx = pts[i].x - pts[j].x, dy = pts[i].y - pts[j].y;
                var d = Math.sqrt(dx*dx + dy*dy);
                if (d < 100) {
                    ctx.beginPath(); ctx.moveTo(pts[i].x, pts[i].y); ctx.lineTo(pts[j].x, pts[j].y);
                    ctx.strokeStyle = 'rgba(201,162,39,' + (.3 * (1 - d/100)) + ')'; ctx.lineWidth = .5; ctx.stroke();
                }
            }
            // Mouse glow
            if (mouse.x > 0) {
                var g = ctx.createRadialGradient(mouse.x, mouse.y, 0, mouse.x, mouse.y, 80);
                g.addColorStop(0, 'rgba(201,162,39,.2)'); g.addColorStop(1, 'rgba(201,162,39,0)');
                ctx.fillStyle = g; ctx.fillRect(0,0,cc.width,cc.height);
            }
            requestAnimationFrame(loop);
        })();
    }

    // ===== SHOOTING STARS =====
    var hero = document.querySelector('.tr-hero');
    if (hero) {
        setInterval(function(){
            if (Math.random() > .5) return;
            var s = document.createElement('div');
            s.className = 'tr-shooting-star';
            var startX = Math.random() * 80;
            var startY = Math.random() * 40;
            s.style.left = startX + '%'; s.style.top = startY + '%';
            s.style.transform = 'rotate(' + (25 + Math.random() * 10) + 'deg)';
            hero.appendChild(s);
            s.animate([
                { opacity: 0, transform: 'rotate(' + s.style.transform.replace(/[^\d\.]/g,'') + 'deg) translateX(0)' },
                { opacity: 1, offset: .3 },
                { opacity: 1, offset: .7 },
                { opacity: 0, transform: 'rotate(' + s.style.transform.replace(/[^\d\.]/g,'') + 'deg) translateX(300px)' }
            ], { duration: 1200, easing: 'ease-out' });
            setTimeout(function(){ s.parentNode && s.parentNode.removeChild(s); }, 1300);
        }, 2500);
    }

    // ===== MOUSE TRAIL GOLD DUST =====
    var dustCanvas = document.createElement('canvas');
    dustCanvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:9998';
    document.body.appendChild(dustCanvas);
    var dctx = dustCanvas.getContext('2d');
    function dustResize(){ dustCanvas.width = window.innerWidth; dustCanvas.height = window.innerHeight; }
    dustResize(); window.addEventListener('resize', dustResize);
    var dusts = [];
    document.addEventListener('mousemove', function(e){
        if (hero && !hero.contains(e.target)) return;
        for (var i=0;i<2;i++){
            dusts.push({
                x: e.clientX + (Math.random()-.5)*8,
                y: e.clientY + (Math.random()-.5)*8,
                vx: (Math.random()-.5)*1.5, vy: -Math.random()*1.5 - .5,
                s: Math.random()*3+1, life: 1, decay: .015 + Math.random()*.015
            });
        }
    });
    (function dustLoop(){
        dctx.clearRect(0,0,dustCanvas.width,dustCanvas.height);
        for (var i=dusts.length-1;i>=0;i--){
            var d = dusts[i];
            d.x += d.vx; d.y += d.vy; d.vy += .05; d.life -= d.decay;
            if (d.life <= 0) { dusts.splice(i,1); continue; }
            dctx.globalAlpha = d.life;
            dctx.fillStyle = '#C9A227';
            dctx.beginPath(); dctx.arc(d.x, d.y, d.s, 0, Math.PI*2); dctx.fill();
        }
        dctx.globalAlpha = 1;
        requestAnimationFrame(dustLoop);
    })();

    // ===== MAP TOOLTIP =====
    var mapTooltip = document.getElementById('trMapTooltip');
    document.querySelectorAll('.tr-map-point').forEach(function(p){
        p.style.cursor = 'pointer';
        p.addEventListener('mouseenter', function(e){
            mapTooltip.innerHTML = '<b>📍 ' + p.dataset.city + '</b><br>' + p.dataset.title + '<br><em>' + p.dataset.student + '</em>';
            mapTooltip.classList.add('show');
            playChime();
        });
        p.addEventListener('mousemove', function(e){
            var wrap = document.getElementById('trMapWrap').getBoundingClientRect();
            mapTooltip.style.left = (e.clientX - wrap.left + 15) + 'px';
            mapTooltip.style.top = (e.clientY - wrap.top - 10) + 'px';
        });
        p.addEventListener('mouseleave', function(){ mapTooltip.classList.remove('show'); });
    });

    // ===== 🌍 WORLD STAGE SPOTLIGHT =====
    var slides = document.querySelectorAll('.tr-stage-slide');
    if (slides.length) {
        var sIdx = 0, sTimer = null;
        var dotsWrap = document.getElementById('stageDots');
        slides.forEach(function(_, i){
            var d = document.createElement('button');
            d.setAttribute('aria-label', 'Slide ' + (i + 1));
            if (i === 0) d.classList.add('active');
            d.addEventListener('click', function(){ goSlide(i); restartAuto(); });
            dotsWrap.appendChild(d);
        });
        function goSlide(n){
            sIdx = (n + slides.length) % slides.length;
            slides.forEach(function(s, i){ s.classList.toggle('active', i === sIdx); });
            dotsWrap.querySelectorAll('button').forEach(function(d, i){ d.classList.toggle('active', i === sIdx); });
        }
        function restartAuto(){
            if (sTimer) clearInterval(sTimer);
            sTimer = setInterval(function(){ goSlide(sIdx + 1); }, 5000);
        }
        document.getElementById('stageNext').addEventListener('click', function(){ goSlide(sIdx + 1); restartAuto(); });
        document.getElementById('stagePrev').addEventListener('click', function(){ goSlide(sIdx - 1); restartAuto(); });
        var stageWrap = document.getElementById('stageWrap');
        stageWrap.addEventListener('mouseenter', function(){ if (sTimer) clearInterval(sTimer); });
        stageWrap.addEventListener('mouseleave', restartAuto);
        restartAuto();
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
    document.querySelectorAll('.tr-count').forEach(function(el){ cio.observe(el); });

    // ===== REVEAL + BARS + CONFETTI + 🔥 FIREWORK =====
    var confettiFired = false;
    var rio = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            e.target.classList.add('in');
            e.target.querySelectorAll('.tr-dist-fill').forEach(function(b){ setTimeout(function(){ b.style.width = b.dataset.w; }, 300); });
            e.target.querySelectorAll('.tr-yearbar').forEach(function(b){ setTimeout(function(){ b.style.height = b.dataset.h; }, 300); });
            if (e.target.id === 'trPodium' && !confettiFired) { confettiFired = true; burstConfetti(); }
            rio.unobserve(e.target);
        });
    }, { threshold: .15 });
    document.querySelectorAll('.tr-rv').forEach(function(el){ rio.observe(el); });

    // Firework on international cards reveal
    var intlIO = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if (!e.isIntersecting) return;
            var card = e.target;
            if (card.dataset.fired) return;
            card.dataset.fired = '1';
            setTimeout(function(){
                card.classList.add('tr-firework');
                playChime();
                setTimeout(function(){ card.classList.remove('tr-firework'); }, 1000);
            }, 200);
            intlIO.unobserve(card);
        });
    }, { threshold: .4 });
    document.querySelectorAll('.tr-card.tr-intl').forEach(function(c){ intlIO.observe(c); });

    // ===== 🎊 CONFETTI =====
    function burstConfetti(){
        var cv = document.getElementById('trConfetti'); if (!cv) return;
        var ctx = cv.getContext('2d');
        cv.width = cv.offsetWidth; cv.height = cv.offsetHeight;
        var P = [], colors = ['#C9A227','#D4AF37','#F7F5F0','#FFE8A0'];
        for (var i=0;i<140;i++) P.push({ x:Math.random()*cv.width, y:-20-Math.random()*cv.height*.3, vx:(Math.random()-.5)*2, vy:2+Math.random()*3, s:3+Math.random()*4, c:colors[i%4], r:Math.random()*Math.PI, vr:(Math.random()-.5)*.2 });
        var t0 = performance.now();
        (function loop(n){
            ctx.clearRect(0,0,cv.width,cv.height);
            P.forEach(function(p){ p.x+=p.vx; p.y+=p.vy; p.vy+=.05; p.r+=p.vr;
                ctx.save(); ctx.translate(p.x,p.y); ctx.rotate(p.r); ctx.fillStyle=p.c; ctx.fillRect(-p.s/2,-p.s/2,p.s,p.s*.6); ctx.restore(); });
            if (n - t0 < 3000) requestAnimationFrame(loop); else ctx.clearRect(0,0,cv.width,cv.height);
        })(t0);
    }

    // ===== TOAST =====
    function showToast(msg){
        var t = document.getElementById('trToast');
        document.getElementById('trToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }
    window.showToast = showToast;

    // ===== FILTER =====
    var chips = document.querySelectorAll('.tr-chip[data-level]');
    var items = document.querySelectorAll('.tr-item');
    var emptyMsg = document.getElementById('trEmpty');
    var countMsg = document.getElementById('trCount');
    var searchInput = document.getElementById('trSearch');
    var yearSelect = document.getElementById('trYear');
    var activeLevel = 'all';

    function applyFilter(){
        var q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        var y = yearSelect ? yearSelect.value : '';
        var shown = 0;
        items.forEach(function(it){
            var matchL = (activeLevel === 'all' || it.dataset.level === activeLevel);
            var matchY = !y || it.dataset.year === y;
            var matchQ = !q || it.dataset.title.indexOf(q) > -1 || it.dataset.student.indexOf(q) > -1 || it.dataset.organizer.indexOf(q) > -1;
            var show = matchL && matchY && matchQ;
            it.classList.toggle('tr-hidden', !show);
            if (show) shown++;
        });
        if (emptyMsg) emptyMsg.classList.toggle('hidden', shown > 0);
        if (countMsg) countMsg.textContent = shown + ' <?= $EN ? "achievements shown" : "prestasi ditampilkan" ?>';
    }
    chips.forEach(function(chip){ chip.addEventListener('click', function(){
        chips.forEach(function(c){ c.classList.remove('active'); });
        chip.classList.add('active'); activeLevel = chip.dataset.level; applyFilter();
    });});
    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (yearSelect) yearSelect.addEventListener('change', applyFilter);

    document.querySelectorAll('.tr-chip[data-view]').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tr-chip[data-view]').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('trTimeline').classList.toggle('view-grid', btn.dataset.view === 'grid');
        });
    });

    var sortSel = document.getElementById('trSort');
    if (sortSel) sortSel.addEventListener('change', function(){
        var tl = document.getElementById('trTimeline');
        var arr = Array.prototype.slice.call(tl.children);
        var v = this.value;
        if (v === 'newest') arr.sort(function(a,b){ return (+b.dataset.year) - (+a.dataset.year); });
        else if (v === 'level') arr.sort(function(a,b){ return (+b.dataset.rank) - (+a.dataset.rank); });
        arr.forEach(function(el){ tl.appendChild(el); });
    });

    window.shareAchievement = function(btn, title, student){
        var url = window.location.href;
        var text = '🏆 ' + title + ' — ' + student;
        if (navigator.share) navigator.share({ title: title, text: text, url: url }).catch(function(){});
        else if (navigator.clipboard) { navigator.clipboard.writeText(text + ' ' + url); showToast('<?= $EN ? "Link copied!" : "Link disalin!" ?>'); }
    };

    // ===== MODAL =====
    document.getElementById('trTimeline').addEventListener('click', function(e){
        if (e.target.closest('.tr-share-btn')) return;
        var card = e.target.closest('.tr-card'); if (!card) return;
        currentAch = {
            title: card.dataset.fullTitle, student: card.dataset.fullStudent,
            nim: card.dataset.nim, prodi: card.dataset.prodi,
            organizer: card.dataset.organizerFull, level: card.dataset.levelLabel, year: card.dataset.yearFull
        };
        document.getElementById('tmLevel').textContent = '🏆 ' + currentAch.level + ' • ' + currentAch.year;
        document.getElementById('tmTitle').textContent = currentAch.title;
        document.getElementById('tmStudent').textContent = currentAch.student;
        document.getElementById('tmNim').textContent = currentAch.nim || '-';
        document.getElementById('tmProdi').textContent = currentAch.prodi || '-';
        document.getElementById('tmOrganizer').textContent = currentAch.organizer || '-';
        document.getElementById('tmYear').textContent = currentAch.year;
        document.getElementById('trModal').classList.add('open');
    });
    window.closeTrModal = function(){ document.getElementById('trModal').classList.remove('open'); };
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeTrModal(); });

    function achText(){ return '🏆 ' + currentAch.title + ' — ' + currentAch.student + ' (' + currentAch.year + ')'; }
    window.modalShareWA = function(){ window.open('https://wa.me/?text=' + encodeURIComponent(achText() + ' ' + window.location.href), '_blank'); };
    window.modalShareFB = function(){ window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank'); };
    window.modalCopy = function(){ navigator.clipboard.writeText(achText() + ' ' + window.location.href); showToast('<?= $EN ? "Copied!" : "Tersalin!" ?>'); };

    function wrapText(ctx, text, x, y, maxW, lh){
        var words = text.split(' '); var line = '';
        for (var n=0;n<words.length;n++){
            var test = line + words[n] + ' ';
            if (ctx.measureText(test).width > maxW && n>0){ ctx.fillText(line, x, y); line = words[n]+' '; y += lh; }
            else line = test;
        }
        ctx.fillText(line, x, y); return y;
    }
    window.downloadShareCard = function(){
        if (!currentAch) return;
        var c = document.createElement('canvas'); c.width = 1080; c.height = 1080;
        var x = c.getContext('2d');
        var g = x.createLinearGradient(0,0,0,1080); g.addColorStop(0,'#061420'); g.addColorStop(1,'#13334F');
        x.fillStyle = g; x.fillRect(0,0,1080,1080);
        x.strokeStyle = '#C9A227'; x.lineWidth = 6; x.strokeRect(40,40,1000,1000);
        x.strokeStyle = 'rgba(201,162,39,.3)'; x.lineWidth = 2; x.strokeRect(60,60,960,960);
        x.fillStyle = '#C9A227'; x.beginPath(); x.arc(540,260,90,0,Math.PI*2); x.fill();
        x.fillStyle = '#0B2239'; x.font = 'bold 100px Georgia'; x.textAlign = 'center'; x.fillText('★', 540, 295);
        x.fillStyle = '#C9A227'; x.font = '600 34px Inter, sans-serif'; x.textAlign = 'center';
        x.fillText((currentAch.level + ' • ' + currentAch.year).toUpperCase(), 540, 420);
        x.fillStyle = '#F7F5F0'; x.font = '600 52px Georgia';
        var y = wrapText(x, currentAch.title, 540, 520, 860, 68);
        x.fillStyle = '#C9A227'; x.font = '400 40px Georgia'; x.fillText(currentAch.student, 540, y + 90);
        x.fillStyle = 'rgba(247,245,240,.6)'; x.font = '28px Inter, sans-serif';
        x.fillText('<?= html_escape(site_name()) ?>', 540, 980);
        var link = document.createElement('a');
        link.download = 'prestasi-' + currentAch.year + '.png';
        link.href = c.toDataURL('image/png');
        link.click();
        showToast('<?= $EN ? "Card downloaded!" : "Kartu diunduh!" ?>');
    };
})();
</script>