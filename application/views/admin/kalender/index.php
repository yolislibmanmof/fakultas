<?php
$categories = $categories ?? ['pmb' => 'PMB', 'akademik' => 'Akademik', 'ujian' => 'Ujian', 'libur' => 'Libur', 'wisuda' => 'Wisuda', 'seminar' => 'Seminar', 'lainnya' => 'Lainnya'];
$current_year = $current_year ?? date('Y');
$years = $years ?? [date('Y')];
$cat_colors = [
    'pmb' => 'bg-green-50 text-green-700',
    'akademik' => 'bg-blue-50 text-blue-700',
    'ujian' => 'bg-yellow-50 text-yellow-700',
    'libur' => 'bg-gray-100 text-gray-600',
    'wisuda' => 'bg-purple-50 text-purple-700',
    'seminar' => 'bg-teal-50 text-teal-700',
    'lainnya' => 'bg-orange-50 text-orange-700',
];
?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Academic Calendar</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Kalender <em class="italic text-gold-muted">Akademik</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Agenda PMB, akademik, ujian, dan libur.</p>
    </div>
    <div class="flex gap-2 self-start">
        <!-- 🔥 Export iCal (subscribe Google Calendar) -->
        <a href="<?= base_url('admin/kalender/ical') ?>" class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2" title="Export iCal untuk Google Calendar">
            <i class="fas fa-calendar-plus"></i>iCal
        </a>
        <a href="<?= base_url('admin/kalender/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Agenda
        </a>
    </div>
</div>

<!-- Filter: Year + Category -->
<div class="bg-white border border-gray-200 p-4 mb-6">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-xs uppercase tracking-editorial text-slate font-semibold">Tahun:</span>
            <select id="yearFilter" class="px-3 py-2 border border-navy/20 bg-white text-navy text-sm">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $current_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex-1"></div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= base_url('admin/kalender?year=' . $current_year) ?>" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 transition <?= !$cat ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">Semua</a>
            <?php foreach ($categories as $key => $label): ?>
            <a href="<?= base_url('admin/kalender?cat=' . $key . '&year=' . $current_year) ?>" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 transition <?= $cat == $key ? 'bg-gold text-navy border-gold' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Search -->
<div class="mb-6">
    <input type="text" id="searchEvents" placeholder="Cari nama agenda..." class="w-full max-w-md px-5 py-3 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
</div>

<!-- Bulk Actions -->
<form id="bulkForm" action="<?= base_url('admin/kalender/bulk_status') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate">Events <?= $current_year ?></p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= count($events) ?> Events</p>
    </div>

    <?php if (empty($events)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2">Belum ada agenda untuk tahun <?= $current_year ?>.</p>
            <a href="<?= base_url('admin/kalender/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah Agenda Pertama
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
        <table class="min-w-full refined-table" id="eventTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="name">Agenda <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-center">Kategori</th>
                    <th class="text-center">Durasi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $e):
                    $same = (date('Y-m-d', strtotime($e->start_date)) === date('Y-m-d', strtotime($e->end_date)));
                    $dur = ceil((strtotime($e->end_date) - strtotime($e->start_date)) / 86400) + 1;
                ?>
                <tr class="group event-row"
                    data-name="<?= html_escape(strtolower($e->event_name)) ?>"
                    data-start="<?= html_escape($e->start_date) ?>"
                    data-end="<?= html_escape($e->end_date) ?>"
                    data-cat="<?= html_escape($e->category) ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $e->id ?>" class="row-check accent-navy"></td>
                    <td>
                        <div class="w-14 bg-navy text-ivory text-center py-2 flex-shrink-0">
                            <div class="font-serif text-lg leading-none"><?= date('d', strtotime($e->start_date)) ?></div>
                            <div class="text-[8px] uppercase tracking-editorial text-gold mt-0.5"><?= date('M Y', strtotime($e->start_date)) ?></div>
                        </div>
                    </td>
                    <td>
                        <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($e->event_name) ?></p>
                        <p class="text-xs text-slate mt-0.5 font-mono"><?= date('d M Y', strtotime($e->start_date)) ?><?= !$same ? ' → ' . date('d M Y', strtotime($e->end_date)) : '' ?></p>
                    </td>
                    <td class="text-center">
                        <span class="<?= $cat_colors[$e->category] ?? 'bg-gray-100 text-gray-600' ?> px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold"><?= $categories[$e->category] ?? ucfirst($e->category) ?></span>
                    </td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= $dur ?> hari</span></td>
                    <td class="text-center">
                        <?php if ($e->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/kalender/duplicate/' . $e->id) ?>" onclick="return confirm('Duplikat agenda ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/kalender/edit/' . $e->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <button type="button" onclick="if(confirm('Yakin hapus agenda ini?')){ fetch('<?= base_url('admin/kalender/delete/' . $e->id) ?>', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(){ location.reload(); }).catch(function(){ alert('Gagal hapus'); }); }" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
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
    // ===== Live Search =====
    var search = document.getElementById('searchEvents');
    var rows = document.querySelectorAll('.event-row');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = rows.length;
    
    if (search) {
        search.addEventListener('input', function(){
            var q = search.value.toLowerCase();
            var shown = 0;
            rows.forEach(function(r){
                var hit = !q || r.dataset.name.indexOf(q) > -1;
                r.style.display = hit ? '' : 'none';
                if (hit) shown++;
            });
            visibleCount.textContent = shown + ' / ' + totalItems + ' Events';
        });
    }
    
    // ===== Year filter (redirect) =====
    var yearFilter = document.getElementById('yearFilter');
    if (yearFilter) {
        yearFilter.addEventListener('change', function(){
            var y = yearFilter.value;
            var cat = <?= json_encode($cat ?? '') ?>;
            var url = '<?= base_url('admin/kalender') ?>?year=' + y;
            if (cat) url += '&cat=' + cat;
            window.location.href = url;
        });
    }
    
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
        if (a === 'deactivate' && !confirm('Nonaktifkan ' + document.querySelectorAll('.row-check:checked').length + ' agenda?')) return;
        document.getElementById('bulkAction').value = a;
        document.getElementById('bulkForm').submit();
    };
    
    window.handleBulk = function(e){
        if (!document.getElementById('bulkAction').value) { e.preventDefault(); return false; }
        return true;
    };
    
    // ===== Sorting =====
    document.querySelectorAll('[data-sort]').forEach(function(th){
        th.addEventListener('click', function(){
            var col = this.dataset.sort;
            var tbody = document.querySelector('#eventTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.event-row'));
            var asc = this.classList.contains('sort-asc');
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            rs.sort(function(a, b){
                var av = a.dataset[col] || '', bv = b.dataset[col] || '';
                return asc ? bv.localeCompare(av) : av.localeCompare(bv);
            });
            rs.forEach(function(r){ tbody.appendChild(r); });
            this.classList.add(asc ? 'sort-desc' : 'sort-asc');
            this.querySelector('i').className = 'fas fa-sort-' + (asc ? 'down' : 'up') + ' text-xs ml-1';
        });
    });
})();
</script>