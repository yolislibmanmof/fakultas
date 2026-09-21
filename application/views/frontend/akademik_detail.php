<?php
$EN  = (get_site_lang() == 'en');
$CI  =& get_instance();
$program = $program ?? NULL;
if (!$program) show_404();

// Defensive: pastikan kurikulum tersedia & ter-group per semester
$curriculum = $curriculum ?? [];
$total_sks  = $total_sks ?? 0;

if (!empty($curriculum)) {
    $first = reset($curriculum);
    if (is_object($first) && isset($first->course_code)) {
        $grouped = [];
        foreach ($curriculum as $r) $grouped[$r->semester][] = $r;
        $curriculum = $grouped;
        if (!$total_sks) foreach ($curriculum as $list) foreach ($list as $r) $total_sks += (int)$r->sks;
    }
}

if (empty($curriculum)) {
    $f  = $CI->db->list_fields('course_study_program');
    $cc = in_array('course_id', $f) ? 'course_id' : 'courses_id';
    $pc = in_array('study_program_id', $f) ? 'study_program_id' : 'program_id';
    $rows = $CI->db->select('courses.*')
        ->from('courses')
        ->join('course_study_program', 'course_study_program.' . $cc . ' = courses.id')
        ->where('course_study_program.' . $pc, $program->id)
        ->order_by('courses.semester', 'ASC')
        ->order_by('courses.course_code', 'ASC')
        ->get()->result();
    foreach ($rows as $r) $curriculum[$r->semester][] = $r;
    foreach ($rows as $r) $total_sks += (int)$r->sks;
}

$semester_count = count($curriculum);
$course_count = 0;
foreach ($curriculum as $list) $course_count += count($list);

$semester_sks = [];
foreach ($curriculum as $sem => $list) {
    $semester_sks[$sem] = array_sum(array_map(function($c){ return (int)$c->sks; }, $list));
}

// 🔥 Related programs (exclude current, same degree preferred)
$related_programs = [];
if (!empty($programs)) {
    $related_programs = array_filter($programs, function($p) use ($program) {
        return $p->id != $program->id;
    });
    // Prioritize same degree
    usort($related_programs, function($a, $b) use ($program) {
        $a_match = ($a->degree === $program->degree) ? 0 : 1;
        $b_match = ($b->degree === $program->degree) ? 0 : 1;
        return $a_match - $b_match;
    });
    $related_programs = array_slice($related_programs, 0, 3);
}
?>

<style>
/* ===== HERO STATS COUNTER ===== */
.stat-counter { font-family:'Fraunces',serif; font-weight:300; line-height:1; color:#C9A227; }

/* ===== VISI DROP-CAP ===== */
.vision-quote {
    position:relative; padding:2rem 2rem 2rem 5rem;
    font-family:'Fraunces',serif; font-size:1.5rem; font-style:italic;
    line-height:1.5; color:#F7F5F0;
}
.vision-quote::before {
    content:'"'; position:absolute; left:1rem; top:-1rem;
    font-size:8rem; line-height:1; color:rgba(201,162,39,.2);
    font-family:'Fraunces',serif; font-weight:700;
}

/* ===== TAB KURIKULUM ===== */
.sem-tabs { display:flex; gap:.5rem; overflow-x:auto; padding-bottom:.5rem; margin-bottom:1.5rem; scrollbar-width:thin; }
.sem-tabs::-webkit-scrollbar { height:6px; }
.sem-tabs::-webkit-scrollbar-track { background:#F7F5F0; }
.sem-tabs::-webkit-scrollbar-thumb { background:#C9A227; border-radius:3px; }
.sem-tab {
    flex-shrink:0; padding:.75rem 1.25rem;
    background:#fff; border:2px solid #e5e7eb;
    font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.15em;
    text-transform:uppercase; font-weight:600; color:#64748b;
    cursor:pointer; transition:all .3s;
    display:flex; flex-direction:column; align-items:center; gap:2px;
    min-width:100px;
}
.sem-tab:hover { border-color:#C9A227; color:#0B2239; }
.sem-tab.active { background:#0B2239; border-color:#0B2239; color:#C9A227; }
.sem-tab .sem-num { font-family:'Fraunces',serif; font-size:1.5rem; font-weight:600; line-height:1; }
.sem-tab .sem-label { font-size:9px; letter-spacing:.2em; }
.sem-tab .sem-sks { font-size:9px; opacity:.7; }

.sem-content { display:none; animation:fadeIn .4s ease; }
.sem-content.active { display:block; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }

/* ===== COURSE CARDS ===== */
.course-card {
    background:#fff; border:1px solid #e5e7eb;
    padding:1.25rem; transition:all .35s cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; cursor:pointer;
}
.course-card::before {
    content:''; position:absolute; left:0; top:0; bottom:0; width:4px;
    background:linear-gradient(180deg, #C9A227, #B8941F);
    transform:scaleY(0); transition:transform .4s;
    transform-origin:bottom;
}
.course-card:hover { transform:translateY(-4px); box-shadow:0 12px 24px rgba(11,34,57,.12); }
.course-card:hover::before { transform:scaleY(1); }
.course-code {
    font-family:'JetBrains Mono',monospace; font-size:10px; letter-spacing:.15em;
    color:#B8941F; font-weight:600; text-transform:uppercase; margin-bottom:.25rem;
}
.course-name { font-family:'Fraunces',serif; font-size:1.05rem; font-weight:500; color:#0B2239; line-height:1.3; margin-bottom:.75rem; }
.course-meta { display:flex; align-items:center; gap:.75rem; font-size:11px; color:#64748b; flex-wrap:wrap; }
.course-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 10px; font-family:'JetBrains Mono',monospace;
    font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
}
.course-badge.sks { background:#0B2239; color:#C9A227; }
.course-badge.wajib { background:#fef3c7; color:#92400e; }
.course-badge.pilihan { background:#dbeafe; color:#1e40af; }
.course-badge.prereq { background:#fef3c7; color:#92400e; }

/* ===== 🔥 COURSE DETAIL MODAL ===== */
.course-modal {
    position:fixed; inset:0; z-index:100;
    background:rgba(6,20,32,.9); backdrop-filter:blur(8px);
    display:none; align-items:center; justify-content:center; padding:1rem;
}
.course-modal.open { display:flex; animation:fadeIn .3s ease; }
.course-modal-card {
    background:#fff; width:100%; max-width:640px; max-height:85vh; overflow-y:auto;
    box-shadow:0 32px 64px rgba(0,0,0,.3);
}
.course-modal-head {
    padding:24px; background:#0B2239; color:#F7F5F0;
    display:flex; justify-content:space-between; align-items:start;
    border-bottom:3px solid #C9A227;
}
.course-modal-close {
    background:none; border:none; color:#C9A227; font-size:24px;
    cursor:pointer; padding:0; width:32px; height:32px;
}
.course-modal-body { padding:24px; }
.course-modal-section { margin-bottom:20px; }
.course-modal-section h4 {
    font-size:10px; text-transform:uppercase; letter-spacing:.15em;
    color:#64748b; font-weight:600; margin-bottom:8px;
}

/* ===== KARIR GRID ===== */
.career-card {
    background:#fff; border:1px solid #e5e7eb; padding:1.5rem;
    text-align:center; transition:all .35s cubic-bezier(.22,1,.36,1);
}
.career-card:hover { transform:translateY(-6px); border-color:#C9A227; box-shadow:0 16px 32px rgba(11,34,57,.1); }
.career-icon {
    width:56px; height:56px; margin:0 auto 1rem;
    background:linear-gradient(135deg, #C9A227, #B8941F);
    color:#0B2239; display:flex; align-items:center; justify-content:center;
    font-size:1.5rem; border-radius:50%;
    transition:transform .4s;
}
.career-card:hover .career-icon { transform:rotate(-8deg) scale(1.1); }

/* ===== LAB CARDS ===== */
.lab-card {
    background:#fff; border:1px solid #e5e7eb; overflow:hidden;
    transition:all .35s cubic-bezier(.22,1,.36,1);
}
.lab-card:hover { transform:translateY(-4px); box-shadow:0 12px 24px rgba(11,34,57,.12); }
.lab-photo { aspect-ratio:16/10; background:linear-gradient(135deg, #0B2239, #13334F); overflow:hidden; }
.lab-photo img { width:100%; height:100%; object-fit:cover; transition:transform .6s; }
.lab-card:hover .lab-photo img { transform:scale(1.08); }

/* ===== AKREDITASI TIMELINE ===== */
.akred-timeline {
    position:relative; padding-left:2rem; border-left:3px solid #C9A227;
}
.akred-timeline::before {
    content:''; position:absolute; left:-9px; top:0;
    width:15px; height:15px; border-radius:50%;
    background:#C9A227; box-shadow:0 0 0 4px #F7F5F0, 0 0 0 5px #C9A227;
}

/* ===== DOSEN MINI CARDS ===== */
.lecturer-mini {
    display:flex; align-items:center; gap:.75rem; padding:.75rem;
    background:#fff; border:1px solid #e5e7eb;
    transition:all .3s;
}
.lecturer-mini:hover { border-color:#C9A227; transform:translateX(4px); }
.lecturer-mini .photo {
    width:48px; height:48px; border-radius:50%; overflow:hidden;
    background:#0B2239; color:#C9A227; display:flex; align-items:center; justify-content:center;
    font-family:'Fraunces',serif; font-weight:700; font-size:1.2rem; flex-shrink:0;
    border:2px solid rgba(201,162,39,.4);
}

/* ===== 🔥 FAQ ACCORDION ===== */
.faq-item { border-bottom:1px solid #e5e7eb; }
.faq-item:last-child { border:0; }
.faq-question {
    display:flex; justify-content:space-between; align-items:center;
    padding:1.25rem 0; cursor:pointer; transition:color .2s;
}
.faq-question:hover { color:#C9A227; }
.faq-question h4 { font-family:'Fraunces',serif; font-size:1.1rem; font-weight:500; color:#0B2239; flex:1; }
.faq-question .faq-icon { color:#C9A227; transition:transform .3s; }
.faq-item.open .faq-icon { transform:rotate(180deg); }
.faq-answer { max-height:0; overflow:hidden; transition:max-height .4s ease; }
.faq-item.open .faq-answer { max-height:500px; }
.faq-answer p { padding-bottom:1.25rem; color:#475569; line-height:1.7; }

/* ===== 🔥 SHARE FLOATING BTN ===== */
.share-fab {
    position:fixed; bottom:24px; right:24px; z-index:80;
    width:56px; height:56px; background:#C9A227; color:#0B2239;
    border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-size:1.25rem; cursor:pointer; box-shadow:0 8px 24px rgba(201,162,39,.4);
    transition:all .3s;
}
.share-fab:hover { transform:scale(1.1); background:#0B2239; color:#C9A227; }

.share-panel {
    position:fixed; bottom:90px; right:24px; z-index:81;
    background:#fff; width:240px; padding:16px;
    box-shadow:0 12px 32px rgba(0,0,0,.15);
    display:none;
}
.share-panel.open { display:block; animation:fadeIn .2s ease; }
.share-option {
    display:flex; align-items:center; gap:12px;
    padding:10px 12px; cursor:pointer; transition:background .15s;
    font-size:13px; color:#0B2239;
}
.share-option:hover { background:#F7F5F0; }
.share-option i { width:20px; text-align:center; color:#C9A227; }

/* ===== 🔥 BOOKMARK BTN ===== */
.bookmark-float {
    position:fixed; bottom:24px; right:90px; z-index:80;
    width:48px; height:48px; background:#fff; border:2px solid #e5e7eb;
    border-radius:50%; display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all .3s; color:#64748b;
}
.bookmark-float:hover { border-color:#C9A227; color:#C9A227; }
.bookmark-float.active { background:#C9A227; border-color:#C9A227; color:#fff; }

/* ===== 🔥 RELATED PROGRAMS ===== */
.related-card {
    background:#fff; border:1px solid #e5e7eb; padding:1.5rem;
    transition:all .3s; position:relative; overflow:hidden;
}
.related-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:#C9A227; transform:scaleX(0); transition:transform .3s;
    transform-origin:left;
}
.related-card:hover { transform:translateY(-4px); box-shadow:0 12px 24px rgba(11,34,57,.1); }
.related-card:hover::before { transform:scaleX(1); }

/* ===== 🔥 TOAST ===== */
.toast {
    position:fixed; bottom:100px; right:24px; z-index:100;
    padding:12px 20px; background:#0B2239; color:#F7F5F0;
    font-size:13px; box-shadow:0 8px 24px rgba(0,0,0,.2);
    transform:translateX(120%); transition:transform .3s;
}
.toast.show { transform:translateX(0); }
.toast i { color:#C9A227; margin-right:8px; }

/* ===== REVEAL ===== */
.ak-reveal { opacity:0; transform:translateY(24px); transition:all .9s cubic-bezier(.22,1,.36,1); }
.ak-reveal.in { opacity:1; transform:none; }

/* ===== CTA SECTION ===== */
.cta-section {
    background:linear-gradient(135deg, #0B2239 0%, #13334F 100%);
    position:relative; overflow:hidden;
}
.cta-section::before {
    content:''; position:absolute; inset:0;
    background-image:
        radial-gradient(circle at 20% 30%, rgba(201,162,39,.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(201,162,39,.08) 0%, transparent 50%);
}

/* ===== PRINT ===== */
@media print {
    .share-fab, .bookmark-float, .share-panel, .toast, .sem-tabs { display:none !important; }
    .sem-content { display:block !important; page-break-inside:avoid; }
    .course-card { break-inside:avoid; }
}
</style>

<!-- ============ HERO ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute -bottom-16 -right-8 font-serif text-[16rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">
        <?= strtoupper(substr($program->name, 0, 1)) ?>
    </div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
        <a href="<?= base_url('akademik') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-ivory/60 hover:text-gold transition mb-8">
            <svg width="20" height="10" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            <?= $EN ? 'All Study Programs' : 'Semua Program Studi' ?>
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <span class="bg-gold text-navy text-[10px] uppercase tracking-editorial px-3 py-1.5 font-bold"><?= html_escape($program->degree) ?></span>
            <?php if (!empty($program->accreditation)): ?>
            <span class="border border-gold/50 text-gold text-[10px] uppercase tracking-editorial px-3 py-1.5 font-semibold">
                <i class="fas fa-certificate mr-1"></i><?= $EN ? 'Accreditation' : 'Akreditasi' ?> <?= html_escape($program->accreditation) ?>
            </span>
            <?php endif; ?>
            <?php if (($alumni_count ?? 0) > 0): ?>
            <span class="border border-ivory/30 text-ivory/80 text-[10px] uppercase tracking-editorial px-3 py-1.5 font-semibold">
                <i class="fas fa-user-graduate mr-1"></i><?= $alumni_count ?> Alumni
            </span>
            <?php endif; ?>
        </div>
        <h1 class="font-serif font-light text-4xl md:text-6xl tracking-[-0.02em] leading-[1.05] max-w-4xl text-balance">
            <?= html_escape($program->name) ?>
        </h1>

        <div class="grid grid-cols-3 gap-6 mt-10 max-w-xl">
            <div>
                <div class="stat-counter text-3xl md:text-4xl" data-count="<?= $total_sks ?>"><?= $total_sks ?></div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1">Total SKS</p>
            </div>
            <div>
                <div class="stat-counter text-3xl md:text-4xl" data-count="<?= $semester_count ?>"><?= $semester_count ?></div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Semesters' : 'Semester' ?></p>
            </div>
            <div>
                <div class="stat-counter text-3xl md:text-4xl" data-count="<?= $course_count ?>"><?= $course_count ?></div>
                <p class="text-[10px] uppercase tracking-editorial text-ivory/60 mt-1"><?= $EN ? 'Courses' : 'Mata Kuliah' ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============ BODY ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12">

            <!-- Main -->
            <div class="lg:col-span-2 space-y-12">

                <!-- Deskripsi -->
                <div class="bg-white border border-gray-200 p-8 md:p-10 ak-reveal">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'About the Program' : 'Tentang Program' ?></p>
                    <h2 class="font-serif text-3xl font-light text-navy tracking-[-0.02em] mb-6"><?= $EN ? 'Description' : 'Deskripsi' ?></h2>
                    <p class="text-gray-700 leading-[1.9] text-[16px]"><?= nl2br(html_escape($program->description ?? '-')) ?></p>
                </div>

                <!-- Visi -->
                <?php if (!empty($program->vision)): ?>
                <div class="bg-navy text-ivory p-8 md:p-10 relative overflow-hidden ak-reveal">
                    <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-gold/40"></div>
                    <div class="absolute bottom-0 right-0 w-20 h-20 border-b-2 border-r-2 border-gold/40"></div>
                    <div class="relative">
                        <p class="editorial-label text-gold mb-4"><?= $EN ? 'Vision' : 'Visi' ?></p>
                        <p class="vision-quote">"<?= nl2br(html_escape($program->vision)) ?>"</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Misi -->
                <?php if (!empty($program->mission)): ?>
                <div class="bg-white border border-gray-200 p-8 md:p-10 ak-reveal">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Mission' : 'Misi' ?></p>
                    <h2 class="font-serif text-3xl font-light text-navy tracking-[-0.02em] mb-6"><?= $EN ? 'Our Mission' : 'Misi Kami' ?></h2>
                    <div class="space-y-4">
                        <?php
                        $missions = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string)$program->mission)), 'strlen'));
                        foreach ($missions as $i => $m):
                        ?>
                        <div class="flex gap-4 items-start">
                            <span class="font-serif text-2xl font-light text-gold-muted leading-none flex-shrink-0 w-8"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                            <p class="text-gray-700 leading-relaxed pt-1 flex-1"><?= html_escape(preg_replace('/^\d+[\.\)]\s*/', '', $m)) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Kurikulum dengan Tabs -->
                <div class="ak-reveal">
                    <div class="flex items-end justify-between mb-8 flex-wrap gap-4">
                        <div>
                            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Course Structure' : 'Struktur Kurikulum' ?></p>
                            <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]"><?= $EN ? 'Curriculum' : 'Kurikulum' ?></h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="window.print()" class="text-xs uppercase tracking-editorial font-semibold text-navy hover:text-gold transition flex items-center gap-2">
                                <i class="fas fa-print"></i><?= $EN ? 'Print' : 'Cetak' ?>
                            </button>
                            <span class="text-right">
                                <span class="font-serif text-4xl font-light text-gold-muted"><?= $total_sks ?></span>
                                <span class="block text-[10px] uppercase tracking-editorial text-slate">Total SKS</span>
                            </span>
                        </div>
                    </div>

                    <?php if (empty($curriculum)): ?>
                        <p class="text-slate bg-white border border-gray-200 p-8"><?= $EN ? 'Curriculum has not been published yet.' : 'Kurikulum belum dipublikasikan.' ?></p>
                    <?php else: ?>
                    <div class="sem-tabs" id="semTabs">
                        <?php foreach ($curriculum as $sem => $list):
                            $smt_sks = $semester_sks[$sem] ?? 0;
                        ?>
                        <button class="sem-tab <?= $sem == array_key_first($curriculum) ? 'active' : '' ?>" data-sem="<?= $sem ?>">
                            <span class="sem-num"><?= $sem ?></span>
                            <span class="sem-label"><?= $EN ? 'Semester' : 'Smt' ?></span>
                            <span class="sem-sks"><?= $smt_sks ?> SKS</span>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ($curriculum as $sem => $list):
                        $smt_sks = $semester_sks[$sem] ?? 0;
                    ?>
                    <div class="sem-content <?= $sem == array_key_first($curriculum) ? 'active' : '' ?>" data-sem="<?= $sem ?>">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <span class="editorial-label text-navy"><?= $EN ? 'Semester' : 'Semester' ?> <?= $sem ?></span>
                                <span class="block text-xs text-slate mt-1"><?= count($list) ?> <?= $EN ? 'courses' : 'mata kuliah' ?> &bull; <?= $smt_sks ?> SKS</span>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <?php foreach ($list as $c):
                                $type = strtolower($c->course_type ?? 'wajib');
                                $badge_cls = strpos($type, 'pilihan') !== false ? 'pilihan' : 'wajib';
                                $has_prereq = !empty($c->prerequisite);
                            ?>
                            <div class="course-card" 
                                 onclick="openCourseModal(this)"
                                 data-code="<?= html_escape($c->course_code ?? '') ?>"
                                 data-name="<?= html_escape($c->name) ?>"
                                 data-sks="<?= (int)$c->sks ?>"
                                 data-type="<?= html_escape($c->course_type ?? 'Wajib') ?>"
                                 data-desc="<?= html_escape($c->description ?? '') ?>"
                                 data-prereq="<?= html_escape($c->prerequisite ?? '') ?>"
                                 data-outcomes="<?= html_escape($c->learning_outcomes ?? '') ?>">
                                <div class="course-code"><?= html_escape($c->course_code ?? '-') ?></div>
                                <h4 class="course-name"><?= html_escape($c->name) ?></h4>
                                <div class="course-meta">
                                    <span class="course-badge sks"><?= $c->sks ?> SKS</span>
                                    <span class="course-badge <?= $badge_cls ?>"><?= ucfirst($c->course_type ?? 'Wajib') ?></span>
                                    <?php if ($has_prereq): ?>
                                    <span class="course-badge prereq"><i class="fas fa-lock text-[8px]"></i> Prereq</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Prospek Karir -->
                <?php if (!empty($career_prospects)): ?>
                <div class="ak-reveal">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Career Opportunities' : 'Prospek Karir' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8">
                        <?= $EN ? 'Where You Can <em class="italic text-gold-muted">Go</em>' : 'Kemana Anda Bisa <em class="italic text-gold-muted">Melangkah</em>' ?>
                    </h2>
                    <div class="grid md:grid-cols-3 gap-5">
                        <?php
                        $career_icons = ['fa-laptop-code', 'fa-chart-line', 'fa-server', 'fa-users-cog', 'fa-project-diagram', 'fa-microscope'];
                        foreach ($career_prospects as $i => $cp):
                        ?>
                        <div class="career-card">
                            <div class="career-icon">
                                <i class="fas <?= $career_icons[$i % count($career_icons)] ?>"></i>
                            </div>
                            <h4 class="font-serif text-lg font-medium text-navy"><?= html_escape($cp) ?></h4>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 🔥 FAQ Section -->
<?php
$faq1_q = $EN ? 'What are the admission requirements?' : 'Apa saja syarat pendaftaran?';
$faq1_a = $EN ? 'Requirements vary by program. Generally, you need a high school diploma or equivalent. Check our PMB page for specific requirements.' : 'Syarat bervariasi per program. Umumnya dibutuhkan ijazah SMA/sederajat. Cek halaman PMB untuk syarat spesifik.';
$faq2_q = $EN ? 'How long is the program?' : 'Berapa lama durasi program?';
$faq2_a = $semester_count . ' ' . ($EN ? 'semesters.' : 'semester.');
$faq3_q = $EN ? 'Is there scholarship available?' : 'Apakah ada beasiswa?';
$faq3_a = $EN ? 'Yes, we offer various scholarships including academic merit, financial need-based, and external partnerships.' : 'Ya, tersedia berbagai beasiswa termasuk prestasi akademik, bantuan finansial, dan kemitraan eksternal.';
$faq4_q = $EN ? 'What is the tuition fee?' : 'Berapa biaya kuliah?';
$faq4_a = $EN ? 'Tuition fees vary. Please contact our admissions office or check the PMB page for the latest information.' : 'Biaya bervariasi. Hubungi kantor admisi atau cek halaman PMB untuk informasi terbaru.';
$default_faqs = array(
    array($faq1_q, $faq1_a),
    array($faq2_q, $faq2_a),
    array($faq3_q, $faq3_a),
    array($faq4_q, $faq4_a)
);
?>
                <div class="bg-white border border-gray-200 p-8 md:p-10 ak-reveal">
                    <p class="editorial-label text-gold-muted mb-4">FAQ</p>
                    <h2 class="font-serif text-3xl font-light text-navy tracking-[-0.02em] mb-6">
                        <?= $EN ? 'Frequently Asked <em class="italic text-gold-muted">Questions</em>' : 'Pertanyaan yang <em class="italic text-gold-muted">Sering Diajukan</em>' ?>
                    </h2>
                    <div class="faq-list">
                        <?php foreach ($default_faqs as $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <h4><?= $faq[0] ?></h4>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p><?= $faq[1] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Dosen Pengampu -->
                <?php if (!empty($lecturers)): ?>
                <div class="ak-reveal">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Teaching Staff' : 'Tenaga Pengajar' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8">
                        <?= $EN ? 'Our <em class="italic text-gold-muted">Lecturers</em>' : 'Dosen <em class="italic text-gold-muted">Kami</em>' ?>
                    </h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <?php foreach ($lecturers as $l): ?>
                        <a href="<?= base_url('dosen/detail/' . $l->id) ?>" class="lecturer-mini">
                            <div class="photo">
                                <?php if (!empty($l->photo) && file_exists(FCPATH . 'assets/uploads/' . $l->photo)): ?>
                                    <img src="<?= base_url('assets/uploads/' . $l->photo) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <?= strtoupper(substr($l->name, 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-navy text-sm truncate"><?= html_escape(trim(($l->title_front ?? '') . ' ' . $l->name)) ?></p>
                                <p class="text-xs text-slate truncate"><?= html_escape($l->expertise ?? '-') ?></p>
                            </div>
                            <i class="fas fa-arrow-right text-gold-muted text-xs"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Laboratorium -->
                <?php if (!empty($labs)): ?>
                <div class="ak-reveal">
                    <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Facilities' : 'Fasilitas' ?></p>
                    <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8">
                        <?= $EN ? 'Our <em class="italic text-gold-muted">Labs</em>' : 'Laboratorium <em class="italic text-gold-muted">Kami</em>' ?>
                    </h2>
                    <div class="grid md:grid-cols-2 gap-5">
                        <?php foreach ($labs as $lab): ?>
                        <div class="lab-card">
                            <div class="lab-photo">
                                <?php if (!empty($lab->photo) && file_exists(FCPATH . 'assets/uploads/' . $lab->photo)): ?>
                                    <img src="<?= base_url('assets/uploads/' . $lab->photo) ?>" alt="<?= html_escape($lab->name) ?>">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-flask text-4xl text-gold/30"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <h4 class="font-serif text-lg font-medium text-navy mb-2"><?= html_escape($lab->name) ?></h4>
                                <?php if (!empty($lab->description)): ?>
                                <p class="text-sm text-slate line-clamp-2"><?= html_escape(character_limiter($lab->description, 100)) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <aside>
                <div class="sticky top-32 space-y-6">
                    <!-- Program Facts -->
                    <div class="bg-white border border-gray-200 p-8 ak-reveal">
                        <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Program Facts' : 'Fakta Program' ?></p>
                        <ul class="space-y-4 text-sm">
                            <li class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-slate"><?= $EN ? 'Level' : 'Jenjang' ?></span>
                                <span class="font-semibold text-navy"><?= html_escape($program->degree) ?></span>
                            </li>
                            <?php if (!empty($program->accreditation)): ?>
                            <li class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-slate"><?= $EN ? 'Accreditation' : 'Akreditasi' ?></span>
                                <span class="font-bold text-gold-muted text-lg"><?= html_escape($program->accreditation) ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($program->accreditation_until)): ?>
                            <li class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-slate"><?= $EN ? 'Valid Until' : 'Berlaku s/d' ?></span>
                                <span class="font-mono text-xs text-navy"><?= date('d M Y', strtotime($program->accreditation_until)) ?></span>
                            </li>
                            <?php endif; ?>
                            <li class="flex justify-between border-b border-gray-100 pb-3">
                                <span class="text-slate">Total SKS</span>
                                <span class="font-semibold text-navy"><?= $total_sks ?></span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-slate"><?= $EN ? 'Duration' : 'Durasi' ?></span>
                                <span class="font-semibold text-navy"><?= $semester_count ?> <?= $EN ? 'semesters' : 'semester' ?></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Kepala Prodi -->
                    <?php if (!empty($program->head_of_study_program)): ?>
                    <div class="bg-navy text-ivory p-8 relative overflow-hidden ak-reveal">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gold/10 rounded-full blur-2xl"></div>
                        <div class="relative">
                            <p class="editorial-label text-gold mb-5"><?= $EN ? 'Program Head' : 'Ketua Program Studi' ?></p>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-gold text-navy rounded-full flex items-center justify-center font-serif font-bold text-2xl flex-shrink-0 border-2 border-gold/60">
                                    <?= strtoupper(substr($program->head_of_study_program, 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-serif text-lg font-medium leading-snug"><?= html_escape($program->head_of_study_program) ?></p>
                                    <p class="text-[10px] uppercase tracking-wider text-ivory/60 mt-1"><?= $EN ? 'Head of Program' : 'Ketua Prodi' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Akreditasi Timeline -->
                    <?php if (!empty($program->accreditation) || !empty($program->accreditation_until)): ?>
                    <div class="bg-white border border-gray-200 p-8 ak-reveal">
                        <p class="editorial-label text-gold-muted mb-5"><?= $EN ? 'Accreditation Status' : 'Status Akreditasi' ?></p>
                        <div class="akred-timeline">
                            <div class="pb-4">
                                <p class="font-serif text-2xl font-bold text-gold-muted mb-1"><?= html_escape($program->accreditation ?? '-') ?></p>
                                <p class="text-xs text-slate"><?= $EN ? 'Current Rating' : 'Peringkat Saat Ini' ?></p>
                            </div>
                            <?php if (!empty($program->accreditation_until)): ?>
                            <div class="pt-4 border-t border-gray-100">
                                <p class="font-mono text-sm text-navy font-semibold"><?= date('d M Y', strtotime($program->accreditation_until)) ?></p>
                                <p class="text-xs text-slate mt-1"><?= $EN ? 'Valid Until' : 'Berlaku Sampai' ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <a href="<?= base_url('download') ?>" class="btn-gold block text-center px-6 py-4 font-semibold uppercase tracking-editorial text-xs ak-reveal">
                        <i class="fas fa-download mr-2"></i><?= $EN ? 'Download Handbook' : 'Unduh Buku Panduan' ?>
                    </a>
                </div>
            </aside>

        </div>
    </div>
</section>

<!-- 🔥 Related Programs -->
<?php if (!empty($related_programs)): ?>
<section class="py-16 md:py-24 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6">
        <div class="ak-reveal">
            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Explore More' : 'Jelajahi Lebih' ?></p>
            <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-10">
                <?= $EN ? 'Related <em class="italic text-gold-muted">Programs</em>' : 'Program <em class="italic text-gold-muted">Terkait</em>' ?>
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($related_programs as $rp): ?>
                <a href="<?= base_url('akademik/detail/' . $rp->slug) ?>" class="related-card block">
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-navy text-ivory text-[10px] uppercase tracking-editorial px-2.5 py-1 font-semibold"><?= html_escape($rp->degree) ?></span>
                        <?php if (!empty($rp->accreditation)): ?>
                        <span class="text-xs font-bold <?= $rp->accreditation === 'Unggul' ? 'text-gold-muted' : 'text-navy' ?>"><?= html_escape($rp->accreditation) ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-serif text-lg font-medium text-navy mb-3 group-hover:text-gold-muted transition"><?= html_escape($rp->name) ?></h3>
                    <p class="text-sm text-slate line-clamp-2 mb-4"><?= html_escape(character_limiter($rp->description ?? '', 100)) ?></p>
                    <span class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial font-semibold text-navy">
                        <?= $EN ? 'View Details' : 'Lihat Detail' ?>
                        <i class="fas fa-arrow-right text-gold-muted"></i>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ CTA DAFTAR ============ -->
<section class="cta-section py-16 md:py-24 text-ivory ak-reveal">
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <p class="editorial-label text-gold mb-5"><?= $EN ? 'Ready to Join?' : 'Siap Bergabung?' ?></p>
            <h2 class="font-serif text-3xl md:text-5xl font-light tracking-[-0.02em] mb-6">
                <?= $EN ? 'Start Your Journey with' : 'Mulai Perjalanan Anda dengan' ?><br>
                <em class="italic text-gold"><?= html_escape($program->name) ?></em>
            </h2>
            <p class="text-ivory/70 mb-10 max-w-xl mx-auto">
                <?= $EN ? 'Join thousands of successful alumni who started their career from this program.' : 'Bergabung dengan ribuan alumni sukses yang memulai karir dari program ini.' ?>
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="<?= base_url('pmb') ?>" class="btn-gold px-10 py-4 font-semibold uppercase tracking-editorial text-xs">
                    <i class="fas fa-user-plus mr-2"></i><?= $EN ? 'Apply Now' : 'Daftar Sekarang' ?>
                </a>
                <a href="<?= base_url('kontak') ?>" class="border border-ivory/30 text-ivory hover:bg-ivory hover:text-navy px-10 py-4 font-semibold uppercase tracking-editorial text-xs transition">
                    <i class="fas fa-envelope mr-2"></i><?= $EN ? 'Contact Us' : 'Hubungi Kami' ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 🔥 Course Detail Modal -->
<div class="course-modal" id="courseModal" onclick="if(event.target===this)closeCourseModal()">
    <div class="course-modal-card">
        <div class="course-modal-head">
            <div>
                <p class="editorial-label text-gold mb-1" id="cmCode">-</p>
                <h3 class="font-serif text-xl font-light" id="cmName">-</h3>
            </div>
            <button class="course-modal-close" onclick="closeCourseModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="course-modal-body">
            <div class="flex flex-wrap gap-2 mb-6">
                <span class="course-badge sks" id="cmSks">- SKS</span>
                <span class="course-badge wajib" id="cmType">-</span>
            </div>
            
            <div class="course-modal-section" id="cmDescSection">
                <h4><?= $EN ? 'Description' : 'Deskripsi' ?></h4>
                <p class="text-gray-700 leading-relaxed text-sm" id="cmDesc">-</p>
            </div>
            
            <div class="course-modal-section" id="cmPrereqSection" style="display:none">
                <h4><?= $EN ? 'Prerequisites' : 'Prasyarat' ?></h4>
                <p class="text-gray-700 text-sm" id="cmPrereq">-</p>
            </div>
            
            <div class="course-modal-section" id="cmOutcomesSection" style="display:none">
                <h4><?= $EN ? 'Learning Outcomes' : 'Capaian Pembelajaran' ?></h4>
                <p class="text-gray-700 text-sm" id="cmOutcomes">-</p>
            </div>
        </div>
    </div>
</div>

<!-- 🔥 Share FAB -->
<button class="share-fab" id="shareFab" title="<?= $EN ? 'Share Program' : 'Bagikan Program' ?>">
    <i class="fas fa-share-alt"></i>
</button>

<div class="share-panel" id="sharePanel">
    <p class="editorial-label text-slate mb-3"><?= $EN ? 'Share this program' : 'Bagikan program ini' ?></p>
    <div class="share-option" onclick="shareWhatsApp()">
        <i class="fab fa-whatsapp"></i> WhatsApp
    </div>
    <div class="share-option" onclick="shareEmail()">
        <i class="fas fa-envelope"></i> Email
    </div>
    <div class="share-option" onclick="shareTwitter()">
        <i class="fab fa-twitter"></i> Twitter
    </div>
    <div class="share-option" onclick="shareFacebook()">
        <i class="fab fa-facebook"></i> Facebook
    </div>
    <div class="share-option" onclick="copyLink()">
        <i class="fas fa-link"></i> <?= $EN ? 'Copy Link' : 'Salin Link' ?>
    </div>
</div>

<!-- 🔥 Bookmark FAB -->
<button class="bookmark-float" id="bookmarkBtn" title="<?= $EN ? 'Bookmark this program' : 'Tandai program ini' ?>">
    <i class="fas fa-bookmark"></i>
</button>

<!-- 🔥 Toast -->
<div id="toast" class="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<script>
(function(){
    var programSlug = '<?= html_escape($program->slug) ?>';
    var programName = '<?= html_escape($program->name) ?>';
    var programUrl = window.location.href;
    
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
    document.querySelectorAll('.stat-counter').forEach(function(el){ io.observe(el); });

    // ===== REVEAL ON SCROLL =====
    var revealObs = new IntersectionObserver(function(es){
        es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); revealObs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.ak-reveal').forEach(function(el){ revealObs.observe(el); });

    // ===== TAB KURIKULUM =====
    var tabs = document.getElementById('semTabs');
    if (tabs) {
        tabs.addEventListener('click', function(e){
            var tab = e.target.closest('.sem-tab');
            if (!tab) return;
            var sem = tab.dataset.sem;
            tabs.querySelectorAll('.sem-tab').forEach(function(t){ t.classList.remove('active'); });
            tab.classList.add('active');
            document.querySelectorAll('.sem-content').forEach(function(c){ c.classList.remove('active'); });
            var target = document.querySelector('.sem-content[data-sem="' + sem + '"]');
            if (target) target.classList.add('active');
        });
    }

    // ===== 🔥 COURSE DETAIL MODAL =====
    window.openCourseModal = function(card){
        var modal = document.getElementById('courseModal');
        document.getElementById('cmCode').textContent = card.dataset.code || '-';
        document.getElementById('cmName').textContent = card.dataset.name || '-';
        document.getElementById('cmSks').textContent = (card.dataset.sks || '0') + ' SKS';
        document.getElementById('cmType').textContent = card.dataset.type || '-';
        
        var desc = card.dataset.desc || '';
        var descSection = document.getElementById('cmDescSection');
        if (desc) {
            document.getElementById('cmDesc').textContent = desc;
            descSection.style.display = 'block';
        } else {
            descSection.style.display = 'none';
        }
        
        var prereq = card.dataset.prereq || '';
        var prereqSection = document.getElementById('cmPrereqSection');
        if (prereq) {
            document.getElementById('cmPrereq').textContent = prereq;
            prereqSection.style.display = 'block';
        } else {
            prereqSection.style.display = 'none';
        }
        
        var outcomes = card.dataset.outcomes || '';
        var outcomesSection = document.getElementById('cmOutcomesSection');
        if (outcomes) {
            document.getElementById('cmOutcomes').textContent = outcomes;
            outcomesSection.style.display = 'block';
        } else {
            outcomesSection.style.display = 'none';
        }
        
        modal.classList.add('open');
    };
    
    window.closeCourseModal = function(){
        document.getElementById('courseModal').classList.remove('open');
    };

    // ===== 🔥 FAQ ACCORDION =====
    window.toggleFaq = function(el){
        var item = el.closest('.faq-item');
        var wasOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(function(f){ f.classList.remove('open'); });
        if (!wasOpen) item.classList.add('open');
    };

    // ===== 🔥 SHARE =====
    var shareFab = document.getElementById('shareFab');
    var sharePanel = document.getElementById('sharePanel');
    
    shareFab.addEventListener('click', function(){
        sharePanel.classList.toggle('open');
    });
    
    document.addEventListener('click', function(e){
        if (!shareFab.contains(e.target) && !sharePanel.contains(e.target)) {
            sharePanel.classList.remove('open');
        }
    });
    
    function closeSharePanel(){ sharePanel.classList.remove('open'); }
    
    window.shareWhatsApp = function(){
        window.open('https://wa.me/?text=' + encodeURIComponent(programName + ' - ' + programUrl), '_blank');
        closeSharePanel();
    };
    
    window.shareEmail = function(){
        window.location.href = 'mailto:?subject=' + encodeURIComponent(programName) + '&body=' + encodeURIComponent(programUrl);
        closeSharePanel();
    };
    
    window.shareTwitter = function(){
        window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(programName) + '&url=' + encodeURIComponent(programUrl), '_blank');
        closeSharePanel();
    };
    
    window.shareFacebook = function(){
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(programUrl), '_blank');
        closeSharePanel();
    };
    
    window.copyLink = function(){
        navigator.clipboard.writeText(programUrl).then(function(){
            showToast('<?= $EN ? "Link copied!" : "Link disalin!" ?>');
        });
        closeSharePanel();
    };

    // ===== 🔥 BOOKMARK =====
    var bookmarks = JSON.parse(localStorage.getItem('program_bookmarks') || '[]');
    var bookmarkBtn = document.getElementById('bookmarkBtn');
    
    function updateBookmarkUI(){
        bookmarkBtn.classList.toggle('active', bookmarks.indexOf(programSlug) > -1);
    }
    
    bookmarkBtn.addEventListener('click', function(){
        var idx = bookmarks.indexOf(programSlug);
        if (idx > -1) {
            bookmarks.splice(idx, 1);
            showToast('<?= $EN ? "Bookmark removed" : "Bookmark dihapus" ?>');
        } else {
            bookmarks.push(programSlug);
            showToast('<?= $EN ? "Program bookmarked!" : "Program ditandai!" ?>');
        }
        localStorage.setItem('program_bookmarks', JSON.stringify(bookmarks));
        updateBookmarkUI();
    });
    
    updateBookmarkUI();

    // ===== 🔥 TOAST =====
    function showToast(msg){
        var toast = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        toast.classList.add('show');
        setTimeout(function(){ toast.classList.remove('show'); }, 2500);
    }
    
    window.showToast = showToast;

    // ===== ESC to close =====
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            closeCourseModal();
            sharePanel.classList.remove('open');
        }
    });
})();
</script>