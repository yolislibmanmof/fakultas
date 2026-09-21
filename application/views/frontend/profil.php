<?php
$EN = (get_site_lang() == 'en');
$CI =& get_instance();
$CI->load->model('Setting_model');

// ===== 🔥 FIX KRITIS: $s tidak pernah di-load → buat fallback object =====
if (!isset($s) || !is_object($s)) {
    $s = new stdClass();
    $s->dean_name      = $CI->Setting_model->get('dean_name', 'Dekan');
    $s->history        = $CI->Setting_model->get('history', '');
    $s->vision         = $CI->Setting_model->get('vision', '');
    $s->mission        = $CI->Setting_model->get('mission', '');
    $s->dean_message   = $CI->Setting_model->get('dean_message', '');
}

// ===== Baca dari settings (anti-mismatch) =====
$dean_photo  = $CI->Setting_model->get('dean_photo', '');
$wadek_photo = $CI->Setting_model->get('wadek_1_photo', '');
$wadek_label = $CI->Setting_model->get('wadek_label', '') ?: ($EN ? 'Vice Dean' : 'Wakil Dekan');
$org_layout  = $CI->Setting_model->get('org_layout', 'tiered');
$dean_name   = $CI->Setting_model->get('dean_name', '') ?: ($s->dean_name ?? 'Dekan');
$wadek_name  = $CI->Setting_model->get('wadek_1_name', '');

// Drop caps untuk Sejarah & Sambutan
$hist_raw = trim((string)($s->history ?? ''));
$hist_first = $hist_raw !== '' ? mb_substr($hist_raw, 0, 1) : '';
$hist_rest  = $hist_raw !== '' ? mb_substr($hist_raw, 1) : '';
$msg_raw = trim((string)($s->dean_message ?? ''));
$msg_first = $msg_raw !== '' ? mb_substr($msg_raw, 0, 1) : '';
$msg_rest  = $msg_raw !== '' ? mb_substr($msg_raw, 1) : '';
$mission_lines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string)($s->mission ?? ''))), 'strlen'));

// ===== Data tambahan (optional) =====
$milestones = json_decode((string)$CI->Setting_model->get('faculty_milestones', ''), true);
if (!is_array($milestones)) $milestones = [];

$core_values = json_decode((string)$CI->Setting_model->get('core_values', ''), true);
if (!is_array($core_values)) $core_values = [];

$priorities = json_decode((string)$CI->Setting_model->get('dean_priorities', ''), true);
if (!is_array($priorities)) $priorities = [];

// ===== 🔥 STATS (fallback ke data controller) =====
$stats = [
    'dosen'    => isset($total_dosen) ? $total_dosen : (isset($lecturers) ? count($lecturers) : 0),
    'prodi'    => isset($programs) ? count($programs) : 0,
    'mahasiswa'=> isset($total_mahasiswa) ? $total_mahasiswa : 0,
    'alumni'   => isset($total_alumni) ? $total_alumni : 0,
];

// Helper: render foto atau inisial
function render_leader_photo($photo_url, $name, $size = 'w-20 h-20', $text_size = 'text-2xl', $style = 'rounded-full') {
    $initial = strtoupper(substr(trim($name ?? '?'), 0, 1));
    if ($photo_url && file_exists(FCPATH . 'assets/uploads/' . $photo_url)): ?>
        <img src="<?= base_url('assets/uploads/' . $photo_url) ?>" class="<?= $size ?> <?= $style ?> object-cover" alt="<?= html_escape($name) ?>">
    <?php else: ?>
        <div class="<?= $size ?> <?= $style ?> bg-navy text-gold flex items-center justify-center <?= $text_size ?> font-serif font-bold flex-shrink-0">
            <?= $initial ?>
        </div>
    <?php endif;
}
?>

<style>
/* ===== REVEAL ===== */
.rv-heritage { opacity: 0; transform: translateY(26px); transition: all .9s cubic-bezier(.22,1,.36,1); transition-delay: var(--d, 0s); }
.rv-heritage.in { opacity: 1; transform: none; }

/* ===== HERITAGE TIMELINE ===== */
.heritage-wrap { position: relative; }
.heritage-line { position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: linear-gradient(180deg, #C9A227 0%, #B8941F 60%, rgba(201,162,39,.2) 100%); transform: translateX(-50%); }
@media (max-width: 767px) { .heritage-line { left: 20px; } }
.heritage-item { position: relative; padding: 1.5rem 0; display: flex; justify-content: center; }
.heritage-dot {
    position: absolute; left: 50%; top: 2rem; transform: translateX(-50%);
    width: 18px; height: 18px; border-radius: 50%;
    background: #fff; border: 3px solid #C9A227; z-index: 2;
    transition: all .4s;
}
.heritage-item:hover .heritage-dot { background: #C9A227; transform: translateX(-50%) scale(1.3); box-shadow: 0 0 0 8px rgba(201,162,39,.15); }
.heritage-card {
    width: 45%; background: #fff; border: 1px solid #e5e7eb; padding: 1.5rem;
    transition: all .4s cubic-bezier(.22,1,.36,1); position: relative;
}
.heritage-item:nth-child(odd) .heritage-card { margin-right: auto; }
.heritage-item:nth-child(even) .heritage-card { margin-left: auto; }
.heritage-card:hover { transform: translateY(-6px); border-color: #C9A227; box-shadow: 0 20px 40px rgba(11,34,57,.12); }
.heritage-year {
    font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 700;
    color: #C9A227; line-height: 1; letter-spacing: -.02em;
}
@media (max-width: 767px) {
    .heritage-item { justify-content: flex-start; padding-left: 3rem; }
    .heritage-dot { left: 20px; }
    .heritage-card { width: 100%; margin-left: 0 !important; margin-right: 0 !important; }
}

/* ===== CORE VALUES ===== */
.cv-card {
    background: #fff; border: 1px solid #e5e7eb; padding: 2rem 1.5rem; text-align: center;
    transition: all .4s cubic-bezier(.22,1,.36,1); position: relative; overflow: hidden;
}
.cv-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #C9A227, #D4AF37, #C9A227);
    transform: scaleX(0); transform-origin: left; transition: transform .5s;
}
.cv-card:hover { transform: translateY(-8px); box-shadow: 0 24px 48px rgba(11,34,57,.14); border-color: rgba(201,162,39,.4); }
.cv-card:hover::before { transform: scaleX(1); }
.cv-icon {
    width: 64px; height: 64px; margin: 0 auto 1.2rem;
    background: linear-gradient(135deg, #0B2239, #13334F);
    color: #C9A227; display: flex; align-items: center; justify-content: center;
    border-radius: 50%; font-size: 1.6rem;
    transition: transform .5s cubic-bezier(.34,1.56,.64,1);
}
.cv-card:hover .cv-icon { transform: rotate(-10deg) scale(1.15); }

/* ===== DEAN PRIORITIES ===== */
.dp-item {
    display: flex; gap: 1.5rem; align-items: flex-start; padding: 1.5rem;
    background: #fff; border: 1px solid #e5e7eb; border-left: 4px solid #C9A227;
    transition: all .4s cubic-bezier(.22,1,.36,1);
}
.dp-item:hover { transform: translateX(8px); box-shadow: 0 16px 32px rgba(11,34,57,.1); border-left-color: #0B2239; }
.dp-num {
    font-family: 'Fraunces', serif; font-size: 3.5rem; font-weight: 300;
    color: #C9A227; line-height: .9; letter-spacing: -.02em; flex-shrink: 0;
    opacity: .7;
}
.dp-item:hover .dp-num { opacity: 1; transform: scale(1.1); transition: all .3s; }

/* ===== EMPTY STATE ===== */
.empty-hint {
    background: linear-gradient(135deg, #FAF8F3 0%, #F5EFE0 100%);
    border: 2px dashed rgba(201,162,39,.4); padding: 2rem; text-align: center;
}
.empty-hint .hint-icon {
    width: 56px; height: 56px; margin: 0 auto 1rem;
    background: rgba(201,162,39,.12); color: #C9A227;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}

/* ===== 🔥 STATS BAND ===== */
.pf-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
@media (max-width: 640px) { .pf-stats { grid-template-columns: repeat(2, 1fr); } }
.pf-stat { border-left: 2px solid #C9A227; padding-left: 1rem; }
.pf-stat-num { font-family: 'Fraunces', serif; font-size: 2.5rem; font-weight: 300; color: #C9A227; line-height: 1; }
.pf-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .2em; color: #64748b; margin-top: 4px; font-weight: 600; }

/* ===== 🔥 BREADCRUMB ===== */
.pf-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 11px; color: #64748b; margin-bottom: 2rem; flex-wrap: wrap; }
.pf-breadcrumb a { color: #64748b; text-decoration: none; transition: color .2s; }
.pf-breadcrumb a:hover { color: #C9A227; }
.pf-breadcrumb .sep { color: #cbd5e1; }

/* ===== 🔥 SHARE / PRINT ===== */
.pf-action-bar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.pf-action-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; background: transparent; border: 1px solid #e5e7eb;
    color: #0B2239; font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.1em; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.pf-action-btn:hover { background: #0B2239; color: #F7F5F0; border-color: #0B2239; }
.pf-action-btn.primary { background: #C9A227; color: #0B2239; border-color: #C9A227; }
.pf-action-btn.primary:hover { background: #0B2239; color: #C9A227; }

/* ===== 🔥 TOAST ===== */
.pf-toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 100;
    background: #0B2239; color: #F7F5F0; padding: 12px 20px;
    font-size: 13px; box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(120%); transition: transform .3s;
}
.pf-toast.show { transform: translateX(0); }
.pf-toast i { color: #C9A227; margin-right: 8px; }

/* ===== 🔥 CTA BROSUR ===== */
.pf-cta {
    background: linear-gradient(135deg, #0B2239 0%, #13334F 100%);
    color: #F7F5F0; padding: 3rem 2rem; text-align: center;
    position: relative; overflow: hidden;
}
.pf-cta::before {
    content: ''; position: absolute; top: -40%; right: -20%; width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(201,162,39,.2), transparent 70%);
    border-radius: 50%;
}

/* ===== PRINT ===== */
@media print {
    .pf-action-bar, .pf-cta, .pf-toast { display: none !important; }
    .rv-heritage { opacity: 1 !important; transform: none !important; }
}
</style>

<!-- ============ HEADER WITH TABS ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="absolute -bottom-16 -left-8 font-serif text-[18rem] leading-none text-navy/5 select-none pointer-events-none hidden lg:block">P</div>
    <div class="container mx-auto px-6 py-20 md:py-28 relative">

        <!-- 🔥 BREADCRUMB -->
        <div class="pf-breadcrumb">
            <a href="<?= base_url() ?>">Home</a>
            <span class="sep">/</span>
            <span class="text-navy font-semibold"><?= $EN ? 'Faculty Profile' : 'Profil Fakultas' ?></span>
            <?php if ($section): ?>
            <span class="sep">/</span>
            <span class="text-gold-muted">
                <?= $section == 'sejarah' ? ($EN ? 'History' : 'Sejarah') :
                   ($section == 'visi_misi' ? ($EN ? 'Vision & Mission' : 'Visi & Misi') :
                   ($section == 'struktur' ? ($EN ? 'Structure' : 'Struktur') :
                   ($EN ? "Dean's Welcome" : 'Sambutan Dekan'))) ?>
            </span>
            <?php endif; ?>
        </div>

        <div class="flex items-end justify-between gap-6 mb-12 flex-wrap">
            <div class="max-w-3xl">
                <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'About the Faculty' : 'Tentang Fakultas' ?></p>
                <h1 class="font-serif font-light text-navy tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                    <?= $EN ? 'Faculty <em class="italic text-gold-muted">Profile</em>' : 'Profil <em class="italic text-gold-muted">Fakultas</em>' ?>
                </h1>
            </div>

            <!-- 🔥 ACTION BAR -->
            <div class="pf-action-bar">
                <button type="button" onclick="shareProfile('facebook')" class="pf-action-btn" title="Share Facebook"><i class="fab fa-facebook-f"></i></button>
                <button type="button" onclick="shareProfile('twitter')" class="pf-action-btn" title="Share Twitter"><i class="fab fa-twitter"></i></button>
                <button type="button" onclick="shareProfile('whatsapp')" class="pf-action-btn" title="Share WhatsApp"><i class="fab fa-whatsapp"></i></button>
                <button type="button" onclick="copyLink()" class="pf-action-btn" title="Copy Link"><i class="fas fa-link"></i></button>
                <button type="button" onclick="window.print()" class="pf-action-btn" title="Print"><i class="fas fa-print"></i></button>
                <a href="<?= base_url('download') ?>" class="pf-action-btn primary"><i class="fas fa-download"></i><?= $EN ? 'Brochure' : 'Brosur' ?></a>
            </div>
        </div>

        <!-- 🔥 STATS BAND -->
        <?php if (array_sum($stats) > 0): ?>
        <div class="pf-stats rv-heritage">
            <?php if ($stats['dosen'] > 0): ?>
            <div class="pf-stat">
                <div class="pf-stat-num pf-count" data-count="<?= $stats['dosen'] ?>">0</div>
                <div class="pf-stat-label"><?= $EN ? 'Faculty' : 'Dosen' ?></div>
            </div>
            <?php endif; ?>
            <?php if ($stats['prodi'] > 0): ?>
            <div class="pf-stat">
                <div class="pf-stat-num pf-count" data-count="<?= $stats['prodi'] ?>">0</div>
                <div class="pf-stat-label"><?= $EN ? 'Programs' : 'Prodi' ?></div>
            </div>
            <?php endif; ?>
            <?php if ($stats['mahasiswa'] > 0): ?>
            <div class="pf-stat">
                <div class="pf-stat-num pf-count" data-count="<?= $stats['mahasiswa'] ?>">0</div>
                <div class="pf-stat-label"><?= $EN ? 'Students' : 'Mahasiswa' ?></div>
            </div>
            <?php endif; ?>
            <?php if ($stats['alumni'] > 0): ?>
            <div class="pf-stat">
                <div class="pf-stat-num pf-count" data-count="<?= $stats['alumni'] ?>">0</div>
                <div class="pf-stat-label">Alumni</div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mt-10">
            <?php $tabs = ['sejarah' => $EN ? 'History' : 'Sejarah', 'visi-misi' => $EN ? 'Vision & Mission' : 'Visi & Misi', 'struktur' => $EN ? 'Structure' : 'Struktur', 'sambutan' => $EN ? "Dean's Welcome" : 'Sambutan Dekan']; ?>
            <?php foreach ($tabs as $key => $label):
                $active = ($section == str_replace('-', '_', $key));
            ?>
                <a href="<?= base_url('profil/' . $key) ?>"
                   class="text-xs uppercase tracking-editorial font-semibold px-5 py-3 transition <?= $active ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CONTENT ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6 max-w-6xl">

        <?php if ($section == 'sejarah'): ?>
        <!-- SEJARAH -->
        <div class="bg-white border border-gray-200 p-8 md:p-12 fade-in relative overflow-hidden">
            <div class="absolute -top-8 -right-4 font-serif text-[10rem] leading-none text-navy/5 select-none pointer-events-none">"</div>
            <p class="editorial-label text-gold-muted mb-5"><?= $EN ? 'History' : 'Sejarah' ?></p>
            <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-[-0.02em] mb-10">
                <?= $EN ? 'Our <em class="italic text-gold-muted">Story</em>' : 'Sejarah <em class="italic text-gold-muted">Fakultas</em>' ?>
            </h2>
            <p class="text-gray-700 leading-[1.95] text-[16.5px]">
                <?php if ($hist_first !== ''): ?><span class="float-left mr-3 mt-2 font-serif text-6xl md:text-7xl leading-[0.75] text-gold-muted"><?= html_escape($hist_first) ?></span><?php endif; ?><?= nl2br(html_escape(ltrim($hist_rest))) ?>
            </p>
        </div>

        <?php if (!empty($milestones)): ?>
        <div class="mt-16 rv-heritage">
            <div class="text-center mb-12">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Milestones' : 'Tonggak Sejarah' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]">
                    <?= $EN ? 'Our <em class="italic text-gold-muted">Journey</em>' : 'Perjalanan <em class="italic text-gold-muted">Kami</em>' ?>
                </h2>
            </div>
            <div class="heritage-wrap">
                <div class="heritage-line"></div>
                <?php foreach ($milestones as $mi => $ms): ?>
                <div class="heritage-item rv-heritage" style="--d:<?= $mi * .08 ?>s">
                    <span class="heritage-dot"></span>
                    <div class="heritage-card">
                        <div class="heritage-year"><?= html_escape($ms['year'] ?? '') ?></div>
                        <h3 class="font-serif text-lg md:text-xl font-medium text-navy mt-2 mb-2"><?= html_escape($ms['title'] ?? '') ?></h3>
                        <?php if (!empty($ms['desc'])): ?>
                            <p class="text-sm text-slate leading-relaxed"><?= html_escape($ms['desc']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php elseif ($hist_raw && strlen($hist_raw) > 100): ?>
        <div class="mt-10 rv-heritage">
            <div class="empty-hint">
                <div class="hint-icon"><i class="fas fa-clock-rotate-left"></i></div>
                <p class="font-serif text-lg text-navy mb-2"><?= $EN ? 'Heritage Timeline not yet configured' : 'Timeline Sejarah belum dikonfigurasi' ?></p>
                <p class="text-xs text-slate uppercase tracking-wider"><?= $EN ? 'Add milestones in Admin → Settings → faculty_milestones (JSON)' : 'Tambahkan tonggak sejarah di Admin → Pengaturan → faculty_milestones (JSON)' ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php elseif ($section == 'visi_misi'): ?>
        <!-- VISI & MISI -->
        <div class="space-y-8">
            <?php if (!empty($s->vision)): ?>
            <div class="bg-navy text-ivory p-8 md:p-12 relative overflow-hidden fade-in">
                <div class="absolute inset-0 hero-pattern"></div>
                <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-gold/40"></div>
                <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-gold/40"></div>
                <div class="relative">
                    <p class="editorial-label text-gold mb-5"><?= $EN ? 'Vision' : 'Visi' ?></p>
                    <h2 class="font-serif text-4xl font-light tracking-[-0.02em] mb-8"><?= $EN ? 'Where we are <em class="italic text-gold">heading</em>' : 'Visi' ?></h2>
                    <p class="font-serif text-xl md:text-2xl font-light italic leading-relaxed">"<?= nl2br(html_escape($s->vision)) ?>"</p>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white border border-gray-200 p-8 md:p-12 fade-in">
                <p class="editorial-label text-gold-muted mb-5"><?= $EN ? 'Mission' : 'Misi' ?></p>
                <h2 class="font-serif text-4xl font-light text-navy tracking-[-0.02em] mb-10"><?= $EN ? 'How we get there' : 'Misi' ?></h2>
                <?php if (!empty($mission_lines)): ?>
                <div class="space-y-6">
                    <?php foreach ($mission_lines as $mi => $mline): ?>
                    <div class="flex gap-6 items-start group">
                        <span class="font-serif text-3xl md:text-4xl font-light text-gold-muted leading-none flex-shrink-0 w-12"><?= str_pad($mi + 1, 2, '0', STR_PAD_LEFT) ?></span>
                        <p class="text-gray-700 leading-relaxed pt-1"><?= html_escape(preg_replace('/^\d+[\.\)]\s*/', '', $mline)) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-gray-700 leading-loose text-[16.5px] space-y-5"><?= nl2br(html_escape($s->mission ?? '')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($core_values)): ?>
        <div class="mt-16 rv-heritage">
            <div class="text-center mb-12">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Foundation' : 'Landasan' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]">
                    <?= $EN ? 'Our <em class="italic text-gold-muted">Values</em>' : 'Nilai-Nilai <em class="italic text-gold-muted">Kami</em>' ?>
                </h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($core_values as $vi => $val): ?>
                <div class="cv-card rv-heritage" style="--d:<?= $vi * .08 ?>s">
                    <div class="cv-icon"><i class="fas <?= html_escape($val['icon'] ?? 'fa-star') ?>"></i></div>
                    <h3 class="font-serif text-xl font-medium text-navy mb-3"><?= html_escape($val['title'] ?? '') ?></h3>
                    <?php if (!empty($val['desc'])): ?>
                        <p class="text-sm text-slate leading-relaxed"><?= html_escape($val['desc']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php elseif ($section == 'struktur'): ?>
        <!-- STRUKTUR ORGANISASI -->
        <style>
        .org-ultra { position: relative; margin: 0 auto; cursor: pointer; }
        .org-stage { overflow-x: auto; padding: 1.5rem 0; scrollbar-width: thin; scrollbar-color: #C9A227 #EDE8DE; }
        .org-stage::-webkit-scrollbar { height: 8px; }
        .org-stage::-webkit-scrollbar-track { background: #EDE8DE; }
        .org-stage::-webkit-scrollbar-thumb { background: #C9A227; border-radius: 4px; }
        .org-inner { position: relative; min-width: max-content; margin: 0 auto; padding: 1rem 2.5rem; }
        .org-svg { position: absolute; top: 0; left: 0; pointer-events: none; z-index: 1; }
        .org-svg path { fill: none; stroke: #C9A227; stroke-width: 2; stroke-linecap: round; stroke-dasharray: 1000; stroke-dashoffset: 1000; }
        .org-ultra.draw .org-svg path { animation: orgDraw 1.2s cubic-bezier(.22,1,.36,1) forwards; animation-delay: var(--d, 0s); }
        @keyframes orgDraw { to { stroke-dashoffset: 0; } }

        .org-level { display: flex; justify-content: center; gap: 2rem; position: relative; z-index: 2; margin-bottom: 3rem; flex-wrap: nowrap; }
        .org-level:last-child { margin-bottom: 0; }
        .org-node {
            position: relative;
            background: linear-gradient(135deg, #fff 0%, #FAF8F3 100%);
            border: 1px solid #e5e7eb; border-top: 3px solid #C9A227;
            padding: 1.25rem 1rem; text-align: center; width: 200px; flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(11,34,57,.08);
            transition: transform .4s cubic-bezier(.22,1,.36,1), box-shadow .4s ease;
        }
        .org-node:hover { transform: translateY(-6px) rotateX(2deg); box-shadow: 0 20px 40px rgba(11,34,57,.18), 0 0 0 1px rgba(201,162,39,.3); }
        .org-node.dean { background: linear-gradient(135deg, #0B2239 0%, #13334F 100%); width: 240px; }
        .org-node.wadek { background: linear-gradient(135deg, #fff 0%, #F5EFE0 100%); width: 220px; }

        .org-photo-wrap { position: relative; width: 90px; height: 90px; margin: 0 auto .75rem; }
        .org-node.dean .org-photo-wrap { width: 110px; height: 110px; }
        .org-photo-wrap::before { content:''; position:absolute; inset:-4px; border-radius:50%; background: conic-gradient(from 0deg, #C9A227, #D4AF37, #B8941F, #C9A227); opacity:0; transition: opacity .4s ease; z-index:0; }
        .org-node:hover .org-photo-wrap::before { opacity:1; animation: orgRing 2s linear infinite; }
        @keyframes orgRing { to { transform: rotate(360deg); } }
        .org-photo-wrap::after { content:''; position:absolute; inset:-2px; border-radius:50%; background:#fff; z-index:1; }
        .org-node.dean .org-photo-wrap::after { background:#0B2239; }
        .org-photo { position:relative; width:100%; height:100%; border-radius:50%; overflow:hidden; z-index:2; transform:scale(0) rotateY(90deg); opacity:0; }
        .org-photo img { width:100%; height:100%; object-fit:cover; }
        .org-photo .init { width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#0B2239; color:#C9A227; font-family:'Fraunces',serif; font-size:2rem; font-weight:700; }

        .org-role { font-size:.6rem; letter-spacing:.25em; text-transform:uppercase; font-weight:700; color:#B8941F; margin-bottom:.25rem; }
        .org-node.dean .org-role { color:#C9A227; }
        .org-name { font-family:'Fraunces',serif; font-weight:600; color:#0B2239; line-height:1.25; font-size:.95rem; margin:0; }
        .org-node.dean .org-name { color:#F7F5F0; font-size:1.05rem; }
        .org-subtitle { font-size:.7rem; color:#64748b; margin-top:.25rem; font-style:italic; }
        .org-node.dean .org-subtitle { color:rgba(247,245,240,.7); }

        .org-ultra.draw .org-photo { animation: orgPop .9s cubic-bezier(.34,1.56,.64,1) forwards; animation-delay: var(--d, 0s); }
        @keyframes orgPop {
            0% { transform:scale(0) rotateY(90deg); opacity:0; }
            55% { transform:scale(1.18) rotateY(-8deg); opacity:1; }
            75% { transform:scale(.95) rotateY(4deg); opacity:1; }
            100% { transform:scale(1) rotateY(0deg); opacity:1; }
        }

        .org-hint { display:none; text-align:center; font-size:.65rem; letter-spacing:.3em; text-transform:uppercase; color:#B8941F; margin-top:.5rem; }
        .org-hint.show { display:block; animation: hintPulse 2s ease infinite; }
        @keyframes hintPulse { 0%,100%{opacity:.5} 50%{opacity:1} }
        </style>

        <?php
        if (!function_exists('org_node_ultra')) {
            function org_node_ultra($photo, $name, $role, $subtitle = '', $delay = 0, $extra_class = '') { ?>
                <div class="org-node <?= $extra_class ?>">
                    <div class="org-photo-wrap">
                        <div class="org-photo" style="--d:<?= $delay + 0.2 ?>s">
                            <?php if ($photo && file_exists(FCPATH . 'assets/uploads/' . $photo)): ?>
                                <img src="<?= base_url('assets/uploads/' . $photo) ?>" alt="<?= html_escape($name) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="init"><?= strtoupper(substr(trim($name ?: '?'), 0, 1)) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="org-role"><?= html_escape($role) ?></p>
                    <p class="org-name"><?= html_escape($name) ?></p>
                    <?php if ($subtitle): ?><p class="org-subtitle"><?= html_escape($subtitle) ?></p><?php endif; ?>
                </div>
            <?php }
        }

        $filled = function ($v) { return !empty($v) && $v !== '-'; };
        $has_wadek = $filled($wadek_name);

        $kaprodis = [];
        if (isset($programs) && is_array($programs)) {
            foreach ($programs as $p) {
                if (!empty($p->head_of_study_program)) {
                    $kaprodis[] = [
                        'name' => $p->head_of_study_program,
                        'role' => $EN ? 'Head' : 'Ketua',
                        'subtitle' => $p->name,
                        'photo' => ($p->head_photo ?? ''),
                    ];
                }
            }
        }
        ?>

        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10 fade-in">
                <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Organization Chart' : 'Bagan Organisasi' ?></p>
                <h2 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-[-0.02em]">
                    <?= $EN ? 'Faculty <em class="italic text-gold-muted">Leadership</em>' : 'Kepemimpinan <em class="italic text-gold-muted">Fakultas</em>' ?>
                </h2>
            </div>

            <div class="org-ultra" id="orgUltra" title="<?= $EN ? 'Click to replay' : 'Klik untuk memutar ulang' ?>">
                <div class="org-stage" id="orgStage">
                    <div class="org-inner" id="orgInner">
                        <svg class="org-svg" id="orgSvg"></svg>

                        <div class="org-level" id="levelDean">
                            <?php org_node_ultra($dean_photo, $dean_name, $EN ? 'Dean' : 'Dekan', $EN ? 'Faculty Leader' : 'Pemimpin Fakultas', 0, 'dean'); ?>
                        </div>

                        <?php if ($has_wadek && $org_layout == 'tiered'): ?>
                        <div class="org-level" id="levelWadek">
                            <?php org_node_ultra($wadek_photo, $wadek_name, $wadek_label, $EN ? 'Deputy' : 'Wakil', 0.6, 'wadek'); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($kaprodis) || ($has_wadek && $org_layout == 'flat')): ?>
                        <div class="org-level" id="levelKaprodi">
                            <?php if ($has_wadek && $org_layout == 'flat'): ?>
                                <?php org_node_ultra($wadek_photo, $wadek_name, $wadek_label, $EN ? 'Deputy' : 'Wakil', 0.6, 'wadek'); ?>
                            <?php endif; ?>
                            <?php foreach ($kaprodis as $i => $k):
                                $delay = (($has_wadek && $org_layout == 'tiered') ? 1.2 : 0.6) + ($i * 0.15);
                                org_node_ultra($k['photo'], $k['name'], $k['role'], $k['subtitle'], $delay);
                            endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="org-hint" id="orgHint">‹ <?= $EN ? 'scroll' : 'geser' ?> ›</p>
            </div>

            <p class="text-center text-xs text-slate mt-8">
                <i class="fas fa-hand-pointer text-gold-muted mr-1"></i><?= $EN ? 'Click the chart to replay the animation.' : 'Klik bagan untuk memutar ulang animasi.' ?>
            </p>
        </div>

        <script>
        (function(){
            var ultra = document.getElementById('orgUltra');
            if (!ultra) return;
            var inner = document.getElementById('orgInner');
            var stage = document.getElementById('orgStage');
            var svg = document.getElementById('orgSvg');
            var hint = document.getElementById('orgHint');

            function getBox(el) {
                var r = el.getBoundingClientRect();
                var pr = inner.getBoundingClientRect();
                return { cx: r.left - pr.left + r.width/2, bot: r.top - pr.top + r.height, top: r.top - pr.top };
            }

            function draw() {
                var w = inner.offsetWidth, h = inner.offsetHeight;
                svg.setAttribute('width', w); svg.setAttribute('height', h);
                svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
                var paths = '', c = 0;

                var dean = document.querySelector('#levelDean .org-node');
                var wadek = document.querySelector('#levelWadek .org-node');
                if (dean && wadek) {
                    var a = getBox(dean), b = getBox(wadek), m = (a.bot + b.top)/2;
                    paths += '<path d="M '+a.cx+' '+a.bot+' L '+a.cx+' '+m+' L '+b.cx+' '+m+' L '+b.cx+' '+b.top+'" style="--d:0.3s"/>';
                    c++;
                }
                var parent = wadek || dean;
                var kids = document.querySelectorAll('#levelKaprodi .org-node');
                if (parent && kids.length) {
                    var p = getBox(parent), f = getBox(kids[0]), m2 = (p.bot + f.top)/2;
                    paths += '<path d="M '+p.cx+' '+p.bot+' L '+p.cx+' '+m2+'" style="--d:'+(0.3+c*0.15)+'s"/>'; c++;
                    var minX = p.cx, maxX = p.cx;
                    kids.forEach(function(k){ var bb = getBox(k); if(bb.cx<minX)minX=bb.cx; if(bb.cx>maxX)maxX=bb.cx; });
                    paths += '<path d="M '+minX+' '+m2+' L '+maxX+' '+m2+'" style="--d:'+(0.3+c*0.15)+'s"/>'; c++;
                    kids.forEach(function(k, i){
                        var bb = getBox(k);
                        paths += '<path d="M '+bb.cx+' '+m2+' L '+bb.cx+' '+bb.top+'" style="--d:'+(0.5+c*0.1+i*0.05)+'s"/>';
                    });
                }
                svg.innerHTML = paths;

                if (hint) hint.classList.toggle('show', stage.scrollWidth > stage.clientWidth + 8);
            }

            function play() {
                ultra.classList.remove('draw');
                void ultra.offsetWidth;
                draw();
                ultra.classList.add('draw');
            }

            window.addEventListener('load', function(){ setTimeout(play, 200); });
            window.addEventListener('resize', function(){ if (ultra.classList.contains('draw')) draw(); });
            ultra.addEventListener('click', play);
        })();
        </script>

        <?php else: ?>
        <!-- SAMBUTAN DEKAN -->
        <div class="bg-white border border-gray-200 p-8 md:p-12 fade-in relative overflow-hidden">
            <div class="absolute -top-8 -right-4 font-serif text-[10rem] leading-none text-navy/5 select-none pointer-events-none">"</div>
            <div class="flex items-center gap-6 mb-10 pb-8 border-b border-gray-200">
                <div class="rounded-full overflow-hidden border-2 border-gold/50 shadow-md flex-shrink-0">
                    <?php render_leader_photo($dean_photo, $dean_name, 'w-20 h-20', 'text-2xl'); ?>
                </div>
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl font-medium text-navy tracking-[-0.01em]"><?= html_escape($dean_name) ?></h2>
                    <p class="editorial-label text-gold-muted mt-2"><?= $EN ? 'Dean of Faculty' : 'Dekan Fakultas' ?></p>
                </div>
            </div>
            <p class="text-gray-700 leading-[1.95] text-[16.5px]">
                <?php if ($msg_first !== ''): ?><span class="float-left mr-3 mt-2 font-serif text-6xl md:text-7xl leading-[0.75] text-gold-muted"><?= html_escape($msg_first) ?></span><?php endif; ?><?= nl2br(html_escape(ltrim($msg_rest))) ?>
            </p>
            <div class="mt-10 pt-8 border-t border-gray-200 flex items-center gap-4">
                <span class="w-16 h-px bg-gold"></span>
                <p class="font-serif italic text-navy"><?= html_escape($dean_name) ?></p>
            </div>
        </div>

        <?php if (!empty($priorities)): ?>
        <div class="mt-16 rv-heritage">
            <div class="text-center mb-12">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Strategic Focus' : 'Fokus Strategis' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]">
                    <?= $EN ? "Dean's <em class=\"italic text-gold-muted\">Priorities</em>" : 'Prioritas <em class="italic text-gold-muted">Dekan</em>' ?>
                </h2>
            </div>
            <div class="grid md:grid-cols-2 gap-5 max-w-4xl mx-auto">
                <?php foreach ($priorities as $pi => $pr): ?>
                <div class="dp-item rv-heritage" style="--d:<?= $pi * .1 ?>s">
                    <span class="dp-num"><?= str_pad($pi + 1, 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-serif text-lg md:text-xl font-medium text-navy mb-2"><?= html_escape($pr['title'] ?? '') ?></h3>
                        <?php if (!empty($pr['desc'])): ?>
                            <p class="text-sm text-slate leading-relaxed"><?= html_escape($pr['desc']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<!-- 🔥 CTA BROSUR -->
<?php if ($section !== 'struktur'): ?>
<section class="py-12 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="pf-cta rv-heritage">
            <div class="relative z-10">
                <p class="editorial-label text-gold mb-3"><?= $EN ? 'Get More Info' : 'Info Selengkapnya' ?></p>
                <h3 class="font-serif text-2xl md:text-3xl font-light mb-6">
                    <?= $EN ? 'Download our <em class="italic text-gold">brochure</em>' : 'Unduh <em class="italic text-gold">brosur</em> kami' ?>
                </h3>
                <a href="<?= base_url('download') ?>" class="btn-gold inline-block px-10 py-4 text-xs uppercase tracking-editorial font-bold">
                    <i class="fas fa-download mr-2"></i><?= $EN ? 'Download Brochure' : 'Unduh Brosur' ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="pf-toast" id="pfToast"><i class="fas fa-check-circle"></i><span id="pfToastMsg"></span></div>

<script>
(function(){
    // ===== REVEAL =====
    var rvObs = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if (e.isIntersecting) {
                e.target.classList.add('in');
                e.target.querySelectorAll('.pf-count').forEach(function(el){
                    if (el.dataset.done) return; el.dataset.done = '1';
                    var t = +el.dataset.count, st = performance.now();
                    (function f(n){
                        var p = Math.min(1, (n - st) / 1400), ease = 1 - Math.pow(1 - p, 3);
                        el.textContent = Math.round(t * ease).toLocaleString('id-ID');
                        if (p < 1) requestAnimationFrame(f);
                    })(st);
                });
                rvObs.unobserve(e.target);
            }
        });
    }, { threshold: .12 });
    document.querySelectorAll('.rv-heritage').forEach(function(el){ rvObs.observe(el); });

    // ===== TOAST =====
    function showToast(msg){
        var t = document.getElementById('pfToast');
        document.getElementById('pfToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }
    window.showToast = showToast;

    // ===== 🔥 SHARE =====
    var url = window.location.href;
    var title = <?= json_encode(($s->dean_name ?? 'Fakultas') . ' - ' . site_name()) ?>;

    window.shareProfile = function(platform){
        var shareUrl = '';
        switch(platform) {
            case 'facebook':
                shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
                break;
            case 'twitter':
                shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url);
                break;
            case 'whatsapp':
                shareUrl = 'https://wa.me/?text=' + encodeURIComponent(title + ' — ' + url);
                break;
        }
        if (shareUrl) window.open(shareUrl, '_blank');
    };

    window.copyLink = function(){
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url);
            showToast('<?= $EN ? "Link copied!" : "Link disalin!" ?>');
        }
    };
})();
</script>