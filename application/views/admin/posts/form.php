<!-- Page Header -->
<div class="mb-10">
    <a href="<?= base_url('admin/posts') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
        Kembali ke Daftar
    </a>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        <?= $post ? 'Edit <em class="italic text-gold-muted">Berita</em>' : 'Tulis <em class="italic text-gold-muted">Berita</em>' ?>
    </h1>
</div>

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

<?= form_open_multipart($post ? 'admin/posts/update/' . $post->id : 'admin/posts/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl space-y-8']) ?>

    <!-- Konten -->
    <div>
        <p class="editorial-label text-gold-muted mb-5">Konten Artikel</p>
        <div class="space-y-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="titleInput" value="<?= set_value('title', $post ? $post->title : '') ?>" required maxlength="255" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                <p class="text-xs text-slate mt-1 italic">Slug URL: <span id="slugPreview" class="font-mono text-gold-muted"></span></p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block editorial-label text-navy">Ringkasan (Excerpt)</label>
                    <!-- AI generate excerpt (dengan fallback ke judul) -->
                    <button type="button" id="aiExcerpt" class="text-xs text-gold-muted hover:text-gold font-semibold">
                        <i class="fas fa-magic mr-1"></i>Auto-generate
                    </button>
                </div>
                <textarea name="excerpt" id="excerptArea" rows="3" maxlength="500" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('excerpt', $post ? $post->excerpt : '') ?></textarea>
                <p class="text-[10px] text-slate mt-1"><span id="excerptCount">0</span>/500 karakter</p>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Isi Artikel <span class="text-red-500">*</span></label>
                <textarea name="content" id="contentArea" rows="12" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('content', $post ? $post->content : '') ?></textarea>
                <p class="text-[10px] text-slate mt-1"><span id="contentCount">0</span> karakter • <span id="wordCount">0</span> kata</p>
            </div>
        </div>
    </div>

    <!-- Metadata -->
    <div class="pt-6 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Kategori & Status</p>
        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="">- Tanpa Kategori -</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= set_select('category_id', (string)$c->id, $post && $post->category_id == $c->id) ?>><?= html_escape($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Tipe</label>
                <select name="type" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="news" <?= set_select('type', 'news', !$post || $post->type == 'news') ?>>Berita</option>
                    <option value="announcement" <?= set_select('type', 'announcement', $post && $post->type == 'announcement') ?>>Pengumuman</option>
                    <option value="event" <?= set_select('type', 'event', $post && $post->type == 'event') ?>>Agenda</option>
                </select>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="draft" <?= set_select('status', 'draft', !$post || $post->status == 'draft') ?>>Draft</option>
                    <option value="published" <?= set_select('status', 'published', $post && $post->status == 'published') ?>>Published</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Gambar -->
    <div class="pt-6 border-t border-gray-200">
        <label class="block editorial-label text-navy mb-2">Gambar Utama</label>
        <div class="flex items-start gap-6">
            <!-- Drag & Drop -->
            <div id="dropZone" class="flex-1 bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-5 text-center transition hover:border-gold cursor-pointer">
                <i class="fas fa-image text-3xl text-slate mb-2"></i>
                <p class="text-sm text-navy mb-2">Drag & drop atau klik untuk pilih</p>
                <input type="file" name="featured_image" id="imgInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                <button type="button" onclick="document.getElementById('imgInput').click()" class="btn-navy px-4 py-2 text-[10px] uppercase tracking-editorial font-semibold">
                    <i class="fas fa-folder-open mr-1"></i>Pilih Gambar
                </button>
                <p class="text-[10px] text-slate mt-2">JPG/PNG/WEBP, maks 3MB.</p>
            </div>
            <div class="flex-shrink-0">
                <img id="imgPreview" src="<?= $post && $post->featured_image ? base_url('assets/uploads/' . $post->featured_image) : '' ?>" class="w-32 h-24 object-cover border border-gray-200 <?= $post && $post->featured_image ? '' : 'hidden' ?>" alt="">
                <div id="imgPlaceholder" class="w-32 h-24 bg-gray-200 flex items-center justify-center <?= $post && $post->featured_image ? 'hidden' : '' ?>"><i class="fas fa-image text-slate/40"></i></div>
                <?php if ($post && $post->featured_image): ?>
                <label class="flex items-center gap-2 mt-2 text-xs text-red-600 cursor-pointer">
                    <input type="checkbox" name="remove_image" value="1" class="accent-red-600"> Hapus gambar
                </label>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i><?= $post ? 'Update' : 'Simpan' ?>
        </button>
        <?php if ($post): ?>
        <a href="<?= base_url('admin/posts/duplicate/' . $post->id) ?>" onclick="return confirm('Duplikat artikel ini sebagai draft?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <a href="<?= base_url('admin/posts/preview/' . $post->id) ?>" target="_blank" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-eye mr-2"></i>Preview
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/posts') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">Batal</a>
    </div>

<?= form_close() ?>

<script>
(function(){
    // Token CSRF untuk semua request AJAX
    var CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
    var imgInput = document.getElementById('imgInput');
    var preview = document.getElementById('imgPreview');
    var placeholder = document.getElementById('imgPlaceholder');
    var dropZone = document.getElementById('dropZone');
    var titleInput = document.getElementById('titleInput');
    var slugPreview = document.getElementById('slugPreview');
    var excerptArea = document.getElementById('excerptArea');
    var contentArea = document.getElementById('contentArea');
    var excerptCount = document.getElementById('excerptCount');
    var contentCount = document.getElementById('contentCount');
    var wordCount = document.getElementById('wordCount');
    
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
            imgInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });
    dropZone.addEventListener('click', function(e){
        if (e.target === dropZone || e.target.tagName === 'I' || e.target.tagName === 'P') {
            imgInput.click();
        }
    });
    
    imgInput.addEventListener('change', function(e){
        if (e.target.files[0]) handleFile(e.target.files[0]);
    });
    
    function handleFile(file){
        if (file.size > 3 * 1024 * 1024) { alert('Ukuran maksimal 3MB.'); imgInput.value = ''; return; }
        if (!/image\/(jpeg|png|webp)/.test(file.type)) { alert('Format harus JPG/PNG/WEBP.'); imgInput.value = ''; return; }
        var reader = new FileReader();
        reader.onload = function(ev){
            preview.src = ev.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
    
    // ===== Slug preview =====
    function updateSlug(){
        var title = titleInput.value.trim().toLowerCase();
        var slug = title.replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').substring(0, 60);
        slugPreview.textContent = slug ? '/berita/detail/' + slug : '';
    }
    titleInput.addEventListener('input', updateSlug);
    updateSlug();
    
    // ===== Character counters =====
    function updateCounts(){
        excerptCount.textContent = excerptArea.value.length;
        contentCount.textContent = contentArea.value.length;
        wordCount.textContent = contentArea.value.trim() ? contentArea.value.trim().split(/\s+/).length : 0;
    }
    excerptArea.addEventListener('input', updateCounts);
    contentArea.addEventListener('input', updateCounts);
    updateCounts();
    
    // ===== AI excerpt generator (dengan fallback ke judul) =====
    document.getElementById('aiExcerpt').addEventListener('click', function(){
        var title   = titleInput.value.trim();
        var content = contentArea.value.trim();

        if (!title && !content) {
            alert('Isi judul atau konten terlebih dahulu.');
            return;
        }

        var fd = new FormData();
        fd.append('title', title);
        fd.append('content', content);
        fd.append(CSRF_NAME, CSRF_HASH);   // wajib agar lolos CSRF

        fetch('<?= base_url('admin/posts/ai_excerpt') ?>', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: fd
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            if (data.ok && data.excerpt) {
                excerptArea.value = data.excerpt;
                updateCounts();
                excerptArea.focus();
            } else if (data.message) {
                alert(data.message);
            } else {
                // Fallback client-side
                var src = content || title;
                var clean = src.replace(/<[^>]*>/g, '').substring(0, 200);
                var lastSpace = clean.lastIndexOf(' ');
                if (lastSpace > 100) clean = clean.substring(0, lastSpace);
                excerptArea.value = clean + (clean.length < src.length ? '...' : '');
                updateCounts();
            }
        })
        .catch(function(){
            // Network error fallback
            var src = content || title;
            var clean = src.replace(/<[^>]*>/g, '').substring(0, 200);
            excerptArea.value = clean + (clean.length < src.length ? '...' : '');
            updateCounts();
        });
    });
})();
</script>