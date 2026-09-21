<?php
$total      = count($lecturers);
$active     = count(array_filter($lecturers, function ($l) { return $l->is_active; }));
$with_photo = count(array_filter($lecturers, function ($l) { return !empty($l->photo); }));
$filter_active = $active ?? null;
$q = $q ?? '';
?>
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Faculty Directory</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Manajemen <em class="italic text-gold-muted">Dosen</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Data tenaga pendidik dan profil akademik.</p>
    </div>
    <div class="flex gap-2 self-start">
        <!-- Export CSV -->
        <button type="button" id="exportCSV" class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-file-csv"></i>Export
        </button>
        <a href="<?= base_url('admin/lecturers/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-user-plus"></i>Tambah Dosen
        </a>
    </div>
</div>

<!-- Stats Chips (sekaligus filter) -->
<div class="grid grid-cols-3 gap-3 mb-6">
    <a href="<?= base_url('admin/lecturers') ?>" class="bg-white border-2 <?= $filter_active === null ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-navy"><?= $total ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total Dosen</p>
    </a>
    <a href="<?= base_url('admin/lecturers?active=1') ?>" class="bg-white border-2 <?= $filter_active === '1' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-green-700"><?= $active ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Aktif</p>
    </a>
    <a href="<?= base_url('admin/lecturers?active=0') ?>" class="bg-white border-2 <?= $filter_active === '0' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-gray-600"><?= $total - $active ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Nonaktif</p>
    </a>
</div>

<!-- Search -->
<div class="mb-6">
    <input type="text" id="searchInput" value="<?= html_escape($q) ?>" placeholder="Cari nama, NIDN, email, keahlian..."
        class="w-full max-w-md px-5 py-3 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
</div>

<!-- Bulk Form -->
<form id="bulkForm" action="<?= base_url('admin/lecturers/bulk_toggle') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate"><?= $filter_active === '1' ? 'Active' : ($filter_active === '0' ? 'Inactive' : 'All') ?> Lecturers</p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= $total ?> Faculty Members</p>
    </div>

    <?php if (empty($lecturers)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-user-tie text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2">Belum ada dosen.</p>
            <a href="<?= base_url('admin/lecturers/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah Dosen Pertama
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
        <table class="min-w-full refined-table" id="lecTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="name">Dosen <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">NIDN</th>
                    <th class="text-left">Keahlian</th>
                    <th class="text-left">Program Studi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lecturers as $l): ?>
                <tr class="group lec-row"
                    data-name="<?= html_escape(strtolower($l->name)) ?>"
                    data-nidn="<?= html_escape(strtolower($l->nidn)) ?>"
                    data-email="<?= html_escape(strtolower($l->email ?? '')) ?>"
                    data-expertise="<?= html_escape(strtolower($l->expertise ?? '')) ?>"
                    data-active="<?= $l->is_active ? '1' : '0' ?>"
                    data-csv-name="<?= html_escape($l->name) ?>"
                    data-csv-nidn="<?= html_escape($l->nidn) ?>"
                    data-csv-email="<?= html_escape($l->email ?? '') ?>"
                    data-csv-phone="<?= html_escape($l->phone ?? '') ?>"
                    data-csv-expertise="<?= html_escape($l->expertise ?? '') ?>"
                    data-csv-prodi="<?= html_escape($l->prodi_names) ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $l->id ?>" class="row-check accent-navy"></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <?php if ($l->photo): ?>
                                <img src="<?= base_url('assets/uploads/' . $l->photo) ?>" class="w-11 h-11 rounded-full object-cover flex-shrink-0 border-2 border-gold/30" alt="">
                            <?php else: ?>
                                <div class="w-11 h-11 rounded-full bg-navy text-gold flex items-center justify-center flex-shrink-0 font-serif font-bold">
                                    <?= strtoupper(substr($l->name, 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition truncate">
                                    <?= html_escape(trim($l->title_front . ' ' . $l->name)) ?><?= $l->title_back ? ', ' . html_escape($l->title_back) : '' ?>
                                </p>
                                <p class="text-xs text-slate mt-0.5"><?= html_escape($l->email ?? '-') ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="font-mono text-xs text-slate"><?= html_escape($l->nidn) ?></span></td>
                    <td><span class="text-sm text-navy"><?= html_escape(character_limiter($l->expertise ?? '-', 40)) ?></span></td>
                    <td><span class="text-xs text-slate"><?= html_escape($l->prodi_names) ?></span></td>
                    <td class="text-center">
                        <?php if ($l->is_active): ?>
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
                            <!-- vCard export -->
                            <a href="<?= base_url('admin/lecturers/vcard/' . $l->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-green-600 hover:text-ivory hover:border-green-600 transition" title="Download vCard">
                                <i class="fas fa-address-card text-xs"></i>
                            </a>
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/lecturers/duplicate/' . $l->id) ?>" onclick="return confirm('Duplikat dosen ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/lecturers/edit/' . $l->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <?= form_open('admin/lecturers/delete/' . $l->id, ['class' => 'inline', 'onsubmit' => "return confirm('Yakin hapus dosen ini?')"]) ?>
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
    // ===== Live Search =====
    var searchInput = document.getElementById('searchInput');
    var rows = document.querySelectorAll('.lec-row');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = rows.length;
    
    var debounce;
    searchInput.addEventListener('input', function(){
        clearTimeout(debounce);
        debounce = setTimeout(function(){
            var q = searchInput.value.toLowerCase();
            var shown = 0;
            rows.forEach(function(r){
                var hit = !q || 
                    r.dataset.name.indexOf(q) > -1 || 
                    r.dataset.nidn.indexOf(q) > -1 || 
                    r.dataset.email.indexOf(q) > -1 || 
                    r.dataset.expertise.indexOf(q) > -1;
                r.style.display = hit ? '' : 'none';
                if (hit) shown++;
            });
            visibleCount.textContent = shown + ' / ' + totalItems + ' Faculty Members';
        }, 150);
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
        if (a === 'deactivate' && !confirm('Nonaktifkan ' + n + ' dosen?')) return;
        if (a === 'activate' && !confirm('Aktifkan ' + n + ' dosen?')) return;
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
            var tbody = document.querySelector('#lecTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.lec-row'));
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
    
    // ===== Export CSV =====
    document.getElementById('exportCSV').addEventListener('click', function(){
        var lines = [['Nama','NIDN','Email','No. HP','Keahlian','Prodi'].join(',')];
        rows.forEach(function(r){
            if (r.style.display === 'none') return;
            lines.push([
                '"' + (r.dataset.csvName || '').replace(/"/g, '""') + '"',
                '"' + (r.dataset.csvNidn || '').replace(/"/g, '""') + '"',
                '"' + (r.dataset.csvEmail || '').replace(/"/g, '""') + '"',
                '"' + (r.dataset.csvPhone || '').replace(/"/g, '""') + '"',
                '"' + (r.dataset.csvExpertise || '').replace(/"/g, '""') + '"',
                '"' + (r.dataset.csvProdi || '').replace(/"/g, '""') + '"'
            ].join(','));
        });
        var blob = new Blob([lines.join('\n')], {type: 'text/csv;charset=utf-8;'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'dosen-' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
    });
})();
</script>