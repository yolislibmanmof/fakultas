<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Digital Learning</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Platform <em class="italic text-gold-muted">E-Learning</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Tautan platform pembelajaran digital fakultas.</p>
    </div>
    <a href="<?= base_url('admin/elearning/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-plus"></i>Tambah Platform
    </a>
</div>

<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate">All Platforms</p>
        <p class="font-mono text-xs text-slate"><?= count($items) ?> Platforms</p>
    </div>

    <?php if (empty($items)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-laptop-code text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2">Belum ada platform e-learning.</p>
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full refined-table">
            <thead>
                <tr>
                    <th class="text-left">Platform</th>
                    <th class="text-left">URL</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $r): ?>
                <tr class="group">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-navy text-gold flex items-center justify-center flex-shrink-0">
                                <i class="fas <?= html_escape($r->icon) ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($r->platform_name) ?></p>
                                <p class="text-xs text-slate mt-0.5 line-clamp-1"><?= html_escape($r->description ?? '-') ?></p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="<?= html_escape($r->url) ?>" target="_blank" class="text-xs font-mono text-gold-muted hover:text-gold transition truncate inline-block max-w-[200px] align-middle">
                                <?= html_escape(character_limiter($r->url, 40)) ?> <i class="fas fa-external-link-alt text-[9px] ml-1"></i>
                            </a>
                            <!-- 🔥 Test URL button -->
                            <button type="button" onclick="testURL(<?= $r->id ?>, '<?= html_escape($r->url) ?>')" class="w-6 h-6 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-500 hover:text-ivory hover:border-blue-500 transition" title="Test koneksi URL">
                                <i class="fas fa-plug text-[10px]"></i>
                            </button>
                        </div>
                        <div id="testResult-<?= $r->id ?>" class="text-xs mt-1 hidden"></div>
                    </td>
                    <td class="text-center">
                        <?php if ($r->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- 🔥 Duplicate button -->
                            <a href="<?= base_url('admin/elearning/duplicate/' . $r->id) ?>" onclick="return confirm('Duplikat platform ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/elearning/edit/' . $r->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <?= form_open('admin/elearning/delete/' . $r->id, ['class' => 'inline', 'onsubmit' => "return confirm('Yakin hapus platform ini?')"]) ?>
                                <button type="submit" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                            <?= form_close() ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
(function(){
    // ===== Test URL Connection =====
    window.testURL = function(id, url){
        var result = document.getElementById('testResult-' + id);
        result.classList.remove('hidden');
        result.innerHTML = '<i class="fas fa-circle-notch fa-spin text-blue-600 mr-1"></i><span class="text-blue-600">Testing...</span>';
        
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
            if (data.ok) {
                result.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-1"></i><span class="text-green-600">OK (HTTP ' + data.code + ')</span>';
            } else {
                result.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 mr-1"></i><span class="text-red-600">' + data.message + '</span>';
            }
        })
        .catch(function(err){
            result.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 mr-1"></i><span class="text-red-600">Error: ' + err.message + '</span>';
        });
    };
})();
</script>