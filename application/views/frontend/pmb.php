<?php
$EN = (get_site_lang() == 'en');
$old = $this->session->flashdata('pmb_old') ?: [];

// 🔥 Deadline countdown (Wave 1 closes)
$deadline_ts = strtotime('2026-04-15 23:59:59');
$now_ts = time();
$days_left = max(0, ceil(($deadline_ts - $now_ts) / 86400));
$is_open = $now_ts < $deadline_ts;

// 🔥 Stats (dummy/placeholder — bisa diganti dari controller)
$stats = [
    'applicants' => $total_applicants ?? 1247,
    'programs'   => count($programs ?? []),
    'scholarship' => 15, // persen max beasiswa
];
?>

<style>
/* ===== STEPPER ===== */
.stepper { display:flex; align-items:flex-start; position:relative; }
.stepper::before { content:''; position:absolute; top:28px; left:10%; right:10%; height:2px; background:#e5e7eb; z-index:0; }
.stepper .line-fill { position:absolute; top:28px; left:10%; height:2px; background:linear-gradient(90deg,#C9A227,#B8941F); z-index:1; width:0; transition:width 1.5s cubic-bezier(.22,1,.36,1); }
.step { flex:1; text-align:center; position:relative; z-index:2; cursor:pointer; }
.step .step-num {
    width:56px; height:56px; margin:0 auto .75rem; border-radius:50%;
    background:#fff; border:2px solid #e5e7eb; color:#64748b;
    display:flex; align-items:center; justify-content:center;
    font-family:'Fraunces',serif; font-weight:600; font-size:1.2rem;
    transition:all .4s;
}
.step.active .step-num { background:#C9A227; border-color:#C9A227; color:#0B2239; transform:scale(1.1); box-shadow:0 0 0 6px rgba(201,162,39,.15); }
.step .step-title { font-family:'Fraunces',serif; font-weight:500; color:#0B2239; font-size:1rem; }
.step .step-desc { font-size:.75rem; color:#64748b; margin-top:.25rem; line-height:1.4; }

/* ===== TIMELINE ===== */
.tl-item { position:relative; padding-left:2.5rem; padding-bottom:2rem; border-left:2px solid #e5e7eb; }
.tl-item:last-child { border-left-color:transparent; padding-bottom:0; }
.tl-item::before { content:''; position:absolute; left:-8px; top:2px; width:14px; height:14px; border-radius:50%; background:#fff; border:3px solid #C9A227; }
.tl-item.past::before { background:#C9A227; }
.tl-date { font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:.15em; color:#B8941F; font-weight:700; text-transform:uppercase; }

/* ===== FAQ ===== */
.faq-item { background:#fff; border:1px solid #e5e7eb; margin-bottom:.75rem; }
.faq-q { width:100%; text-align:left; padding:1.25rem 1.5rem; background:none; border:none; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:1rem; font-family:'Fraunces',serif; font-size:1.05rem; font-weight:500; color:#0B2239; }
.faq-q i { color:#C9A227; transition:transform .3s; flex-shrink:0; }
.faq-item.open .faq-q i { transform:rotate(45deg); }
.faq-a { max-height:0; overflow:hidden; transition:max-height .4s ease; }
.faq-a-inner { padding:0 1.5rem 1.25rem; color:#64748b; font-size:.9rem; line-height:1.7; }

/* ===== SCHOLARSHIP ===== */
.sch-card { background:linear-gradient(135deg,#0B2239,#13334F); color:#F7F5F0; padding:2rem; position:relative; overflow:hidden; transition:all .4s; }
.sch-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(11,34,57,.3); }
.sch-card::before { content:''; position:absolute; top:-30px; right:-30px; width:100px; height:100px; background:radial-gradient(circle,rgba(201,162,39,.3),transparent 70%); border-radius:50%; }

.pmb-reveal { opacity:0; transform:translateY(24px); transition:all .9s cubic-bezier(.22,1,.36,1); }
.pmb-reveal.in { opacity:1; transform:none; }
.pmb-input { width:100%; padding:.9rem 1.1rem; background:#fff; border:2px solid #e5e7eb; color:#0B2239; transition:border-color .3s; }
.pmb-input:focus { outline:none; border-color:#C9A227; }

/* ===== 🔥 COUNTDOWN ===== */
.pmb-countdown { display:flex; gap:12px; justify-content:center; margin-top:2rem; }
.pmb-cd-box { background:rgba(247,245,240,.08); backdrop-filter:blur(8px); border:1px solid rgba(247,245,240,.15); padding:14px 18px; min-width:80px; text-align:center; }
.pmb-cd-num { font-family:'Fraunces',serif; font-size:2.5rem; font-weight:300; color:#C9A227; line-height:1; }
.pmb-cd-label { font-size:9px; letter-spacing:.2em; text-transform:uppercase; color:rgba(247,245,240,.7); margin-top:4px; }

/* ===== 🔥 HERO STATS ===== */
.pmb-hero-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:3rem; max-width:600px; }
.pmb-stat { border-left:2px solid #C9A227; padding-left:16px; }
.pmb-stat-num { font-family:'Fraunces',serif; font-size:2.5rem; font-weight:300; color:#C9A227; line-height:1; }
.pmb-stat-label { font-size:10px; text-transform:uppercase; letter-spacing:.2em; color:rgba(247,245,240,.6); margin-top:6px; }

/* ===== 🔥 TESTIMONIAL ===== */
.testi-card { background:#fff; border:1px solid #e5e7eb; padding:2rem; position:relative; transition:all .3s; }
.testi-card:hover { transform:translateY(-4px); box-shadow:0 16px 32px rgba(11,34,57,.1); }
.testi-card::before { content:'"'; position:absolute; top:-10px; left:16px; font-family:'Fraunces',serif; font-size:5rem; color:#C9A227; opacity:.2; line-height:1; }
.testi-avatar { width:56px; height:56px; border-radius:50%; background:#0B2239; color:#C9A227; display:flex; align-items:center; justify-content:center; font-family:'Fraunces',serif; font-weight:700; font-size:1.3rem; margin-bottom:1rem; }

/* ===== 🔥 FORM VALIDATION ===== */
.pmb-input.valid { border-color:#10b981; }
.pmb-input.invalid { border-color:#ef4444; }
.field-hint { font-size:11px; color:#64748b; margin-top:4px; min-height:16px; }
.field-hint.error { color:#ef4444; }
.field-hint.success { color:#10b981; }

/* ===== 🔥 PROGRAM PREVIEW ===== */
.prog-preview { background:#FAF8F3; border:1px solid #e5e7eb; padding:16px; margin-top:8px; display:none; }
.prog-preview.visible { display:block; animation:fadeIn .3s ease; }
@keyframes fadeIn { from {opacity:0;transform:translateY(-4px)} to {opacity:1;transform:none} }

/* ===== 🔥 DRAFT BADGE ===== */
.draft-badge {
    display:none; align-items:center; gap:6px; padding:4px 10px;
    background:#fef3c7; color:#92400e; font-size:10px; font-weight:700;
    text-transform:uppercase; letter-spacing:.1em;
}
.draft-badge.visible { display:inline-flex; }
.draft-badge button { background:none; border:none; color:#92400e; cursor:pointer; font-size:11px; }

/* ===== 🔥 TOAST ===== */
.pmb-toast {
    position:fixed; bottom:24px; right:24px; z-index:100;
    background:#0B2239; color:#F7F5F0; padding:12px 20px;
    font-size:13px; box-shadow:0 8px 24px rgba(0,0,0,.2);
    transform:translateX(120%); transition:transform .3s;
}
.pmb-toast.show { transform:translateX(0); }
.pmb-toast i { color:#C9A227; margin-right:8px; }

/* ===== 🔥 CLOSED STATE ===== */
.closed-banner { background:#ef4444; color:#fff; padding:12px 20px; text-align:center; font-size:13px; font-weight:600; }

@media (max-width: 640px) {
    .pmb-hero-stats { grid-template-columns:repeat(3,1fr); gap:12px; }
    .pmb-stat-num { font-size:1.75rem; }
    .pmb-cd-box { min-width:64px; padding:10px 12px; }
    .pmb-cd-num { font-size:1.75rem; }
}
</style>

<!-- ============ HERO ============ -->
<section class="bg-navy text-ivory relative overflow-hidden">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="absolute -bottom-16 -right-8 font-serif text-[16rem] leading-none text-ivory/5 select-none pointer-events-none hidden lg:block">P</div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
        <p class="editorial-label text-gold mb-5"><?= $EN ? 'Admission 2026/2027' : 'Penerimaan 2026/2027' ?></p>
        <h1 class="font-serif font-light text-4xl md:text-6xl tracking-[-0.02em] leading-[1.05] max-w-3xl text-balance">
            <?= $EN ? 'New Student <em class="italic text-gold">Admission</em>' : 'Penerimaan <em class="italic text-gold">Mahasiswa Baru</em>' ?>
        </h1>
        <p class="text-ivory/70 mt-6 text-lg max-w-2xl"><?= $EN ? 'Your journey to becoming part of our academic family starts here.' : 'Perjalanan Anda menjadi bagian dari keluarga akademik kami dimulai di sini.' ?></p>
        
        <div class="mt-8 flex gap-4 flex-wrap">
            <a href="#daftar" class="btn-gold px-8 py-4 font-semibold uppercase tracking-editorial text-xs"><i class="fas fa-user-plus mr-2"></i><?= $EN ? 'Apply Now' : 'Daftar Sekarang' ?></a>
            <a href="#alur" class="border border-ivory/30 text-ivory hover:bg-ivory hover:text-navy px-8 py-4 font-semibold uppercase tracking-editorial text-xs transition"><?= $EN ? 'See the Flow' : 'Lihat Alur' ?></a>
        </div>

        <!-- 🔥 COUNTDOWN -->
        <?php if ($is_open): ?>
        <div class="pmb-countdown pmb-reveal" id="countdown">
            <div class="pmb-cd-box">
                <div class="pmb-cd-num" id="cdDays"><?= $days_left ?></div>
                <div class="pmb-cd-label"><?= $EN ? 'Days' : 'Hari' ?></div>
            </div>
            <div class="pmb-cd-box">
                <div class="pmb-cd-num" id="cdHours">00</div>
                <div class="pmb-cd-label"><?= $EN ? 'Hours' : 'Jam' ?></div>
            </div>
            <div class="pmb-cd-box">
                <div class="pmb-cd-num" id="cdMins">00</div>
                <div class="pmb-cd-label"><?= $EN ? 'Minutes' : 'Menit' ?></div>
            </div>
            <div class="pmb-cd-box">
                <div class="pmb-cd-num" id="cdSecs">00</div>
                <div class="pmb-cd-label"><?= $EN ? 'Seconds' : 'Detik' ?></div>
            </div>
        </div>
        <p class="text-center text-xs text-ivory/50 mt-3 uppercase tracking-wider">
            <i class="fas fa-clock mr-1"></i><?= $EN ? 'Wave 1 closes' : 'Gelombang 1 ditutup' ?> · 15 April 2026
        </p>
        <?php endif; ?>

        <!-- 🔥 HERO STATS -->
        <div class="pmb-hero-stats pmb-reveal">
            <div class="pmb-stat">
                <div class="pmb-stat-num pmb-count" data-count="<?= $stats['applicants'] ?>">0</div>
                <div class="pmb-stat-label"><?= $EN ? 'Applicants' : 'Pendaftar' ?></div>
            </div>
            <div class="pmb-stat">
                <div class="pmb-stat-num pmb-count" data-count="<?= $stats['programs'] ?>">0</div>
                <div class="pmb-stat-label"><?= $EN ? 'Programs' : 'Prodi' ?></div>
            </div>
            <div class="pmb-stat">
                <div class="pmb-stat-num">100%</div>
                <div class="pmb-stat-label"><?= $EN ? 'Scholarship Max' : 'Max Beasiswa' ?></div>
            </div>
        </div>
    </div>
</section>

<!-- ============ STEPPER ============ -->
<section class="py-16 md:py-24 bg-ivory" id="alur">
    <div class="container mx-auto px-6 max-w-5xl">
        <div class="text-center mb-14 pmb-reveal">
            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'How it works' : 'Cara Kerja' ?></p>
            <h2 class="font-serif text-3xl md:text-5xl font-light text-navy tracking-[-0.02em]"><?= $EN ? 'Admission <em class="italic text-gold-muted">Flow</em>' : 'Alur <em class="italic text-gold-muted">Pendaftaran</em>' ?></h2>
        </div>

        <div class="stepper pmb-reveal" id="stepper">
            <div class="line-fill" id="lineFill"></div>
            <?php
            $steps = [
                ['1', $EN?'Register':'Daftar', $EN?'Fill the online form':'Isi formulir online'],
                ['2', $EN?'Verify':'Verifikasi', $EN?'Admin reviews documents':'Admin meninjau berkas'],
                ['3', $EN?'Test':'Seleksi', $EN?'Selection / interview':'Tes / wawancara'],
                ['4', $EN?'Announce':'Pengumuman', $EN?'Results published':'Hasil diumumkan'],
                ['5', $EN?'Re-register':'Daftar Ulang', $EN?'Confirm your seat':'Konfirmasi kursi Anda'],
            ];
            foreach ($steps as $i => $st): ?>
            <div class="step <?= $i == 0 ? 'active' : '' ?>" data-step="<?= $i ?>">
                <div class="step-num"><?= $st[0] ?></div>
                <div class="step-title"><?= $st[1] ?></div>
                <div class="step-desc"><?= $st[2] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ TIMELINE + SCHOLARSHIP ============ -->
<section class="py-16 md:py-24 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12">
            <div class="pmb-reveal">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Important Dates' : 'Tanggal Penting' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-10"><?= $EN ? 'Admission <em class="italic text-gold-muted">Timeline</em>' : 'Linimasa <em class="italic text-gold-muted">PMB</em>' ?></h2>
                <?php
                $timeline = [
                    ['01 Feb 2026', $EN?'Early Bird Opens':'Gelombang Awal Dibuka', true],
                    ['15 Apr 2026', $EN?'Wave 1 Closes':'Gelombang 1 Ditutup', $now_ts < strtotime('2026-04-15')],
                    ['01 Jun 2026', $EN?'Wave 2 Opens':'Gelombang 2 Dibuka', false],
                    ['20 Jul 2026', $EN?'Final Selection':'Seleksi Akhir', false],
                    ['01 Sep 2026', $EN?'Academic Year Starts':'Perkuliahan Dimulai', false],
                ];
                ?>
                <div>
                    <?php foreach ($timeline as $t): ?>
                    <div class="tl-item <?= $t[2] ? 'past' : '' ?>">
                        <p class="tl-date"><?= $t[0] ?></p>
                        <p class="font-serif text-lg font-medium text-navy mt-1"><?= $t[1] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pmb-reveal" style="transition-delay:.15s">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Financial Support' : 'Bantuan Biaya' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-10"><?= $EN ? 'Scholarships' : 'Beasiswa' ?></h2>
                <div class="space-y-5">
                    <div class="sch-card">
                        <div class="flex items-center gap-3 mb-3"><i class="fas fa-medal text-gold text-2xl"></i><h3 class="font-serif text-xl font-medium"><?= $EN ? 'Achievement Scholarship' : 'Beasiswa Prestasi' ?></h3></div>
                        <p class="text-ivory/70 text-sm leading-relaxed"><?= $EN ? 'Up to 100% tuition waiver for national/international achievement holders.' : 'Pembebasan UKT hingga 100% bagi peraih prestasi nasional/internasional.' ?></p>
                    </div>
                    <div class="sch-card">
                        <div class="flex items-center gap-3 mb-3"><i class="fas fa-hand-holding-heart text-gold text-2xl"></i><h3 class="font-serif text-xl font-medium"><?= $EN ? 'Need-Based Grant' : 'Beasiswa KIP / Bantuan' ?></h3></div>
                        <p class="text-ivory/70 text-sm leading-relaxed"><?= $EN ? 'Support for students with financial needs through KIP-Kuliah and internal grants.' : 'Dukungan bagi mahasiswa melalui KIP-Kuliah dan bantuan internal.' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ PROGRAMS + FAQ ============ -->
<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12">
            <div class="pmb-reveal">
                <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Choose Your Path' : 'Pilih Jalur Anda' ?></p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8"><?= $EN ? 'Available <em class="italic text-gold-muted">Programs</em>' : 'Program <em class="italic text-gold-muted">Tersedia</em>' ?></h2>
                <div class="space-y-4">
                    <?php foreach ($programs as $p): ?>
                    <a href="<?= base_url('akademik/detail/' . $p->slug) ?>" class="flex items-center gap-4 bg-white border border-gray-200 p-5 hover:border-gold hover:translate-x-2 transition">
                        <div class="w-12 h-12 bg-navy text-gold rounded-full flex items-center justify-center font-serif font-bold text-lg flex-shrink-0"><?= strtoupper(substr($p->name,0,1)) ?></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-serif text-lg font-medium text-navy"><?= html_escape($p->name) ?></p>
                            <p class="text-xs text-slate mt-0.5"><?= html_escape($p->degree) ?> &bull; <?= $EN ? 'Accreditation' : 'Akreditasi' ?> <?= html_escape($p->accreditation ?? '-') ?></p>
                        </div>
                        <i class="fas fa-arrow-right text-gold-muted"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pmb-reveal" style="transition-delay:.15s">
                <p class="editorial-label text-gold-muted mb-3">FAQ</p>
                <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em] mb-8"><?= $EN ? 'Common <em class="italic text-gold-muted">Questions</em>' : 'Pertanyaan <em class="italic text-gold-muted">Umum</em>' ?></h2>
                <div id="faqList">
                    <?php
                    $faqs = [
                        [$EN?'What are the requirements?':'Apa saja persyaratan?', $EN?'High school diploma, report cards, and a scanned photo. Specific tracks may require additional documents.':'Ijazah/SKL, rapor, dan pas foto. Jalur tertentu mungkin butuh berkas tambahan.'],
                        [$EN?'Is there an entrance test?':'Apakah ada tes masuk?', $EN?'Selection is based on report review; some programs may hold an interview.':'Seleksi berbasis nilai rapor; beberapa prodi mungkin mengadakan wawancara.'],
                        [$EN?'Can I work while studying?':'Bisakah kuliah sambil bekerja?', $EN?'Yes, we offer evening/weekend classes for several programs.':'Ya, beberapa prodi menyediakan kelas malam/akhir pekan.'],
                        [$EN?'How do I apply for scholarship?':'Bagaimana cara mengajukan beasiswa?', $EN?'Select the scholarship option during registration; our team will verify your documents.':'Pilih opsi beasiswa saat mendaftar; tim kami akan memverifikasi berkas Anda.'],
                    ];
                    foreach ($faqs as $i => $f): ?>
                    <div class="faq-item">
                        <button type="button" class="faq-q"><?= $f[0] ?><i class="fas fa-plus"></i></button>
                        <div class="faq-a"><div class="faq-a-inner"><?= $f[1] ?></div></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ 🔥 TESTIMONIALS ============ -->
<section class="py-16 md:py-24 bg-white border-t border-gray-200">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 pmb-reveal">
            <p class="editorial-label text-gold-muted mb-3"><?= $EN ? 'Student Voices' : 'Suara Mahasiswa' ?></p>
            <h2 class="font-serif text-3xl md:text-4xl font-light text-navy tracking-[-0.02em]">
                <?= $EN ? 'Why they <em class="italic text-gold-muted">chose us</em>' : 'Mengapa mereka <em class="italic text-gold-muted">memilih kami</em>' ?>
            </h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <?php
            $testis = [
                ['Andini Putri', 'Teknik Informatika \'22', $EN ? 'The admission process was smooth and the scholarship support changed my life. Now I work at a top tech company.' : 'Proses admisi lancar dan dukungan beasiswa mengubah hidup saya. Kini saya bekerja di perusahaan teknologi ternama.'],
                ['Budi Santoso', 'Sistem Informasi \'21', $EN ? 'The faculty mentors guided me from day one. I won national competitions and got internship offers before graduation.' : 'Dosen pembimbing membimbing sejak hari pertama. Saya juara kompetisi nasional dan dapat tawaran magang sebelum lulus.'],
                ['Citra Dewi', 'Manajemen \'23', $EN ? 'The campus environment is collaborative. I built a startup with my classmates that now has real customers.' : 'Lingkungan kampus kolaboratif. Saya membangun startup dengan teman sekelas yang kini punya pelanggan nyata.'],
            ];
            foreach ($testis as $t): ?>
            <div class="testi-card pmb-reveal">
                <div class="testi-avatar"><?= strtoupper(substr($t[0], 0, 1)) ?></div>
                <p class="text-sm text-slate leading-relaxed mb-4 italic">"<?= $t[2] ?>"</p>
                <p class="font-serif font-medium text-navy"><?= html_escape($t[0]) ?></p>
                <p class="text-[10px] uppercase tracking-wider text-slate mt-1"><?= html_escape($t[1]) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ FORM DAFTAR ============ -->
<section class="py-16 md:py-24 bg-navy text-ivory relative overflow-hidden" id="daftar">
    <div class="absolute inset-0 hero-pattern"></div>
    <div class="container mx-auto px-6 max-w-3xl relative z-10">
        <div class="text-center mb-10 pmb-reveal">
            <p class="editorial-label text-gold mb-3"><?= $EN ? 'Start Now' : 'Mulai Sekarang' ?></p>
            <h2 class="font-serif text-3xl md:text-5xl font-light tracking-[-0.02em]"><?= $EN ? 'Register <em class="italic text-gold">Online</em>' : 'Daftar <em class="italic text-gold">Online</em>' ?></h2>
        </div>

        <?php if ($this->session->flashdata('pmb_success')): ?>
        <div class="bg-green-500/10 border-l-4 border-green-400 text-green-300 px-5 py-4 mb-6 text-sm pmb-reveal">
            <i class="fas fa-check-circle mr-2"></i><?= $EN ? 'Application received! Our admission team will contact you.' : 'Pendaftaran diterima! Tim admisi akan menghubungi Anda.' ?>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('pmb_errors')): ?>
        <div class="bg-red-500/10 border-l-4 border-red-400 text-red-300 px-5 py-4 mb-6 text-sm pmb-reveal">
            <?= $this->session->flashdata('pmb_errors') ?>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('pmb/submit') ?>" method="POST" id="pmbForm" class="bg-white text-navy p-8 md:p-10 pmb-reveal space-y-5" novalidate>
            <div class="flex items-center justify-between mb-2">
                <p class="editorial-label text-gold-muted"><?= $EN ? 'Personal Data' : 'Data Pribadi' ?></p>
                <span class="draft-badge" id="draftBadge">
                    <i class="fas fa-save"></i><span><?= $EN ? 'Draft saved' : 'Draft tersimpan' ?></span>
                    <button type="button" onclick="clearDraft()"><i class="fas fa-times"></i></button>
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Full Name' : 'Nama Lengkap' ?> *</label>
                    <input type="text" name="full_name" id="f_name" required value="<?= html_escape($old['full_name'] ?? '') ?>" class="pmb-input" placeholder="<?= $EN ? 'Your full name' : 'Nama lengkap Anda' ?>">
                    <p class="field-hint" id="hint_name"></p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">NISN</label>
                    <input type="text" name="nisn" id="f_nisn" value="<?= html_escape($old['nisn'] ?? '') ?>" class="pmb-input font-mono" placeholder="1234567890" maxlength="10">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Email *</label>
                    <input type="email" name="email" id="f_email" required value="<?= html_escape($old['email'] ?? '') ?>" class="pmb-input" placeholder="nama@email.com">
                    <p class="field-hint" id="hint_email"></p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Phone / WhatsApp' : 'No. HP / WhatsApp' ?> *</label>
                    <input type="text" name="phone" id="f_phone" required value="<?= html_escape($old['phone'] ?? '') ?>" class="pmb-input" placeholder="08xx-xxxx-xxxx">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Birth Date' : 'Tanggal Lahir' ?></label>
                    <input type="date" name="birth_date" value="<?= html_escape($old['birth_date'] ?? '') ?>" class="pmb-input">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Origin School' : 'Asal Sekolah' ?></label>
                    <input type="text" name="origin_school" value="<?= html_escape($old['origin_school'] ?? '') ?>" class="pmb-input" placeholder="SMA Negeri 1 ...">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-200">
                <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Study Program & Track' : 'Prodi & Jalur' ?></p>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Study Program' : 'Program Studi' ?> *</label>
                        <select name="study_program_id" id="f_prodi" required class="pmb-input">
                            <option value=""><?= $EN ? '-- Choose --' : '-- Pilih --' ?></option>
                            <?php foreach ($programs as $p): ?>
                            <option value="<?= $p->id ?>" data-degree="<?= html_escape($p->degree) ?>" data-accreditation="<?= html_escape($p->accreditation ?? '-') ?>" data-desc="<?= html_escape(character_limiter($p->description ?? '', 80)) ?>" <?= ($old['study_program_id'] ?? '') == $p->id ? 'selected' : '' ?>><?= html_escape($p->name) ?> (<?= $p->degree ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <!-- 🔥 Program Preview -->
                        <div class="prog-preview" id="progPreview">
                            <p class="text-xs text-gold-muted font-bold uppercase tracking-wider mb-1"><i class="fas fa-info-circle mr-1"></i><?= $EN ? 'Program info' : 'Info prodi' ?></p>
                            <p class="text-sm text-navy font-medium" id="pvDegree"></p>
                            <p class="text-xs text-slate mt-1" id="pvDesc"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Admission Track' : 'Jalur Pendaftaran' ?> *</label>
                        <select name="track" required class="pmb-input">
                            <option value=""><?= $EN ? '-- Choose --' : '-- Pilih --' ?></option>
                            <option value="reguler" <?= ($old['track'] ?? '') == 'reguler' ? 'selected' : '' ?>><?= $EN ? 'Regular (Report Review)' : 'Reguler (Nilai Rapor)' ?></option>
                            <option value="prestasi" <?= ($old['track'] ?? '') == 'prestasi' ? 'selected' : '' ?>><?= $EN ? 'Achievement Track' : 'Jalur Prestasi' ?></option>
                            <option value="beasiswa" <?= ($old['track'] ?? '') == 'beasiswa' ? 'selected' : '' ?>><?= $EN ? 'Scholarship Track' : 'Jalur Beasiswa' ?></option>
                            <option value="afirmasi" <?= ($old['track'] ?? '') == 'afirmasi' ? 'selected' : '' ?>><?= $EN ? 'Affirmation (KIP-K)' : 'Afirmasi (KIP-K)' ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Message (optional)' : 'Pesan (opsional)' ?></label>
                <textarea name="message" id="f_message" rows="4" maxlength="500" class="pmb-input" placeholder="<?= $EN ? 'Questions or additional info...' : 'Pertanyaan atau info tambahan...' ?>"><?= html_escape($old['message'] ?? '') ?></textarea>
                <p class="text-[10px] text-slate mt-1 text-right"><span id="msgCount">0</span> / 500</p>
            </div>

            <div class="flex items-start gap-2 text-xs text-slate">
                <input type="checkbox" id="agree" name="agree" required class="mt-1">
                <label for="agree"><?= $EN ? 'I agree to the terms and conditions and confirm that the data provided is accurate.' : 'Saya menyetujui syarat & ketentuan dan menyatakan data yang diisi benar.' ?></label>
            </div>

            <button type="submit" id="submitBtn" class="btn-gold w-full py-4 font-semibold uppercase tracking-editorial text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-paper-plane mr-2"></i><?= $EN ? 'Submit Application' : 'Kirim Pendaftaran' ?>
            </button>
        </form>
    </div>
</section>

<div class="pmb-toast" id="pmbToast"><i class="fas fa-check-circle"></i><span id="pmbToastMsg"></span></div>

<script>
(function(){
    // ===== REVEAL =====
    var obs = new IntersectionObserver(function(es){
        es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } });
    }, { threshold: .1 });
    document.querySelectorAll('.pmb-reveal').forEach(function(el){ obs.observe(el); });

    // ===== 🔥 COUNTERS =====
    var cio = new IntersectionObserver(function(es){
        es.forEach(function(e){
            if(!e.isIntersecting) return;
            var el = e.target;
            if(el.dataset.done) return; el.dataset.done = '1';
            var t = +el.dataset.count, st = performance.now();
            (function f(n){
                var p = Math.min(1, (n - st) / 1400), ease = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(t * ease).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(f);
            })(st);
            cio.unobserve(el);
        });
    }, { threshold: .3 });
    document.querySelectorAll('.pmb-count').forEach(function(el){ cio.observe(el); });

    // ===== 🔥 COUNTDOWN =====
    var deadline = new Date('<?= date('c', $deadline_ts) ?>').getTime();
    function updCountdown(){
        var now = Date.now();
        var diff = deadline - now;
        if (diff <= 0) {
            ['cdDays','cdHours','cdMins','cdSecs'].forEach(function(id){
                var el = document.getElementById(id); if (el) el.textContent = '00';
            });
            return;
        }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        document.getElementById('cdDays').textContent = d;
        document.getElementById('cdHours').textContent = String(h).padStart(2,'0');
        document.getElementById('cdMins').textContent = String(m).padStart(2,'0');
        document.getElementById('cdSecs').textContent = String(s).padStart(2,'0');
    }
    if (document.getElementById('cdDays')) { updCountdown(); setInterval(updCountdown, 1000); }

    // ===== STEPPER =====
    var stepper = document.getElementById('stepper');
    var fill = document.getElementById('lineFill');
    if (stepper && fill) {
        var io = new IntersectionObserver(function(es){
            es.forEach(function(e){
                if(!e.isIntersecting) return;
                io.unobserve(e.target);
                var steps = stepper.querySelectorAll('.step');
                var i = 0;
                var timer = setInterval(function(){
                    steps.forEach(function(s, idx){ s.classList.toggle('active', idx <= i); });
                    fill.style.width = (i / (steps.length - 1)) * 80 + '%';
                    i++;
                    if (i > steps.length - 1) clearInterval(timer);
                }, 600);
            });
        }, { threshold: .3 });
        io.observe(stepper);
    }

    // ===== FAQ =====
    document.querySelectorAll('.faq-q').forEach(function(btn){
        btn.addEventListener('click', function(){
            var item = btn.closest('.faq-item');
            var ans = item.querySelector('.faq-a');
            var open = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(function(o){
                o.classList.remove('open');
                o.querySelector('.faq-a').style.maxHeight = null;
            });
            if (!open) {
                item.classList.add('open');
                ans.style.maxHeight = ans.scrollHeight + 'px';
            }
        });
    });

    // ===== 🔥 TOAST =====
    function showToast(msg){
        var t = document.getElementById('pmbToast');
        document.getElementById('pmbToastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(function(){ t.classList.remove('show'); }, 2500);
    }
    window.showToast = showToast;

    // ===== 🔥 PROGRAM PREVIEW =====
    var prodiSelect = document.getElementById('f_prodi');
    var progPreview = document.getElementById('progPreview');
    if (prodiSelect && progPreview) {
        function updPreview(){
            var opt = prodiSelect.options[prodiSelect.selectedIndex];
            if (opt && opt.value) {
                document.getElementById('pvDegree').textContent = opt.dataset.degree + ' · Akreditasi ' + opt.dataset.accreditation;
                document.getElementById('pvDesc').textContent = opt.dataset.desc || '';
                progPreview.classList.add('visible');
            } else {
                progPreview.classList.remove('visible');
            }
        }
        prodiSelect.addEventListener('change', updPreview);
        updPreview();
    }

    // ===== 🔥 VALIDATION =====
    var nameField = document.getElementById('f_name');
    var emailField = document.getElementById('f_email');
    
    function validateField(field, hintId, validator){
        if (!field) return;
        var hint = document.getElementById(hintId);
        field.addEventListener('blur', function(){
            var val = field.value.trim();
            if (!val) { field.classList.remove('valid', 'invalid'); if (hint) { hint.textContent = ''; hint.classList.remove('error', 'success'); } return; }
            var result = validator(val);
            if (result === true) { field.classList.add('valid'); field.classList.remove('invalid'); if (hint) { hint.textContent = '✓ Valid'; hint.classList.add('success'); hint.classList.remove('error'); } }
            else { field.classList.add('invalid'); field.classList.remove('valid'); if (hint) { hint.textContent = '✗ ' + result; hint.classList.add('error'); hint.classList.remove('success'); } }
        });
    }
    validateField(nameField, 'hint_name', function(v){ return v.length >= 3 ? true : '<?= $EN ? "Minimum 3 characters" : "Minimal 3 karakter" ?>'; });
    validateField(emailField, 'hint_email', function(v){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? true : '<?= $EN ? "Invalid email" : "Email tidak valid" ?>'; });

    // ===== 🔥 CHAR COUNTER =====
    var msgField = document.getElementById('f_message');
    var msgCount = document.getElementById('msgCount');
    if (msgField && msgCount) {
        msgField.addEventListener('input', function(){ msgCount.textContent = this.value.length; });
        msgCount.textContent = msgField.value.length;
    }

    // ===== 🔥 AUTO-SAVE DRAFT =====
    var form = document.getElementById('pmbForm');
    var draftKey = 'pmb_form_draft';
    var badge = document.getElementById('draftBadge');
    
    function saveDraft(){
        if (!form) return;
        var data = {
            full_name: form.full_name.value,
            email: form.email.value,
            phone: form.phone.value,
            nisn: form.nisn.value,
            birth_date: form.birth_date.value,
            origin_school: form.origin_school.value,
            study_program_id: form.study_program_id.value,
            track: form.track.value,
            message: form.message.value,
            saved_at: Date.now()
        };
        if (data.full_name || data.email || data.phone) {
            localStorage.setItem(draftKey, JSON.stringify(data));
            badge.classList.add('visible');
        }
    }
    
    function loadDraft(){
        if (!form) return;
        var saved = localStorage.getItem(draftKey);
        if (!saved) return;
        try {
            var data = JSON.parse(saved);
            if (Date.now() - data.saved_at > 14 * 24 * 60 * 60 * 1000) { localStorage.removeItem(draftKey); return; }
            Object.keys(data).forEach(function(k){ if (form[k]) form[k].value = data[k]; });
            badge.classList.add('visible');
            if (msgField) msgCount.textContent = (data.message || '').length;
        } catch(e){}
    }
    
    window.clearDraft = function(){
        localStorage.removeItem(draftKey);
        badge.classList.remove('visible');
        if (form) form.reset();
        showToast('<?= $EN ? "Draft cleared" : "Draft dihapus" ?>');
    };
    
    if (form) {
        form.addEventListener('input', function(){
            clearTimeout(window._draftTimer);
            window._draftTimer = setTimeout(saveDraft, 800);
        });
        form.addEventListener('submit', function(){ localStorage.removeItem(draftKey); });
        <?php if (empty($old)): ?>loadDraft();<?php endif; ?>
    }
})();
</script>