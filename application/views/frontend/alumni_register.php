<?php $EN = (get_site_lang() == 'en'); ?>
<!-- ============ HEADER ============ -->
<section class="bg-ivory border-b border-gray-200 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-gold/5 to-transparent"></div>
    <div class="container mx-auto px-6 py-16 md:py-24 relative">
        <div class="max-w-3xl">
            <p class="editorial-label text-gold-muted mb-6"><?= $EN ? 'Alumni Registration' : 'Pendaftaran Alumni' ?></p>
            <h1 class="font-serif font-light text-navy tracking-[-0.03em] leading-[1.02] text-5xl md:text-7xl">
                <?= $EN ? 'Join Our <em class="italic text-gold-muted">Network</em>' : 'Bergabung dengan <em class="italic text-gold-muted">Jaringan</em>' ?>
            </h1>
            <p class="text-slate mt-6 text-lg leading-relaxed">
                <?= $EN ? 'Register your profile to be part of the faculty alumni directory. Profiles will be verified by the admin before publication.' : 'Daftarkan profil Anda untuk menjadi bagian dari direktori alumni fakultas. Profil akan diverifikasi admin sebelum dipublikasikan.' ?>
            </p>
        </div>
    </div>
</section>

<section class="py-16 md:py-24 bg-ivory">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12 max-w-6xl mx-auto">

            <!-- Form -->
            <div class="lg:col-span-2">
                <?php if ($this->session->flashdata('register_success')): ?>
                <div class="bg-white border border-gray-200 border-l-4 border-l-green-500 p-10 text-center fade-in">
                    <div class="w-20 h-20 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6"><i class="fas fa-check"></i></div>
                    <h2 class="font-serif text-3xl font-light text-navy mb-4"><?= $EN ? 'Registration Received!' : 'Pendaftaran Diterima!' ?></h2>
                    <p class="text-slate leading-relaxed max-w-md mx-auto">
                        <?= $EN ? 'Thank you for registering. Your profile will be reviewed by the admin before being published to the directory.' : 'Terima kasih telah mendaftar. Profil Anda akan ditinjau admin sebelum dipublikasikan ke direktori.' ?>
                    </p>
                    <a href="<?= base_url('alumni') ?>" class="btn-navy inline-block mt-8 px-8 py-3 text-xs uppercase tracking-editorial font-semibold"><?= $EN ? 'Back to Directory' : 'Ke Direktori' ?></a>
                </div>
                <?php else: ?>

                <?php if ($this->session->flashdata('register_errors')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 mb-8 text-sm">
                    <?= $this->session->flashdata('register_errors') ?>
                </div>
                <?php endif; ?>

                <!-- FORM PROGRESS -->
                <div class="mb-6 bg-white border border-gray-200 p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="editorial-label text-slate"><?= $EN ? 'Form Completion' : 'Kelengkapan Form' ?></span>
                        <span class="font-mono text-xs font-bold text-gold-muted" id="progressPct">0%</span>
                    </div>
                    <div class="h-2 bg-ivory-warm overflow-hidden">
                        <div id="progressBar" class="h-full bg-gradient-to-r from-navy to-gold transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>

                <?= form_open('alumni/submit', ['class' => 'bg-white border border-gray-200 p-8 md:p-10 space-y-8', 'id' => 'regForm']) ?>

                    <div>
                        <p class="editorial-label text-gold-muted mb-5"><?= $EN ? '01 — Personal Data' : '01 — Data Pribadi' ?></p>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Full Name' : 'Nama Lengkap' ?> <span class="text-red-500">*</span></label>
                                <input type="text" name="full_name" id="f_name" value="<?= set_value('full_name') ?>" required maxlength="150" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="<?= set_value('email') ?>" required class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Phone' : 'No. HP' ?></label>
                                <input type="text" name="phone" value="<?= set_value('phone') ?>" maxlength="20" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2">NIM</label>
                                <input type="text" name="nim" value="<?= set_value('nim') ?>" maxlength="30" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy font-mono">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Password' : 'Password' ?> <span class="text-red-500">*</span></label>
                                <input type="password" name="password" id="f_pwd" required class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                                <div class="h-1.5 bg-ivory-warm mt-2 overflow-hidden"><div id="pwdBar" class="h-full transition-all duration-300" style="width:0%"></div></div>
                                <p class="text-[10px] text-slate mt-1" id="pwdLabel"></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <p class="editorial-label text-gold-muted mb-5"><?= $EN ? '02 — Academic' : '02 — Akademik' ?></p>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Study Program' : 'Program Studi' ?> <span class="text-red-500">*</span></label>
                                <select name="study_program_id" id="f_prodi" required class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                                    <option value="">- <?= $EN ? 'Choose' : 'Pilih' ?> -</option>
                                    <?php foreach ($programs as $p): ?>
                                    <option value="<?= $p->id ?>" <?= set_select('study_program_id', (string)$p->id) ?>><?= html_escape($p->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Graduation Year' : 'Tahun Lulus' ?> <span class="text-red-500">*</span></label>
                                <input type="number" name="graduation_year" id="f_year" min="1980" max="<?= date('Y') ?>" value="<?= set_value('graduation_year') ?>" required class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy font-mono">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <p class="editorial-label text-gold-muted mb-5"><?= $EN ? '03 — Career' : '03 — Karir' ?></p>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Current Position' : 'Posisi Saat Ini' ?></label>
                                <input type="text" name="current_position" id="f_position" value="<?= set_value('current_position') ?>" maxlength="150" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Company' : 'Perusahaan' ?></label>
                                <input type="text" name="company" id="f_company" value="<?= set_value('company') ?>" maxlength="150" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Industry' : 'Industri' ?></label>
                                <input type="text" name="industry" value="<?= set_value('industry') ?>" list="industryList" maxlength="100" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                                <datalist id="industryList">
                                    <option value="Teknologi"><option value="Keuangan"><option value="Pendidikan"><option value="Kesehatan">
                                    <option value="Manufaktur"><option value="Telekomunikasi"><option value="Energi"><option value="Pemerintahan">
                                </datalist>
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'City' : 'Kota' ?></label>
                                <input type="text" name="city" value="<?= set_value('city') ?>" maxlength="100" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Country' : 'Negara' ?></label>
                                <input type="text" name="country" value="<?= set_value('country', 'Indonesia') ?>" maxlength="100" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy">
                            </div>
                            <div>
                                <label class="block editorial-label text-navy mb-2">LinkedIn URL</label>
                                <input type="url" name="linkedin_url" value="<?= set_value('linkedin_url') ?>" placeholder="https://linkedin.com/in/..." maxlength="255" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block editorial-label text-navy mb-2">Website URL</label>
                                <input type="url" name="website_url" value="<?= set_value('website_url') ?>" placeholder="https://..." maxlength="255" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200 space-y-5">
                        <p class="editorial-label text-gold-muted"><?= $EN ? '04 — Story' : '04 — Cerita' ?></p>
                        <div>
                            <label class="block editorial-label text-navy mb-2">Bio</label>
                            <textarea name="bio" id="f_bio" rows="4" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('bio') ?></textarea>
                            <p class="text-[10px] text-slate mt-1"><span id="bioCount">0</span>/2000</p>
                        </div>
                        <div>
                            <label class="block editorial-label text-navy mb-2"><?= $EN ? 'Achievements (one per line)' : 'Prestasi (satu per baris)' ?></label>
                            <textarea name="achievements" rows="3" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('achievements') ?></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <button type="submit" class="btn-gold w-full px-8 py-4 font-semibold uppercase tracking-editorial text-xs">
                            <i class="fas fa-paper-plane mr-2"></i><?= $EN ? 'Submit Registration' : 'Kirim Pendaftaran' ?>
                        </button>
                        <p class="text-xs text-slate mt-4 text-center"><?= $EN ? 'Your profile will be published after admin verification.' : 'Profil Anda akan dipublikasikan setelah verifikasi admin.' ?></p>
                    </div>

                <?= form_close() ?>
                <?php endif; ?>
            </div>

            <!-- Benefits Sidebar + LIVE PREVIEW -->
            <aside>
                <div class="sticky top-32 space-y-6">

                    <!-- LIVE PROFILE PREVIEW -->
                    <div class="bg-navy text-ivory p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-gold/40"></div>
                        <p class="editorial-label text-gold mb-5"><?= $EN ? 'Live Preview' : 'Preview Langsung' ?></p>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 rounded-full bg-navy-light border-2 border-gold flex items-center justify-center font-serif text-2xl text-gold flex-shrink-0" id="pvInitial">?</div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium leading-snug truncate" id="pvName"><?= $EN ? 'Your Name' : 'Nama Anda' ?></p>
                                <p class="text-xs text-ivory/60 truncate" id="pvRole">—</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-gold text-navy text-[9px] uppercase tracking-editorial px-2 py-1 font-bold" id="pvYear">Class of —</span>
                            <span class="border border-gold/50 text-gold text-[9px] uppercase tracking-editorial px-2 py-1 font-semibold truncate max-w-[140px]" id="pvProdi">—</span>
                        </div>
                    </div>

                    <div class="bg-navy text-ivory p-8 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-gold/40"></div>
                        <p class="editorial-label text-gold mb-6"><?= $EN ? 'Why Join?' : 'Mengapa Bergabung?' ?></p>
                        <div class="space-y-5">
                            <div class="flex gap-4"><span class="font-serif text-2xl font-light text-gold leading-none w-8">01</span><p class="text-sm text-ivory/80 leading-relaxed"><?= $EN ? 'Expand your professional network with fellow alumni.' : 'Perluas jaringan profesional dengan sesama alumni.' ?></p></div>
                            <div class="flex gap-4"><span class="font-serif text-2xl font-light text-gold leading-none w-8">02</span><p class="text-sm text-ivory/80 leading-relaxed"><?= $EN ? 'Access career opportunities and collaborations.' : 'Akses peluang karir dan kolaborasi.' ?></p></div>
                            <div class="flex gap-4"><span class="font-serif text-2xl font-light text-gold leading-none w-8">03</span><p class="text-sm text-ivory/80 leading-relaxed"><?= $EN ? 'Give back: mentorship and inspiration for current students.' : 'Berbagi kembali: mentoring dan inspirasi untuk mahasiswa.' ?></p></div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 p-8">
                        <p class="editorial-label text-gold-muted mb-4"><?= $EN ? 'Already registered?' : 'Sudah terdaftar?' ?></p>
                        <p class="text-sm text-slate leading-relaxed mb-5"><?= $EN ? 'Browse the alumni directory to reconnect with old friends.' : 'Jelajahi direktori alumni untuk terhubung kembali dengan teman lama.' ?></p>
                        <a href="<?= base_url('alumni') ?>" class="btn-navy inline-block px-6 py-3 text-xs uppercase tracking-editorial font-semibold"><?= $EN ? 'View Directory' : 'Lihat Direktori' ?></a>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</section>

<script>
(function(){
    var $ = function(id){ return document.getElementById(id); };

    // ===== LIVE PREVIEW =====
    function updPreview(){
        var name = $('f_name') ? $('f_name').value.trim() : '';
        var pos = $('f_position') ? $('f_position').value.trim() : '';
        var comp = $('f_company') ? $('f_company').value.trim() : '';
        var year = $('f_year') ? $('f_year').value : '';
        var prodiSel = $('f_prodi');
        var prodi = (prodiSel && prodiSel.selectedIndex > 0) ? prodiSel.options[prodiSel.selectedIndex].text : '';

        if ($('pvName')) $('pvName').textContent = name || '<?= $EN ? "Your Name" : "Nama Anda" ?>';
        if ($('pvInitial')) $('pvInitial').textContent = name ? name.charAt(0).toUpperCase() : '?';
        if ($('pvRole')) $('pvRole').textContent = (pos && comp) ? pos + ' @ ' + comp : (pos || comp || '—');
        if ($('pvYear')) $('pvYear').textContent = 'Class of ' + (year || '—');
        if ($('pvProdi')) $('pvProdi').textContent = prodi || '—';
    }

    // ===== FORM PROGRESS =====
    var required = ['f_name', 'f_pwd', 'f_prodi', 'f_year'];
    function updProgress(){
        var filled = 0;
        required.forEach(function(id){
            var el = $(id);
            if (el && el.value && el.value.trim() !== '') filled++;
        });
        var email = document.querySelector('input[name="email"]');
        if (email && email.value) filled++;
        var pct = Math.round((filled / 5) * 100);
        if ($('progressBar')) $('progressBar').style.width = pct + '%';
        if ($('progressPct')) $('progressPct').textContent = pct + '%';
    }

    // ===== PASSWORD STRENGTH =====
    function updPwd(){
        var pwd = $('f_pwd') ? $('f_pwd').value : '';
        var s = 0;
        if (pwd.length >= 6) s++;
        if (pwd.length >= 10) s++;
        if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) s++;
        if (/[0-9]/.test(pwd)) s++;
        if (/[^A-Za-z0-9]/.test(pwd)) s++;
        var bar = $('pwdBar');
        if (!bar) return;
        bar.style.width = (pwd ? Math.max(15, s * 20) : 0) + '%';
        bar.style.background = s <= 2 ? '#ef4444' : (s <= 3 ? '#f59e0b' : '#10b981');
        if ($('pwdLabel')) $('pwdLabel').textContent = pwd ? (s <= 2 ? '<?= $EN ? "Weak" : "Lemah" ?>' : (s <= 3 ? '<?= $EN ? "Medium" : "Sedang" ?>' : '<?= $EN ? "Strong" : "Kuat" ?>')) : '';
    }

    // ===== BIO COUNTER =====
    function updBio(){
        var bio = $('f_bio');
        if (bio && $('bioCount')) $('bioCount').textContent = bio.value.length;
    }

    // Bind all
    document.addEventListener('input', function(e){
        updPreview(); updProgress(); updPwd(); updBio();
    });
    document.addEventListener('change', function(e){
        updPreview(); updProgress();
    });

    updPreview(); updProgress(); updBio();
})();
</script>