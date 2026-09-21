<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/facilities') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $facility ? 'Edit <em class="italic text-gold-muted">Fasilitas</em>' : 'Tambah <em class="italic text-gold-muted">Fasilitas</em>' ?>
        </h1>
    </div>
</div>

<!-- 🔥 FIX: Display validation errors dari flashdata + form_validation -->
<?php
$error_flash = $this->session->flashdata('error');
$validation_err = validation_errors();
$combined_error = $error_flash ?: $validation_err;
?>
<?php if ($combined_error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm flash-anim">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div class="flex-1"><?= $combined_error ?></div>
    </div>
<?php endif; ?>

<?= form_open_multipart($facility ? 'admin/facilities/update/' . $facility->id : 'admin/facilities/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl']) ?>

    <div class="space-y-8">

        <!-- Informasi Dasar -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Informasi Dasar</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="facName" value="<?= set_value('name', $facility ? $facility->name : '') ?>" required maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block editorial-label text-navy mb-2">Tipe Fasilitas <span class="text-red-500">*</span></label>
                        <select name="type" id="facType" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <?php foreach ($types as $key => $label): ?>
                            <option value="<?= $key ?>" <?= set_select('type', $key, $facility && $facility->type == $key) ?>><?= html_escape($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Lokasi</label>
                        <input type="text" name="location" id="facLocation" list="locationList" value="<?= set_value('location', $facility ? $facility->location : '') ?>" placeholder="Contoh: Gedung A Lt. 2" maxlength="200" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <datalist id="locationList">
                            <option value="Gedung A Lt. 1">
                            <option value="Gedung A Lt. 2">
                            <option value="Gedung A Lt. 3">
                            <option value="Gedung B Lt. 1">
                            <option value="Gedung B Lt. 2">
                            <option value="Gedung C">
                            <option value="Basement">
                            <option value="Rooftop">
                            <option value="Outdoor Area">
                        </datalist>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Kapasitas (orang)</label>
                        <input type="number" name="capacity" id="facCapacity" min="0" max="99999" value="<?= set_value('capacity', $facility ? $facility->capacity : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                        <p class="text-[10px] text-slate mt-1">Maks 99,999 orang.</p>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Status</label>
                        <select name="is_active" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <option value="1" <?= set_select('is_active', '1', !$facility || $facility->is_active) ?>>Aktif</option>
                            <option value="0" <?= set_select('is_active', '0', $facility && !$facility->is_active) ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deskripsi dengan auto-generate -->
        <div class="pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <label class="block editorial-label text-navy">Deskripsi</label>
                <button type="button" id="autoDesc" class="text-xs text-gold-muted hover:text-gold font-semibold">
                    <i class="fas fa-magic mr-1"></i>Auto-generate
                </button>
            </div>
            <textarea name="description" id="facDesc" rows="4" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('description', $facility ? $facility->description : '') ?></textarea>
            <p class="text-[10px] text-slate mt-1">Maks 2000 karakter.</p>
        </div>

        <!-- Galeri Foto (UPGRADED) -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Galeri Foto (multiple upload)</label>
            
            <div id="dropZone" class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-6 text-center transition hover:border-gold cursor-pointer">
                <i class="fas fa-images text-3xl text-slate mb-3"></i>
                <p class="text-sm text-navy mb-2">Drag & drop foto di sini, atau klik untuk memilih</p>
                <input type="file" name="images[]" id="multiImageInput" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                <button type="button" onclick="document.getElementById('multiImageInput').click()" class="btn-navy px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fas fa-folder-open mr-2"></i>Pilih Foto
                </button>
                <p class="text-xs text-slate mt-3">JPG/PNG/WEBP, maksimal 5MB per file. Foto pertama menjadi sampul. Maks 10 foto total.</p>
            </div>

            <div id="newImagesPreview" class="mt-4 hidden">
                <p class="text-xs text-slate mb-2 uppercase tracking-wider">Foto Baru yang Akan Ditambahkan:</p>
                <div id="newImagesList" class="flex flex-wrap gap-3"></div>
            </div>

            <?php if ($facility && $facility->images):
                $imgs = json_decode($facility->images, true);
                if (is_array($imgs) && !empty($imgs)): ?>
                <div class="mt-4">
                    <p class="text-xs text-slate mb-2 uppercase tracking-wider">Galeri Saat Ini (centang untuk hapus):</p>
                    <div class="grid grid-cols-4 md:grid-cols-6 gap-3">
                        <?php foreach ($imgs as $idx => $img): ?>
                        <label class="relative group cursor-pointer block">
                            <input type="checkbox" name="remove_images[]" value="<?= $idx ?>" class="absolute top-2 left-2 w-4 h-4 accent-red-600 z-10">
                            <img src="<?= base_url('assets/uploads/' . $img) ?>" class="w-full aspect-square object-cover border border-gray-200 group-hover:opacity-50 transition" alt="">
                            <?php if ($idx === 0): ?>
                                <span class="absolute bottom-1 right-1 bg-gold text-navy text-[8px] uppercase tracking-wider font-bold px-1.5 py-0.5">Sampul</span>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-[10px] text-slate mt-2 italic">
                        <i class="fas fa-info-circle text-gold-muted mr-1"></i>
                        Centang foto yang ingin dihapus, lalu klik Simpan.
                    </p>
                </div>
                <?php endif;
            endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Fasilitas
        </button>
        <?php if ($facility): ?>
        <a href="<?= base_url('admin/facilities/duplicate/' . $facility->id) ?>" onclick="return confirm('Duplikat fasilitas ini?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/facilities') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function() {
    var input = document.getElementById('multiImageInput');
    var previewWrap = document.getElementById('newImagesPreview');
    var previewList = document.getElementById('newImagesList');
    var dropZone = document.getElementById('dropZone');
    var nameInput = document.getElementById('facName');
    var typeSelect = document.getElementById('facType');
    var descArea = document.getElementById('facDesc');
    var capacityInput = document.getElementById('facCapacity');
    
    if (!input) return;
    
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
            handleFiles(e.dataTransfer.files);
        }
    });
    dropZone.addEventListener('click', function(e){
        if (e.target === dropZone || e.target.tagName === 'I' || e.target.tagName === 'P') {
            input.click();
        }
    });
    
    input.addEventListener('change', function(e){
        handleFiles(e.target.files);
    });
    
    function handleFiles(fileList){
        var files = Array.from(fileList || []);
        if (files.length === 0) { previewWrap.classList.add('hidden'); return; }
        
        var errors = [];
        files.forEach(function(f){
            if (f.size > 5 * 1024 * 1024) errors.push(f.name + ' > 5MB');
            if (!/image\/(jpeg|png|webp)/.test(f.type)) errors.push(f.name + ' format tidak valid');
        });
        if (errors.length > 0) {
            alert('Error:\n• ' + errors.join('\n• '));
            input.value = '';
            return;
        }
        
        previewList.innerHTML = '';
        previewWrap.classList.remove('hidden');
        files.forEach(function(file, idx){
            var reader = new FileReader();
            reader.onload = function(ev){
                var wrapper = document.createElement('div');
                wrapper.className = 'relative';
                var img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'w-24 h-24 object-cover border-2 border-gold/40';
                wrapper.appendChild(img);
                if (idx === 0) {
                    var badge = document.createElement('span');
                    badge.className = 'absolute bottom-1 right-1 bg-gold text-navy text-[8px] uppercase tracking-wider font-bold px-1.5 py-0.5';
                    badge.textContent = 'Sampul';
                    wrapper.appendChild(badge);
                }
                var size = document.createElement('p');
                size.className = 'text-[10px] text-slate mt-1 text-center';
                size.textContent = Math.round(file.size/1024) + ' KB';
                wrapper.appendChild(size);
                previewList.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }
    
    // ===== Auto-generate description =====
    var autoBtn = document.getElementById('autoDesc');
    if (autoBtn) {
        autoBtn.addEventListener('click', function(){
            var type = typeSelect.value;
            var name = nameInput.value.trim() || '[Nama Fasilitas]';
            var cap = capacityInput.value ? ' dengan kapasitas ' + capacityInput.value + ' orang' : '';
            
            var templates = {
                laboratory: name + ' adalah laboratorium modern' + cap + ' yang dilengkapi dengan peralatan terkini untuk mendukung kegiatan praktikum dan riset mahasiswa.',
                classroom: name + ' merupakan ruang kelas nyaman' + cap + ' yang dilengkapi dengan fasilitas multimedia untuk mendukung proses belajar mengajar yang interaktif.',
                library: name + ' menyediakan koleksi buku dan jurnal lengkap' + cap + ' sebagai pusat literasi dan referensi akademik mahasiswa.',
                mosque: name + ' adalah tempat ibadah yang nyaman' + cap + ' untuk menunjang kegiatan spiritual civitas akademika.',
                sport: name + ' merupakan fasilitas olahraga' + cap + ' untuk mendukung kegiatan jasmani dan prestasi mahasiswa.',
                other: name + ' adalah fasilitas pendukung' + cap + ' untuk menunjang kegiatan akademik dan non-akademik.'
            };
            
            descArea.value = templates[type] || templates.other;
            descArea.focus();
        });
    }
})();
</script>