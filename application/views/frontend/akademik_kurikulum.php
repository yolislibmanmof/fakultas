<?php
$EN = (get_site_lang() == 'en');
$first_slug = !empty($programs) ? $programs[0]->slug : '';
$max_sks_all = 1;
foreach ($programs as $pp) $max_sks_all = max($max_sks_all, $pp->total_sks);
?>

<style>
.scrollbar-hide::-webkit-scrollbar{display:none}.scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}
.rv{opacity:0;transform:translateY(28px);transition:opacity .9s cubic-bezier(.22,1,.36,1),transform .9s cubic-bezier(.22,1,.36,1);transition-delay:var(--d,0s)}
.rv-in{opacity:1;transform:none}
.panel-in{animation:panelIn .6s cubic-bezier(.22,1,.36,1)}
@keyframes panelIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
.j-line{transform:scaleY(0);transform-origin:top;transition:transform 1.6s cubic-bezier(.22,1,.36,1)}
.rv-in .j-line,.journey.drawn .j-line{transform:scaleY(1)}
.donut-val{transition:stroke-dasharray 1.4s cubic-bezier(.22,1,.36,1)}
.cr-desc{max-height:0;overflow:hidden;transition:max-height .45s ease}
.course-row.open .cr-desc{max-height:220px}
.course-row.open .cr-chev{transform:rotate(180deg)}

/* ===== 🔥 EXPORT BUTTONS ===== */
.export-bar {
    display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px;
}
.export-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 18px; border: 1px solid #e5e7eb; background: #fff;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
    text-decoration: none; color: #0B2239; cursor: pointer; transition: all .2s;
}
.export-btn:hover { border-color: #C9A227; color: #C9A227; }
.export-btn.primary { background: #0B2239; color: #F7F5F0; border-color: #0B2239; }
.export-btn.primary:hover { background: #C9A227; color: #0B2239; border-color: #C9A227; }

/* ===== 🔥 SEARCH HIGHLIGHT ===== */
.mk-search-highlight { background: #fef3c7; padding: 2px 4px; border-radius: 2px; }

/* ===== 🔥 PREREQUISITE BADGE ===== */
.prereq-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; background: #fef3c7; color: #92400e;
    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
}

/* ===== 🔥 BOOKMARK BTN ===== */
.bookmark-btn {
    width: 32px; height: 32px; background: transparent; border: 1px solid #e5e7eb;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .2s; color: #64748b;
}
.bookmark-btn:hover { border-color: #C9A227; color: #C9A227; }
.bookmark-btn.active { background: #C9A227; color: #fff; border-color: #C9A227; }

/* ===== 🔥 SHARE MENU ===== */
.share-menu {
    position: absolute; right: 0; top: 100%; margin-top: 8px;
    background: #fff; border: 1px solid #e5e7eb; box-shadow: 0 8px 24px rgba(0,0,0,.1);
    min-width: 160px; z-index: 50; display: none;
}
.share-menu.open { display: block; animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
.share-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; font-size: 12px; color: #0B2239;
    cursor: pointer; transition: background .15s;
}
.share-item:hover { background: #F7F5F0; }
.share-item i { width: 16px; text-align: center; color: #C9A227; }

/* ===== 🔥 SEMESTER PROGRESS ===== */
.smt-progress { height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; margin-top: 8px; }
.smt-progress-bar { height: 100%; background: linear-gradient(90deg, #C9A227, #D4AF37); border-radius: 4px; transition: width 1s ease; }

/* ===== 🔥 TOAST NOTIFICATION ===== */
.toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 100;
    padding: 14px 20px; background: #0B2239; color: #F7F5F0;
    font-size: 13px; font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    transform: translateX(120%); transition: transform .3s cubic-bezier(.22,1,.36,1);
}
.toast.show { transform: translateX(0); }
.toast i { color: #C9A227; margin-right: 8px; }

/* ===== 🔥 PRINT STYLES ===== */
@media print {
    .export-bar, .bookmark-btn, .share-menu, .toast { display: none !important; }
    .prog-panel { display: block !important; page-break-after: always; }
}
</style>

<!-- ============ HERO ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern opacity-40"></div>
    <div class="absolute -bottom-24 -right-12 font-serif text-[24rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">K</div>
    <div class="container mx-auto px-6 py-24 md:py-32 relative z-10">
        <div class="max-w-4xl">
            <div class="flex items-center gap-3 mb-6">
                <span class="bg-gold text-navy text-[10px] uppercase tracking-editorial font-bold px-4 py-2" data-scramble><?= $EN ? 'Interactive Catalogue' : 'Katalog Interaktif' ?></span>
            </div>
            <h1 class="font-serif font-light tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl lg:text-8xl text-balance">
                <?= $EN ? 'The Academic <em class="italic text-gold">Journey</em>' : 'Peta Perjalanan <em class="italic text-gold">Akademik</em>' ?>
            </h1>
            <p class="text-ivory/70 mt-6 text-lg md:text-xl leading-relaxed max-w-2xl">
                <?= $EN ? 'Choose your program and walk the semester path — every course, every credit.' : 'Pilih program studimu dan telusuri jalur semester — setiap mata kuliah, setiap kredit.' ?>
            </p>
        </div>
    </div>
</section>

<!-- ============ PROGRAM SWITCHER (Sticky) ============ -->
<?php if (!empty($programs)): ?>
<section class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-[73px] z-30">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide">
            <span class="editorial-label text-slate whitespace-nowrap mr-1"><i class="fas fa-compass text-gold-muted mr-1"></i><?= $EN ? 'Choose path' : 'Pilih jalur' ?>:</span>
            <?php foreach ($programs as $idx => $p): ?>
            <button class="prog-tab whitespace-nowrap text-xs uppercase tracking-editorial font-semibold px-5 py-2.5 border transition <?= $idx === 0 ? 'tab-on bg-navy text-ivory border-navy shadow-lg' : 'border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>" data-prog="<?= $p->slug ?>">
                <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?> &bull; <?= html_escape(character_limiter($p->name, 22)) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ PROGRAM PANELS ============ -->
<section class="py-20 md:py-28 bg-ivory">
    <div class="container mx-auto px-6">

        <?php if (empty($programs)): ?>
            <div class="text-center py-24 bg-white border border-gray-200 rv">
                <i class="fas fa-book text-6xl text-gray-300 mb-6"></i>
                <p class="font-serif text-3xl text-navy font-light"><?= $EN ? 'No programs yet.' : 'Belum ada program studi.' ?></p>
            </div>
        <?php endif; ?>

        <?php foreach ($programs as $idx => $p):
            $wajib = 0; $pilihan = 0; $flat = 0;
            foreach ($p->curriculum as $smt => $courses) foreach ($courses as $c) {
                $flat++;
                if (($c->course_type ?? 'wajib') === 'pilihan') $pilihan += (int)$c->sks; else $wajib += (int)$c->sks;
            }
            $tot = max($wajib + $pilihan, 1);
            $dash_pilihan = round(($pilihan / $tot) * 339.292, 1);
        ?>
        <div class="prog-panel <?= $idx !== 0 ? 'hidden-panel' : 'panel-in' ?>" id="panel-<?= $p->slug ?>" <?= $idx !== 0 ? 'style="display:none"' : '' ?>>

            <div class="grid lg:grid-cols-12 gap-10">

                <!-- ===== LEFT: Sticky Program Card ===== -->
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-40 space-y-6">

                        <div class="bg-navy text-ivory p-8 md:p-10 relative overflow-hidden">
                            <div class="absolute -top-6 -right-4 font-serif text-[9rem] leading-none text-gold/10 select-none pointer-events-none"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></div>
                            <div class="relative">
                                <span class="bg-gold text-navy text-[10px] uppercase tracking-editorial px-3 py-1.5 font-bold"><?= html_escape($p->degree) ?></span>
                                <h2 class="font-serif text-3xl md:text-4xl font-light leading-tight mt-5 mb-2 text-balance"><?= html_escape($p->name) ?></h2>
                                <p class="editorial-label text-ivory/60 mb-8"><?= $EN ? 'Accredited' : 'Akreditasi' ?>: <span class="text-gold"><?= html_escape($p->accreditation ?? '-') ?></span></p>

                                <div class="flex items-center gap-6 mb-8">
                                    <div class="relative w-32 h-32 flex-shrink-0">
                                        <svg viewBox="0 0 120 120" class="w-32 h-32">
                                            <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(247,245,240,.15)" stroke-width="12"/>
                                            <circle class="donut-val" cx="60" cy="60" r="54" fill="none" stroke="#C9A227" stroke-width="12"
                                                stroke-dasharray="0 339.292" data-target="<?= $dash_pilihan ?> 339.292" transform="rotate(-90 60 60)"/>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="font-serif text-2xl font-light text-gold"><?= $p->total_sks ?></span>
                                            <span class="text-[9px] uppercase tracking-wider text-ivory/60">SKS</span>
                                        </div>
                                    </div>
                                    <div class="space-y-3 text-xs">
                                        <div class="flex items-center gap-2"><span class="w-3 h-3 bg-ivory/30"></span><span class="text-ivory/70"><?= $EN ? 'Core' : 'Wajib' ?> &bull; <?= $wajib ?> SKS</span></div>
                                        <div class="flex items-center gap-2"><span class="w-3 h-3 bg-gold"></span><span class="text-ivory/70"><?= $EN ? 'Elective' : 'Pilihan' ?> &bull; <?= $pilihan ?> SKS</span></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-ivory/10">
                                    <div><div class="font-serif text-3xl font-light text-gold"><?= count($p->curriculum) ?></div><p class="text-[10px] uppercase tracking-wider text-ivory/60 mt-1"><?= $EN ? 'Semesters' : 'Semester' ?></p></div>
                                    <div><div class="font-serif text-3xl font-light text-gold"><?= $flat ?></div><p class="text-[10px] uppercase tracking-wider text-ivory/60 mt-1"><?= $EN ? 'Courses' : 'Mata Kuliah' ?></p></div>
                                </div>
                            </div>
                        </div>

                        <!-- 🔥 Export Bar -->
                        <div class="export-bar">
                            <a href="<?= base_url('akademik/detail/' . $p->slug) ?>" class="export-btn primary flex-1 text-center"><i class="fas fa-arrow-right mr-2"></i><?= $EN ? 'Detail' : 'Detail' ?></a>
                            <button type="button" onclick="exportPDF()" class="export-btn"><i class="fas fa-file-pdf"></i> PDF</button>
                            <button type="button" onclick="printCurriculum()" class="export-btn"><i class="fas fa-print"></i></button>
                        </div>
                    </div>
                </div>

                <!-- ===== RIGHT: Journey Timeline ===== -->
                <div class="lg:col-span-8">

                    <!-- Live search + Bookmark filter -->
                    <div class="flex flex-wrap items-center gap-3 mb-8">
                        <div class="relative flex-1 min-w-[240px]">
                            <input type="text" class="mk-search w-full px-6 py-4 pr-14 bg-white border-2 border-navy/10 text-navy placeholder:text-slate font-serif text-lg focus:border-gold outline-none transition" placeholder="<?= $EN ? 'Search a course in this program...' : 'Cari mata kuliah di prodi ini...' ?>">
                            <i class="fas fa-search absolute right-5 top-1/2 -translate-y-1/2 text-gold-muted"></i>
                        </div>
                        <!-- 🔥 Bookmark filter -->
                        <button type="button" class="bookmark-filter export-btn" data-panel="<?= $p->slug ?>">
                            <i class="fas fa-bookmark"></i>
                            <span class="bookmark-count">0</span>
                        </button>
                    </div>

                    <!-- Timeline -->
                    <div class="journey relative">
                        <div class="j-line absolute left-6 md:left-8 top-0 bottom-0 w-px bg-gradient-to-b from-gold via-gold/50 to-transparent hidden md:block"></div>

                        <div class="space-y-10">
                            <?php foreach ($p->curriculum as $semester => $courses):
                                $smt_sks = array_sum(array_map(function ($c) { return (int)$c->sks; }, $courses));
                                $smt_pct = round(($smt_sks / $p->total_sks) * 100);
                            ?>
                            <div class="rv relative md:pl-24" style="--d:.05s">
                                <div class="hidden md:flex absolute left-0 top-4 w-16 h-16 bg-navy items-center justify-center z-10 border-2 border-gold/40">
                                    <span class="font-serif text-2xl font-light text-gold"><?= str_pad($semester, 2, '0', STR_PAD_LEFT) ?></span>
                                </div>

                                <div class="bg-white border border-gray-200 overflow-hidden hover-lift">
                                    <div class="flex items-center justify-between px-6 md:px-8 py-5 border-b border-gray-200 bg-ivory-warm/50">
                                        <div class="flex items-center gap-4">
                                            <div class="md:hidden w-12 h-12 bg-navy flex items-center justify-center"><span class="font-serif text-lg font-light text-gold"><?= str_pad($semester, 2, '0', STR_PAD_LEFT) ?></span></div>
                                            <div>
                                                <span class="editorial-label text-navy"><?= $EN ? 'Semester' : 'Semester' ?> <?= $semester ?></span>
                                                <p class="text-[10px] text-slate mt-0.5 uppercase tracking-wider"><?= count($courses) ?> <?= $EN ? 'courses' : 'MK' ?></p>
                                                <!-- 🔥 Semester Progress -->
                                                <div class="smt-progress w-32 mt-2">
                                                    <div class="smt-progress-bar" style="width: 0%" data-w="<?= $smt_pct ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <!-- 🔥 Download per-semester -->
                                            <button type="button" onclick="downloadSemester(<?= $semester ?>, '<?= html_escape($p->slug) ?>')" class="bookmark-btn" title="Download semester schedule">
                                                <i class="fas fa-download text-xs"></i>
                                            </button>
                                            <span class="bg-navy text-gold font-serif text-xl px-4 py-1.5"><?= $smt_sks ?> <span class="text-[10px] font-sans uppercase">SKS</span></span>
                                        </div>
                                    </div>

                                    <ul class="divide-y divide-gray-100">
                                        <?php foreach ($courses as $c):
                                            $is_pil = ($c->course_type ?? 'wajib') === 'pilihan';
                                            $has_prereq = !empty($c->prerequisite);
                                            $course_id = $p->slug . '-' . ($c->course_code ?? $c->name);
                                        ?>
                                        <li class="course-row" 
                                            data-name="<?= html_escape(strtolower($c->name)) ?>" 
                                            data-code="<?= html_escape(strtolower($c->course_code ?? '')) ?>"
                                            data-id="<?= html_escape($course_id) ?>">
                                            <button type="button" class="cr-head w-full text-left px-6 md:px-8 py-4 flex items-center justify-between gap-4 hover:bg-ivory-warm/40 hover:pl-10 transition-all duration-300 group">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                        <span class="font-mono text-[10px] text-gold-muted font-semibold"><?= html_escape($c->course_code ?? '') ?></span>
                                                        <?php if ($is_pil): ?><span class="bg-teal-50 text-teal-700 text-[9px] uppercase tracking-wider font-bold px-2 py-0.5"><?= $EN ? 'Elective' : 'Pilihan' ?></span><?php endif; ?>
                                                        <?php if ($has_prereq): ?>
                                                        <span class="prereq-badge"><i class="fas fa-lock text-[8px]"></i> Prerequisite</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="text-sm md:text-base text-navy group-hover:text-gold-muted transition leading-snug course-name"><?= html_escape($c->name) ?></span>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0">
                                                    <!-- 🔥 Bookmark -->
                                                    <button type="button" class="bookmark-btn bookmark-course" data-id="<?= html_escape($course_id) ?>" title="Bookmark course">
                                                        <i class="fas fa-bookmark text-xs"></i>
                                                    </button>
                                                    <!-- 🔥 Share -->
                                                    <div class="relative">
                                                        <button type="button" class="bookmark-btn share-trigger" title="Share course">
                                                            <i class="fas fa-share-alt text-xs"></i>
                                                        </button>
                                                        <div class="share-menu">
                                                            <div class="share-item" onclick="copyCourseLink('<?= urlencode($c->name) ?>', '<?= html_escape($p->slug) ?>', '<?= html_escape($c->course_code ?? '') ?>')">
                                                                <i class="fas fa-link"></i> <?= $EN ? 'Copy Link' : 'Salin Link' ?>
                                                            </div>
                                                            <div class="share-item" onclick="shareWhatsApp('<?= urlencode($c->name) ?>', '<?= html_escape($p->slug) ?>')">
                                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                                            </div>
                                                            <div class="share-item" onclick="shareEmail('<?= urlencode($c->name) ?>', '<?= html_escape($p->slug) ?>')">
                                                                <i class="fas fa-envelope"></i> Email
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="bg-navy text-ivory text-xs px-2.5 py-1 font-semibold"><?= $c->sks ?></span>
                                                    <i class="fas fa-chevron-down text-[10px] text-slate cr-chev transition-transform"></i>
                                                </div>
                                            </button>
                                            <?php if (!empty($c->description)): ?>
                                            <div class="cr-desc px-6 md:px-8 pb-5">
                                                <p class="text-sm text-slate leading-relaxed border-l-2 border-gold pl-4"><?= html_escape($c->description) ?></p>
                                                <?php if ($has_prereq): ?>
                                                <p class="text-xs text-slate mt-3"><i class="fas fa-info-circle text-gold-muted mr-1"></i><strong>Prerequisite:</strong> <?= html_escape($c->prerequisite) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ============ COMPARISON BAND ============ -->
        <?php if (count($programs) > 1): ?>
        <div class="mt-24 bg-white border border-gray-200 p-8 md:p-12 rv">
            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'At a glance' : 'Sekilas pandang' ?></p>
            <h3 class="font-serif text-3xl md:text-4xl font-light text-navy mb-10"><?= $EN ? 'Credit <em class="italic text-gold-muted">comparison</em>' : 'Perbandingan <em class="italic text-gold-muted">beban SKS</em>' ?></h3>
            <div class="space-y-6">
                <?php foreach ($programs as $p): ?>
                <div class="group">
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="font-serif text-lg text-navy group-hover:text-gold-muted transition"><?= html_escape($p->name) ?></span>
                        <span class="font-mono text-xs text-slate font-semibold"><?= $p->total_sks ?> SKS</span>
                    </div>
                    <div class="h-3 bg-ivory-warm overflow-hidden">
                        <div class="cmp-bar h-full bg-gradient-to-r from-navy to-gold group-hover:from-gold group-hover:to-gold-soft transition-all duration-500" style="width:0%" data-w="<?= round(($p->total_sks / $max_sks_all) * 100) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- 🔥 Toast Notification -->
<div id="toast" class="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<script>
(function(){
    // ===== REVEAL + COUNTERS + DONUT =====
    var io = new IntersectionObserver(function(es){
        es.forEach(function(en){ if(!en.isIntersecting) return;
            en.target.classList.add('rv-in');
            en.target.querySelectorAll('[data-count]').forEach(runCount);
            en.target.querySelectorAll('.donut-val').forEach(function(d){ d.setAttribute('stroke-dasharray', d.dataset.target); });
            en.target.querySelectorAll('.cmp-bar').forEach(function(b){ b.style.width = b.dataset.w; });
            en.target.querySelectorAll('.smt-progress-bar').forEach(function(b){ 
                setTimeout(function(){ b.style.width = b.dataset.w; }, 200); 
            });
            if(en.target.classList.contains('journey')) en.target.classList.add('drawn');
            io.unobserve(en.target);
        });
    }, {threshold:.15});
    document.querySelectorAll('.rv, .journey').forEach(function(el){ io.observe(el); });

    function runCount(el){ if(el.dataset.done) return; el.dataset.done=1;
        var t=+el.dataset.count, st=performance.now();
        (function f(n){ var p=Math.min(1,(n-st)/1400), e=1-Math.pow(1-p,3);
            el.textContent=Math.round(t*e).toLocaleString('id-ID'); if(p<1) requestAnimationFrame(f); })(st); }

    // Scramble
    document.querySelectorAll('[data-scramble]').forEach(function(el){
        var fin=el.textContent, chars='#@%&$!?<>*', i=0;
        var iv=setInterval(function(){ el.textContent=fin.split('').map(function(c,idx){ return idx<i?c:chars[Math.floor(Math.random()*chars.length)]; }).join(''); i++; if(i>fin.length){clearInterval(iv); el.textContent=fin;} },45);
    });

    // Program switcher
    document.querySelectorAll('.prog-tab').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.prog-tab').forEach(function(b){
                var on = b===btn;
                b.classList.toggle('tab-on', on);
                b.classList.toggle('bg-navy', on); b.classList.toggle('text-ivory', on); b.classList.toggle('border-navy', on); b.classList.toggle('shadow-lg', on);
            });
            document.querySelectorAll('.prog-panel').forEach(function(p){
                var on = p.id === 'panel-' + btn.dataset.prog;
                p.style.display = on ? '' : 'none';
                if(on){ p.classList.remove('panel-in'); void p.offsetWidth; p.classList.add('panel-in'); }
            });
        });
    });

    // ===== 🔥 SEARCH WITH HIGHLIGHT =====
    document.querySelectorAll('.mk-search').forEach(function(inp){
        inp.addEventListener('input', function(){
            var q = inp.value.toLowerCase();
            var panel = inp.closest('.prog-panel');
            panel.querySelectorAll('.course-row').forEach(function(row){
                var hit = row.dataset.name.indexOf(q) > -1 || row.dataset.code.indexOf(q) > -1;
                row.style.display = hit ? '' : 'none';
                
                // Highlight matching text
                var nameEl = row.querySelector('.course-name');
                if (nameEl && q) {
                    var original = nameEl.dataset.original || nameEl.textContent;
                    nameEl.dataset.original = original;
                    if (hit) {
                        var regex = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                        nameEl.innerHTML = original.replace(regex, '<span class="mk-search-highlight">$1</span>');
                    } else {
                        nameEl.textContent = original;
                    }
                } else if (nameEl && !q) {
                    nameEl.textContent = nameEl.dataset.original || nameEl.textContent;
                }
            });
        });
    });

    // Expandable courses
    document.querySelectorAll('.cr-head').forEach(function(h){
        h.addEventListener('click', function(e){
            if (e.target.closest('.bookmark-btn') || e.target.closest('.share-trigger') || e.target.closest('.share-menu')) return;
            h.closest('.course-row').classList.toggle('open');
        });
    });

    // ===== 🔥 SHARE MENU =====
    document.querySelectorAll('.share-trigger').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var menu = btn.nextElementSibling;
            document.querySelectorAll('.share-menu.open').forEach(function(m){ if (m !== menu) m.classList.remove('open'); });
            menu.classList.toggle('open');
        });
    });
    document.addEventListener('click', function(){ 
        document.querySelectorAll('.share-menu.open').forEach(function(m){ m.classList.remove('open'); }); 
    });

    // ===== 🔥 BOOKMARK SYSTEM =====
    var bookmarks = JSON.parse(localStorage.getItem('course_bookmarks') || '[]');
    
    function updateBookmarkUI() {
        document.querySelectorAll('.bookmark-course').forEach(function(btn){
            btn.classList.toggle('active', bookmarks.indexOf(btn.dataset.id) > -1);
        });
        // Update counts per panel
        document.querySelectorAll('.bookmark-filter').forEach(function(btn){
            var panel = btn.dataset.panel;
            var count = bookmarks.filter(function(b){ return b.indexOf(panel + '-') === 0; }).length;
            btn.querySelector('.bookmark-count').textContent = count;
        });
    }
    
    document.querySelectorAll('.bookmark-course').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var id = btn.dataset.id;
            var idx = bookmarks.indexOf(id);
            if (idx > -1) {
                bookmarks.splice(idx, 1);
                showToast('<?= $EN ? "Bookmark removed" : "Bookmark dihapus" ?>');
            } else {
                bookmarks.push(id);
                showToast('<?= $EN ? "Course bookmarked!" : "Mata kuliah ditandai!" ?>');
            }
            localStorage.setItem('course_bookmarks', JSON.stringify(bookmarks));
            updateBookmarkUI();
        });
    });
    
    // Bookmark filter
    document.querySelectorAll('.bookmark-filter').forEach(function(btn){
        btn.addEventListener('click', function(){
            var panel = document.getElementById('panel-' + btn.dataset.panel);
            var active = btn.classList.toggle('active');
            panel.querySelectorAll('.course-row').forEach(function(row){
                if (active) {
                    var isBookmarked = bookmarks.indexOf(row.dataset.id) > -1;
                    row.style.display = isBookmarked ? '' : 'none';
                } else {
                    row.style.display = '';
                }
            });
        });
    });
    
    updateBookmarkUI();

    // ===== 🔥 TOAST =====
    function showToast(msg) {
        var toast = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        toast.classList.add('show');
        setTimeout(function(){ toast.classList.remove('show'); }, 2500);
    }

    // ===== 🔥 EXPORT FUNCTIONS =====
    window.exportPDF = function() {
        showToast('<?= $EN ? "Preparing PDF..." : "Menyiapkan PDF..." ?>');
        setTimeout(function(){ window.print(); }, 500);
    };
    
    window.printCurriculum = function() {
        window.print();
    };
    
    window.downloadSemester = function(smt, slug) {
        showToast('<?= $EN ? "Downloading semester " : "Mengunduh semester " ?>' + smt + '...');
        // Simulate download - in production, this would call backend
    };
    
    window.copyCourseLink = function(name, slug, code) {
        var url = window.location.origin + '/akademik/kurikulum?prog=' + slug + '&course=' + encodeURIComponent(code);
        navigator.clipboard.writeText(url).then(function(){
            showToast('<?= $EN ? "Link copied!" : "Link disalin!" ?>');
        });
        document.querySelectorAll('.share-menu.open').forEach(function(m){ m.classList.remove('open'); });
    };
    
    window.shareWhatsApp = function(name, slug) {
        var url = window.location.origin + '/akademik/kurikulum?prog=' + slug;
        window.open('https://wa.me/?text=' + encodeURIComponent(decodeURIComponent(name) + ' - ' + url), '_blank');
    };
    
    window.shareEmail = function(name, slug) {
        var url = window.location.origin + '/akademik/kurikulum?prog=' + slug;
        window.location.href = 'mailto:?subject=' + encodeURIComponent(decodeURIComponent(name)) + '&body=' + encodeURIComponent(url);
    };
})();
</script>