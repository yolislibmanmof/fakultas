<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Student Portal</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Portal <em class="italic text-gold-muted">Mahasiswa</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Layanan digital yang tampil di halaman Kemahasiswaan.</p>
    </div>
    <a href="<?= base_url('admin/portal/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-plus"></i>Tambah Layanan
    </a>
</div>

<!-- 🔥 Search + View Toggle -->
<div class="flex flex-col md:flex-row gap-4 mb-6">
    <input type="text" id="searchPortal" placeholder="Cari layanan..." 
        class="flex-1 max-w-md px-5 py-3 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
    <div class="flex items-center gap-2">
        <button type="button" id="viewTable" class="px-3 py-2 border border-navy bg-navy text-ivory text-xs uppercase tracking-editorial font-semibold" title="View Table">
            <i class="fas fa-list"></i>
        </button>
        <button type="button" id="viewGrid" class="px-3 py-2 border border-navy/20 text-navy hover:bg-navy hover:text-ivory text-xs uppercase tracking-editorial font-semibold" title="View Grid">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</div>

<!-- Bulk Form -->
<form id="bulkForm" action="<?= base_url('admin/portal/bulk_toggle') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table View -->
<div id="tableView" class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate">All Services</p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= count($items) ?> Items</p>
    </div>

    <?php if (empty($items)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-user-graduate text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2">Belum ada layanan portal.</p>
            <a href="<?= base_url('admin/portal/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah Layanan Pertama
            </a>
        </div>
    <?php else: ?>
    
    <!-- Bulk bar -->
    <div id="bulkBar" class="hidden px-6 py-3 bg-navy text-ivory flex items-center gap-4 border-b border-gold/30">
        <span class="text-sm"><strong id="bulkCount">0</strong> dipilih</span>
        <button type="button" onclick="setBulk('activate')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-green-600 hover:bg-green-500 transition">Aktifkan</button>
        <button type="button" onclick="setBulk('deactivate')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-yellow-600 hover:bg-yellow-500 transition">Nonaktifkan</button>
        <button type="button" onclick="clearSel()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 border border-ivory/30 hover:bg-ivory/10 transition ml-auto">Batal</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="portalTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="title">Layanan <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Kategori</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="sort_order">Urutan <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $r): ?>
                <tr class="group portal-row"
                    data-title="<?= html_escape(strtolower($r->title)) ?>"
                    data-sort="<?= (int)$r->sort_order ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $r->id ?>" class="row-check accent-navy"></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-<?= html_escape($r->color) ?>-50 text-<?= html_escape($r->color) ?>-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas <?= html_escape($r->icon) ?>"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($r->title) ?></p>
                                <p class="text-xs text-slate mt-0.5 truncate font-mono"><?= html_escape(character_limiter($r->link_url, 50)) ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-xs uppercase tracking-wider text-slate"><?= $cats[$r->category] ?? ucfirst($r->category) ?></span></td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= $r->sort_order ?></span></td>
                    <td class="text-center">
                        <?php if ($r->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Test URL -->
                            <button type="button" onclick="testURL(<?= $r->id ?>, '<?= html_escape($r->link_url) ?>')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-500 hover:text-ivory hover:border-blue-500 transition" title="Test URL">
                                <i class="fas fa-plug text-xs"></i>
                            </button>
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/portal/duplicate/' . $r->id) ?>" onclick="return confirm('Duplikat layanan ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/portal/edit/' . $r->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <?= form_open('admin/portal/delete/' . $r->id, ['class' => 'inline', 'onsubmit' => "return confirm('Yakin hapus layanan ini?')"]) ?>
                                <button type="submit" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                            <?= form_close() ?>
                        </div>
                        <div id="testResult-<?= $r->id ?>" class="text-xs mt-1 hidden"></div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- 🔥 Grid View (card-based) -->
<div id="gridView" class="hidden">
    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-4" id="portalGrid">
        <?php foreach ($items as $r): ?>
        <div class="portal-card bg-white border border-gray-200 overflow-hidden hover:shadow-lg hover:border-gold/30 transition"
             data-title="<?= html_escape(strtolower($r->title)) ?>">
            <div class="bg-<?= html_escape($r->color) ?>-50 p-6 text-center">
                <div class="w-16 h-16 bg-<?= html_escape($r->color) ?>-100 text-<?= html_escape($r->color) ?>-600 flex items-center justify-center rounded-full mx-auto mb-3 text-2xl">
                    <i class="fas <?= html_escape($r->icon) ?>"></i>
                </div>
                <h3 class="font-serif text-lg font-medium text-navy mb-1"><?= html_escape($r->title) ?></h3>
                <p class="text-xs text-slate"><?= $cats[$r->category] ?? ucfirst($r->category) ?></p>
            </div>
            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-slate">Sort: <?= $r->sort_order ?></span>
                    <?php if ($r->is_active): ?>
                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2 py-0.5 text-[10px] uppercase tracking-wider font-semibold rounded">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 px-2 py-0.5 text-[10px] uppercase tracking-wider font-semibold rounded">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                        </span>
                    <?php endif; ?>
                </div>
                <div class="flex gap-1">
                    <a href="<?= base_url('admin/portal/edit/' . $r->id) ?>" class="flex-1 text-center py-1.5 border border-gray-200 text-slate text-xs hover:bg-navy hover:text-ivory transition">Edit</a>
                    <a href="<?= html_escape($r->link_url) ?>" target="_blank" class="flex-1 text-center py-1.5 border border-gray-200 text-slate text-xs hover:bg-gold hover:text-navy transition">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</form>

<script>
(function(){
    // ===== View Toggle =====
    var viewTable = document.getElementById('viewTable');
    var viewGrid = document.getElementById('viewGrid');
    var tableView = document.getElementById('tableView');
    var gridView = document.getElementById('gridView');
    
    viewTable.addEventListener('click', function(){
        tableView.classList.remove('hidden');
        gridView.classList.add('hidden');
        viewTable.classList.add('bg-navy', 'text-ivory');
        viewTable.classList.remove('border-navy/20', 'text-navy');
        viewGrid.classList.remove('bg-navy', 'text-ivory');
        viewGrid.classList.add('border-navy/20', 'text-navy');
    });
    
    viewGrid.addEventListener('click', function(){
        gridView.classList.remove('hidden');
        tableView.classList.add('hidden');
        viewGrid.classList.add('bg-navy', 'text-ivory');
        viewGrid.classList.remove('border-navy/20', 'text-navy');
        viewTable.classList.remove('bg-navy', 'text-ivory');
        viewTable.classList.add('border-navy/20', 'text-navy');
    });
    
    // ===== Live Search =====
    var searchInput = document.getElementById('searchPortal');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = document.querySelectorAll('.portal-row').length;
    
    searchInput.addEventListener('input', function(){
        var q = searchInput.value.toLowerCase();
        var shown = 0;
        document.querySelectorAll('.portal-row').forEach(function(r){
            var hit = !q || r.dataset.title.indexOf(q) > -1;
            r.style.display = hit ? '' : 'none';
            if (hit) shown++;
        });
        document.querySelectorAll('.portal-card').forEach(function(c){
            var hit = !q || c.dataset.title.indexOf(q) > -1;
            c.style.display = hit ? '' : 'none';
        });
        visibleCount.textContent = shown + ' / ' + totalItems + ' Items';
    });
    
    // ===== Bulk Select =====
    var selectAll = document.getElementById('selectAll');
    var checks = document.querySelectorAll('.row-check');
    var bulkBar = document.getElementById('bulkBar');
    var bulkCount = document.getElementById('bulkCount');
    
    function updBulk(){
        var n = document.querySelectorAll('.row-check:checked').length;
        bulkCount.textContent = n;
        bulkBar.classList.toggle('hidden', n === 0);
    }
    if (selectAll) selectAll.addEventListener('change', function(){
        checks.forEach(function(c){ c.checked = selectAll.checked; });
        updBulk();
    });
    checks.forEach(function(c){ c.addEventListener('change', updBulk); });
    
    window.clearSel = function(){
        checks.forEach(function(c){ c.checked = false; });
        if (selectAll) selectAll.checked = false;
        updBulk();
    };
    
    window.setBulk = function(a){
        if (a === 'deactivate' && !confirm('Nonaktifkan ' + document.querySelectorAll('.row-check:checked').length + ' layanan?')) return;
        document.getElementById('bulkAction').value = a;
        document.getElementById('bulkForm').submit();
    };
    
    window.handleBulk = function(e){
        if (!document.getElementById('bulkAction').value) { e.preventDefault(); return false; }
        return true;
    };
    
    // ===== Test URL =====
    window.testURL = function(id, url){
        var result = document.getElementById('testResult-' + id);
        result.classList.remove('hidden');
        result.innerHTML = '<i class="fas fa-circle-notch fa-spin text-blue-600 mr-1"></i><span class="text-blue-600">Testing...</span>';
        
        fetch('<?= base_url('admin/portal/test_url') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
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
            result.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 mr-1"></i><span class="text-red-600">Error</span>';
        });
    };
    
    // ===== Table Sorting =====
    document.querySelectorAll('[data-sort]').forEach(function(th){
        th.addEventListener('click', function(){
            var col = this.dataset.sort;
            var tbody = document.querySelector('#portalTable tbody');
            var rows = Array.from(tbody.querySelectorAll('.portal-row'));
            var asc = this.classList.contains('sort-asc');
            
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            
            rows.sort(function(a, b){
                var av = a.dataset[col] || '', bv = b.dataset[col] || '';
                if (col === 'sort_order') {
                    av = parseInt(av) || 0; bv = parseInt(bv) || 0;
                    return asc ? av - bv : bv - av;
                }
                return asc ? av.localeCompare(bv) : bv.localeCompare(av);
            });
            
            rows.forEach(function(row){ tbody.appendChild(row); });
            this.classList.add(asc ? 'sort-desc' : 'sort-asc');
            this.querySelector('i').className = 'fas fa-sort-' + (asc ? 'down' : 'up') + ' text-xs ml-1';
        });
    });
})();
</script>