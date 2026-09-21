<div class="mb-10">
    <a href="<?= base_url('admin/portal') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
        Kembali ke Daftar
    </a>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        <?= $item ? 'Edit <em class="italic text-gold-muted">Layanan</em>' : 'Tambah <em class="italic text-gold-muted">Layanan</em>' ?>
    </h1>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 text-sm"><?= validation_errors() ?></div>
<?php endif; ?>

<?= form_open($item ? 'admin/portal/update/' . $item->id : 'admin/portal/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-3xl space-y-8']) ?>

    <div>
        <p class="editorial-label text-gold-muted mb-5">Informasi Layanan</p>
        <div class="space-y-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Judul Layanan <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="titleInput" value="<?= set_value('title', $item ? $item->title : '') ?>" required placeholder="Contoh: KRS Online" maxlength="255" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">URL Tujuan <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input type="url" name="link_url" id="urlInput" value="<?= set_value('link_url', $item ? $item->link_url : '') ?>" required placeholder="https://..." maxlength="500" class="flex-1 px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                    <!-- 🔥 Test URL button -->
                    <button type="button" id="testBtn" class="px-4 py-2.5 bg-navy text-ivory text-xs uppercase tracking-editorial font-semibold hover:bg-gold hover:text-navy transition">
                        <i class="fas fa-plug mr-1"></i>Test
                    </button>
                </div>
                <div id="urlTestResult" class="text-xs mt-2 hidden"></div>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Deskripsi Singkat</label>
                <textarea name="description" rows="2" maxlength="500" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= set_value('description', $item ? $item->description : '') ?></textarea>
                <p class="text-[10px] text-slate mt-1">Maks 500 karakter.</p>
            </div>
        </div>
    </div>

    <div class="pt-6 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Tampilan</p>
        <div class="grid md:grid-cols-4 gap-5">
            <div class="md:col-span-2">
                <label class="block editorial-label text-navy mb-2">Ikon (Font Awesome)</label>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-11 h-11 bg-navy text-gold flex items-center justify-center flex-shrink-0"><i id="iconPrev" class="fas <?= html_escape($item ? $item->icon : 'fa-link') ?> text-xl"></i></span>
                    <input type="text" name="icon" id="iconInput" value="<?= set_value('icon', $item ? $item->icon : 'fa-link') ?>" placeholder="fa-graduation-cap" maxlength="50" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                </div>
                <!-- 🔥 Icon Quick Picker -->
                <div class="p-3 bg-ivory-warm/30 border border-gray-200">
                    <p class="text-[10px] text-slate mb-2 uppercase tracking-wider">Quick pick:</p>
                    <div class="grid grid-cols-8 gap-1.5">
                        <?php 
                        $portal_icons = ['fa-graduation-cap','fa-book','fa-calendar-alt','fa-file-pdf','fa-users','fa-id-card','fa-money-bill','fa-credit-card','fa-award','fa-trophy','fa-clipboard-list','fa-envelope','fa-phone','fa-map-marker-alt','fa-wifi','fa-laptop','fa-download','fa-upload','fa-print','fa-search','fa-bell','fa-comments','fa-question-circle','fa-external-link-alt','fa-university','fa-building','fa-home','fa-user','fa-user-graduate','fa-link','fa-book-open','fa-flask'];
                        foreach ($portal_icons as $icon): ?>
                        <button type="button" onclick="setIcon('<?= $icon ?>')" class="w-7 h-7 border border-gray-200 flex items-center justify-center text-navy hover:bg-gold hover:text-navy hover:border-gold transition" title="<?= $icon ?>">
                            <i class="fas <?= $icon ?> text-xs"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Warna</label>
                <select name="color" id="colorSelect" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <?php foreach ($colors as $c): ?>
                    <option value="<?= $c ?>" <?= set_select('color', $c, $item && $item->color == $c) ?>><?= ucfirst($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- 🔥 Color visual preview -->
                <div id="colorPreview" class="mt-2 p-3 bg-<?= html_escape($item ? $item->color : 'blue') ?>-50 text-<?= html_escape($item ? $item->color : 'blue') ?>-600 rounded text-center text-xs font-semibold">
                    <i class="fas <?= html_escape($item ? $item->icon : 'fa-link') ?> mr-1"></i>Preview
                </div>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Kategori</label>
                <select name="category" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <?php foreach ($cats as $key => $label): ?>
                    <option value="<?= $key ?>" <?= set_select('category', $key, $item && $item->category == $key) ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="block editorial-label text-navy mb-2">Urutan Tampil</label>
                <input type="number" name="sort_order" value="<?= set_value('sort_order', $item ? $item->sort_order : 0) ?>" min="0" max="999" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                <p class="text-[10px] text-slate mt-1">Angka lebih kecil muncul lebih dulu.</p>
            </div>
        </div>
    </div>

    <label class="flex items-center gap-3 pt-6 border-t border-gray-200 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" <?= (!$item || $item->is_active) ? 'checked' : '' ?> class="w-5 h-5 accent-navy">
        <div>
            <p class="text-sm font-medium text-navy">Layanan Aktif</p>
            <p class="text-xs text-slate">Tampilkan di halaman Kemahasiswaan</p>
        </div>
    </label>

    <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs"><i class="fas fa-save mr-2"></i>Simpan</button>
        <?php if ($item): ?>
        <a href="<?= base_url('admin/portal/duplicate/' . $item->id) ?>" onclick="return confirm('Duplikat layanan ini?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/portal') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">Batal</a>
    </div>

<?= form_close() ?>

<script>
(function(){
    var input = document.getElementById('iconInput');
    var prev = document.getElementById('iconPrev');
    var urlInput = document.getElementById('urlInput');
    var testBtn = document.getElementById('testBtn');
    var urlTestResult = document.getElementById('urlTestResult');
    var titleInput = document.getElementById('titleInput');
    var colorSelect = document.getElementById('colorSelect');
    var colorPreview = document.getElementById('colorPreview');
    
    // ===== Icon preview =====
    input.addEventListener('input', function(){
        prev.className = 'fas ' + (input.value.trim() || 'fa-link') + ' text-xl';
        updateColorPreview();
    });
    
    window.setIcon = function(icon){
        input.value = icon;
        prev.className = 'fas ' + icon + ' text-xl';
        updateColorPreview();
    };
    
    // ===== Color preview =====
    function updateColorPreview(){
        var color = colorSelect.value;
        var icon = input.value.trim() || 'fa-link';
        colorPreview.className = 'mt-2 p-3 bg-' + color + '-50 text-' + color + '-600 rounded text-center text-xs font-semibold';
        colorPreview.innerHTML = '<i class="fas ' + icon + ' mr-1"></i>Preview';
    }
    colorSelect.addEventListener('change', updateColorPreview);
    
    // ===== Test URL =====
    testBtn.addEventListener('click', function(){
        var url = urlInput.value.trim();
        if (!url) { alert('Masukkan URL terlebih dahulu.'); return; }
        
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i>Testing...';
        urlTestResult.classList.remove('hidden');
        urlTestResult.innerHTML = '<span class="text-blue-600"><i class="fas fa-circle-notch fa-spin mr-1"></i>Mengecek koneksi...</span>';
        
        fetch('<?= base_url('admin/portal/test_url') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
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
            urlTestResult.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Error</span>';
        });
    });
    
    // 🔥 Auto-suggest URL dari title (jika URL kosong)
    titleInput.addEventListener('blur', function(){
        if (!urlInput.value.trim() && titleInput.value.trim()) {
            var title = titleInput.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-');
            urlInput.value = 'https://' + title + '.example.com';
        }
    });
})();
</script>