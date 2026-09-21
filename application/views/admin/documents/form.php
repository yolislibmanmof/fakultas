<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/documents') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $document ? 'Edit <em class="italic text-gold-muted">Dokumen</em>' : 'Upload <em class="italic text-gold-muted">Dokumen</em>' ?>
        </h1>
    </div>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open_multipart($document ? 'admin/documents/update/' . $document->id : 'admin/documents/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-3xl', 'id' => 'docForm']) ?>

    <div class="space-y-8">

        <!-- Informasi Dokumen -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Informasi Dokumen</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Judul Dokumen <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="titleInput" value="<?= set_value('title', $document ? $document->title : '') ?>" required placeholder="Contoh: Formulir Cuti Studi" maxlength="255"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <p class="text-xs text-slate mt-1">Maksimal 255 karakter.</p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Kategori</label>
                    <select name="category" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= $key ?>" <?= set_select('category', $key, $document && $document->category == $key) ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" maxlength="1000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('description', $document ? $document->description : '') ?></textarea>
                    <p class="text-xs text-slate mt-1">Maksimal 1000 karakter.</p>
                </div>
            </div>
        </div>

        <!-- File Upload -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">File Dokumen</label>
            <!-- 🔥 Drag & Drop Area -->
            <div id="dropZone" class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-6 text-center transition hover:border-gold hover:bg-ivory-warm cursor-pointer">
                <i class="fas fa-file-upload text-3xl text-slate mb-3"></i>
                <p class="text-sm text-navy mb-2">Drag & drop file di sini, atau klik untuk memilih</p>
                <input type="file" name="userfile" id="fileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar"
                    class="hidden">
                <button type="button" onclick="document.getElementById('fileInput').click()" class="btn-navy px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fas fa-folder-open mr-2"></i>Pilih File
                </button>
                <p class="text-xs text-slate mt-3">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR. Maksimal 10MB.</p>
            </div>
            
            <!-- 🔥 File Info Preview -->
            <div id="filePreview" class="hidden mt-4 p-4 bg-green-50 border border-green-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-800" id="fileName"></p>
                        <p class="text-xs text-green-600" id="fileSize"></p>
                    </div>
                    <button type="button" onclick="clearFile()" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <p id="fileInfo" class="hidden text-xs text-navy font-mono font-semibold mt-2"></p>
            
            <?php if ($document): ?>
                <div class="mt-3 p-3 bg-ivory-warm/30 border border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-slate">
                        <i class="fas fa-paperclip text-gold-muted mr-1"></i>
                        File saat ini: <span class="font-mono font-semibold text-navy"><?= html_escape($document->file_name) ?></span> (<?= $document->file_size ?> KB)
                    </p>
                    <a href="<?= base_url('assets/uploads/documents/' . $document->file_path) ?>" target="_blank" class="text-xs text-gold-muted hover:text-gold font-semibold">
                        <i class="fas fa-external-link-alt mr-1"></i>Download
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status -->
        <label class="flex items-center gap-3 pt-6 border-t border-gray-200 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" <?= (!$document || $document->is_active) ? 'checked' : '' ?> class="w-5 h-5 accent-navy">
            <div>
                <p class="text-sm font-medium text-navy">Dokumen Aktif</p>
                <p class="text-xs text-slate">Tampilkan di Download Center publik</p>
            </div>
        </label>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Dokumen
        </button>
        <a href="<?= base_url('admin/documents') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function(){
    var input = document.getElementById('fileInput');
    var info = document.getElementById('fileInfo');
    var dropZone = document.getElementById('dropZone');
    var filePreview = document.getElementById('filePreview');
    var fileName = document.getElementById('fileName');
    var fileSize = document.getElementById('fileSize');
    var titleInput = document.getElementById('titleInput');
    
    if (!input) return;
    
    // ===== Drag & Drop =====
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName){
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e){
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(function(eventName){
            dropZone.addEventListener(eventName, function(){
                dropZone.classList.add('border-gold', 'bg-gold/10');
            });
        });
        
        ['dragleave', 'drop'].forEach(function(eventName){
            dropZone.addEventListener(eventName, function(){
                dropZone.classList.remove('border-gold', 'bg-gold/10');
            });
        });
        
        dropZone.addEventListener('drop', function(e){
            var files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileSelect(files[0]);
            }
        });
    }
    
    input.addEventListener('change', function(){
        if (input.files.length > 0) {
            handleFileSelect(input.files[0]);
        }
    });
    
    function handleFileSelect(file){
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file maksimal 10MB.');
            input.value = '';
            return;
        }
        
        // Validate file type
        var allowed = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.zip', '.rar'];
        var ext = '.' + file.name.split('.').pop().toLowerCase();
        if (allowed.indexOf(ext) === -1) {
            alert('Format file tidak didukung.');
            input.value = '';
            return;
        }
        
        // Show preview
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size) + ' — Tipe: ' + file.type;
        filePreview.classList.remove('hidden');
        
        // 🔥 Auto-generate title dari filename (jika title kosong)
        if (!titleInput.value.trim()) {
            var name = file.name.replace(/\.[^/.]+$/, ''); // remove extension
            titleInput.value = name.replace(/[_-]/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
        }
        
        info.textContent = 'Terpilih: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
        info.classList.remove('hidden');
    }
    
    function formatFileSize(bytes){
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    
    window.clearFile = function(){
        input.value = '';
        filePreview.classList.add('hidden');
        info.classList.add('hidden');
    };
})();
</script>