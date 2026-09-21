<?php $EN = (get_site_lang() == 'en'); ?>

<!-- 🔥 UPGRADE: Tambahan style (addition only) -->
<style>
/* ===== HERO STATS ===== */
.kl-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; max-width: 720px; margin-top: 3rem; }
@media (max-width: 640px) { .kl-stats { grid-template-columns: repeat(2, 1fr); } }
.kl-stat { border-left: 2px solid #C9A227; padding-left: 1rem; }
.kl-stat-num { font-family: 'Fraunces', serif; font-weight: 300; font-size: 2.5rem; line-height: 1; color: #0B2239; }
.kl-stat-label { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: #64748b; margin-top: .25rem; font-weight: 600; }

/* ===== ORM FILTER CHIPS ===== */
.orm-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 2rem; }
.orm-chip {
    padding: .55rem 1.1rem; font-size: 10px; letter-spacing: .18em; text-transform: uppercase;
    font-weight: 700; border: 1px solid #e5e7eb; background: #fff; color: #64748b;
    cursor: pointer; transition: all .25s; display: inline-flex; align-items: center; gap: .4rem;
}
.orm-chip:hover { border-color: #C9A227; color: #0B2239; transform: translateY(-2px); }
.orm-chip.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }
.orm-chip .orm-n { font-family: 'Fraunces', serif; font-size: .8rem; }

/* ===== ORM CARD UPGRADE ===== */
.orm-card {
    position: relative; background: #fff; border: 1px solid #e5e7eb; padding: 2rem;
    overflow: hidden; transition: all .4s cubic-bezier(.22,1,.36,1);
}
.orm-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--oc, #C9A227), transparent);
    transform: scaleX(0); transform-origin: left; transition: transform .5s;
}
.orm-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(11,34,57,.12); border-color: rgba(201,162,39,.4); }
.orm-card:hover::before { transform: scaleX(1); }
.orm-icon-wrap {
    width: 56px; height: 56px; background: #0B2239; color: #C9A227;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    margin-bottom: 1.5rem; position: relative; transition: all .4s cubic-bezier(.34,1.56,.64,1);
}
.orm-card:hover .orm-icon-wrap { background: #C9A227; color: #0B2239; transform: rotate(-8deg) scale(1.1); }
.orm-ghost {
    position: absolute; top: -1rem; right: -0.5rem; font-family: 'Fraunces', serif;
    font-size: 5rem; line-height: 1; color: rgba(11,34,57,.04); pointer-events: none;
    font-weight: 700; transition: color .4s;
}
.orm-card:hover .orm-ghost { color: rgba(201,162,39,.15); }
.orm-cat-tag {
    display: inline-block; font-size: 9px; letter-spacing: .2em; text-transform: uppercase;
    font-weight: 700; padding: 3px 10px; border-radius: 2px; margin-bottom: .75rem;
    background: rgba(201,162,39,.1); color: #B8941F;
}

/* ===== FEATURED SCHOLARSHIP ===== */
.fs-card {
    background: linear-gradient(135deg, #0B2239 0%, #13334F 100%);
    color: #F7F5F0; padding: 2.5rem; position: relative; overflow: hidden;
    border: 1px solid rgba(201,162,39,.3);
}
.fs-card::before {
    content: ''; position: absolute; top: -40%; right: -20%; width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(201,162,39,.2), transparent 70%);
    border-radius: 50%;
}
.fs-badge {
    display: inline-flex; align-items: center; gap: .5rem; padding: 4px 12px;
    background: #C9A227; color: #0B2239; font-size: 9px; letter-spacing: .25em;
    text-transform: uppercase; font-weight: 800;
}
.fs-amount {
    font-family: 'Fraunces', serif; font-size: 2rem; font-weight: 600;
    color: #C9A227; margin-top: .5rem; line-height: 1.1;
}

/* ===== REVEAL ===== */
.kl-rv { opacity: 0; transform: translateY(26px); transition: all .9s cubic-bezier(.22,1,.36,1); transition-delay: var(--d, 0s); }
.kl-rv.in { opacity: 1; transform: none; }
.kl-hidden { display: none !important; }

/* ===== COUNTER ===== */
.kl-count { font-family: 'Fraunces', serif; font-weight: 300; color: #0B2239; }

/* ===== BEASISWA TABLE ROW HIGHLIGHT ===== */
.refined-table tr:hover td { background: rgba(201,162,39,.05); }

/* ===== 🔥 SEARCH BAR ===== */
.kl-search-wrap { position: relative; max-width: 400px; margin-bottom: 1.5rem; }
.kl-search-wrap input { width: 100%; padding: 12px 16px 12px 44px; border: 2px solid #e5e7eb; background: #fff; font-size: 13px; transition: border-color .2s; }
.kl-search-wrap input:focus { outline: none; border-color: #C9A227; }
.kl-search-wrap i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #C9A227; }

/* ===== 🔥 SORT BUTTONS ===== */
.kl-sort-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin-bottom: 1rem; }
.kl-sort-btn {
    padding: 6px 12px; font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
    font-weight: 700; border: 1px solid #e5e7eb; background: #fff; color: #64748b;
    cursor: pointer; transition: all .2s;
}
.kl-sort-btn:hover { border-color: #C9A227; color: #0B2239; }
.kl-sort-btn.active { background: #0B2239; color: #C9A227; border-color: #0B2239; }

/* ===== 🔥 PRINT BUTTON ===== */
.print-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; background: transparent; border: 1px solid #0B2239;
    color: #0B2239; font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.1em; cursor: pointer; transition: all .2s;
}
.print-btn:hover { background: #0B2239; color: #F7F5F0; }

/* ===== 🔥 ACHIEVEMENT CARD ===== */
.ach-card {
    position: relative; background: #fff; border: 1px solid #e5e7eb;
    padding: 1.5rem; transition: all .3s; overflow: hidden;
}
.ach-card::before {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
    background: linear-gradient(180deg, #C9A227, #B8941F);
}
.ach-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(11,34,57,.1); }
.ach-medal {
    width: 48px; height: 48px; background: linear-gradient(135deg, #C9A227, #B8941F);
    color: #0B2239; display: flex; align-items: center; justify-content: center;
    border-radius: 50%; font-size: 1.25rem; flex-shrink: 0;
}

/* ===== 🔥 E-LEARNING STATUS ===== */
.el-status {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; font-size: 9px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
}
.el-status.online { background: rgba(16,185,129,.1); color: #10b981; }
.el-status.offline { background: rgba(239,68,68,.1); color: #ef4444; }
.el-status .dot { width: 6px; height: 6px; border-radius: 50%; animation: elPulse 2s infinite; }
.el-status.online .dot { background: #10b981; }
@keyframes elPulse { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }

@media print {
    .kl-search-wrap, .kl-sort-bar, .print-btn, .wa-fab { display: none !important; }
}
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="absolute -bottom-16 -left-8 font-serif text-[18rem] leading-none text-navy/5 select-none pointer-events-none hidden lg:block">K</div>
    <div class="container mx-auto px-6 py-20 md:py-28 relative">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Student Affairs' : 'Kemahasiswaan' ?></p>
            <h1 class="font-serif font-light text-navy tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                <?= $EN ? 'Campus <em class="italic text-gold-muted">Life</em>' : 'Kehidupan <em class="italic text-gold-muted">Kampus</em>' ?>
            </h1>
            <p class="text-slate mt-6 text-lg leading-relaxed">
                <?= $EN ? 'Organizations, scholarships, service portals, and the entire ecosystem supporting your success.' : 'Organisasi, beasiswa, portal layanan, dan seluruh ekosistem pendukung kesuksesan Anda.' ?>
            </p>
        </div>

        <!-- Stats Band -->
        <div class="kl-stats kl-rv">
            <div class="kl-stat">
                <div class="kl-stat-num kl-count" data-count="<?= count($orgs) ?>">0</div>
                <div class="kl-stat-label"><?= $EN ? 'Organizations' : 'Ormawa' ?></div>
            </div>
            <div class="kl-stat">
                <div class="kl-stat-num kl-count" data-count="<?= count($beasiswa) ?>">0</div>
                <div class="kl-stat-label"><?= $EN ? 'Scholarships' : 'Beasiswa' ?></div>
            </div>
            <div class="kl-stat">
                <div class="kl-stat-num kl-count" data-count="<?= count($portal_items) ?>">0</div>
                <div class="kl-stat-label"><?= $EN ? 'Digital Services' : 'Layanan' ?></div>
            </div>
            <div class="kl-stat">
                <div class="kl-stat-num kl-count" data-count="<?= count($elearning_links) ?>">0</div>
                <div class="kl-stat-label">E-Learning</div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-ivory">
    <div class="container mx-auto px-6 space-y-24">

        <!-- ============ 01. ORMAWA ============ -->
        <div>
            <div class="flex items-end justify-between mb-10 pb-5 border-b-2 border-navy flex-wrap gap-4">
                <div class="flex items-baseline gap-5">
                    <span class="font-serif text-4xl md:text-5xl font-light text-gold-muted leading-none">01</span>
                    <div>
                        <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]"><?= $EN ? 'Student Organizations' : 'Organisasi Mahasiswa' ?></h2>
                        <p class="editorial-label text-slate mt-2"><span id="ormCount"><?= count($orgs) ?></span> <?= $EN ? 'Organizations' : 'Ormawa' ?></p>
                    </div>
                </div>
            </div>

            <!-- 🔥 SEARCH + FILTER -->
            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6 kl-rv">
                <div class="kl-search-wrap flex-1">
                    <i class="fas fa-search"></i>
                    <input type="text" id="ormSearch" placeholder="<?= $EN ? 'Search organizations...' : 'Cari organisasi...' ?>">
                </div>
            </div>

            <div class="orm-chips kl-rv" id="ormChips">
                <button class="orm-chip active" data-cat="all"><?= $EN ? 'All' : 'Semua' ?> <span class="orm-n"><?= count($orgs) ?></span></button>
                <?php
                $cat_map = [
                    'Pemerintahan' => ['fa-landmark', 'fa-gavel', 'fa-balance-scale'],
                    'Akademik'     => ['fa-laptop-code', 'fa-database', 'fa-flask', 'fa-graduation-cap'],
                    'Minat & Bakat'=> ['fa-robot', 'fa-futbol', 'fa-music', 'fa-palette', 'fa-camera'],
                ];
                foreach ($cat_map as $cat_name => $cat_icons): ?>
                <button class="orm-chip" data-cat="<?= $cat_name ?>"><?= $cat_name ?></button>
                <?php endforeach; ?>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7" id="ormGrid">
                <?php
                foreach ($orgs as $oi => $o):
                    $org_cat = 'Minat & Bakat';
                    foreach ($cat_map as $cn => $ci) {
                        if (in_array($o['icon'], $ci)) { $org_cat = $cn; break; }
                    }
                    $color_map = [
                        'blue' => '#3b82f6', 'red' => '#ef4444', 'green' => '#22c55e',
                        'purple' => '#a855f7', 'orange' => '#f97316', 'teal' => '#14b8a6',
                    ];
                    $oc = $color_map[$o['color']] ?? '#C9A227';
                ?>
                <div class="orm-card kl-rv group" 
                     data-cat="<?= $org_cat ?>" 
                     data-name="<?= strtolower(html_escape($o['name'])) ?>"
                     style="--oc:<?= $oc ?>; --d:<?= ($oi % 6) * .06 ?>s">
                    <span class="orm-ghost"><?= str_pad($oi + 1, 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="orm-icon-wrap" style="background: <?= $oc ?>15; color: <?= $oc ?>">
                        <i class="fas <?= $o['icon'] ?>"></i>
                    </div>
                    <span class="orm-cat-tag"><?= $org_cat ?></span>
                    <h3 class="font-serif text-xl font-medium text-navy mb-2 tracking-[-0.01em] group-hover:text-gold-muted transition"><?= html_escape($o['name']) ?></h3>
                    <p class="text-sm text-slate leading-relaxed"><?= html_escape($o['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ============ 02. BEASISWA ============ -->
        <div>
            <div class="flex items-end justify-between mb-10 pb-5 border-b-2 border-navy flex-wrap gap-4">
                <div class="flex items-baseline gap-5">
                    <span class="font-serif text-4xl md:text-5xl font-light text-gold-muted leading-none">02</span>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]"><?= $EN ? 'Scholarships' : 'Informasi Beasiswa' ?></h2>
                </div>
                <!-- 🔥 PRINT BUTTON -->
                <button type="button" class="print-btn" onclick="window.print()">
                    <i class="fas fa-print"></i><?= $EN ? 'Print' : 'Cetak' ?>
                </button>
            </div>

            <!-- Featured Scholarship Highlight -->
            <?php
            $featured = null;
            foreach ($beasiswa as $b) {
                if (stripos($b['name'], 'Unggulan') !== false || stripos($b['name'], 'KIP') !== false) {
                    $featured = $b; break;
                }
            }
            if ($featured): ?>
            <div class="fs-card mb-8 kl-rv">
                <div class="relative z-10">
                    <span class="fs-badge"><i class="fas fa-star mr-1"></i><?= $EN ? 'Featured' : 'Unggulan' ?></span>
                    <h3 class="font-serif text-2xl md:text-3xl font-light text-ivory mt-4 mb-3"><?= html_escape($featured['name']) ?></h3>
                    <p class="text-ivory/70 mb-4 text-sm max-w-xl"><?= $EN ? 'A comprehensive scholarship program from' : 'Program beasiswa komprehensif dari' ?> <?= html_escape($featured['source']) ?> <?= $EN ? 'covering your entire academic journey.' : 'yang mencakup seluruh perjalanan akademik Anda.' ?></p>
                    <div class="fs-amount"><?= html_escape($featured['amount']) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 🔥 SORT BAR -->
            <div class="kl-sort-bar">
                <span class="editorial-label text-slate"><?= $EN ? 'Sort' : 'Urutkan' ?>:</span>
                <button class="kl-sort-btn active" data-sort="default"><?= $EN ? 'Default' : 'Bawaan' ?></button>
                <button class="kl-sort-btn" data-sort="name">A–Z</button>
                <button class="kl-sort-btn" data-sort="amount"><?= $EN ? 'Highest Amount' : 'Nominal Tertinggi' ?></button>
            </div>

            <div class="bg-white border border-gray-200 overflow-hidden fade-in">
                <div class="overflow-x-auto">
                    <table class="min-w-full refined-table" id="beasiswaTable">
                        <thead>
                            <tr>
                                <th class="px-8 py-4 text-left"><?= $EN ? 'Scholarship Name' : 'Nama Beasiswa' ?></th>
                                <th class="px-4 py-4 text-left"><?= $EN ? 'Source' : 'Sumber' ?></th>
                                <th class="px-8 py-4 text-right"><?= $EN ? 'Amount' : 'Nominal' ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($beasiswa as $b): ?>
                            <tr class="hover:bg-ivory-warm/40 transition group"
                                data-name="<?= strtolower(html_escape($b['name'])) ?>"
                                data-amount="<?= preg_replace('/[^0-9]/', '', $b['amount']) ?>">
                                <td class="px-8 py-5 font-serif text-base font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($b['name']) ?></td>
                                <td class="px-4 py-5 text-slate"><?= html_escape($b['source']) ?></td>
                                <td class="px-8 py-5 text-right font-semibold text-gold-muted"><?= html_escape($b['amount']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============ 03. TRACER STUDY ============ -->
        <?php if (!empty($tracer) && $tracer->is_active): ?>
        <div class="bg-navy text-ivory p-10 md:p-16 text-center relative overflow-hidden fade-in">
            <div class="absolute inset-0 hero-pattern"></div>
            <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-gold/40"></div>
            <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-gold/40"></div>
            <div class="relative max-w-2xl mx-auto">
                <div class="relative inline-block mb-8">
                    <div class="pulse-ring w-24 h-24 rounded-full border-2 border-gold flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-3xl text-gold"></i>
                    </div>
                </div>
                <p class="editorial-label text-ivory/60 mb-4"><?= $EN ? 'Alumni Tracking' : 'Pelacakan Alumni' ?></p>
                <h3 class="font-serif text-3xl md:text-4xl font-light tracking-[-0.02em] mb-5"><?= html_escape($tracer->title) ?></h3>
                <p class="text-ivory/70 mb-10 leading-relaxed"><?= html_escape($tracer->description) ?></p>
                <a href="<?= html_escape($tracer->form_url) ?>" target="_blank" rel="noopener" class="btn-gold inline-block px-10 py-4 font-semibold uppercase tracking-editorial text-xs">
                    <i class="fas fa-external-link-alt mr-2"></i><?= $EN ? 'Fill Tracer Study' : 'Isi Tracer Study' ?>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ 04. PORTAL MAHASISWA ============ -->
        <?php if (!empty($portal_items)): ?>
        <div>
            <div class="flex items-end justify-between mb-10 pb-5 border-b-2 border-navy flex-wrap gap-4">
                <div class="flex items-baseline gap-5">
                    <span class="font-serif text-4xl md:text-5xl font-light text-gold-muted leading-none">04</span>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]"><?= $EN ? 'Student Digital Services' : 'Layanan Digital Mahasiswa' ?></h2>
                </div>
            </div>

            <!-- 🔥 PORTAL SEARCH -->
            <div class="kl-search-wrap kl-rv">
                <i class="fas fa-search"></i>
                <input type="text" id="portalSearch" placeholder="<?= $EN ? 'Search services...' : 'Cari layanan...' ?>">
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="portalGrid">
                <?php foreach ($portal_items as $pi => $p): ?>
                <a href="<?= html_escape($p->link_url) ?>" 
                   class="portal-card bg-white border border-gray-200 p-6 kl-rv hover-lift group flex items-center gap-5 relative overflow-hidden" 
                   data-title="<?= strtolower(html_escape($p->title)) ?>"
                   style="--d:<?= $pi * .05 ?>s">
                    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-10"></div>
                    <div class="w-12 h-12 bg-navy text-gold flex items-center justify-center text-lg flex-shrink-0 group-hover:bg-gold group-hover:text-navy transition">
                        <i class="fas <?= html_escape($p->icon) ?>"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-serif text-lg font-medium text-navy group-hover:text-gold-muted transition tracking-[-0.01em]"><?= html_escape($p->title) ?></h3>
                        <p class="text-xs text-slate mt-1 truncate"><?= html_escape($p->description ?? '') ?></p>
                    </div>
                    <i class="fas fa-arrow-right text-xs text-slate ml-auto group-hover:text-gold group-hover:translate-x-1 transition"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============ 05. E-LEARNING ============ -->
        <?php if (!empty($elearning_links)): ?>
        <div>
            <div class="flex items-end justify-between mb-10 pb-5 border-b-2 border-navy">
                <div class="flex items-baseline gap-5">
                    <span class="font-serif text-4xl md:text-5xl font-light text-gold-muted leading-none">05</span>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]"><?= $EN ? 'E-Learning Platforms' : 'Platform E-Learning' ?></h2>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-7">
                <?php foreach ($elearning_links as $ei => $e): ?>
                <a href="<?= html_escape($e->url) ?>" target="_blank" rel="noopener" class="bg-white border border-gray-200 p-8 kl-rv hover-lift group flex items-center gap-6 relative overflow-hidden" style="--d:<?= $ei * .06 ?>s">
                    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gold scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-10"></div>
                    <div class="w-14 h-14 bg-navy text-gold flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-gold group-hover:text-navy transition">
                        <i class="fas <?= html_escape($e->icon) ?>"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h3 class="font-serif text-xl font-medium text-navy group-hover:text-gold-muted transition tracking-[-0.01em]"><?= html_escape($e->platform_name) ?></h3>
                            <!-- 🔥 STATUS INDICATOR -->
                            <span class="el-status online"><span class="dot"></span>Online</span>
                        </div>
                        <p class="text-sm text-slate mt-1"><?= html_escape($e->description ?? '') ?></p>
                    </div>
                    <i class="fas fa-external-link-alt text-sm text-slate group-hover:text-gold transition"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- CTA Join Ormawa -->
        <div class="bg-navy text-ivory p-10 md:p-16 relative overflow-hidden kl-rv">
            <div class="absolute inset-0 hero-pattern"></div>
            <div class="absolute top-0 left-0 w-24 h-24 border-t-2 border-l-2 border-gold/40"></div>
            <div class="absolute bottom-0 right-0 w-24 h-24 border-b-2 border-r-2 border-gold/40"></div>
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-8 max-w-4xl mx-auto">
                <div>
                    <p class="editorial-label text-gold mb-3"><?= $EN ? 'Get Involved' : 'Ayo Bergabung' ?></p>
                    <h3 class="font-serif text-2xl md:text-4xl font-light tracking-[-0.02em]">
                        <?= $EN ? 'Find your <em class="italic text-gold">community</em> on campus.' : 'Temukan <em class="italic text-gold">komunitas</em> Anda di kampus.' ?>
                    </h3>
                </div>
                <a href="<?= base_url('kontak') ?>" class="btn-gold px-10 py-4 font-semibold uppercase tracking-editorial text-xs flex-shrink-0">
                    <i class="fas fa-handshake mr-2"></i><?= $EN ? 'Contact Ormawa' : 'Hubungi Ormawa' ?>
                </a>
            </div>
        </div>

    </div>
</section>

<script>
(function(){
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
    document.querySelectorAll('.kl-count').forEach(function(el){ cio.observe(el); });

    // ===== REVEAL =====
    var rio = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if (e.isIntersecting) { e.target.classList.add('in'); rio.unobserve(e.target); }
        });
    }, { threshold: .1 });
    document.querySelectorAll('.kl-rv').forEach(function(el){ rio.observe(el); });

    // ===== 🔥 ORM SEARCH + FILTER =====
    var ormSearch = document.getElementById('ormSearch');
    var chips = document.querySelectorAll('.orm-chip');
    var ormCards = document.querySelectorAll('#ormGrid .orm-card');
    var ormCount = document.getElementById('ormCount');
    var activeCat = 'all';

    function applyOrmFilter(){
        var q = (ormSearch ? ormSearch.value : '').toLowerCase().trim();
        var shown = 0;
        ormCards.forEach(function(card){
            var name = card.dataset.name || '';
            var cat = card.dataset.cat || '';
            var matchQ = !q || name.indexOf(q) > -1;
            var matchC = activeCat === 'all' || cat === activeCat;
            var show = matchQ && matchC;
            card.classList.toggle('kl-hidden', !show);
            if (show) shown++;
        });
        if (ormCount) ormCount.textContent = shown;
    }

    if (ormSearch) ormSearch.addEventListener('input', applyOrmFilter);

    chips.forEach(function(chip){
        chip.addEventListener('click', function(){
            chips.forEach(function(c){ c.classList.remove('active'); });
            chip.classList.add('active');
            activeCat = chip.dataset.cat;
            applyOrmFilter();
        });
    });

    // ===== 🔥 BEASISWA SORT =====
    var sortBtns = document.querySelectorAll('.kl-sort-btn');
    var tbody = document.querySelector('#beasiswaTable tbody');
    if (tbody && sortBtns.length) {
        sortBtns.forEach(function(btn){
            btn.addEventListener('click', function(){
                sortBtns.forEach(function(b){ b.classList.remove('active'); });
                btn.classList.add('active');
                var mode = btn.dataset.sort;
                var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                rows.sort(function(a, b){
                    if (mode === 'name') return a.dataset.name.localeCompare(b.dataset.name, 'id');
                    if (mode === 'amount') return (+b.dataset.amount) - (+a.dataset.amount);
                    return 0;
                });
                rows.forEach(function(row){ tbody.appendChild(row); });
            });
        });
    }

    // ===== 🔥 PORTAL SEARCH =====
    var portalSearch = document.getElementById('portalSearch');
    var portalCards = document.querySelectorAll('#portalGrid .portal-card');
    if (portalSearch) {
        portalSearch.addEventListener('input', function(){
            var q = this.value.toLowerCase().trim();
            portalCards.forEach(function(card){
                var title = card.dataset.title || '';
                card.classList.toggle('kl-hidden', q && title.indexOf(q) === -1);
            });
        });
    }
})();
</script>