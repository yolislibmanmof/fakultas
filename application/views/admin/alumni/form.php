<!-- Page Header -->
<div class="mb-10">
    <a href="<?= base_url('admin/alumni') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
        Kembali
    </a>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        <?= $a ? 'Edit <em class="italic text-gold-muted">Alumni</em>' : 'Tambah <em class="italic text-gold-muted">Alumni</em>' ?>
    </h1>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 text-sm"><?= validation_errors() ?></div>
<?php endif; ?>

<?= form_open_multipart($a ? 'admin/alumni/update/' . $a->id : 'admin/alumni/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl space-y-8']) ?>

    <div>
        <p class="editorial-label text-gold-muted mb-5">Data Pribadi</p>
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" maxlength="150" value="<?= set_value('full_name', $a ? $a->full_name : '') ?>" required class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" maxlength="150" value="<?= set_value('email', $a ? $a->email : '') ?>" required class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">NIM</label>
                <input type="text" name="nim" maxlength="30" value="<?= set_value('nim', $a ? $a->nim : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">No. HP</label>
                <input type="text" name="phone" maxlength="20" value="<?= set_value('phone', $a ? $a->phone : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Password <?= $a ? '(kosongkan jika tidak diubah)' : '' ?></label>
                <input type="password" name="password" id="pwInput" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                <!-- 🔥 Password strength meter -->
                <div class="mt-2 h-1.5 bg-gray-200 overflow-hidden">
                    <div id="pwMeter" class="h-full w-0 transition-all duration-300"></div>
                </div>
                <p id="pwLabel" class="text-[10px] text-slate mt-1 uppercase tracking-wider"></p>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="pending" <?= set_select('status', 'pending', $a && $a->status == 'pending') ?>>Pending</option>
                    <option value="approved" <?= set_select('status', 'approved', $a && $a->status == 'approved') ?>>Approved</option>
                    <option value="rejected" <?= set_select('status', 'rejected', $a && $a->status == 'rejected') ?>>Rejected</option>
                </select>
            </div>
        </div>
    </div>

    <div class="pt-6 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Akademik</p>
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Program Studi</label>
                <select name="study_program_id" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="">- Pilih -</option>
                    <?php foreach ($programs as $p): ?>
                    <option value="<?= $p->id ?>" <?= set_select('study_program_id', (string)$p->id, $a && $a->study_program_id == $p->id) ?>><?= html_escape($p->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Tahun Lulus</label>
                <input type="number" name="graduation_year" min="1950" max="<?= date('Y') + 1 ?>" value="<?= set_value('graduation_year', $a ? $a->graduation_year : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
            </div>
        </div>
    </div>

    <div class="pt-6 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Karir & Kontak</p>
        <div class="grid md:grid-cols-2 gap-5">
            <div><label class="block editorial-label text-navy mb-2">Posisi</label><input type="text" name="current_position" maxlength="150" value="<?= set_value('current_position', $a ? $a->current_position : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"></div>
            <div><label class="block editorial-label text-navy mb-2">Perusahaan</label><input type="text" name="company" list="companyList" maxlength="150" value="<?= set_value('company', $a ? $a->company : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"></div>
            <div><label class="block editorial-label text-navy mb-2">Industri</label><input type="text" name="industry" list="industryList" maxlength="100" value="<?= set_value('industry', $a ? $a->industry : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"></div>
            <div><label class="block editorial-label text-navy mb-2">Kota</label><input type="text" name="city" maxlength="100" value="<?= set_value('city', $a ? $a->city : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"></div>
            <div><label class="block editorial-label text-navy mb-2">Negara</label><input type="text" name="country" list="countryList" maxlength="100" value="<?= set_value('country', $a ? $a->country : 'Indonesia') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"></div>
            <div><label class="block editorial-label text-navy mb-2">LinkedIn</label><input type="url" name="linkedin_url" maxlength="255" placeholder="https://linkedin.com/in/..." value="<?= set_value('linkedin_url', $a ? $a->linkedin_url : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm"></div>
            <div class="md:col-span-2"><label class="block editorial-label text-navy mb-2">Website</label><input type="url" name="website_url" maxlength="255" placeholder="https://..." value="<?= set_value('website_url', $a ? $a->website_url : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm"></div>
        </div>
        <!-- 🔥 Datalists saran -->
        <datalist id="industryList">
            <option value="Teknologi"><option value="Keuangan"><option value="Pendidikan"><option value="Kesehatan">
            <option value="Manufaktur"><option value="Telekomunikasi"><option value="Energi"><option value="Retail">
            <option value="Pemerintahan"><option value="Startup"><option value="Konsultan"><option value="Media">
        </datalist>
        <datalist id="countryList">
            <option value="Indonesia"><option value="Malaysia"><option value="Singapore"><option value="Jepang">
            <option value="Korea Selatan"><option value="Jerman"><option value="Belanda"><option value="Inggris">
            <option value="Amerika Serikat"><option value="Australia">
        </datalist>
        <datalist id="companyList">
            <option value="Google"><option value="Tokopedia"><option value="Gojek"><option value="Telkom Indonesia">
            <option value="Bank BCA"><option value="Astra"><option value="Unilever"><option value="Startup Lokal">
        </datalist>
    </div>

    <div class="pt-6 border-t border-gray-200 space-y-5">
        <p class="editorial-label text-gold-muted">Cerita & Foto</p>
        <div><label class="block editorial-label text-navy mb-2">Bio</label><textarea name="bio" rows="3" maxlength="2000" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('bio', $a ? $a->bio : '') ?></textarea></div>
        <div><label class="block editorial-label text-navy mb-2">Prestasi (satu per baris)</label><textarea name="achievements" rows="3" maxlength="2000" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('achievements', $a ? $a->achievements : '') ?></textarea></div>
        <div>
            <label class="block editorial-label text-navy mb-2">Foto</label>
            <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-xs hover:file:bg-gold hover:file:text-navy file:cursor-pointer">
            <p class="text-[10px] text-slate mt-1">JPG/PNG/WEBP, maksimal 2MB.</p>
            <!-- 🔥 Live preview foto -->
            <div class="mt-3 flex items-center gap-3">
                <img id="photoPreview" src="<?= ($a && $a->photo) ? base_url('assets/uploads/' . $a->photo) : '' ?>"
                     class="w-20 h-20 rounded-full object-cover border border-gold/30 <?= ($a && $a->photo) ? '' : 'hidden' ?>" alt="">
                <div id="photoPlaceholder" class="w-20 h-20 rounded-full bg-ivory-warm border-2 border-dashed border-navy/20 flex items-center justify-center text-slate <?= ($a && $a->photo) ? 'hidden' : '' ?>">
                    <i class="fas fa-user text-xl"></i>
                </div>
                <p class="text-xs text-slate">Preview foto profil</p>
            </div>
        </div>
    </div>

    <div class="flex gap-3 pt-6 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs"><i class="fas fa-save mr-2"></i>Simpan</button>
        <a href="<?= base_url('admin/alumni') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">Batal</a>
    </div>

<?= form_close() ?>

<script>
(function(){
    // ===== Live photo preview =====
    var input = document.getElementById('photoInput');
    var preview = document.getElementById('photoPreview');
    var placeholder = document.getElementById('photoPlaceholder');
    input.addEventListener('change', function(){
        var file = input.files && input.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { alert('Ukuran foto maksimal 2MB.'); input.value = ''; return; }
        if (!/image\/(jpeg|png|webp)/.test(file.type)) { alert('Format harus JPG/PNG/WEBP.'); input.value = ''; return; }
        var reader = new FileReader();
        reader.onload = function(e){
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    // ===== Password strength meter =====
    var pw = document.getElementById('pwInput');
    var meter = document.getElementById('pwMeter');
    var label = document.getElementById('pwLabel');
    pw.addEventListener('input', function(){
        var v = pw.value, score = 0;
        if (v.length >= 6) score++;
        if (v.length >= 10) score++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        var pct = Math.min(100, score * 20);
        var colors = ['#ef4444', '#ef4444', '#f59e0b', '#f59e0b', '#22c55e', '#16a34a'];
        var labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Cukup', 'Kuat', 'Sangat kuat'];
        meter.style.width = pct + '%';
        meter.style.background = colors[score];
        label.textContent = v ? labels[score] : '';
        label.style.color = colors[score];
    });
})();
</script>