<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Course Catalogue</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Mata <em class="italic text-gold-muted">Kuliah</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Kurikulum per semester untuk setiap program studi.</p>
    </div>
    <div class="flex gap-2 self-start">
        <!-- 🔥 Export CSV -->
        <a href="<?= base_url('admin/courses/export_csv' . ($q || $prodi ? '?' . http_build_query(array_filter(['q' => $q, 'prodi' => $prodi])) : '')) ?>" 
           class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2" title="Export ke CSV">
            <i class="fas fa-file-csv"></i>
        </a>
        <a href="<?= base_url('admin/courses/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah MK
        </a>
    </div>
</div>

<!-- Search + Filter -->
<div class="flex flex-col md:flex-row gap-4 mb-6">
    <form action="<?= base_url('admin/courses') ?>" method="GET" class="relative flex-1 max-w-md">
        <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Cari kode / nama MK..."
            class="w-full px-5 py-3 pr-12 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
        <button type="submit" class="absolute right-0 top-0 bottom-0 w-12 text-navy hover:text-gold transition"><i class="fas fa-search"></i></button>
        <?php if ($prodi): ?><input type="hidden" name="prodi" value="<?= html_escape($prodi) ?>"><?php endif; ?>
    </form>
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('admin/courses') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$prodi ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">Semua Prodi</a>
        <?php foreach ($programs as $p): ?>
        <a href="<?= base_url('admin/courses?prodi=' . $p->id) ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $prodi == $p->id ? 'bg-gold text-navy border-gold' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
            <?= html_escape(character_limiter($p->name, 20)) ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- 🔥 Bulk action form -->
<form id="bulkForm" action="<?= base_url('admin/courses/bulk_delete') ?>" method="POST" onsubmit="return confirm('Yakin hapus ' + document.querySelectorAll('.row-check:checked').length + ' mata kuliah?')">
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-4">
            <p class="editorial-label text-slate">All Courses</p>
            <p class="font-mono text-xs text-slate"><?= count($courses) ?> Courses</p>
        </div>
        
        <!-- 🔥 Bulk actions bar -->
        <div id="bulkActions" class="flex items-center gap-2 opacity-50 pointer-events-none transition-all">
            <span class="text-xs text-slate"><span id="selectedCount">0</span> dipilih</span>
            <button type="submit" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition">
                <i class="fas fa-trash mr-1"></i>Hapus
            </button>
            <button type="button" onclick="clearSelection()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100 transition">
                Batal
            </button>
        </div>
    </div>

    <?php if (empty($courses)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-book text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2"><?= ($q || $prodi) ? 'Tidak ada MK yang cocok.' : 'Belum ada mata kuliah.' ?></p>
            <?php if (!$q && !$prodi): ?>
            <a href="<?= base_url('admin/courses/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah MK Pertama
            </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="coursesTable">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" id="selectAll" class="accent-navy" title="Pilih semua">
                    </th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="course_code">
                        Kode <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="name">
                        Nama Mata Kuliah <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-left">Prodi</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="semester">
                        Smt <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="sks">
                        SKS <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Silabus</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                <tr class="group course-row" 
                    data-code="<?= html_escape($c->course_code) ?>"
                    data-name="<?= html_escape($c->name) ?>"
                    data-semester="<?= (int)$c->semester ?>"
                    data-sks="<?= (int)$c->sks ?>">
                    <td>
                        <input type="checkbox" name="ids[]" value="<?= $c->id ?>" class="row-check accent-navy">
                    </td>
                    <td><span class="font-mono text-xs text-gold-muted font-semibold"><?= html_escape($c->course_code) ?></span></td>
                    <td>
                        <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($c->name) ?></p>
                        <p class="text-xs text-slate mt-0.5 line-clamp-1"><?= html_escape(character_limiter($c->description ?? '-', 60)) ?></p>
                    </td>
                    <td><span class="text-xs text-slate"><?= html_escape($c->prodi_names) ?></span></td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= $c->semester ?></span></td>
                    <td class="text-center"><span class="bg-navy text-ivory text-xs px-2.5 py-1 font-semibold"><?= $c->sks ?></span></td>
                    <td class="text-center">
                        <span class="<?= $c->course_type == 'wajib' ? 'bg-blue-50 text-blue-700' : 'bg-teal-50 text-teal-700' ?> px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold"><?= ucfirst($c->course_type) ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($c->syllabus_file): ?>
                            <a href="<?= base_url('assets/uploads/syllabus/' . $c->syllabus_file) ?>" target="_blank" class="inline-flex items-center gap-1 text-gold-muted hover:text-gold text-xs font-semibold" title="Unduh silabus">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- 🔥 Duplicate button -->
                            <a href="<?= base_url('admin/courses/duplicate/' . $c->id) ?>" onclick="return confirm('Duplikat MK ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/courses/edit/' . $c->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
<button type="button" onclick="if(confirm('Yakin hapus MK ini?')){ fetch('<?= base_url('admin/courses/delete/' . $c->id) ?>', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(){ location.reload(); }).catch(function(){ alert('Gagal hapus'); }); }" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
    <i class="fas fa-trash text-xs"></i>
</button>
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
    // ===== Bulk select =====
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.row-check');
    var bulkActions = document.getElementById('bulkActions');
    var selectedCount = document.getElementById('selectedCount');

    function updateBulkActions() {
        var checked = document.querySelectorAll('.row-check:checked');
        selectedCount.textContent = checked.length;
        if (checked.length > 0) {
            bulkActions.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            bulkActions.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb){ cb.checked = selectAll.checked; });
            updateBulkActions();
        });
    }

    checkboxes.forEach(function(cb){
        cb.addEventListener('change', updateBulkActions);
    });

    window.clearSelection = function() {
        checkboxes.forEach(function(cb){ cb.checked = false; });
        if (selectAll) selectAll.checked = false;
        updateBulkActions();
    };

    // ===== Table sorting =====
    document.querySelectorAll('[data-sort]').forEach(function(th){
        th.addEventListener('click', function(){
            var column = this.dataset.sort;
            var table = document.getElementById('coursesTable');
            var tbody = table.querySelector('tbody');
            var rows = Array.from(tbody.querySelectorAll('tr'));
            
            var isAsc = this.classList.contains('sort-asc');
            
            // Reset all headers
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            
            // Sort rows
            rows.sort(function(a, b){
                var aVal = a.dataset[column] || '';
                var bVal = b.dataset[column] || '';
                
                if (column === 'semester' || column === 'sks') {
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