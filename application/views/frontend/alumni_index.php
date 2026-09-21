<?php $EN = (get_site_lang() == 'en'); ?>

<?php
// ===== KOORDINAT PETA (inline, anti-mismatch) =====
$map_coords = [
    'Indonesia'=>[72,62],'Malaysia'=>[68,56],'Singapore'=>[67,58],'Thailand'=>[65,50],
    'Vietnam'=>[69,48],'Philippines'=>[76,48],'Japan'=>[82,35],'South Korea'=>[78,34],
    'China'=>[72,38],'India'=>[60,48],'Pakistan'=>[56,42],'Bangladesh'=>[62,46],
    'Saudi Arabia'=>[48,46],'UAE'=>[51,48],'Qatar'=>[50,48],'Turkey'=>[46,34],
    'Egypt'=>[45,44],'Nigeria'=>[38,56],'South Africa'=>[44,80],'Kenya'=>[48,62],
    'Germany'=>[39,28],'France'=>[37,30],'UK'=>[35,24],'Netherlands'=>[38,26],
    'Belgium'=>[37,28],'Spain'=>[34,34],'Italy'=>[39,34],'Russia'=>[55,20],
    'Canada'=>[18,22],'USA'=>[18,34],'Mexico'=>[14,44],'Brazil'=>[27,68],
    'Argentina'=>[25,82],'Australia'=>[82,78],'New Zealand'=>[90,85],
];
// ===== NORMALISASI ALIAS NEGARA =====
$country_aliases = [
    'united kingdom'=>'UK','inggris'=>'UK','britain'=>'UK','england'=>'UK',
    'united states'=>'USA','amerika serikat'=>'USA','amerika'=>'USA','us'=>'USA',
    'jepang'=>'Japan','korea selatan'=>'South Korea','korea'=>'South Korea',
    'tiongkok'=>'China','arab saudi'=>'Saudi Arabia','saudi'=>'Saudi Arabia',
    'uni emirat arab'=>'UAE','emirates'=>'UAE','turki'=>'Turkey','mesir'=>'Egypt',
    'afrika selatan'=>'South Africa','jerman'=>'Germany','prancis'=>'France','perancis'=>'France',
    'belanda'=>'Netherlands','spanyol'=>'Spain','italia'=>'Italy','rusia'=>'Russia',
    'kanada'=>'Canada','meksiko'=>'Mexico','brasil'=>'Brazil','selandia baru'=>'New Zealand',
    'singapura'=>'Singapore','filipina'=>'Philippines',
];
function al_country_key($raw, $aliases) {
    $raw = trim((string)$raw);
    if ($raw === '') return 'Indonesia';
    $low = strtolower($raw);
    if (isset($aliases[$low])) return $aliases[$low];
    // sudah canonical?
    return $raw;
}
?>

<style>
/* ===== PETA DUNIA ===== */
.world-map {
    position: relative; width: 100%; aspect-ratio: 2 / 1;
    background:
        radial-gradient(ellipse at 30% 40%, rgba(201,162,39,.15) 0%, transparent 50%),
        radial-gradient(ellipse at 70% 70%, rgba(19,51,79,.4) 0%, transparent 50%),
        linear-gradient(135deg, #0B2239 0%, #13334F 100%);
    overflow: hidden;
    border-top: 3px solid #C9A227;
}
.world-map::before {
    content:''; position:absolute; inset:0;
    background-image:
        linear-gradient(rgba(201,162,39,.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(201,162,39,.08) 1px, transparent 1px);
    background-size: 5% 10%;
    pointer-events:none;
}
.world-map .region-label {
    position:absolute; font-family:'JetBrains Mono',monospace;
    font-size:9px; letter-spacing:.2em; color:rgba(247,245,240,.25);
    text-transform:uppercase; pointer-events:none;
}
.map-dot {
    position:absolute; transform:translate(-50%, -50%);
    width:14px; height:14px;
    cursor:pointer; z-index:3;
}
.map-dot .dot-core {
    position:absolute; inset:4px; border-radius:50%;
    background: #C9A227;
    box-shadow: 0 0 12px #C9A227;
    transition: all .3s;
}
.map-dot .dot-pulse {
    position:absolute; inset:0; border-radius:50%;
    border:2px solid #C9A227;
    animation: dotPulse 2s ease-out infinite;
}
.map-dot.large .dot-core { inset:2px; }
.map-dot.large .dot-pulse { animation-duration: 2.5s; }
.map-dot.small .dot-core { inset:5px; }
.map-dot.small .dot-pulse { animation-duration: 1.5s; }
@keyframes dotPulse {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(2.8); opacity: 0; }
}
.map-dot:hover .dot-core { background:#F7F5F0; box-shadow: 0 0 20px #C9A227, 0 0 40px rgba(201,162,39,.5); transform:scale(1.4); }
.map-dot.selected .dot-core { background:#F7F5F0; box-shadow: 0 0 20px #C9A227, 0 0 40px rgba(201,162,39,.6); transform:scale(1.5); }
.map-dot.selected .dot-pulse { border-color:#F7F5F0; }

/* Tooltip peta */
.map-tooltip {
    position:absolute; z-index:10;
    background:#0B2239; color:#F7F5F0;
    border:1px solid rgba(201,162,39,.4); border-left:3px solid #C9A227;
    padding:.6rem .9rem; min-width:180px;
    font-family:'JetBrains Mono',monospace; font-size:11px;
    pointer-events:none; opacity:0; transform:translateY(6px);
    transition:all .2s;
    box-shadow:0 8px 24px rgba(0,0,0,.4);
}
.map-tooltip.visible { opacity:1; transform:translateY(0); }
.map-tooltip .tt-country { font-family:'Fraunces',serif; font-size:14px; color:#C9A227; font-weight:600; margin-bottom:4px; }
.map-tooltip .tt-count { font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:#F7F5F0; line-height:1; }
.map-tooltip .tt-cities { margin-top:6px; padding-top:6px; border-top:1px dashed rgba(201,162,39,.3); color:rgba(247,245,240,.7); font-size:10px; line-height:1.6; }
.map-tooltip .tt-hint { margin-top:6px; color:rgba(201,162,39,.8); font-size:9px; letter-spacing:.1em; text-transform:uppercase; }

/* ===== CHART ANGKATAN (BAR) ===== */
.year-chart { display:flex; align-items:flex-end; gap:4px; height:140px; }
.year-bar { flex:1; background:linear-gradient(180deg, #C9A227 0%, #B8941F 100%); position:relative; transition:all .4s cubic-bezier(.22,1,.36,1); min-height:4px; cursor:pointer; }
.year-bar:hover { filter:brightness(1.2); transform:scaleY(1.02); }
.year-bar::after { content: attr(data-count); position:absolute; top:-18px; left:50%; transform:translateX(-50%); font-family:'JetBrains Mono',monospace; font-size:10px; color:#0B2239; font-weight:700; opacity:0; transition:opacity .3s; }
.year-bar:hover::after { opacity:1; }
.year-bar .yr-label { position:absolute; bottom:-20px; left:50%; transform:translateX(-50%); font-family:'JetBrains Mono',monospace; font-size:9px; color:#64748b; letter-spacing:.1em; }

/* ===== HALL OF FAME CARDS ===== */
.fame-card {
    position:relative; overflow:hidden;
    background: linear-gradient(135deg, #fff 0%, #FAF8F3 100%);
    border:1px solid #e5e7eb;
    border-top:3px solid #C9A227;
    transition:all .5s cubic-bezier(.22,1,.36,1);
    transform-style: preserve-3d;
    display:block;
}
.fame-card:hover { transform:translateY(-8px); box-shadow:0 24px 48px rgba(11,34,57,.2); }
.fame-card::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:100px; height:100px;
    background: radial-gradient(circle, rgba(201,162,39,.2), transparent 70%);
    border-radius:50%;
    transition:all .6s;
}
.fame-card:hover::before { transform:scale(2); }
.fame-badge {
    position:absolute; top:12px; left:12px;
    background:#C9A227; color:#0B2239;
    font-family:'JetBrains Mono',monospace; font-size:9px;
    letter-spacing:.2em; font-weight:700; text-transform:uppercase;
    padding:3px 10px;
    z-index:2;
}
.fame-photo {
    position:relative; width:100%; aspect-ratio:1;
    background: linear-gradient(135deg, #0B2239, #13334F);
    overflow:hidden;
}
.fame-photo img { width:100%; height:100%; object-fit:cover; transition:transform .6s; }
.fame-card:hover .fame-photo img { transform:scale(1.08); }
.fame-photo .fame-initial {
    width:100%; height:100%; display:flex; align-items:center; justify-content:center;
    font-family:'Fraunces',serif; font-size:4rem; font-weight:700;
    color:#C9A227;
}

/* ===== DIRECTORI CARDS ===== */
.dir-card { transition:all .4s cubic-bezier(.22,1,.36,1); }
.dir-card:hover { transform:translateY(-6px); box-shadow:0 16px 32px rgba(11,34,57,.15); }
.dir-card.hidden-filter { display:none !important; }

/* ===== COUNTER ANIM ===== */
.counter-alumni { font-family:'Fraunces',serif; font-weight:300; line-height:1; color:#C9A227; }

/* ===== REVEAL ===== */
.al-reveal { opacity:0; transform:translateY(24px); transition:all .9s cubic-bezier(.22,1,.36,1); }
.al-reveal.in { opacity:1; transform:none; }

/* ===== LIVE SEARCH ===== */
.search-box { position:relative; }
.search-box .search-icon { position:absolute; left:1.2rem; top:50%; transform:translateY(-50%); color:#C9A227; }
.search-box input { padding-left:3rem; }

/* ===== ACTIVE COUNTRY CHIP ===== */
.country-chip {
    display:none; align-items:center; gap:8px;
    background:#0B2239; color:#F7F5F0;
    padding:6px 14px; font-size:11px; font-weight:600;
}
.country-chip.visible { display:inline-flex; }
.country-chip button { background:none; border:none; color:#C9A227; cursor:pointer; font-size:12px; }
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute -bottom-24 -right-8 font-serif text-[16rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">A</div>
    <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="max-w-3xl">
                <p class="editorial-label text-gold mb-6"><?= $EN ? 'Global Alumni Network' : 'Jaringan Alumni Global' ?></p>
                <h1 class="font-serif font-light tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                    <?= $EN ? 'Our <em class="italic text-gold">Alumni</em> Directory' : 'Direktori <em class="italic text-gold">Alumni</em>' ?>
                </h1>
                <p class="text-ivory/70 mt-6 text-lg leading-relaxed max-w-2xl">
                    <?= $EN ? 'Connecting graduates across industries and countries — our pride spreading impact worldwide.' : 'Menghubungkan para lulusan lintas industri dan negara — kebanggaan kami yang tersebar memberi dampak di seluruh dunia.' ?>
                </p>
            </div>
            <div class="hidden md:flex gap-10 text-right">
                <div>
                    <div class="counter-alumni text-6xl md:text-7xl" data-count="<?= $stats['total'] ?>"><?= $stats['total'] ?></div>
                    <p class="editorial-label text-ivory/60 mt-2"><?= $EN ? 'Alumni' : 'Alumni' ?></p>
                </div>
                <div>
                    <div class="counter-alumni text-6xl md:text-7xl text-ivory/80" data-count="<?= $stats['countries'] ?>"><?= $stats['countries'] ?></div>
                    <p class="editorial-label text-ivory/60 mt-2"><?= $EN ? 'Countries' : 'Negara' ?></p>
                </div>
                <div>
                    <div class="counter-alumni text-6xl md:text-7xl text-ivory/60" data-count="<?= $stats['industries'] ?>"><?= $stats['industries'] ?></div>
                    <p class="editorial-label text-ivory/60 mt-2"><?= $EN ? 'Industries' : 'Industri' ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============  WORLD MAP SECTION ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6 max-w-6xl">
        <div class="al-reveal mb-12">
            <p class="editorial-label text-gold-muted mb-3">Global Footprint</p>
            <h2 class="font-serif text-3xl md:text-5xl font-light text-navy tracking-tight">
                <?= $EN ? 'Where our alumni <em class="italic text-gold-muted">live</em>' : 'Di mana alumni kami <em class="italic text-gold-muted">berada</em>' ?>
            </h2>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 al-reveal" style="transition-delay:.15s">
            <!-- PETA -->
            <div class="lg:col-span-8">
                <div class="world-map" id="worldMap">
                    <span class="region-label" style="left:8%; top:15%;">Americas</span>
                    <span class="region-label" style="left:35%; top:15%;">Europe</span>
                    <span class="region-label" style="left:55%; top:12%;">Asia</span>
                    <span class="region-label" style="left:42%; top:55%;">Africa</span>
                    <span class="region-label" style="left:78%; top:70%;">Oceania</span>

                    <?php foreach ($map_data as $md):
                        $key = al_country_key($md['country'], $country_aliases);
                        if (!isset($map_coords[$key])) continue; // negara tak dikenal → skip dot, tetap ada di ranking
                        $xy = $map_coords[$key];
                        $size = $md['count'] >= 5 ? 'large' : ($md['count'] >= 2 ? '' : 'small');
                    ?>
                    <div class="map-dot <?= $size ?>"
                         style="left:<?= $xy[0] ?>%; top:<?= $xy[1] ?>%"
                         data-c="<?= html_escape($key) ?>"
                         data-count="<?= $md['count'] ?>"
                         data-cities='<?= htmlspecialchars(json_encode($md['cities'])) ?>'
                         title="<?= html_escape($md['country']) ?>: <?= $md['count'] ?> alumni">
                        <span class="dot-pulse"></span>
                        <span class="dot-core"></span>
                    </div>
                    <?php endforeach; ?>

                    <div class="map-tooltip" id="mapTooltip"></div>
                </div>
                <p class="text-xs text-slate mt-4 italic"><i class="fas fa-hand-pointer text-gold-muted mr-1"></i><?= $EN ? 'Hover the golden dots to see distribution. <strong>Click</strong> a dot to filter the directory by country.' : 'Arahkan kursor ke titik emas untuk melihat sebaran. <strong>Klik</strong> titik untuk memfilter direktori per negara.' ?></p>
            </div>

            <!-- RANKING NEGARA -->
            <div class="lg:col-span-4">
                <div class="bg-white border border-gray-200 p-6 h-full">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Top Countries' : 'Negara Teratas' ?></p>
                    <div class="space-y-3">
                        <?php foreach (array_slice($map_data, 0, 8) as $i => $md):
                            $pct = $stats['total'] > 0 ? round(($md['count'] / $stats['total']) * 100) : 0;
                        ?>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-navy text-gold flex items-center justify-center font-serif text-xs font-bold flex-shrink-0"><?= $i + 1 ?></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <span class="text-sm font-medium text-navy truncate"><?= html_escape($md['country']) ?></span>
                                    <span class="font-mono text-xs text-gold-muted font-bold"><?= $md['count'] ?></span>
                                </div>
                                <div class="h-1 bg-ivory-warm overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-navy to-gold bar-country" style="width:0%" data-w="<?= $pct ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ CHART ANGKATAN + TOP COMPANIES ============ -->
<section class="py-16 md:py-24 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6 max-w-6xl">
        <div class="grid lg:grid-cols-12 gap-8">
            <!-- CHART ANGKATAN -->
            <div class="lg:col-span-7 bg-ivory border border-gray-200 p-8 al-reveal">
                <p class="editorial-label text-gold-muted mb-3">Graduation Cohorts</p>
                <h3 class="font-serif text-2xl md:text-3xl font-light text-navy mb-8">
                    <?= $EN ? 'By <em class="italic text-gold-muted">year</em>' : 'Per <em class="italic text-gold-muted">angkatan</em>' ?>
                </h3>
                <?php if (empty($year_chart)): ?>
                    <p class="text-slate text-sm">Belum ada data angkatan.</p>
                <?php else:
                    $maxYear = max(array_map(function($y){ return $y->count; }, $year_chart));
                ?>
                <div class="year-chart">
                    <?php foreach ($year_chart as $yc):
                        $h = $maxYear > 0 ? ($yc->count / $maxYear) * 100 : 0;
                    ?>
                    <div class="year-bar" data-count="<?= $yc->count ?>" style="height:<?= max(4, $h) ?>%">
                        <span class="yr-label"><?= substr($yc->graduation_year, 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-slate mt-6 italic"><i class="fas fa-info-circle text-gold-muted mr-1"></i>Hover bar untuk lihat jumlah alumni.</p>
                <?php endif; ?>
            </div>

            <!-- TOP COMPANIES -->
            <div class="lg:col-span-5 bg-ivory border border-gray-200 p-8 al-reveal" style="transition-delay:.1s">
                <p class="editorial-label text-gold-muted mb-3">Top Employers</p>
                <h3 class="font-serif text-2xl md:text-3xl font-light text-navy mb-8">
                    <?= $EN ? 'Where they <em class="italic text-gold-muted">work</em>' : 'Di mana mereka <em class="italic text-gold-muted">bekerja</em>' ?>
                </h3>
                <?php if (empty($top_companies)): ?>
                    <p class="text-slate text-sm">Belum ada data perusahaan.</p>
                <?php else:
                    $maxCo = max(array_map(function($c){ return $c->count; }, $top_companies));
                    foreach ($top_companies as $co):
                        $pct = $maxCo > 0 ? ($co->count / $maxCo) * 100 : 0;
                ?>
                <div class="mb-4">
                    <div class="flex justify-between items-baseline mb-1.5">
                        <span class="text-sm font-medium text-navy truncate pr-3"><?= html_escape($co->company) ?></span>
                        <span class="font-mono text-xs text-gold-muted font-bold flex-shrink-0"><?= $co->count ?></span>
                    </div>
                    <div class="h-1.5 bg-white overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-gold to-gold-muted bar-company" style="width:0%" data-w="<?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============ HALL OF FAME ============ -->
<?php if (!empty($hall_of_fame)): ?>
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6 max-w-6xl">
        <div class="flex items-end justify-between mb-10 al-reveal flex-wrap gap-4">
            <div>
                <p class="editorial-label text-gold-muted mb-3">Featured Alumni</p>
                <h2 class="font-serif text-3xl md:text-5xl font-light text-navy tracking-tight">
                    <?= $EN ? 'Hall of <em class="italic text-gold-muted">Fame</em>' : 'Alumni <em class="italic text-gold-muted">Unggulan</em>' ?>
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-10 h-px bg-gold"></span>
                <p class="editorial-label text-slate"><?= count($hall_of_fame) ?> <?= $EN ? 'profiles' : 'profil' ?></p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($hall_of_fame as $hf): ?>
            <a href="<?= base_url('alumni/view/' . $hf->id) ?>" class="fame-card al-reveal">
                <div class="fame-badge"><i class="fas fa-star mr-1"></i>Fame</div>
                <div class="fame-photo">
                    <?php if (!empty($hf->photo) && file_exists(FCPATH . 'assets/uploads/' . $hf->photo)): ?>
                        <img src="<?= base_url('assets/uploads/' . $hf->photo) ?>" alt="<?= html_escape($hf->full_name) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="fame-initial"><?= strtoupper(substr($hf->full_name, 0, 1)) ?></div>
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <h4 class="font-serif text-lg font-medium text-navy leading-snug mb-1"><?= html_escape($hf->full_name) ?></h4>
                    <p class="text-xs text-slate leading-snug line-clamp-2 mb-2">
                        <?= html_escape($hf->current_position ?? '') ?>
                        <?php if ($hf->company): ?><span class="text-gold-muted font-semibold"> @ <?= html_escape($hf->company) ?></span><?php endif; ?>
                    </p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <span class="font-mono text-[10px] uppercase tracking-wider text-slate"><?= $hf->graduation_year ?></span>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-gold-muted"><?= $EN ? 'View' : 'Lihat' ?> →</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ FILTER + DIREKTORI ============ -->
<section class="py-16 md:py-24 bg-white border-t border-gray-200" id="directorySection">
    <div class="container mx-auto px-6 max-w-6xl">

        <div class="mb-10 al-reveal">
            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Full Directory' : 'Direktori Lengkap' ?></p>
            <h2 class="font-serif text-3xl md:text-5xl font-light text-navy tracking-tight">
                <?= $EN ? 'Find your <em class="italic text-gold-muted">batchmate</em>' : 'Temukan <em class="italic text-gold-muted">rekan</em> Anda' ?>
            </h2>
        </div>

        <!-- Live Filter Bar -->
        <div class="bg-ivory border border-gray-200 p-6 mb-10 al-reveal">
            <div class="grid md:grid-cols-4 gap-4">
                <div class="md:col-span-2 search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="liveSearch" placeholder="<?= $EN ? 'Search name, company, position...' : 'Cari nama, perusahaan, posisi...' ?>"
                        class="w-full px-4 py-3 border border-navy/20 bg-white text-navy focus:border-gold outline-none transition">
                </div>
                <select id="filterYear" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
                    <option value=""><?= $EN ? 'All Years' : 'Semua Tahun' ?></option>
                    <?php foreach ($years as $y): ?>
                    <option value="<?= $y->graduation_year ?>"><?= $y->graduation_year ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="filterProdi" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
                    <option value=""><?= $EN ? 'All Programs' : 'Semua Prodi' ?></option>
                    <?php foreach ($programs as $p): ?>
                    <option value="<?= $p->id ?>"><?= html_escape($p->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs flex-wrap gap-3">
                <span class="text-slate" id="resultCount"><i class="fas fa-users text-gold-muted mr-1"></i><?= count($alumni) ?> <?= $EN ? 'alumni found' : 'alumni ditemukan' ?></span>
                <span class="country-chip" id="countryChip">
                    <i class="fas fa-globe text-gold"></i>
                    <span id="countryChipLabel"></span>
                    <button type="button" id="clearCountry" title="Clear"><i class="fas fa-times"></i></button>
                </span>
                <button type="button" id="resetFilter" class="text-gold-muted hover:text-navy uppercase tracking-wider font-semibold"><i class="fas fa-undo mr-1"></i>Reset</button>
            </div>
        </div>

        <!-- Cards Grid -->
        <?php if (empty($alumni)): ?>
            <div class="text-center py-24 bg-ivory border border-gray-200">
                <i class="fas fa-user-graduate text-5xl text-gray-300 mb-5"></i>
                <p class="font-serif text-2xl text-navy font-light"><?= $EN ? 'No alumni found.' : 'Belum ada alumni.' ?></p>
            </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7" id="alumniGrid">
            <?php foreach ($alumni as $a): ?>
            <div class="dir-card bg-white border border-gray-200 group relative overflow-hidden flex flex-col"
                 data-name="<?= strtolower(html_escape($a->full_name)) ?>"
                 data-company="<?= strtolower(html_escape($a->company ?? '')) ?>"
                 data-position="<?= strtolower(html_escape($a->current_position ?? '')) ?>"
                 data-year="<?= $a->graduation_year ?>"
                 data-prodi="<?= $a->study_program_id ?>"
                 data-country="<?= html_escape(al_country_key($a->country ?? '', $country_aliases)) ?>">

                <div class="absolute top-0 left-0 right-0 h-[3px] bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-10"></div>

                <div class="p-7 flex-1">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-navy text-gold flex items-center justify-center font-serif text-2xl flex-shrink-0 border-2 border-gold/40">
                            <?php if (!empty($a->photo) && file_exists(FCPATH . 'assets/uploads/' . $a->photo)): ?>
                                <img src="<?= base_url('assets/uploads/' . $a->photo) ?>" class="w-full h-full object-cover" alt="" loading="lazy">
                            <?php else: ?>
                                <?= strtoupper(substr($a->full_name, 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <span class="bg-navy text-ivory text-[10px] uppercase tracking-editorial px-3 py-1.5 font-semibold"><?= $a->graduation_year ?></span>
                    </div>

                    <h3 class="font-serif text-xl font-medium text-navy leading-snug group-hover:text-gold-muted transition mb-2">
                        <?= html_escape($a->full_name) ?>
                    </h3>
                    <p class="text-sm text-slate leading-relaxed mb-1">
                        <?php if ($a->current_position): ?><?= html_escape($a->current_position) ?><?php endif; ?>
                        <?php if ($a->company): ?> <span class="text-gold-muted font-semibold">@ <?= html_escape($a->company) ?></span><?php endif; ?>
                    </p>
                    <p class="text-xs text-slate mt-2">
                        <i class="fas fa-map-marker-alt text-gold-muted mr-1"></i><?= html_escape($a->city ?? '') ?><?= $a->country && $a->country !== 'Indonesia' ? ', ' . html_escape($a->country) : '' ?>
                        <?php if ($a->prodi_name): ?><span class="mx-2 text-gray-300">•</span><?= html_escape($a->prodi_name) ?><?php endif; ?>
                    </p>
                </div>

                <div class="px-7 py-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="<?= base_url('alumni/view/' . $a->id) ?>" class="text-xs uppercase tracking-editorial font-semibold text-navy hover:text-gold transition">
                        <?= $EN ? 'View Profile' : 'Lihat Profil' ?> &rarr;
                    </a>
                    <div class="flex gap-2">
                        <?php if ($a->linkedin_url): ?>
                        <a href="<?= html_escape($a->linkedin_url) ?>" target="_blank" rel="noopener" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition">
                            <i class="fab fa-linkedin-in text-xs"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($a->website_url): ?>
                        <a href="<?= html_escape($a->website_url) ?>" target="_blank" rel="noopener" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition">
                            <i class="fas fa-globe text-xs"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ CTA JOIN ============ -->
<section class="py-16 md:py-24 bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-gold/40"></div>
    <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-gold/40"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-2xl mx-auto text-center al-reveal">
            <p class="editorial-label text-gold mb-4"><?= $EN ? 'Are you our alumni?' : 'Anda alumni kami?' ?></p>
            <h3 class="font-serif text-3xl md:text-5xl font-light tracking-[-0.02em] mb-8">
                <?= $EN ? 'Join the directory & reconnect' : 'Bergabung dengan direktori & terhubung kembali' ?>
            </h3>
            <a href="<?= base_url('alumni/register') ?>" class="btn-gold inline-block px-10 py-4 font-semibold uppercase tracking-editorial text-xs">
                <i class="fas fa-user-plus mr-2"></i><?= $EN ? 'Register Now' : 'Daftar Sekarang' ?>
            </a>
        </div>
    </div>
</section>

<script>
(function(){
    // ===== COUNTER ANIMATION =====
    var io = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            var el = e.target;
            if(el.dataset.done) return; el.dataset.done='1';
            var target = parseInt(el.dataset.count) || 0;
            var duration = 1500, start = performance.now();
            function anim(now){
                var p = Math.min((now - start) / duration, 1);
                var ease = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.floor(ease * target).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(anim);
                else el.textContent = target.toLocaleString('id-ID');
            }
            requestAnimationFrame(anim);
            io.unobserve(el);
        });
    }, { threshold: .3 });
    document.querySelectorAll('.counter-alumni').forEach(function(el){ io.observe(el); });

    // ===== REVEAL ON SCROLL =====
    var revealObs = new IntersectionObserver(function(es){
        es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); revealObs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.al-reveal').forEach(function(el){ revealObs.observe(el); });

    // ===== BAR ANIMATION =====
    var barObs = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            var el = e.target;
            el.querySelectorAll('.bar-country, .bar-company').forEach(function(b){
                setTimeout(function(){ b.style.width = b.dataset.w; }, 300);
            });
            barObs.unobserve(el);
        });
    }, { threshold: .2 });
    document.querySelectorAll('.bg-white, .bg-ivory').forEach(function(el){ barObs.observe(el); });

    // ===== MAP TOOLTIP + CLICK-TO-FILTER =====
    var map = document.getElementById('worldMap');
    var tooltip = document.getElementById('mapTooltip');
    var activeCountry = '';
    var countryChip = document.getElementById('countryChip');
    var countryChipLabel = document.getElementById('countryChipLabel');

    if (map && tooltip) {
        map.addEventListener('mouseover', function(e){
            var dot = e.target.closest('.map-dot');
            if (!dot) { tooltip.classList.remove('visible'); return; }
            var c = dot.dataset.c, count = dot.dataset.count;
            var cities = {};
            try { cities = JSON.parse(dot.dataset.cities); } catch(err){}
            var cityList = Object.keys(cities).slice(0, 4).map(function(k){
                return '· ' + k + ' <span style="color:#C9A227">(' + cities[k] + ')</span>';
            }).join('<br>');
            tooltip.innerHTML = '<div class="tt-country">' + c + '</div>' +
                '<div class="tt-count">' + count + ' <span style="font-size:10px;color:rgba(247,245,240,.5)">alumni</span></div>' +
                (cityList ? '<div class="tt-cities">' + cityList + '</div>' : '') +
                '<div class="tt-hint"><i class="fas fa-mouse-pointer mr-1"></i>Click to filter directory</div>';
            tooltip.classList.add('visible');
        });
        map.addEventListener('mousemove', function(e){
            if(!tooltip.classList.contains('visible')) return;
            var rect = map.getBoundingClientRect();
            var x = e.clientX - rect.left + 15;
            var y = e.clientY - rect.top + 15;
            if (x + 200 > rect.width) x = e.clientX - rect.left - 200;
            if (y + 120 > rect.height) y = e.clientY - rect.top - 120;
            tooltip.style.left = x + 'px';
            tooltip.style.top = y + 'px';
        });
        map.addEventListener('mouseout', function(e){
            if (!e.target.closest('.map-dot')) tooltip.classList.remove('visible');
        });

        // Klik dot → filter direktori
        map.addEventListener('click', function(e){
            var dot = e.target.closest('.map-dot');
            if (!dot) return;
            var c = dot.dataset.c;
            if (activeCountry === c) {
                activeCountry = '';
                dot.classList.remove('selected');
            } else {
                map.querySelectorAll('.map-dot.selected').forEach(function(d){ d.classList.remove('selected'); });
                activeCountry = c;
                dot.classList.add('selected');
            }
            updateChip();
            applyFilter();
            document.getElementById('directorySection').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    function updateChip(){
        if (activeCountry) {
            countryChipLabel.textContent = activeCountry;
            countryChip.classList.add('visible');
        } else {
            countryChip.classList.remove('visible');
        }
    }
    document.getElementById('clearCountry').addEventListener('click', function(){
        activeCountry = '';
        map.querySelectorAll('.map-dot.selected').forEach(function(d){ d.classList.remove('selected'); });
        updateChip();
        applyFilter();
    });

    // ===== LIVE FILTER DIREKTORI =====
    var searchInput = document.getElementById('liveSearch');
    var yearSel = document.getElementById('filterYear');
    var prodiSel = document.getElementById('filterProdi');
    var grid = document.getElementById('alumniGrid');
    var countEl = document.getElementById('resultCount');
    var resetBtn = document.getElementById('resetFilter');

    function applyFilter(){
        if (!grid) return;
        var q = (searchInput.value || '').toLowerCase();
        var year = yearSel.value;
        var prodi = prodiSel.value;
        var shown = 0;
        grid.querySelectorAll('.dir-card').forEach(function(card){
            var name = card.dataset.name || '';
            var company = card.dataset.company || '';
            var position = card.dataset.position || '';
            var cy = card.dataset.year || '';
            var cp = card.dataset.prodi || '';
            var cc = card.dataset.country || '';
            var matchQ = !q || name.indexOf(q) > -1 || company.indexOf(q) > -1 || position.indexOf(q) > -1;
            var matchY = !year || cy === year;
            var matchP = !prodi || cp === prodi;
            var matchC = !activeCountry || cc === activeCountry;
            if (matchQ && matchY && matchP && matchC) {
                card.classList.remove('hidden-filter');
                shown++;
            } else {
                card.classList.add('hidden-filter');
            }
        });
        if (countEl) countEl.innerHTML = '<i class="fas fa-users text-gold-muted mr-1"></i>' + shown + ' <?= $EN ? "alumni found" : "alumni ditemukan" ?>';
    }
    window.applyFilter = applyFilter;

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (yearSel) yearSel.addEventListener('change', applyFilter);
    if (prodiSel) prodiSel.addEventListener('change', applyFilter);
    if (resetBtn) resetBtn.addEventListener('click', function(){
        searchInput.value = ''; yearSel.value = ''; prodiSel.value = '';
        activeCountry = '';
        map.querySelectorAll('.map-dot.selected').forEach(function(d){ d.classList.remove('selected'); });
        updateChip();
        applyFilter();
    });
})();
</script>