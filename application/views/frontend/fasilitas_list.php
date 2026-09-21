<?php
$EN = (get_site_lang() == 'en');

$total_facilities = 0; $total_capacity = 0; $all_facilities = []; $idx = 0;
foreach ($grouped as $type => $items) { foreach ($items as $f) { $f->_type = $type; $f->_idx = $idx++; $all_facilities[] = $f; $total_facilities++; $total_capacity += (int)($f->capacity ?? 0); } }
$max_cap = 1; foreach ($all_facilities as $f) $max_cap = max($max_cap, (int)($f->capacity ?? 0));

// 🏆 Facility of the Day
$fotd = !empty($all_facilities) ? $all_facilities[(int)date('z') % count($all_facilities)] : null;

// 🛜 Amenity detection
function detect_amenities($desc){
    $d = strtolower((string)$desc); $out = [];
    if (strpos($d,'wifi')!==false || strpos($d,'wi-fi')!==false) $out[] = ['fa-wifi','WiFi'];
    if (preg_match('/\bac\b|ber-?ac|air condition/', $d)) $out[] = ['fa-snowflake','AC'];
    if (strpos($d,'proyektor')!==false || strpos($d,'projector')!==false) $out[] = ['fa-video','Proyektor'];
    if (strpos($d,'audio')!==false || strpos($d,'sound')!==false) $out[] = ['fa-volume-up','Audio'];
    if (strpos($d,'parkir')!==false || strpos($d,'parking')!==false) $out[] = ['fa-parking','Parkir'];
    return $out;
}

$cat_meta = [
    'laboratory'=>['color'=>'#a855f7','icon'=>'fas fa-flask'], 'classroom'=>['color'=>'#3b82f6','icon'=>'fas fa-chalkboard'],
    'library'=>['color'=>'#C9A227','icon'=>'fas fa-book-reader'], 'mosque'=>['color'=>'#22c55e','icon'=>'fas fa-mosque'],
    'sport'=>['color'=>'#ef4444','icon'=>'fas fa-running'], 'other'=>['color'=>'#6b7280','icon'=>'fas fa-building'],
];
$labels = [
    'laboratory'=>['Laboratorium','fas fa-flask','Laboratory'], 'classroom'=>['Ruang Kelas','fas fa-chalkboard','Classrooms'],
    'library'=>['Perpustakaan','fas fa-book-reader','Library'], 'mosque'=>['Tempat Ibadah','fas fa-mosque','Prayer Room'],
    'sport'=>['Fasilitas Olahraga','fas fa-running','Sports'], 'other'=>['Fasilitas Lainnya','fas fa-building','Others'],
];
?>

<style>
.fac-hero{position:relative;overflow:hidden;background:linear-gradient(165deg,#061420 0%,#0B2239 60%,#13334F 100%)}
#facConstellation{position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:1}
.fac-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}
.fac-stat-card{background:#fff;border:1px solid #e5e7eb;padding:20px;text-align:center;transition:all .3s}
.fac-stat-card:hover{transform:translateY(-4px);border-color:rgba(201,162,39,.4)}
.fac-stat-num{font-family:'Fraunces',serif;font-size:2.6rem;font-weight:300;color:#C9A227;line-height:1}
.fac-stat-label{font-size:10px;text-transform:uppercase;letter-spacing:.2em;color:#64748b;margin-top:6px;font-weight:600}
.fac-search-wrap{position:relative;flex:1;min-width:220px}
.fac-search-wrap input{width:100%;padding:14px 50px 14px 50px;border:2px solid #e5e7eb;background:#fff;font-size:14px}
.fac-search-wrap input:focus{outline:none;border-color:#C9A227}
.fac-search-wrap .si{position:absolute;left:20px;top:50%;transform:translateY(-50%);color:#C9A227}
.fac-mic{position:absolute;right:8px;top:50%;transform:translateY(-50%);width:36px;height:36px;border-radius:50%;border:none;background:#0B2239;color:#C9A227;cursor:pointer;transition:all .2s}
.fac-mic:hover,.fac-mic.rec{background:#C9A227;color:#0B2239}
.fac-mic.rec{animation:micPulse 1s infinite}
@keyframes micPulse{0%,100%{box-shadow:0 0 0 0 rgba(201,162,39,.5)}50%{box-shadow:0 0 0 8px rgba(201,162,39,0)}}
.fac-range{-webkit-appearance:none;width:130px;height:6px;background:#e5e7eb;border-radius:3px;outline:none}
.fac-range::-webkit-slider-thumb{-webkit-appearance:none;width:18px;height:18px;border-radius:50%;background:#C9A227;cursor:pointer;border:2px solid #fff}
.fac-chips{position:sticky;top:73px;z-index:20;background:rgba(247,245,240,.95);backdrop-filter:blur(8px);padding:12px 0}
.fac-chip{white-space:nowrap;padding:8px 16px;background:#fff;border:1px solid #e5e7eb;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#475569;text-decoration:none;transition:all .2s;cursor:pointer}
.fac-chip:hover{border-color:#C9A227;color:#0B2239}
.fac-chip.active{background:#0B2239;color:#C9A227;border-color:#0B2239}

/*  FOTD */
.fotd-banner{background:linear-gradient(135deg,#0B2239,#13334F);border:1px solid rgba(201,162,39,.4);position:relative;overflow:hidden}
.fotd-banner::after{content:'';position:absolute;top:0;left:-80%;width:50%;height:100%;background:linear-gradient(105deg,transparent,rgba(201,162,39,.15),transparent);transform:skewX(-20deg);animation:fotdShine 4s infinite}
@keyframes fotdShine{0%{left:-80%}60%,100%{left:130%}}

/* 🧙 WIZARD */
.wiz-step{display:none}.wiz-step.active{display:block;animation:wizIn .4s ease}
@keyframes wizIn{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:none}}
.wiz-opt{padding:14px 18px;background:#fff;border:2px solid #e5e7eb;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:10px;font-size:14px}
.wiz-opt:hover{border-color:#C9A227;transform:translateY(-2px)}
.wiz-opt.sel{background:#0B2239;color:#C9A227;border-color:#0B2239}
.wiz-dots{display:flex;gap:6px}.wiz-dot{width:8px;height:8px;border-radius:50%;background:#e5e7eb}.wiz-dot.on{background:#C9A227}

/* 🃏 CARD */
.fac-card{position:relative;overflow:hidden;background:#fff;border:1px solid #e5e7eb;transition:box-shadow .4s;transform-style:preserve-3d;will-change:transform}
.fac-card:hover{box-shadow:0 20px 40px rgba(11,34,57,.15)}
.fac-card .glare{position:absolute;inset:0;z-index:6;pointer-events:none;opacity:0;transition:opacity .3s;background:radial-gradient(circle at var(--gx,50%) var(--gy,50%),rgba(255,255,255,.3),transparent 60%)}
.fac-card:hover .glare{opacity:1}
.fac-card .fac-img{transition:transform .7s ease}.fac-card:hover .fac-img{transform:scale(1.05)}
.fac-card.hidden-search{display:none!important}
.fac-card.flash{animation:facFlash 1.2s ease}
@keyframes facFlash{0%,100%{box-shadow:none}30%{box-shadow:0 0 0 4px #C9A227,0 20px 40px rgba(201,162,39,.4)}}
.fac-card.compare-sel{outline:3px solid #3b82f6}
.fac-cap-bar{height:5px;background:#EDE8DE;overflow:hidden;margin-top:8px}
.fac-cap-fill{height:100%;width:0;background:linear-gradient(90deg,#B8941F,#D4AF37);transition:width 1.2s cubic-bezier(.22,1,.36,1)}
.fac-status{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;padding:3px 8px;border-radius:999px}
.fac-status.open{background:#ecfdf5;color:#10b981}.fac-status.closed{background:#fef2f2;color:#ef4444}
.fac-fav{position:absolute;top:10px;left:10px;z-index:8;width:34px;height:34px;border-radius:50%;background:rgba(11,34,57,.75);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s}
.fac-fav.on{color:#ef4444;background:#fff}
.fac-stars{display:inline-flex;gap:2px;cursor:pointer}
.fac-stars i{color:#d1d5db;font-size:12px;transition:color .2s}
.fac-stars i.on{color:#C9A227}
.fac-amen{display:inline-flex;align-items:center;gap:4px;font-size:9px;color:#64748b;background:#F7F5F0;border:1px solid #e5e7eb;padding:2px 6px;border-radius:3px}
.fac-crowd{height:4px;background:#EDE8DE;overflow:hidden;width:70px;display:inline-block;vertical-align:middle}
.fac-crowd-fill{height:100%;transition:width 1s}
.fac-grid.view-list{grid-template-columns:1fr!important}
.fac-grid.view-list .fac-card{display:flex}
.fac-grid.view-list .fac-card .fac-photo{width:260px;flex-shrink:0;aspect-ratio:auto}
.fac-grid.view-list .fac-card .fac-body{flex:1}
@media(max-width:767px){.fac-grid.view-list .fac-card{flex-direction:column}.fac-grid.view-list .fac-card .fac-photo{width:100%}}

/* modals */
.fac-modal{position:fixed;inset:0;z-index:100;background:rgba(6,20,32,.92);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;padding:1rem}
.fac-modal.open{display:flex;animation:fadeIn .3s ease}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.fac-modal-card{background:#fff;max-width:900px;width:100%;max-height:90vh;overflow-y:auto;border-top:4px solid #C9A227}
.fac-modal-head{padding:1.25rem 1.5rem;background:#0B2239;color:#F7F5F0;display:flex;justify-content:space-between;align-items:center}
.fac-modal-close{background:none;border:none;color:#C9A227;font-size:22px;cursor:pointer}
.fac-modal-body{padding:1.5rem}
.cmp-table{width:100%;border-collapse:collapse}
.cmp-table td,.cmp-table th{padding:10px 12px;border-bottom:1px solid #e5e7eb;font-size:13px;text-align:left}
.cmp-table th{background:#F7F5F0;font-size:10px;text-transform:uppercase;letter-spacing:.1em}
.book-btn{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:transparent;border:1px solid #C9A227;color:#C9A227;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;cursor:pointer;transition:all .2s}
.book-btn:hover{background:#C9A227;color:#0B2239}
.icon-btn{width:30px;height:30px;border:1px solid #e5e7eb;background:#fff;color:#64748b;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .2s}
.icon-btn:hover{background:#C9A227;color:#0B2239}
.fac-toast{position:fixed;bottom:24px;right:24px;z-index:200;background:#0B2239;color:#F7F5F0;padding:12px 20px;font-size:13px;border-left:3px solid #C9A227;box-shadow:0 8px 24px rgba(0,0,0,.3);transform:translateX(120%);transition:transform .3s}
.fac-toast.show{transform:translateX(0)}
.confetti-piece{position:fixed;width:8px;height:12px;z-index:300;pointer-events:none}
@media print{#facConstellation,.fac-chips,.fac-modal,.fac-toast,#wizardSec{display:none!important}}
</style>

<!-- ============ HERO ============ -->
<section class="fac-hero text-ivory">
    <canvas id="facConstellation"></canvas>
    <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold mb-6"><i class="fas fa-building mr-2"></i><?= $EN ? 'Campus Facilities' : 'Fasilitas Kampus' ?></p>
            <h1 class="font-serif font-light tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                <span class="fac-scr" data-text="<?= $EN ? 'World-Class' : 'Fasilitas' ?>"><?= $EN ? 'World-Class' : 'Fasilitas' ?></span>
                <em class="italic text-gold fac-scr" data-text="<?= $EN ? 'Facilities' : 'Kampus' ?>"><?= $EN ? 'Facilities' : 'Kampus' ?></em>
            </h1>
            <p class="text-ivory/70 mt-6 text-lg leading-relaxed"><?= $EN ? 'Modern infrastructure supporting your academic journey.' : 'Sarana dan prasarana modern yang mendukung perjalanan akademik Anda.' ?></p>
        </div>
    </div>
</section>

<!-- ============ STATS + SEARCH + SORT ============ -->
<?php if (!empty($grouped)): ?>
<section class="py-12 bg-white border-b border-gray-200">
    <div class="container mx-auto px-6">
        <div class="fac-stats mb-8">
            <div class="fac-stat-card"><div class="fac-stat-num fac-count" data-count="<?= $total_facilities ?>">0</div><p class="fac-stat-label"><?= $EN?'Total Facilities':'Total Fasilitas' ?></p></div>
            <div class="fac-stat-card"><div class="fac-stat-num fac-count" data-count="<?= $total_capacity ?>">0</div><p class="fac-stat-label"><?= $EN?'Total Capacity':'Total Kapasitas' ?></p></div>
            <div class="fac-stat-card"><div class="fac-stat-num fac-count" data-count="<?= count($grouped) ?>">0</div><p class="fac-stat-label"><?= $EN?'Categories':'Kategori' ?></p></div>
        </div>
        <div class="flex flex-col md:flex-row md:items-center gap-4 flex-wrap">
            <div class="fac-search-wrap">
                <i class="fas fa-search si"></i>
                <input type="text" id="facSearch" placeholder="<?= $EN?'Search name / location...':'Cari nama / lokasi...' ?>">
                <button type="button" class="fac-mic" id="facMic" title="<?= $EN?'Voice search':'Cari suara' ?>"><i class="fas fa-microphone"></i></button>
            </div>
            <div class="flex items-center gap-2"><span class="text-[10px] uppercase font-bold text-slate"><?= $EN?'Min Cap':'Kap.Min' ?>:</span>
                <input type="range" id="facCap" class="fac-range" min="0" max="<?= $max_cap ?>" value="0" step="10"><span class="font-mono text-xs text-gold-muted w-10" id="facCapVal">0</span></div>
            <label class="flex items-center gap-2 text-xs text-slate cursor-pointer"><input type="checkbox" id="facOpen"> <?= $EN?'Open now':'Buka' ?></label>
            <select id="facSort" class="px-3 py-2 border-2 border-gray-200 bg-white text-xs uppercase font-semibold text-navy">
                <option value="default"><?= $EN?'Sort':'Urut' ?></option><option value="name"><?= $EN?'Name':'Nama' ?></option>
                <option value="cap"><?= $EN?'Capacity':'Kapasitas' ?></option><option value="rate"><?= $EN?'Rating':'Rating' ?></option>
            </select>
            <div class="flex gap-2">
                <button type="button" class="fac-view-btn active px-3 py-2 border border-navy/20 bg-navy text-ivory" data-view="grid"><i class="fas fa-th"></i></button>
                <button type="button" class="fac-view-btn px-3 py-2 border border-navy/20 bg-white text-navy" data-view="list"><i class="fas fa-list"></i></button>
            </div>
        </div>
    </div>
</section>

<!-- CHIPS -->
<div class="fac-chips"><div class="container mx-auto px-6 flex gap-2 overflow-x-auto">
    <button class="fac-chip" id="favChip">❤️ <?= $EN?'Favorites':'Favorit' ?> <span id="favCount">0</span></button>
    <?php foreach ($grouped as $type => $items): $info=$labels[$type]??$labels['other']; ?>
    <a href="#cat-<?= $type ?>" class="fac-chip"><i class="<?= $info[1] ?> mr-1"></i><?= $EN?$info[2]:$info[0] ?></a>
    <?php endforeach; ?>
</div></div>

<!-- 🏆 FACILITY OF THE DAY -->
<?php if ($fotd): ?>
<section class="py-10 bg-ivory"><div class="container mx-auto px-6">
    <div class="fotd-banner text-ivory p-6 md:p-8 flex flex-col md:flex-row md:items-center gap-6">
        <div class="w-16 h-16 bg-gold text-navy flex items-center justify-center text-2xl flex-shrink-0"><i class="fas fa-crown"></i></div>
        <div class="flex-1">
            <p class="editorial-label text-gold mb-1"><?= $EN?'Facility of the Day':'Fasilitas Hari Ini' ?></p>
            <h3 class="font-serif text-2xl md:text-3xl font-light"><?= html_escape($fotd->name) ?></h3>
            <p class="text-ivory/70 text-sm mt-1"><?= html_escape($fotd->location ?? '') ?> · <?= (int)($fotd->capacity ?? 0) ?> <?= $EN?'seats':'orang' ?></p>
        </div>
        <button type="button" class="book-btn !border-gold !text-gold hover:!bg-gold hover:!text-navy" data-book="<?= html_escape($fotd->name) ?>"><i class="fas fa-calendar-check"></i><?= $EN?'Book':'Booking' ?></button>
    </div>
</div></section>
<?php endif; ?>

<!-- 🧙 SMART FINDER WIZARD (pengganti denah) -->
<section class="py-14 bg-white border-b border-gray-200" id="wizardSec"><div class="container mx-auto px-6">
    <div class="text-center mb-8">
        <p class="editorial-label text-gold-muted mb-3"><i class="fas fa-magic mr-1"></i><?= $EN?'Smart Finder':'Pencari Cerdas' ?></p>
        <h2 class="font-serif text-3xl md:text-4xl font-light text-navy"><?= $EN?'Find your <em class="italic text-gold-muted">space</em>':'Temukan <em class="italic text-gold-muted">ruang Anda</em>' ?></h2>
    </div>
    <div class="max-w-2xl mx-auto bg-ivory border border-gray-200 p-6 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <div class="wiz-dots"><span class="wiz-dot on"></span><span class="wiz-dot"></span><span class="wiz-dot"></span></div>
            <span class="text-xs text-slate" id="wizStepLabel">1 / 3</span>
        </div>
        <div class="wiz-step active" data-step="1">
            <p class="font-serif text-xl text-navy mb-4"><?= $EN?'What will you do?':'Apa kegiatan Anda?' ?></p>
            <div class="grid grid-cols-2 gap-3">
                <div class="wiz-opt" data-cat="laboratory"><i class="fas fa-flask text-purple-500"></i><?= $EN?'Experiment':'Praktikum' ?></div>
                <div class="wiz-opt" data-cat="library"><i class="fas fa-book-reader text-gold"></i><?= $EN?'Study':'Belajar' ?></div>
                <div class="wiz-opt" data-cat="classroom"><i class="fas fa-chalkboard text-blue-500"></i><?= $EN?'Lecture':'Kuliah' ?></div>
                <div class="wiz-opt" data-cat="sport"><i class="fas fa-running text-red-500"></i><?= $EN?'Exercise':'Olahraga' ?></div>
                <div class="wiz-opt" data-cat="mosque"><i class="fas fa-mosque text-green-500"></i><?= $EN?'Pray':'Ibadah' ?></div>
                <div class="wiz-opt" data-cat=""><i class="fas fa-th text-slate"></i><?= $EN?'Anything':'Semua' ?></div>
            </div>
        </div>
        <div class="wiz-step" data-step="2">
            <p class="font-serif text-xl text-navy mb-4"><?= $EN?'How many people?':'Berapa orang?' ?></p>
            <div class="grid grid-cols-2 gap-3">
                <div class="wiz-opt" data-cap="0"><i class="fas fa-user"></i><?= $EN?'Solo':'Sendiri' ?></div>
                <div class="wiz-opt" data-cap="0"><i class="fas fa-user-friends"></i>1-20</div>
                <div class="wiz-opt" data-cap="20"><i class="fas fa-users"></i>21-60</div>
                <div class="wiz-opt" data-cap="60"><i class="fas fa-user-friends"></i>60+</div>
            </div>
        </div>
        <div class="wiz-step" data-step="3">
            <p class="font-serif text-xl text-navy mb-4"><?= $EN?'Need it open now?':'Harus buka sekarang?' ?></p>
            <div class="grid grid-cols-2 gap-3">
                <div class="wiz-opt" data-open="1"><i class="fas fa-door-open text-green-500"></i><?= $EN?'Yes, open':'Ya, buka' ?></div>
                <div class="wiz-opt" data-open="0"><i class="fas fa-clock text-slate"></i><?= $EN?'Anytime':'Kapan saja' ?></div>
            </div>
        </div>
        <div class="flex justify-between mt-6">
            <button type="button" id="wizBack" class="text-xs uppercase font-bold text-slate hover:text-navy" style="visibility:hidden">← <?= $EN?'Back':'Kembali' ?></button>
            <button type="button" id="wizNext" class="btn-gold px-6 py-3 text-xs uppercase tracking-editorial font-bold"><?= $EN?'Next':'Lanjut' ?> →</button>
        </div>
    </div>
</div></section>
<?php endif; ?>

<!-- ============ CATEGORIES ============ -->
<section class="py-24 bg-ivory"><div class="container mx-auto px-6 space-y-24">
    <?php if (empty($grouped)): ?>
        <div class="text-center py-24 bg-white border border-gray-200"><i class="fas fa-building text-5xl text-gray-300 mb-5"></i><p class="font-serif text-2xl text-navy font-light"><?= $EN?'No facility data.':'Data fasilitas belum tersedia.' ?></p></div>
    <?php else: $sec_no=0; foreach ($grouped as $type => $items): $info=$labels[$type]??$labels['other']; $sec_no++; ?>
    <div class="fade-in" data-category="<?= $type ?>" id="cat-<?= $type ?>">
        <div class="flex items-end justify-between mb-10 pb-5 border-b-2 border-navy">
            <div class="flex items-baseline gap-5">
                <span class="font-serif text-4xl md:text-5xl font-light text-gold-muted leading-none"><?= str_pad($sec_no,2,'0',STR_PAD_LEFT) ?></span>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-navy text-gold flex items-center justify-center text-lg"><i class="<?= $info[1] ?>"></i></div>
                    <div><h2 class="font-serif text-3xl md:text-4xl font-light text-navy"><?= $EN?$info[2]:$info[0] ?></h2><p class="editorial-label text-slate mt-1"><span class="cat-count"><?= count($items) ?></span> <?= $EN?'Facilities':'Fasilitas' ?></p></div>
                </div>
            </div>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7 fac-grid">
            <?php foreach ($items as $f):
                $imgs=[]; if(!empty($f->images)){$d=json_decode($f->images,true); if(is_array($d))$imgs=$d;}
                $img=!empty($imgs)?$imgs[0]:null; $cap=(int)($f->capacity??0);
                $cap_pct=$max_cap>0?round($cap/$max_cap*100):0;
                $amen=detect_amenities($f->description);
            ?>
            <div class="fac-card group relative" data-idx="<?= $f->_idx ?>" data-cat="<?= $type ?>"
                 data-name="<?= strtolower(html_escape($f->name)) ?>" data-location="<?= strtolower(html_escape($f->location??'')) ?>"
                 data-catname="<?= strtolower($EN ? $info[2] : $info[0]) ?>"
                 data-cap="<?= $cap ?>" data-images='<?= htmlspecialchars(json_encode($imgs)) ?>'
                 data-fullname="<?= html_escape($f->name) ?>" data-prodi="<?= html_escape($f->location??'') ?>">
                <div class="glare"></div>
                <button type="button" class="fac-fav" data-fav title="Favorite"><i class="far fa-heart"></i></button>
                <div class="fac-photo aspect-[4/3] bg-gray-200 overflow-hidden relative">
                    <?php if($img): ?>
                        <img src="<?= base_url('assets/uploads/'.$img) ?>" alt="<?= html_escape($f->name) ?>" class="fac-img w-full h-full object-cover" loading="lazy">
                        <?php if(count($imgs)>1): ?><button type="button" class="fac-gallery-trigger absolute bottom-4 right-4 bg-navy/90 text-ivory text-[10px] uppercase tracking-editorial px-3 py-1.5 hover:bg-gold hover:text-navy transition z-10"><i class="fas fa-images mr-1"></i><?= count($imgs) ?></button><?php endif; ?>
                    <?php else: ?><div class="w-full h-full bg-gradient-to-br from-navy to-navy-light flex items-center justify-center"><i class="<?= $info[1] ?> text-5xl text-gold/30"></i></div><?php endif; ?>
                    <?php if($cap): ?><div class="absolute bottom-4 left-4 bg-navy/90 text-ivory text-[10px] uppercase tracking-editorial px-3 py-1.5 z-10"><i class="fas fa-users mr-1 text-gold"></i><?= $cap ?></div><?php endif; ?>
                </div>
                <div class="fac-body p-6 md:p-7">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="font-serif text-xl font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($f->name) ?></h3>
                        <span class="fac-status" data-status>—</span>
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                       <span class="fac-stars" data-rate><?php for($s=1;$s<=5;$s++): ?><i class="far fa-star" data-v="<?= $s ?>"></i><?php endfor; ?></span>
                        <span class="text-[10px] text-slate" data-rateval>0.0</span>
                    </div>
                    <?php if($f->location): ?><p class="text-xs text-slate mb-2"><i class="fas fa-map-marker-alt text-gold-muted mr-2"></i><?= html_escape($f->location) ?></p><?php endif; ?>
                    <?php if(!empty($amen)): ?><div class="flex flex-wrap gap-1 mb-2"><?php foreach($amen as $am): ?><span class="fac-amen"><i class="fas <?= $am[0] ?>"></i><?= $am[1] ?></span><?php endforeach; ?></div><?php endif; ?>
                    <?php if($f->description): ?><p class="text-sm text-gray-600 leading-relaxed line-clamp-2"><?= html_escape(character_limiter($f->description,100)) ?></p><?php endif; ?>
                    <?php if($cap): ?><div class="fac-cap-bar"><div class="fac-cap-fill" data-w="<?= $cap_pct ?>%"></div></div><?php endif; ?>
                    <div class="flex items-center gap-2 text-[10px] text-slate mt-2" title="<?= $EN?'Open 07:00-17:00':'Buka 07:00-17:00' ?>">
                        <span class="fac-crowd"><span class="fac-crowd-fill" data-crowd></span></span><span data-crowdlabel>—</span>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                        <button type="button" class="book-btn" data-book="<?= html_escape($f->name) ?>"><i class="fas fa-calendar-check"></i><?= $EN?'Book':'Booking' ?></button>
                        <div class="flex gap-1">
                            <button type="button" class="icon-btn" data-brochure title="<?= $EN?'Brochure PNG':'Brosur' ?>"><i class="fas fa-image"></i></button>
                            <label class="icon-btn" title="<?= $EN?'Compare':'Bandingkan' ?>" style="cursor:pointer"><input type="checkbox" data-compare style="display:none"><i class="fas fa-balance-scale"></i></label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <div id="facEmpty" class="hidden text-center py-16"><i class="fas fa-search text-5xl text-gray-300 mb-4"></i><p class="font-serif text-xl text-navy font-light"><?= $EN?'No match.':'Tidak ada yang cocok.' ?></p></div>
    <?php endif; ?>
</div></section>

<!-- COMPARE FLOAT BTN -->
<button type="button" id="cmpBtn" class="fixed bottom-24 right-6 z-90 bg-blue-600 text-white px-5 py-3 rounded-full shadow-lg hidden" style="z-index:90"><i class="fas fa-balance-scale mr-2"></i><?= $EN?'Compare':'Bandingkan' ?> (<span id="cmpCount">0</span>)</button>

<!-- GALLERY MODAL -->
<div class="fac-modal" id="galleryModal" onclick="if(event.target===this)closeGallery()"><div class="fac-modal-card">
    <div class="fac-modal-head"><h3 class="font-serif text-lg" id="galTitle">-</h3><button class="fac-modal-close" onclick="closeGallery()">&times;</button></div>
    <div class="fac-modal-body"><div style="aspect-ratio:16/10;background:#0B2239;overflow:hidden;margin-bottom:12px"><img id="galMain" style="width:100%;height:100%;object-fit:cover" alt=""></div><div class="flex gap-2 overflow-x-auto" id="galThumbs"></div></div>
</div></div>

<!-- COMPARE MODAL -->
<div class="fac-modal" id="cmpModal" onclick="if(event.target===this)closeCmp()"><div class="fac-modal-card" style="max-width:700px">
    <div class="fac-modal-head"><h3 class="font-serif text-lg"><i class="fas fa-balance-scale mr-2"></i><?= $EN?'Comparison':'Perbandingan' ?></h3><button class="fac-modal-close" onclick="closeCmp()">&times;</button></div>
    <div class="fac-modal-body"><table class="cmp-table" id="cmpTable"></table></div>
</div></div>

<!-- BOOKING MODAL -->
<div class="fac-modal" id="bookModal" onclick="if(event.target===this)closeBook()"><div class="fac-modal-card" style="max-width:480px">
    <div class="fac-modal-head"><h3 class="font-serif text-lg"><i class="fas fa-calendar-check mr-2"></i><?= $EN?'Book':'Booking' ?></h3><button class="fac-modal-close" onclick="closeBook()">&times;</button></div>
    <form class="fac-modal-body" id="bookForm">
        <label style="display:block;font-size:10px;text-transform:uppercase;font-weight:700;color:#64748b;margin-bottom:4px"><?= $EN?'Facility':'Fasilitas' ?></label>
        <input type="text" id="bookFac" readonly style="width:100%;padding:10px;border:2px solid #e5e7eb;margin-bottom:12px">
        <label style="display:block;font-size:10px;text-transform:uppercase;font-weight:700;color:#64748b;margin-bottom:4px"><?= $EN?'Name':'Nama' ?></label>
        <input type="text" id="bookName" required style="width:100%;padding:10px;border:2px solid #e5e7eb;margin-bottom:12px">
        <label style="display:block;font-size:10px;text-transform:uppercase;font-weight:700;color:#64748b;margin-bottom:4px"><?= $EN?'Date':'Tanggal' ?></label>
        <input type="date" id="bookDate" required style="width:100%;padding:10px;border:2px solid #e5e7eb;margin-bottom:12px">
        <button type="submit" class="btn-gold w-full px-6 py-3 text-xs uppercase tracking-editorial font-bold"><?= $EN?'Send':'Kirim' ?></button>
    </form>
</div></div>

<!-- SHORTCUTS MODAL -->
<div class="fac-modal" id="kbModal" onclick="if(event.target===this)this.classList.remove('open')"><div class="fac-modal-card" style="max-width:420px">
    <div class="fac-modal-head"><h3 class="font-serif text-lg">Shortcuts</h3><button class="fac-modal-close" onclick="document.getElementById('kbModal').classList.remove('open')">&times;</button></div>
    <div class="fac-modal-body" style="font-size:13px">
        <p><kbd>/</kbd> Focus search</p><p><kbd>f</kbd> Toggle favorites filter</p><p><kbd>?</kbd> Help</p><p><kbd>Esc</kbd> Close modal</p>
    </div>
</div></div>

<div class="fac-toast" id="facToast"><i class="fas fa-check-circle mr-2"></i><span id="facToastMsg"></span></div>

<script>
(function(){
    var EN=<?= $EN?'true':'false' ?>;
    var state={q:'',minCap:0,onlyOpen:false,cat:'',favOnly:false};
    var hour=new Date().getHours(); var isOpenNow=hour>=7&&hour<17;

    // Constellation
    var cc=document.getElementById('facConstellation');
    if(cc){var ctx=cc.getContext('2d'),pts=[],m={x:-999,y:-999};
        function rs(){cc.width=cc.offsetWidth;cc.height=cc.offsetHeight;}rs();window.addEventListener('resize',rs);
        cc.parentElement.addEventListener('mousemove',function(e){var r=cc.getBoundingClientRect();m.x=e.clientX-r.left;m.y=e.clientY-r.top;});
        cc.parentElement.addEventListener('mouseleave',function(){m.x=-999;m.y=-999;});
        for(var i=0;i<60;i++)pts.push({x:Math.random()*cc.width,y:Math.random()*cc.height,vx:(Math.random()-.5)*.3,vy:(Math.random()-.5)*.3,s:Math.random()*1.5+.8});
        (function loop(){ctx.clearRect(0,0,cc.width,cc.height);
            pts.forEach(function(p){p.x+=p.vx;p.y+=p.vy;if(p.x<0||p.x>cc.width)p.vx*=-1;if(p.y<0||p.y>cc.height)p.vy*=-1;
                var dx=m.x-p.x,dy=m.y-p.y,d=Math.sqrt(dx*dx+dy*dy);if(d<120&&d>0){p.x+=dx/d*.3;p.y+=dy/d*.3;}
                ctx.beginPath();ctx.arc(p.x,p.y,p.s,0,6.28);ctx.fillStyle='rgba(201,162,39,.7)';ctx.fill();});
            for(var a=0;a<pts.length;a++)for(var b=a+1;b<pts.length;b++){var dx=pts[a].x-pts[b].x,dy=pts[a].y-pts[b].y,d=Math.sqrt(dx*dx+dy*dy);
                if(d<100){ctx.beginPath();ctx.moveTo(pts[a].x,pts[a].y);ctx.lineTo(pts[b].x,pts[b].y);ctx.strokeStyle='rgba(201,162,39,'+(.3*(1-d/100))+')';ctx.lineWidth=.5;ctx.stroke();}}
            requestAnimationFrame(loop);})();}

    // Scramble
    document.querySelectorAll('.fac-scr').forEach(function(el){var fin=el.dataset.text,ch='#@%&$!?<>*',i=0;
        var iv=setInterval(function(){el.textContent=fin.split('').map(function(c,x){return x<i?c:ch[Math.floor(Math.random()*ch.length)];}).join('');i++;if(i>fin.length){clearInterval(iv);el.textContent=fin;}},50);});

    // Counters + bars
    var io=new IntersectionObserver(function(es){es.forEach(function(e){if(!e.isIntersecting)return;
        e.target.querySelectorAll('.fac-count').forEach(runCount);
        e.target.querySelectorAll('.fac-cap-fill').forEach(function(b){setTimeout(function(){b.style.width=b.dataset.w;},200);});
        io.unobserve(e.target);});},{threshold:.15});
    document.querySelectorAll('.fac-stat-card,.fac-card,section').forEach(function(el){io.observe(el);});
    function runCount(el){if(el.dataset.done)return;el.dataset.done=1;var t=+el.dataset.count,st=performance.now();
        (function f(n){var p=Math.min(1,(n-st)/1400),e=1-Math.pow(1-p,3);el.textContent=Math.round(t*e).toLocaleString('id-ID');if(p<1)requestAnimationFrame(f);})(st);}

    // Tilt
    document.querySelectorAll('.fac-card').forEach(function(c){c.addEventListener('mousemove',function(e){var r=c.getBoundingClientRect(),px=(e.clientX-r.left)/r.width-.5,py=(e.clientY-r.top)/r.height-.5;
        c.style.transform='perspective(900px) rotateY('+(px*6)+'deg) rotateX('+(-py*6)+'deg) translateY(-6px)';c.style.setProperty('--gx',((px+.5)*100)+'%');c.style.setProperty('--gy',((py+.5)*100)+'%');});
        c.addEventListener('mouseleave',function(){c.style.transform='';});});

    // Status + crowd
    document.querySelectorAll('[data-status]').forEach(function(s){s.textContent=isOpenNow?(EN?'Open':'Buka'):(EN?'Closed':'Tutup');s.classList.add(isOpenNow?'open':'closed');});
    document.querySelectorAll('.fac-card').forEach(function(c){c.dataset.open=isOpenNow?'1':'0';
        var crowd=c.querySelector('[data-crowd]'),lbl=c.querySelector('[data-crowdlabel]');
        var pct=hour<7||hour>=17?15:(hour>=10&&hour<14?85:(hour>=8&&hour<10?55:40));
        if(crowd){crowd.style.width=pct+'%';crowd.style.background=pct>70?'#ef4444':(pct>40?'#f59e0b':'#22c55e');}
        if(lbl)lbl.textContent=pct>70?(EN?'Crowded':'Ramai'):(pct>40?(EN?'Moderate':'Sedang'):(EN?'Quiet':'Sepi'));});

    // ===== FAVORITES =====
    var favs=JSON.parse(localStorage.getItem('fac_favs')||'[]');
    function refreshFavUI(){document.querySelectorAll('.fac-card').forEach(function(c){var on=favs.indexOf(c.dataset.idx)>-1;c.querySelector('.fac-fav').classList.toggle('on',on);c.querySelector('.fac-fav i').className=on?'fas fa-heart':'far fa-heart';c.dataset.fav=on?'1':'0';});
        document.getElementById('favCount').textContent=favs.length;}
    refreshFavUI();
    document.addEventListener('click',function(e){var b=e.target.closest('.fac-fav');if(!b)return;e.stopPropagation();
        var idx=b.closest('.fac-card').dataset.idx;var i=favs.indexOf(idx);if(i>-1)favs.splice(i,1);else favs.push(idx);
        localStorage.setItem('fac_favs',JSON.stringify(favs));refreshFavUI();applyFilter();});
    document.getElementById('favChip').addEventListener('click',function(){state.favOnly=!state.favOnly;this.classList.toggle('active',state.favOnly);applyFilter();});

    // ===== RATING =====
    document.querySelectorAll('.fac-card').forEach(function(c){
        var key='fac_rate_'+c.dataset.idx;var val=+(localStorage.getItem(key)||0);
        paintStars(c,val);c.querySelector('[data-rateval]').textContent=val?val+'.0':'0.0';c.dataset.rate=val;
        c.querySelector('[data-rate]').addEventListener('click',function(e){var st=e.target.closest('i');if(!st)return;
            var v=+st.dataset.v;localStorage.setItem(key,v);c.dataset.rate=v;paintStars(c,v);c.querySelector('[data-rateval]').textContent=v+'.0';showToast((EN?'Rated ':'Dinilai ')+'★'+v);});});
    function paintStars(c,v){c.querySelectorAll('[data-rate] i').forEach(function(i){var on=+i.dataset.v<=v;i.className=(on?'fas':'far')+' fa-star'+(on?' on':'');});}

    // ===== UNIFIED FILTER =====
    var search=document.getElementById('facSearch'),capR=document.getElementById('facCap'),capV=document.getElementById('facCapVal'),openC=document.getElementById('facOpen');
    var cards=document.querySelectorAll('.fac-card'),sections=document.querySelectorAll('[data-category]'),emptyM=document.getElementById('facEmpty');
    function applyFilter(){var q=state.q,minCap=state.minCap,shown=0;
        sections.forEach(function(sec){var s=0;
            sec.querySelectorAll('.fac-card').forEach(function(c){
                var hQ=!q||(c.dataset.name||'').indexOf(q)>-1||(c.dataset.location||'').indexOf(q)>-1||(c.dataset.catname||'').indexOf(q)>-1;
                var hC=(+c.dataset.cap)>=minCap;var hO=!state.onlyOpen||c.dataset.open==='1';
                var hCat=!state.cat||c.dataset.cat===state.cat;var hF=!state.favOnly||c.dataset.fav==='1';
                var hit=hQ&&hC&&hO&&hCat&&hF;c.classList.toggle('hidden-search',!hit);if(hit){s++;shown++;}});
            var cnt=sec.querySelector('.cat-count');if(cnt)cnt.textContent=s;sec.style.display=s===0?'none':'';});
        if(emptyM)emptyM.classList.toggle('hidden',shown>0);}
    window.applyFilter=applyFilter;
    var scrollT=null;
    function scrollToResults(){clearTimeout(scrollT);scrollT=setTimeout(function(){
        var first=null;sections.forEach(function(s){if(!first&&s.style.display!=='none')first=s;});
        if(first)first.scrollIntoView({behavior:'smooth',block:'start'});
    },350);}
    search.addEventListener('input',function(){state.q=this.value.toLowerCase().trim();applyFilter();scrollToResults();});
    capR.addEventListener('input',function(){state.minCap=+this.value;capV.textContent=this.value;applyFilter();});
    openC.addEventListener('change',function(){state.onlyOpen=this.checked;
        if(this.checked&&!isOpenNow)showToast(EN?'Outside opening hours (07:00-17:00) — all marked Closed':'Di luar jam buka (07:00-17:00) — semua ditandai Tutup');
        applyFilter();});

    // Voice dengan feedback
    var mic=document.getElementById('facMic');var SR=window.SpeechRecognition||window.webkitSpeechRecognition;
    if(SR){var rec=new SR();rec.lang=EN?'en-US':'id-ID';rec.interimResults=false;rec.maxAlternatives=1;
        rec.onresult=function(e){var t=e.results[0][0].transcript;search.value=t;state.q=t.toLowerCase().trim();applyFilter();scrollToResults();mic.classList.remove('rec');showToast('🎤 '+t);};
        rec.onerror=function(){mic.classList.remove('rec');showToast(EN?'Voice failed — check mic permission':'Gagal suara — cek izin mikrofon');};
        rec.onend=function(){mic.classList.remove('rec');};
        mic.addEventListener('click',function(){mic.classList.add('rec');showToast(EN?'Listening...':'Mendengarkan...');try{rec.start();}catch(err){mic.classList.remove('rec');}});}
    else mic.style.display='none';

    // Sort
    document.getElementById('facSort').addEventListener('change',function(){var v=this.value;
        document.querySelectorAll('.fac-grid').forEach(function(g){var arr=Array.prototype.slice.call(g.children);
            arr.sort(function(a,b){if(v==='name')return (a.dataset.name||'').localeCompare(b.dataset.name||'');
                if(v==='cap')return (+b.dataset.cap)-(+a.dataset.cap); if(v==='rate')return (+b.dataset.rate)-(+a.dataset.rate); return 0;});
            arr.forEach(function(c){g.appendChild(c);});});});

    // View
    document.querySelectorAll('.fac-view-btn').forEach(function(b){b.addEventListener('click',function(){
        document.querySelectorAll('.fac-view-btn').forEach(function(x){x.classList.remove('bg-navy','text-ivory','active');x.classList.add('bg-white','text-navy');});
        b.classList.add('bg-navy','text-ivory','active');b.classList.remove('bg-white','text-navy');
        document.querySelectorAll('.fac-grid').forEach(function(g){g.classList.toggle('view-list',b.dataset.view==='list');});});});

    // ===== WIZARD =====
    var wiz={step:1,cat:'',cap:0,open:0};
    var steps=document.querySelectorAll('.wiz-step'),dots=document.querySelectorAll('.wiz-dot');
    function showStep(n){wiz.step=n;steps.forEach(function(s){s.classList.toggle('active',+s.dataset.step===n);});
        dots.forEach(function(d,i){d.classList.toggle('on',i<n);});
        document.getElementById('wizStepLabel').textContent=n+' / 3';
        document.getElementById('wizBack').style.visibility=n===1?'hidden':'visible';
        document.getElementById('wizNext').innerHTML=n===3?(EN?'Find!':'Cari!')+' 🔍':(EN?'Next':'Lanjut')+' →';}
    document.querySelectorAll('.wiz-opt').forEach(function(o){o.addEventListener('click',function(){
        o.parentElement.querySelectorAll('.wiz-opt').forEach(function(x){x.classList.remove('sel');});o.classList.add('sel');
        if(o.dataset.cat!==undefined)wiz.cat=o.dataset.cat; if(o.dataset.cap!==undefined)wiz.cap=+o.dataset.cap; if(o.dataset.open!==undefined)wiz.open=+o.dataset.open;});});
    document.getElementById('wizBack').addEventListener('click',function(){if(wiz.step>1)showStep(wiz.step-1);});
    document.getElementById('wizNext').addEventListener('click',function(){
        if(wiz.step<3){showStep(wiz.step+1);return;}
        state.cat=wiz.cat;state.minCap=wiz.cap;state.onlyOpen=wiz.open===1;capR.value=wiz.cap;capV.textContent=wiz.cap;openC.checked=wiz.open===1;
        applyFilter();
        var first=null;sections.forEach(function(s){if(!first&&s.style.display!=='none')first=s;});
        if(first){first.scrollIntoView({behavior:'smooth'});showToast((EN?'Found matches!':'Ketemu!'));}else showToast(EN?'No match':'Tidak ada');});
    showStep(1);

    // ===== COMPARE =====
    var cmpSel=[];var cmpBtn=document.getElementById('cmpBtn');
    document.addEventListener('change',function(e){if(!e.target.matches('[data-compare]'))return;
        var card=e.target.closest('.fac-card');var idx=card.dataset.idx;
        if(e.target.checked){if(cmpSel.length>=2){e.target.checked=false;showToast(EN?'Max 2':'Maks 2');return;}cmpSel.push(card);card.classList.add('compare-sel');}
        else{cmpSel=cmpSel.filter(function(c){return c!==card;});card.classList.remove('compare-sel');}
        document.getElementById('cmpCount').textContent=cmpSel.length;cmpBtn.classList.toggle('hidden',cmpSel.length!==2);});
    cmpBtn.addEventListener('click',function(){if(cmpSel.length!==2)return;
        var a=cmpSel[0],b=cmpSel[1];
        var rows=[['Name',a.dataset.fullname,b.dataset.fullname],['Capacity',a.dataset.cap,b.dataset.cap],['Status',a.querySelector('[data-status]').textContent,b.querySelector('[data-status]').textContent],['Rating',a.dataset.rate,b.dataset.rate],['Location',a.dataset.prodi,b.dataset.prodi]];
        var html='<tr><th></th><th>'+a.dataset.fullname+'</th><th>'+b.dataset.fullname+'</th></tr>';
        rows.forEach(function(r){html+='<tr><th>'+r[0]+'</th><td>'+r[1]+'</td><td>'+r[2]+'</td></tr>';});
        document.getElementById('cmpTable').innerHTML=html;document.getElementById('cmpModal').classList.add('open');});
    window.closeCmp=function(){document.getElementById('cmpModal').classList.remove('open');};

    // ===== GALLERY =====
    var gM=document.getElementById('galleryModal'),gI=document.getElementById('galMain'),gT=document.getElementById('galThumbs'),gTi=document.getElementById('galTitle');
    window.closeGallery=function(){gM.classList.remove('open');};
    document.querySelectorAll('.fac-gallery-trigger').forEach(function(b){b.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();
        var card=b.closest('.fac-card');var imgs=[];try{imgs=JSON.parse(card.dataset.images);}catch(err){}if(!imgs.length)return;
        gTi.textContent=card.querySelector('h3').textContent;gI.src='<?= base_url("assets/uploads/")?>'+imgs[0];gT.innerHTML='';
        imgs.forEach(function(im){var t=document.createElement('div');t.style.cssText='width:70px;height:52px;flex-shrink:0;overflow:hidden;cursor:pointer;border:2px solid transparent';
            t.innerHTML='<img src="<?= base_url("assets/uploads/")?>'+im+'" style="width:100%;height:100%;object-fit:cover">';
            t.addEventListener('click',function(){gI.src='<?= base_url("assets/uploads/")?>'+im;});gT.appendChild(t);});
        gM.classList.add('open');});});

    // ===== BOOKING + CONFETTI =====
    var bM=document.getElementById('bookModal');window.closeBook=function(){bM.classList.remove('open');};
    document.querySelectorAll('[data-book]').forEach(function(b){b.addEventListener('click',function(){document.getElementById('bookFac').value=b.dataset.book;bM.classList.add('open');});});
    document.getElementById('bookForm').addEventListener('submit',function(e){e.preventDefault();
        var list=JSON.parse(localStorage.getItem('fac_bookings')||'[]');list.push({fac:document.getElementById('bookFac').value,name:document.getElementById('bookName').value,date:document.getElementById('bookDate').value});
        localStorage.setItem('fac_bookings',JSON.stringify(list));closeBook();confetti();showToast((EN?'Booking sent! ':'Booking terkirim! ')+'🎉');this.reset();});
    function confetti(){for(var i=0;i<60;i++){var p=document.createElement('div');p.className='confetti-piece';
        p.style.left=(50+(Math.random()-.5)*40)+'vw';p.style.top='40vh';p.style.background=['#C9A227','#D4AF37','#F7F5F0','#3b82f6','#ef4444'][i%5];
        document.body.appendChild(p);
        p.animate([{transform:'translate(0,0) rotate(0)',opacity:1},{transform:'translate('+((Math.random()-.5)*300)+'px,'+(200+Math.random()*300)+'px) rotate('+(Math.random()*720)+'deg)',opacity:0}],{duration:1200+Math.random()*600,easing:'cubic-bezier(.22,1,.36,1)'}).onfinish=function(){this.effect.target.remove();};}}

    // ===== BROCHURE PNG =====
    document.querySelectorAll('[data-brochure]').forEach(function(b){b.addEventListener('click',function(){
        var card=b.closest('.fac-card');var name=card.dataset.fullname;
        var c=document.createElement('canvas');c.width=800;c.height=500;var x=c.getContext('2d');
        var g=x.createLinearGradient(0,0,0,500);g.addColorStop(0,'#061420');g.addColorStop(1,'#13334F');x.fillStyle=g;x.fillRect(0,0,800,500);
        x.strokeStyle='#C9A227';x.lineWidth=4;x.strokeRect(20,20,760,460);
        x.fillStyle='#C9A227';x.font='bold 20px Inter';x.textAlign='center';x.fillText('FACILITY PROFILE',400,80);
        x.fillStyle='#F7F5F0';x.font='600 40px Georgia';x.fillText(name,400,200);
        x.fillStyle='#C9A227';x.font='24px Inter';x.fillText('Capacity: '+card.dataset.cap,400,260);
        x.fillStyle='rgba(247,245,240,.6)';x.font='18px Inter';x.fillText('<?= html_escape(site_name()) ?>',400,440);
        var a=document.createElement('a');a.download=name.replace(/\s+/g,'-')+'.png';a.href=c.toDataURL('image/png');a.click();showToast(EN?'Brochure downloaded!':'Brosur diunduh!');});});

    // ===== KEYBOARD =====
    document.addEventListener('keydown',function(e){
        if(e.target.tagName==='INPUT'||e.target.tagName==='TEXTAREA')return;
        if(e.key==='/'){e.preventDefault();search.focus();}
        else if(e.key.toLowerCase()==='f'){state.favOnly=!state.favOnly;document.getElementById('favChip').classList.toggle('active',state.favOnly);applyFilter();}
        else if(e.key==='?'){document.getElementById('kbModal').classList.add('open');}
        else if(e.key==='Escape'){closeGallery();closeBook();closeCmp();document.getElementById('kbModal').classList.remove('open');}});

    function showToast(m){var t=document.getElementById('facToast');document.getElementById('facToastMsg').textContent=m;t.classList.add('show');setTimeout(function(){t.classList.remove('show');},2500);}
})();
</script>