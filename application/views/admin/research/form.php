<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/research') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $item ? 'Edit <em class="italic text-gold-muted">Riset</em>' : 'Input Riset <em class="italic text-gold-muted">Baru</em>' ?>
        </h1>
    </div>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open_multipart($item ? 'admin/research/update/' . $item->id : 'admin/research/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl', 'id' => 'researchForm']) ?>

    <div class="space-y-8">

        <!-- Informasi Riset -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Informasi Riset</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Judul Riset <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="titleInput" value="<?= set_value('title', $item ? $item->title : '') ?>" required maxlength="255" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
                <div class="grid md:grid-cols-4 gap-5">
                    <div class="md:col-span-2">
                        <label class="block editorial-label text-navy mb-2">Dosen Peneliti <span class="text-red-500">*</span></label>
                        <select name="lecturer_id" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <option value="">- Pilih Dosen -</option>
                            <?php foreach ($lecturers as $l): ?>
                            <option value="<?= $l->id ?>" <?= set_select('lecturer_id', (string)$l->id, $item && $item->lecturer_id == $l->id) ?>>
                                <?= html_escape(trim($l->title_front . ' ' . $l->name)) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Jenis <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <option value="research" <?= set_select('type', 'research', !$item || $item->type == 'research') ?>>Penelitian</option>
                            <option value="community_service" <?= set_select('type', 'community_service', $item && $item->type == 'community_service') ?>>Pengabdian</option>
                        </select>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Tahun <span class="text-red-500">*</span></label>
                        <!-- 🔥 FIX: Tahun dinamis (sinkron controller) -->
                        <input type="number" name="year" min="1990" max="<?= date('Y') + 1 ?>" value="<?= set_value('year', $item ? $item->year : date('Y')) ?>" required class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                        <p class="text-[10px] text-slate mt-1">1990 - <?= date('Y') + 1 ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendanaan -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Pendanaan & Status</p>
            <div class="grid md:grid-cols-3 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Sumber Dana</label>
                    <!-- 🔥 Auto-suggest -->
                    <input type="text" name="funding_source" list="fundingList" value="<?= set_value('funding_source', $item ? $item->funding_source : '') ?>" placeholder="Contoh: Hibah Dikti" maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <datalist id="fundingList">
                        <option value="Hibah Dikti">
                        <option value="Hibah Internal Universitas">
                        <option value="Hibah Kemenristek">
                        <option value="Kerjasama Industri">
                        <option value="Mandiri">
                        <option value="LPDP">
                        <option value="Hibah Internasional">
                        <option value="Dana Desa">
                        <option value="CSR Perusahaan">
                    </datalist>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Jumlah Dana (Rp)</label>
                    <input type="number" name="amount" id="amountInput" value="<?= set_value('amount', $item ? $item->amount : '') ?>" min="0" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                    <!-- 🔥 Format preview -->
                    <p id="amountFormat" class="text-[10px] text-gold-muted mt-1 font-semibold hidden"></p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <option value="proposed" <?= set_select('status', 'proposed', $item && $item->status == 'proposed') ?>>Diajukan</option>
                        <option value="ongoing" <?= set_select('status', 'ongoing', !$item || $item->status == 'ongoing') ?>>Berjalan</option>
                        <option value="completed" <?= set_select('status', 'completed', $item && $item->status == 'completed') ?>>Selesai</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Abstrak -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Abstrak</label>
            <textarea name="abstract" rows="6" maxlength="5000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('abstract', $item ? $item->abstract : '') ?></textarea>
            <p class="text-[10px] text-slate mt-1"><span id="abstractCount">0</span>/5000 karakter</p>
        </div>

        <!-- Dokumen -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Dokumen Pendukung (PDF, opsional)</label>
            <!-- 🔥 Drag & drop -->
            <div id="dropZone" class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-5 text-center transition hover:border-gold cursor-pointer">
                <i class="fas fa-file-pdf text-3xl text-slate mb-2"></i>
                <p class="text-sm text-navy mb-2">Drag & drop PDF di sini</p>
                <input type="file" name="document_file" id="pdfInput" accept=".pdf,.doc,.docx" class="hidden">
                <button type="button" onclick="document.getElementById('pdfInput').click()" class="btn-navy px-4 py-2 text-[10px] uppercase tracking-editorial font-semibold">
                    <i class="fas fa-folder-open mr-1"></i>Pilih File
                </button>
                <p id="pdfInfo" class="hidden text-xs text-navy font-mono font-semibold mt-2"></p>
                <p class="text-xs text-slate mt-3">PDF/DOC/DOCX, maksimal 10MB.</p>
            </div>
            <?php if ($item && $item->document_file): ?>
                <div class="mt-3 p-3 bg-ivory-warm/30 border border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-slate">
                        <i class="fas fa-paperclip text-gold-muted mr-1"></i>
                        File saat ini: <span class="font-mono font-semibold text-navy"><?= html_escape($item->document_file) ?></span>
                    </p>
                    <a href="<?= base_url('assets/uploads/research/' . $item->document_file) ?>" target="_blank" class="text-xs text-gold-muted hover:text-gold font-semibold">
                        <i class="fas fa-external-link-alt mr-1"></i>Download
                    </a>
                </div>
                <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                    <input type="checkbox" name="remove_file" value="1" class="accent-red-600"> Hapus dokumen ini
                </label>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Riset
        </button>
        <?php if ($item): ?>
        <a href="<?= base_url('admin/research/duplicate/' . $item->id) ?>" onclick="return confirm('Duplikat riset ini sebagai proposal baru?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/research') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function(){
    var pdfInput = document.getElementById('pdfInput');
    var pdfInfo = document.getElementById('pdfInfo');
    var dropZone = document.getElementById('dropZone');
    var amountInput = document.getElementById('amountInput');
    var amountFormat = document.getElementById('amountFormat');
    var abstractArea = document.querySelector('textarea[name="abstract"]');
    var abstractCount = document.getElementById('abstractCount');
    
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
            pdfInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });
    
    pdfInput.addEventListener('change', function(){
        if (pdfInput.files[0]) handleFile(pdfInput.files[0]);
    });
    
    function handleFile(file){
        // 🔥 Client-side validation
        if (file.size > 10 * 1024 * 1024) { alert('Ukuran maksimal 10MB.'); pdfInput.value = ''; return; }
        var ext = file.name.split('.').pop().toLowerCase();
        if (['pdf','doc','docx'].indexOf(ext) === -1) { alert('Format harus PDF/DOC/DOCX.'); pdfInput.value = ''; return; }
        pdfInfo.textContent = 'Terpilih: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
        pdfInfo.classList.remove('hidden');
    }
    
    // ===== Format jumlah dana =====
    function formatRupiah(num){
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    amountInput.addEventListener('input', function(){
        var val = parseInt(amountInput.value) || 0;
        if (val > 0) {
            amountFormat.textContent = formatRupiah(val);
            amountFormat.classList.remove('hidden');
        } else {
            amountFormat.classList.add('hidden');
        }
    });
    // Trigger on load
    if (amountInput.value) amountInput.dispatchEvent(new Event('input'));
    
    // ===== Character counter =====
    if (abstractArea && abstractCount) {
        abstractArea.addEventListener('input', function(){
            abstractCount.textContent = abstractArea.value.length;
        });
        abstractCount.textContent = abstractArea.value.length;
    }
})();
</script>