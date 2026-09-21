<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Resource Center</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Download <em class="italic text-gold-muted">Center</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Dokumen resmi, formulir, dan panduan akademik.</p>
    </div>
    <a href="<?= base_url('admin/documents/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-upload"></i>Upload Dokumen
    </a>
</div>

<!-- Search + Filter Chips -->
<div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
    <form action="<?= base_url('admin/documents') ?>" method="GET" class="relative flex-1 max-w-md">
        <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Cari judul / nama file..."
            class="w-full px-5 py-3 pr-12 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
        <button type="submit" class="absolute right-0 top-0 bottom-0 w-12 text-navy hover:text-gold transition"><i class="fas fa-search"></i></button>
        <?php if ($cat): ?><input type="hidden" name="cat" value="<?= html_escape($cat) ?>"><?php endif; ?>
    </form>
    <div class="flex flex-wrap items-center gap-2">
        <a href="<?= base_url('admin/documents') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$cat ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
            Semua (<?= $total_all ?>)
        </a>
        <?php foreach ($categories as $key => $label): ?>
        <a href="<?= base_url('admin/documents?cat=' . $key) ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $cat == $key ? 'bg-gold text-navy border-gold' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
            <?= $label ?> (<?= $cat_counts[$key] ?>)
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- 🔥 Bulk Actions Form -->
<form id="bulkForm" action="<?= base_url('admin/documents/bulk') ?>" method="POST" onsubmit="return handleBulkAction(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate"><?= $cat ? html_escape($categories[$cat]) : 'All Documents' ?><?= $q ? ' — hasil untuk "' . html_escape($q) . '"' : '' ?></p>
        <p class="font-mono text-xs text-slate"><?= count($documents) ?> Files</p>
    </div>

    <?php if (empty($documents)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-file-pdf text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2"><?= ($q || $cat) ? 'Tidak ada dokumen yang cocok.' : 'Belum ada dokumen.' ?></p>
            <p class="text-sm text-slate"><?= ($q || $cat) ? 'Coba kata kunci atau kategori lain.' : 'Klik "Upload Dokumen" untuk menambahkan.' ?></p>
        </div>
    <?php else: ?>
    
    <!-- 🔥 Bulk Actions Bar -->
    <div id="bulkBar" class="hidden px-6 py-3 bg-navy text-ivory flex items-center gap-4 border-b border-gold/30">
        <span class="text-sm"><strong id="bulkCount">0</strong> dipilih</span>
        <button type="button" onclick="setBulkAction('activate')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-green-600 hover:bg-green-500 transition">
            <i class="fas fa-check mr-1"></i>Aktifkan
        </button>
        <button type="button" onclick="setBulkAction('deactivate')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-yellow-600 hover:bg-yellow-500 transition">
            <i class="fas fa-eye-slash mr-1"></i>Nonaktifkan
        </button>
        <button type="button" onclick="setBulkAction('delete')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-red-600 hover:bg-red-500 transition">
            <i class="fas fa-trash mr-1"></i>Hapus
        </button>
        <button type="button" onclick="clearSelection()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 border border-ivory/30 hover:bg-ivory/10 transition ml-auto">Batal</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="docsTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy" title="Pilih semua"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="title">
                        Dokumen <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-left">Kategori</th>
                    <th class="text-right">Ukuran</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="download_count">
                        Diunduh <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $d):
                    $ext = strtolower(pathinfo($d->file_name, PATHINFO_EXTENSION));
                    $icon = $ext == 'pdf' ? 'fa-file-pdf text-red-600' : 'fa-file-word text-blue-600';
                ?>
                <tr class="group doc-row" 
                    data-title="<?= html_escape($d->title) ?>"
                    data-download="<?= (int)$d->download_count ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $d->id ?>" class="row-check accent-navy"></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 bg-ivory-warm flex items-center justify-center flex-shrink-0 text-lg">
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition truncate"><?= html_escape($d->title) ?></p>
                                <p class="text-xs text-slate mt-0.5 font-mono"><?= html_escape($d->file_name) ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-xs uppercase tracking-wider text-slate"><?= html_escape($categories[$d->category] ?? ucfirst($d->category)) ?></span></td>
                    <td class="text-right"><span class="font-mono text-xs text-slate"><?= $d->file_size ?> KB</span></td>
                    <td class="text-center">
                        <span class="inline-flex items-center gap-1.5 bg-gold/10 text-gold-muted border border-gold/20 px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold">
                            <i class="fas fa-download text-[9px]"></i><?= number_format($d->download_count) ?>x
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ($d->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- 🔥 Reset counter button -->
                            <button type="button" onclick="resetCounter(<?= $d->id ?>, '<?= html_escape($d->title) ?>')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-amber-500 hover:text-ivory hover:border-amber-500 transition" title="Reset download counter">
                                <i class="fas fa-sync-alt text-xs"></i>
                            </button>
                            <a href="<?= base_url('admin/documents/edit/' . $d->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <?= form_open('admin/documents/delete/' . $d->id, ['class' => 'inline', 'onsubmit' => "return confirm('Yakin hapus dokumen ini?')"]) ?>
                                <button type="submit" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
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
</form>

<script>
(function(){
    // ===== Bulk Select =====
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.row-check');
    var bulkBar = document.getElementById('bulkBar');
    var bulkCount = document.getElementById('bulkCount');
    
    function updateBulkBar() {
        var checked = document.querySelectorAll('.row-check:checked');
        bulkCount.textContent = checked.length;
        if (checked.length > 0) {
            bulkBar.classList.remove('hidden');
        } else {
            bulkBar.classList.add('hidden');
        }
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function(){
            checkboxes.forEach(function(cb){ cb.checked = selectAll.checked; });
            updateBulkBar();
        });
    }
    
    checkboxes.forEach(function(cb){
        cb.addEventListener('change', updateBulkBar);
    });
    
    window.clearSelection = function(){
        checkboxes.forEach(function(cb){ cb.checked = false; });
        if (selectAll) selectAll.checked = false;
        updateBulkBar();
    };
    
    window.setBulkAction = function(action){
        if (action === 'delete' && !confirm('Yakin hapus ' + document.querySelectorAll('.row-check:checked').length + ' dokumen?')) return;
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkForm').submit();
    };
    
    window.handleBulkAction = function(e){
        if (!document.getElementById('bulkAction').value) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    // ===== Reset Counter =====
    window.resetCounter = function(id, title){
        if (!confirm('Reset download counter untuk "' + title + '"?')) return;
        fetch('<?= base_url('admin/documents/reset_counter/') ?>' + id, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(function(){ 
            alert('Counter berhasil direset!');
            window.location.reload();
        })
        .catch(function(){ alert('Gagal reset counter.'); });
    };
    
    // ===== Table Sorting =====
    document.querySelectorAll('[data-sort]').forEach(function(th){
        th.addEventListener('click', function(){
            var column = this.dataset.sort;
            var tbody = document.querySelector('#docsTable tbody');
            var rows = Array.from(tbody.querySelectorAll('.doc-row'));
            var isAsc = this.classList.contains('sort-asc');
            
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            
            rows.sort(function(a, b){
                var aVal = a.dataset[column] || '';
                var bVal = b.dataset[column] || '';
                
                if (column === 'download_count') {
                    aVal = parseInt(aVal) || 0;
                    bVal = parseInt(bVal) || 0;
                    return isAsc ? bVal - aVal : aVal - bVal;
                }
                
                return isAsc ? bVal.localeCompare(aVal) : aVal.localeCompare(bVal);
            });
            
            rows.forEach(function(row){ tbody.appendChild(row); });
            
            this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
            this.querySelector('i').className = 'fas fa-sort-' + (isAsc ? 'down' : 'up') + ' text-xs ml-1';
        });
    });
})();
</script>