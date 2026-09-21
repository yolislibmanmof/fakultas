<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/lecturers') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $lecturer ? 'Edit Profil <em class="italic text-gold-muted">Dosen</em>' : 'Tambah Dosen <em class="italic text-gold-muted">Baru</em>' ?>
        </h1>
    </div>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open_multipart($lecturer ? 'admin/lecturers/update/' . $lecturer->id : 'admin/lecturers/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl', 'id' => 'lecForm']) ?>

    <div class="space-y-8">

        <!-- Identitas -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Identitas</p>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">NIDN <span class="text-red-500">*</span></label>
                    <input type="text" name="nidn" id="nidnInput" value="<?= set_value('nidn', $lecturer ? $lecturer->nidn : '') ?>" required maxlength="10" pattern="\d{10}" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                    <!-- 🔥 NIDN validation feedback -->
                    <div id="nidnStatus" class="text-xs mt-1 hidden"></div>
                    <p class="text-[10px] text-slate mt-1">10 digit angka.</p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="nameInput" value="<?= set_value('name', $lecturer ? $lecturer->name : '') ?>" required maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Gelar Depan</label>
                    <input type="text" name="title_front" id="titleFront" value="<?= set_value('title_front', $lecturer ? $lecturer->title_front : '') ?>" placeholder="Dr. / Prof." maxlength="50" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <!-- 🔥 Quick pick gelar depan -->
                    <div class="flex flex-wrap gap-1 mt-1">
                        <button type="button" onclick="setTitle('titleFront', 'Dr. ')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">Dr.</button>
                        <button type="button" onclick="setTitle('titleFront', 'Prof. ')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">Prof.</button>
                        <button type="button" onclick="setTitle('titleFront', 'Prof. Dr. ')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">Prof. Dr.</button>
                        <button type="button" onclick="setTitle('titleFront', 'Ir. ')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">Ir.</button>
                    </div>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Gelar Belakang</label>
                    <input type="text" name="title_back" id="titleBack" value="<?= set_value('title_back', $lecturer ? $lecturer->title_back : '') ?>" placeholder="M.Kom., M.T." maxlength="50" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <div class="flex flex-wrap gap-1 mt-1">
                        <button type="button" onclick="appendTitle('titleBack', ', S.Kom.')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">S.Kom.</button>
                        <button type="button" onclick="appendTitle('titleBack', ', M.Kom.')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">M.Kom.</button>
                        <button type="button" onclick="appendTitle('titleBack', ', M.T.')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">M.T.</button>
                        <button type="button" onclick="appendTitle('titleBack', ', Ph.D.')" class="text-[10px] px-2 py-0.5 bg-ivory-warm border border-navy/10 hover:bg-gold hover:text-navy">Ph.D.</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Kontak</p>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Email</label>
                    <input type="email" name="email" value="<?= set_value('email', $lecturer ? $lecturer->email : '') ?>" maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">No. HP</label>
                    <input type="text" name="phone" value="<?= set_value('phone', $lecturer ? $lecturer->phone : '') ?>" maxlength="20" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
            </div>
        </div>

        <!-- Akademik -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Profil Akademik</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Bidang Keahlian</label>
                    <!-- 🔥 Auto-suggest keahlian -->
                    <input type="text" name="expertise" list="expertiseList" value="<?= set_value('expertise', $lecturer ? $lecturer->expertise : '') ?>" placeholder="Contoh: Artificial Intelligence, Machine Learning" maxlength="300" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <datalist id="expertiseList">
                        <option value="Artificial Intelligence">
                        <option value="Machine Learning">
                        <option value="Deep Learning">
                        <option value="Data Science">
                        <option value="Software Engineering">
                        <option value="Computer Networks">
                        <option value="Cyber Security">
                        <option value="Internet of Things">
                        <option value="Database Systems">
                        <option value="Web Development">
                        <option value="Mobile Development">
                        <option value="Computer Vision">
                        <option value="Natural Language Processing">
                        <option value="Robotics">
                        <option value="Cloud Computing">
                    </datalist>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Pendidikan Terakhir</label>
                    <input type="text" name="education" list="educationList" value="<?= set_value('education', $lecturer ? $lecturer->education : '') ?>" placeholder="Contoh: S3 Ilmu Komputer" maxlength="500" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <datalist id="educationList">
                        <option value="S1 Teknik Informatika">
                        <option value="S1 Sistem Informasi">
                        <option value="S2 Ilmu Komputer">
                        <option value="S2 Teknik Informatika">
                        <option value="S3 Ilmu Komputer">
                        <option value="S3 Teknik Informatika">
                    </datalist>
                </div>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block editorial-label text-navy mb-2">URL Google Scholar</label>
                        <input type="url" name="google_scholar_url" value="<?= set_value('google_scholar_url', $lecturer ? $lecturer->google_scholar_url : '') ?>" placeholder="https://scholar.google.com/..." maxlength="500" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">URL SINTA</label>
                        <input type="url" name="sinta_url" value="<?= set_value('sinta_url', $lecturer ? $lecturer->sinta_url : '') ?>" placeholder="https://sinta.kemdikbud.go.id/..." maxlength="500" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Afiliasi Prodi -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-4">Program Studi (Afiliasi)</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-ivory-warm/30 p-5 border border-navy/10">
                <?php foreach ($prodi_list as $p): ?>
                <label class="flex items-center gap-3 p-3 bg-white border border-navy/10 hover:border-navy/30 cursor-pointer transition">
                    <input type="checkbox" name="prodi_ids[]" value="<?= $p->id ?>" <?= in_array($p->id, $checked) ? 'checked' : '' ?> class="accent-navy">
                    <span class="text-sm text-navy"><?= html_escape($p->name) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Foto Dosen -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Foto Dosen</label>
            <div class="flex items-start gap-6">
                <div class="flex-1">
                    <!-- 🔥 Drag & drop -->
                    <div id="dropZone" class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-5 text-center transition hover:border-gold cursor-pointer">
                        <i class="fas fa-user-circle text-3xl text-slate mb-2"></i>
                        <p class="text-sm text-navy mb-2">Drag & drop atau klik untuk pilih</p>
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <button type="button" onclick="document.getElementById('photoInput').click()" class="btn-navy px-4 py-2 text-[10px] uppercase tracking-editorial font-semibold">
                            <i class="fas fa-folder-open mr-1"></i>Pilih Foto
                        </button>
                        <p class="text-[10px] text-slate mt-2">JPG/PNG/WEBP, maks 2MB. Disarankan rasio 1:1.</p>
                    </div>
                    <?php if ($lecturer && $lecturer->photo): ?>
                        <label class="flex items-center gap-2 mt-3 text-sm text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_photo" value="1" class="accent-red-600" onchange="togglePreview()"> Hapus foto saat ini
                        </label>
                    <?php endif; ?>
                </div>
                <div class="flex-shrink-0">
                    <?php if ($lecturer && $lecturer->photo): ?>
                        <img id="photoPreview" src="<?= base_url('assets/uploads/' . $lecturer->photo) ?>" class="w-28 h-28 rounded-full object-cover border-2 border-gold/40 shadow-sm" alt="">
                    <?php else: ?>
                        <img id="photoPreview" src="" class="hidden w-28 h-28 rounded-full object-cover border-2 border-gold/40 shadow-sm" alt="">
                        <div id="photoPlaceholder" class="w-28 h-28 rounded-full bg-navy text-gold flex items-center justify-center font-serif text-3xl font-bold">?</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Status -->
        <label class="flex items-center gap-3 pt-6 border-t border-gray-200 cursor-pointer group">
            <input type="checkbox" name="is_active" value="1" <?= $lecturer ? ($lecturer->is_active ? 'checked' : '') : 'checked' ?> class="w-5 h-5 accent-navy">
            <div>
                <p class="text-sm font-medium text-navy">Dosen Aktif</p>
                <p class="text-xs text-slate">Tampilkan di direktori publik</p>
            </div>
        </label>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Profil
        </button>
        <?php if ($lecturer): ?>
        <a href="<?= base_url('admin/lecturers/duplicate/' . $lecturer->id) ?>" onclick="return confirm('Duplikat dosen ini?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <!-- 🔥 Download vCard -->
        <a href="<?= base_url('admin/lecturers/vcard/' . $lecturer->id) ?>" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-address-card mr-2"></i>Download vCard
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/lecturers') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function() {
    var input = document.getElementById('photoInput');
    var preview = document.getElementById('photoPreview');
    var placeholder = document.getElementById('photoPlaceholder');
    var removeCb = document.querySelector('input[name="remove_photo"]');
    var dropZone = document.getElementById('dropZone');
    var nidnInput = document.getElementById('nidnInput');
    var nidnStatus = document.getElementById('nidnStatus');
    var form = document.getElementById('lecForm');
    var excludeId = <?= json_encode($lecturer ? $lecturer->id : 0) ?>;
    
    // ===== Drag & Drop =====
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
        if (file.size > 2 * 1024 * 1024) { alert('Ukuran maksimal 2MB.'); input.value = ''; return; }
        if (!/image\/(jpeg|png|webp)/.test(file.type)) { alert('Format harus JPG/PNG/WEBP.'); input.value = ''; return; }
        var reader = new FileReader();
        reader.onload = function(ev){
            preview.src = ev.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            if (removeCb) removeCb.checked = false;
        };
        reader.readAsDataURL(file);
    }

    window.togglePreview = function() {
        if (!preview) return;
        if (removeCb && removeCb.checked) {
            preview.classList.add('hidden');
            if (placeholder) placeholder.classList.remove('hidden');
        } else {
            if (preview.src) preview.classList.remove('hidden');
        }
    };
    
    // ===== NIDN Validation (client-side + AJAX uniqueness check) =====
    var nidnDebounce;
    nidnInput.addEventListener('input', function(){
        var val = nidnInput.value.replace(/\D/g, '').substring(0, 10);
        nidnInput.value = val;
        
        if (val.length === 0) {
            nidnStatus.classList.add('hidden');
            return;
        }
        
        if (val.length < 10) {
            nidnStatus.className = 'text-xs mt-1 text-amber-600';
            nidnStatus.innerHTML = '<i class="fas fa-clock mr-1"></i>Masukkan 10 digit (' + val.length + '/10)';
            return;
        }
        
        // Check uniqueness via AJAX
        clearTimeout(nidnDebounce);
        nidnDebounce = setTimeout(function(){
            fetch('<?= base_url('admin/lecturers/check_nidn') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'nidn=' + val + '&exclude_id=' + excludeId
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (data.unique) {
                    nidnStatus.className = 'text-xs mt-1 text-green-600';
                    nidnStatus.innerHTML = '<i class="fas fa-check-circle mr-1"></i>NIDN tersedia';
                } else {
                    nidnStatus.className = 'text-xs mt-1 text-red-600';
                    nidnStatus.innerHTML = '<i class="fas fa-times-circle mr-1"></i>NIDN sudah dipakai dosen lain';
                }
            })
            .catch(function(){
                nidnStatus.classList.add('hidden');
            });
        }, 300);
    });
    
    // Block submit if NIDN invalid
    form.addEventListener('submit', function(e){
        var val = nidnInput.value;
        if (val.length !== 10) {
            e.preventDefault();
            nidnStatus.className = 'text-xs mt-1 text-red-600';
            nidnStatus.innerHTML = '<i class="fas fa-times-circle mr-1"></i>NIDN harus 10 digit';
            nidnInput.focus();
            return false;
        }
    });
    
    // ===== Quick fill gelar =====
    window.setTitle = function(id, val){
        document.getElementById(id).value = val;
    };
    window.appendTitle = function(id, val){
        var input = document.getElementById(id);
        var current = input.value;
        if (current.indexOf(val) === -1) {
            input.value = current + val;
        }
    };
})();
</script>