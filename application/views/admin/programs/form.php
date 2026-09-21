<?php
$degrees = $degrees ?? ['D3','D4','S1','S2','S3','Profesi','Spesialis'];
$accreditations = $accreditations ?? ['Unggul','Baik Sekali','Baik','A','B','C','Terakreditasi','Belum'];
?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/programs') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $program ? 'Edit <em class="italic text-gold-muted">Program Studi</em>' : 'Tambah <em class="italic text-gold-muted">Program Studi</em>' ?>
        </h1>
    </div>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open_multipart($program ? 'admin/programs/update/' . $program->id : 'admin/programs/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl', 'id' => 'prodiForm']) ?>

    <div class="space-y-8">

        <!-- Identitas -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Informasi Dasar</p>
            <div class="grid md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block editorial-label text-navy mb-2">Nama Program Studi <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="prodiName" value="<?= set_value('name', $program ? $program->name : '') ?>" required maxlength="150"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <p class="text-xs text-slate mt-1 italic">Slug URL: <span id="slugPreview" class="font-mono text-gold-muted"></span></p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Jenjang <span class="text-red-500">*</span></label>
                    <!-- 🔥 FIX: Sinkron dengan controller (7 pilihan) -->
                    <select name="degree" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <?php foreach ($degrees as $d): ?>
                        <option value="<?= $d ?>" <?= set_select('degree', $d, $program && $program->degree == $d) ?>><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Akreditasi + Kaprodi -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Akreditasi BAN-PT & Ketua Prodi</p>
            <div class="grid md:grid-cols-3 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Peringkat</label>
                    <!-- 🔥 FIX: Sinkron dengan controller -->
                    <select name="accreditation" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <option value="">- Belum Ada -</option>
                        <?php foreach ($accreditations as $a): ?>
                        <option value="<?= $a ?>" <?= set_select('accreditation', $a, $program && $program->accreditation == $a) ?>><?= $a ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Berlaku Sampai</label>
                    <input type="date" name="accreditation_until" id="accUntil" value="<?= set_value('accreditation_until', $program ? $program->accreditation_until : '') ?>"
                        min="<?= date('Y-m-d', strtotime('-5 years')) ?>" max="<?= date('Y-m-d', strtotime('+10 years')) ?>"
                        class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                    <!-- 🔥 Warning jika dekat expired -->
                    <div id="accWarning" class="text-[10px] mt-1 hidden"></div>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Ketua Program Studi</label>
                    <input type="text" name="head_of_study_program" maxlength="150" value="<?= set_value('head_of_study_program', $program ? $program->head_of_study_program : '') ?>"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
            </div>

            <!-- Foto Kaprodi -->
            <div class="mt-5">
                <label class="block editorial-label text-navy mb-2">Foto Ketua Prodi (untuk Struktur Organisasi)</label>
                <div class="flex items-center gap-4">
                    <!-- 🔥 Drag & drop -->
                    <div id="dropZone" class="flex-1 bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-3 text-center transition hover:border-gold cursor-pointer">
                        <input type="file" name="head_photo" id="kaprodiPhoto" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <button type="button" onclick="document.getElementById('kaprodiPhoto').click()" class="btn-navy px-4 py-2 text-[10px] uppercase tracking-editorial font-semibold">
                            <i class="fas fa-folder-open mr-1"></i>Pilih Foto
                        </button>
                        <p class="text-[10px] text-slate mt-1">JPG/PNG/WEBP, maks 2MB.</p>
                    </div>
                    <img id="pv_kaprodi" src="<?= ($program && !empty($program->head_photo)) ? base_url('assets/uploads/' . $program->head_photo) : '' ?>"
                        class="w-16 h-16 rounded-full object-cover border-2 border-gold/40 <?= ($program && !empty($program->head_photo)) ? '' : 'hidden' ?>" alt="">
                </div>
                <?php if ($program && !empty($program->head_photo)): ?>
                    <label class="flex items-center gap-2 mt-2 text-xs text-red-600 cursor-pointer w-fit">
                        <input type="checkbox" name="remove_head_photo" value="1" class="accent-red-600"> Hapus foto kaprodi
                    </label>
                <?php endif; ?>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Deskripsi Program</label>
            <textarea name="description" rows="4" maxlength="3000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('description', $program ? $program->description : '') ?></textarea>
        </div>

        <!-- Visi Misi -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Visi & Misi</p>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Visi</label>
                    <textarea name="vision" rows="6" maxlength="1000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed italic"><?= set_value('vision', $program ? $program->vision : '') ?></textarea>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Misi</label>
                    <textarea name="mission" rows="6" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('mission', $program ? $program->mission : '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Status -->
        <label class="flex items-center gap-3 pt-6 border-t border-gray-200 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" <?= $program ? ($program->is_active ? 'checked' : '') : 'checked' ?> class="w-5 h-5 accent-navy">
            <div>
                <p class="text-sm font-medium text-navy">Program Aktif</p>
                <p class="text-xs text-slate">Tampilkan di website publik</p>
            </div>
        </label>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Program
        </button>
        <?php if ($program): ?>
        <a href="<?= base_url('admin/programs/duplicate/' . $program->id) ?>" onclick="return confirm('Duplikat prodi ini?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/programs') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function(){
    var input = document.getElementById('kaprodiPhoto');
    var pv = document.getElementById('pv_kaprodi');
    var dropZone = document.getElementById('dropZone');
    var nameInput = document.getElementById('prodiName');
    var slugPreview = document.getElementById('slugPreview');
    var accUntil = document.getElementById('accUntil');
    var accWarning = document.getElementById('accWarning');
    
    // ===== Drag & drop =====
    ['dragenter','dragover','dragleave','drop'].forEach(function(ev){
        dropZone.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); });
    });
    ['dragenter','dragover'].forEach(function(ev){
        dropZone.addEventListener(ev, function(){ dropZone.classList.add('border-gold', 'bg-gold/10'); });
    });
    ['dragleave','drop'].forEach(function(ev){
        dropZone.addEventListener(ev, function(){ dropZone.classList.remove('border-gold', 'bg-gold/10'); });
    });
    dropZone.addEventListener('drop', function(e){
        if (e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });
    
    input.addEventListener('change', function(e){
        if (e.target.files[0]) handleFile(e.target.files[0]);
    });
    
    function handleFile(file){
        // 🔥 Client-side validation
        if (file.size > 2 * 1024 * 1024) { alert('Ukuran maksimal 2MB.'); input.value = ''; return; }
        if (!/image\/(jpeg|png|webp)/.test(file.type)) { alert('Format harus JPG/PNG/WEBP.'); input.value = ''; return; }
        var r = new FileReader();
        r.onload = function(ev){ pv.src = ev.target.result; pv.classList.remove('hidden'); };
        r.readAsDataURL(file);
    }
    
    // ===== Slug preview =====
    function updateSlug(){
        var name = nameInput.value.trim().toLowerCase();
        var slug = name.replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').substring(0, 60);
        slugPreview.textContent = slug ? '/akademik/detail/' + slug : '';
    }
    nameInput.addEventListener('input', updateSlug);
    updateSlug();
    
    // ===== Accreditation expiry warning =====
    function checkAccExpiry(){
        accWarning.classList.add('hidden');
        if (!accUntil.value) return;
        
        var until = new Date(accUntil.value);
        var now = new Date();
        var days = Math.floor((until - now) / 86400000);
        
        if (days < 0) {
            accWarning.className = 'text-[10px] mt-1 text-red-600 font-semibold';
            accWarning.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>SUDAH EXPIRED ' + Math.abs(days) + ' hari lalu!';
        } else if (days <= 90) {
            accWarning.className = 'text-[10px] mt-1 text-amber-600 font-semibold';
            accWarning.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Akan expired dalam ' + days + ' hari!';
        } else if (days <= 180) {
            accWarning.className = 'text-[10px] mt-1 text-blue-600';
            accWarning.innerHTML = '<i class="fas fa-info-circle mr-1"></i>' + days + ' hari lagi';
        }
    }
    accUntil.addEventListener('change', checkAccExpiry);
    checkAccExpiry();
})();
</script>