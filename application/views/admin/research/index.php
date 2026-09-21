<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Research Pipeline</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Riset & <em class="italic text-gold-muted">Pengabdian</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Portofolio penelitian dan pengabdian kepada masyarakat.</p>
    </div>
    <a href="<?= base_url('admin/research/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-plus"></i>Tambah Riset
    </a>
</div>

<!-- 🔥 Stats Chips (sekaligus filter) -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
    <a href="<?= base_url('admin/research') ?>" class="bg-white border-2 <?= !$type && !$status ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-navy"><?= $counts['all'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total</p>
    </a>
    <a href="<?= base_url('admin/research?type=research') ?>" class="bg-white border-2 <?= $type == 'research' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-purple-700"><?= $counts['research'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Penelitian</p>
    </a>
    <a href="<?= base_url('admin/research?type=community_service') ?>" class="bg-white border-2 <?= $type == 'community_service' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-teal-700"><?= $counts['community'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Pengabdian</p>
    </a>
    <a href="<?= base_url('admin/research?status=ongoing') ?>" class="bg-white border-2 <?= $status == 'ongoing' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-blue-700"><?= $counts['ongoing'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Berjalan</p>
    </a>
    <a href="<?= base_url('admin/research?status=completed') ?>" class="bg-white border-2 <?= $status == 'completed' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-green-700"><?= $counts['completed'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Selesai</p>
    </a>
</div>

<!-- Search + Filter + Export -->
<div class="flex flex-col md:flex-row gap-4 mb-6">
    <input type="text" id="searchResearch" value="<?= html_escape($q) ?>" placeholder="Cari judul / peneliti / sumber dana..."
        class="flex-1 max-w-md px-5 py-3 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
    
    <select id="filterYear" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
        <option value="">Semua Tahun</option>
        <?php foreach ($years as $y): ?>
        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
    </select>
    
    <select id="filterLecturer" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
        <option value="">Semua Dosen</option>
        <?php foreach ($lecturers as $l): ?>
        <option value="<?= $l->id ?>" <?= $lecturer == $l->id ? 'selected' : '' ?>><?= html_escape($l->name) ?></option>
        <?php endforeach; ?>
    </select>
    
    <!-- 🔥 Export CSV -->
    <button type="button" id="exportCSV" class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
        <i class="fas fa-file-csv"></i>Export
    </button>
</div>

<!-- Bulk Form -->
<form id="bulkForm" action="<?= base_url('admin/research/bulk_status') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate">All Research<?= $q ? ' — hasil untuk "' . html_escape($q) . '"' : '' ?></p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= count($items) ?> Projects</p>
    </div>

    <?php if (empty($items)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-flask text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2"><?= ($q || $type || $status || $year) ? 'Tidak ada riset yang cocok.' : 'Belum ada riset.' ?></p>
            <a href="<?= base_url('admin/research/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah Riset Pertama
            </a>
        </div>
    <?php else: ?>
    
    <!-- Bulk bar -->
    <div id="bulkBar" class="hidden px-6 py-3 bg-navy text-ivory flex items-center gap-4 border-b border-gold/30">
        <span class="text-sm"><strong id="bulkCount">0</strong> dipilih</span>
        <button type="button" onclick="setBulk('ongoing')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-blue-600 hover:bg-blue-500 transition">Berjalan</button>
        <button type="button" onclick="setBulk('completed')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-green-600 hover:bg-green-500 transition">Selesai</button>
        <button type="button" onclick="setBulk('proposed')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-yellow-600 hover:bg-yellow-500 transition">Diajukan</button>
        <button type="button" onclick="clearSel()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 border border-ivory/30 hover:bg-ivory/10 transition ml-auto">Batal</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="researchTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="title">Judul Riset <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Peneliti</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="year">Tahun <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Sumber Dana</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $r): ?>
                <tr class="group research-row"
                    data-title="<?= html_escape(strtolower($r->title)) ?>"
                    data-lecturer="<?= html_escape(strtolower($r->lecturer_name ?? '')) ?>"
                    data-funding="<?= html_escape(strtolower($r->funding_source ?? '')) ?>"
                    data-year="<?= (int)$r->year ?>"
                    data-type="<?= html_escape($r->type) ?>"
                    data-status="<?= html_escape($r->status) ?>"
                    data-lecturer-id="<?= (int)$r->lecturer_id ?>"
                    data-csv-title="<?= html_escape($r->title) ?>"
                    data-csv-lecturer="<?= html_escape($r->lecturer_name ?? '') ?>"
                    data-csv-type="<?= $r->type ?>"
                    data-csv-year="<?= $r->year ?>"
                    data-csv-funding="<?= html_escape($r->funding_source ?? '') ?>"
                    data-csv-amount="<?= html_escape($r->amount ?? '') ?>"
                    data-csv-status="<?= $r->status ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $r->id ?>" class="row-check accent-navy"></td>
                    <td class="max-w-xs">
                        <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($r->title) ?></p>
                    </td>
                    <td><span class="text-sm text-slate"><?= html_escape(trim(($r->title_front ?? '') . ' ' . ($r->lecturer_name ?? ''))) ?></span></td>
                    <td class="text-center">
                        <?php if ($r->type == 'research'): ?>
                            <span class="bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold">Research</span>
                        <?php else: ?>
                            <span class="bg-teal-50 text-teal-700 border border-teal-200 px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold">Community</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= $r->year ?></span></td>
                    <td>
                        <?php if ($r->funding_source): ?>
                            <span class="inline-flex items-center gap-1.5 text-xs text-gold-muted font-semibold">
                                <i class="fas fa-coins text-[10px]"></i><?= html_escape($r->funding_source) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-xs text-slate">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($r->status == 'completed'): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Selesai
                            </span>
                        <?php elseif ($r->status == 'ongoing'): ?>
                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>Berjalan
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-yellow-50 text-yellow-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Diajukan
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Export CSV per dosen -->
                            <a href="<?= base_url('admin/research/export_csv/' . $r->lecturer_id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-green-600 hover:text-ivory hover:border-green-600 transition" title="Export CSV dosen ini">
                                <i class="fas fa-file-csv text-xs"></i>
                            </a>
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/research/duplicate/' . $r->id) ?>" onclick="return confirm('Duplikat riset ini sebagai proposal baru?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/research/edit/' . $r->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <?= form_open('admin/research/delete/' . $r->id, ['class' => 'inline', 'onsubmit' => "return confirm('Yakin hapus riset ini?')"]) ?>
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
    var searchInput = document.getElementById('searchResearch');
    var filterYear = document.getElementById('filterYear');
    var filterLecturer = document.getElementById('filterLecturer');
    var rows = document.querySelectorAll('.research-row');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = rows.length;
    var currentType = <?= json_encode($type ?? '') ?>;
    var currentStatus = <?= json_encode($status ?? '') ?>;
    
    // ===== Live Search + Filter =====
    function applyFilters(){
        var q = (searchInput.value || '').toLowerCase();
        var yr = filterYear.value;
        var lec = filterLecturer.value;
        var shown = 0;
        
        rows.forEach(function(r){
            var hitQ = !q || r.dataset.title.indexOf(q) > -1 || r.dataset.lecturer.indexOf(q) > -1 || r.dataset.funding.indexOf(q) > -1;
            var hitY = !yr || r.dataset.year === yr;
            var hitL = !lec || r.dataset.lecturerId === lec;
            var hitT = !currentType || r.dataset.type === currentType;
            var hitS = !currentStatus || r.dataset.status === currentStatus;
            var show = hitQ && hitY && hitL && hitT && hitS;
            r.style.display = show ? '' : 'none';
            if (show) shown++;
        });
        visibleCount.textContent = shown + ' / ' + totalItems + ' Projects';
    }
    
    searchInput.addEventListener('input', applyFilters);
    filterYear.addEventListener('change', function(){
        window.location.href = '<?= base_url('admin/research') ?>?year=' + this.value + (currentType ? '&type=' + currentType : '') + (currentStatus ? '&status=' + currentStatus : '');
    });
    filterLecturer.addEventListener('change', function(){
        window.location.href = '<?= base_url('admin/research') ?>?lecturer=' + this.value + (currentType ? '&type=' + currentType : '') + (currentStatus ? '&status=' + currentStatus : '');
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
        checks.forEach(function(c){ 
            if (c.closest('tr').style.display !== 'none') c.checked = selectAll.checked; 
        });
        updBulk();
    });
    checks.forEach(function(c){ c.addEventListener('change', updBulk); });
    
    window.clearSel = function(){
        checks.forEach(function(c){ c.checked = false; });
        if (selectAll) selectAll.checked = false;
        updBulk();
    };
    
    window.setBulk = function(a){
        var n = document.querySelectorAll('.row-check:checked').length;
        if (!confirm('Update ' + n + ' riset ke status "' + a + '"?')) return;
        document.getElementById('bulkAction').value = a;
        document.getElementById('bulkForm').submit();
    };
    
    window.handleBulk = function(e){
        if (!document.getElementById('bulkAction').value) { e.preventDefault(); return false; }
        return true;
    };
    
    // ===== Table Sorting =====
    document.querySelectorAll('[data-sort]').forEach(function(th){
        th.addEventListener('click', function(){
            var col = this.dataset.sort;
            var tbody = document.querySelector('#researchTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.research-row'));
            var asc = this.classList.contains('sort-asc');
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            rs.sort(function(a, b){
                if (col === 'year') return asc ? (+b.dataset.year) - (+a.dataset.year) : (+a.dataset.year) - (+b.dataset.year);
                return asc ? b.dataset[col].localeCompare(a.dataset[col]) : a.dataset[col].localeCompare(b.dataset[col]);
            });
            rs.forEach(function(r){ tbody.appendChild(r); });
            this.classList.add(asc ? 'sort-desc' : 'sort-asc');
            this.querySelector('i').className = 'fas fa-sort-' + (asc ? 'down' : 'up') + ' text-xs ml-1';
        });
    });
    
    // ===== Export CSV (filtered) =====
    function csvEsc(v){ return '"' + String(v||'').replace(/"/g, '""') + '"'; }
    document.getElementById('exportCSV').addEventListener('click', function(){
        var lines = [['Judul','Peneliti','Jenis','Tahun','Sumber Dana','Jumlah Dana','Status'].join(',')];
        rows.forEach(function(r){
            if (r.style.display === 'none') return;
            lines.push([
                csvEsc(r.dataset.csvTitle), csvEsc(r.dataset.csvLecturer), csvEsc(r.dataset.csvType),
                csvEsc(r.dataset.csvYear), csvEsc(r.dataset.csvFunding), csvEsc(r.dataset.csvAmount),
                csvEsc(r.dataset.csvStatus)
            ].join(','));
        });
        var blob = new Blob([lines.join('\n')], {type: 'text/csv;charset=utf-8;'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'riset-' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
    });
})();
</script>