<div class="mb-10">
    <a href="<?= base_url('admin/elearning') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
        Kembali ke Daftar
    </a>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        <?= $item ? 'Edit <em class="italic text-gold-muted">Platform</em>' : 'Tambah <em class="italic text-gold-muted">Platform</em>' ?>
    </h1>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 text-sm"><?= validation_errors() ?></div>
<?php endif; ?>

<?= form_open($item ? 'admin/elearning/update/' . $item->id : 'admin/elearning/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-3xl space-y-8']) ?>

    <div>
        <p class="editorial-label text-gold-muted mb-5">Informasi Platform</p>
        <div class="space-y-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Nama Platform <span class="text-red-500">*</span></label>
                <input type="text" name="platform_name" id="platformName" value="<?= set_value('platform_name', $item ? $item->platform_name : '') ?>" required placeholder="Contoh: Moodle Fakultas" maxlength="100" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">URL Platform <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input type="url" name="url" id="urlInput" value="<?= set_value('url', $item ? $item->url : '') ?>" required placeholder="https://elearning.fakultas.ac.id" maxlength="500" class="flex-1 px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                    <!-- 🔥 Test URL button -->
                    <button type="button" id="testBtn" class="px-4 py-2.5 bg-navy text-ivory text-xs uppercase tracking-editorial font-semibold hover:bg-gold hover:text-navy transition">
                        <i class="fas fa-plug mr-1"></i>Test
                    </button>
                </div>
                <div id="urlTestResult" class="text-xs mt-2 hidden"></div>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Deskripsi</label>
                <textarea name="description" rows="2" maxlength="500" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('description', $item ? $item->description : '') ?></textarea>
                <p class="text-xs text-slate mt-1">Maksimal 500 karakter.</p>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Ikon (Font Awesome)</label>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-11 h-11 bg-navy text-gold flex items-center justify-center flex-shrink-0"><i id="iconPrev" class="fas <?= html_escape($item ? $item->icon : 'fa-laptop') ?> text-xl"></i></span>
                    <input type="text" name="icon" id="iconInput" value="<?= set_value('icon', $item ? $item->icon : 'fa-laptop') ?>" placeholder="fa-laptop" maxlength="50" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                </div>
                
                <!-- 🔥 Icon Quick Picker -->
                <div class="p-4 bg-ivory-warm/30 border border-gray-200">
                    <p class="text-xs text-slate mb-3">Quick pick:</p>
                    <div class="grid grid-cols-6 md:grid-cols-12 gap-2">
                        <?php 
                        $common_icons = [
                            'fa-laptop', 'fa-laptop-code', 'fa-graduation-cap', 'fa-book', 'fa-book-open',
                            'fa-chalkboard-teacher', 'fa-users', 'fa-video', 'fa-headset', 'fa-comments',
                            'fa-cloud', 'fa-database', 'fa-flask', 'fa-microscope', 'fa-calculator',
                            'fa-globe', 'fa-server', 'fa-code', 'fa-desktop', 'fa-tablet-alt',
                            'fa-mobile-alt', 'fa-wifi', 'fa-satellite-dish', 'fa-rocket'
                        ];
                        foreach ($common_icons as $icon): ?>
                        <button type="button" onclick="setIcon('<?= $icon ?>')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-navy hover:bg-gold hover:text-navy hover:border-gold transition" title="<?= $icon ?>">
                            <i class="fas <?= $icon ?> text-sm"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <label class="flex items-center gap-3 pt-6 border-t border-gray-200 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" <?= (!$item || $item->is_active) ? 'checked' : '' ?> class="w-5 h-5 accent-navy">
        <div>
            <p class="text-sm font-medium text-navy">Platform Aktif</p>
            <p class="text-xs text-slate">Tampilkan di halaman Kemahasiswaan</p>
        </div>
    </label>

    <div class="flex gap-3 pt-6 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs"><i class="fas fa-save mr-2"></i>Simpan</button>
        <?php if ($item): ?>
        <!-- 🔥 Duplicate button (edit mode only) -->
        <a href="<?= base_url('admin/elearning/duplicate/' . $item->id) ?>" onclick="return confirm('Duplikat platform ini?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/elearning') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">Batal</a>
    </div>

<?= form_close() ?>

<script>
(function(){
    var input = document.getElementById('iconInput');
    var prev = document.getElementById('iconPrev');
    var urlInput = document.getElementById('urlInput');
    var testBtn = document.getElementById('testBtn');
    var urlTestResult = document.getElementById('urlTestResult');
    var platformName = document.getElementById('platformName');
    
    if (!input || !prev) return;
    
    input.addEventListener('input', function(){
        prev.className = 'fas ' + (input.value.trim() || 'fa-laptop') + ' text-xl';
    });
    
    // ===== Icon Quick Picker =====
    window.setIcon = function(icon){
        input.value = icon;
        prev.className = 'fas ' + icon + ' text-xl';
    };
    
    // ===== Test URL =====
    if (testBtn && urlInput) {
        testBtn.addEventListener('click', function(){
            var url = urlInput.value.trim();
            if (!url) {
                alert('Masukkan URL terlebih dahulu.');
                return;
            }
            
            testBtn.disabled = true;
            testBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i>Testing...';
            urlTestResult.classList.remove('hidden');
            urlTestResult.innerHTML = '<span class="text-blue-600"><i class="fas fa-circle-notch fa-spin mr-1"></i>Mengecek koneksi...</span>';
            
            fetch('<?= base_url('admin/elearning/test_url') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'url=' + encodeURIComponent(url)
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                testBtn.disabled = false;
                testBtn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test';
                
                if (data.ok) {
                    urlTestResult.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>URL dapat diakses (HTTP ' + data.code + ')</span>';
                } else {
                    urlTestResult.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>' + data.message + '</span>';
                }
            })
            .catch(function(err){
                testBtn.disabled = false;
                testBtn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test';
                urlTestResult.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Error: ' + err.message + '</span>';
            });
        });
        
        // 🔥 Auto-detect platform name dari URL (jika kosong)
        urlInput.addEventListener('blur', function(){
            if (!platformName.value.trim() && urlInput.value.trim()) {
                try {
                    var url = new URL(urlInput.value);
                    var host = url.hostname.replace('www.', '');
                    var name = host.split('.')[0];
                    platformName.value = name.charAt(0).toUpperCase() + name.slice(1);
                } catch(e) {
                    // ignore invalid URL
                }
            }
        });
    }
})();
</script>