<?php
$labels = [
    'laboratory' => ['Laboratorium', 'fa-flask'],
    'classroom'  => ['Ruang Kelas', 'fa-chalkboard'],
    'library'    => ['Perpustakaan', 'fa-book-reader'],
    'mosque'     => ['Tempat Ibadah', 'fa-mosque'],
    'sport'      => ['Olahraga', 'fa-running'],
    'other'      => ['Lainnya', 'fa-building'],
];
$type_counts = [];
foreach ($facilities as $f) {
    $type_counts[$f->type] = ($type_counts[$f->type] ?? 0) + 1;
}
$filter_type = $filter_type ?? null;
?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Campus Facilities</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Manajemen <em class="italic text-gold-muted">Fasilitas</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Sarana dan prasarana kampus dengan galeri foto.</p>
    </div>
    <div class="flex gap-2 self-start">
        <a href="<?= base_url('admin/facilities/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Fasilitas
        </a>
    </div>
</div>

<!-- Stats per tipe (sekaligus filter) -->
<div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
    <a href="<?= base_url('admin/facilities') ?>" class="bg-white border-2 <?= !$filter_type ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <i class="fas fa-layer-group text-navy/60 mb-2"></i>
        <div class="font-serif text-2xl text-navy"><?= count($facilities) ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total</p>
    </a>
    <?php foreach ($labels as $key => $info): ?>
    <a href="<?= base_url('admin/facilities?type=' . $key) ?>" class="bg-white border-2 <?= $filter_type == $key ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <i class="fas <?= $info[1] ?> text-gold-muted mb-2"></i>
        <div class="font-serif text-2xl text-navy"><?= $type_counts[$key] ?? 0 ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1"><?= $info[0] ?></p>
    </a>
    <?php endforeach; ?>
</div>

<!-- Search -->
<div class="mb-6">
    <input type="text" id="searchFacilities" placeholder="Cari nama / lokasi fasilitas..." class="w-full max-w-md px-5 py-3 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
</div>

<!-- Bulk Actions Form -->
<form id="bulkForm" action="<?= base_url('admin/facilities/bulk_toggle') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate"><?= $filter_type ? html_escape($labels[$filter_type][0] ?? 'Filter') : 'All Facilities' ?></p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= count($facilities) ?> Items</p>
    </div>

    <?php if (empty($facilities)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-building text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2">Belum ada fasilitas.</p>
            <a href="<?= base_url('admin/facilities/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2.5 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-2"></i>Tambah Fasilitas Pertama
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
        <table class="min-w-full refined-table" id="facTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="name">Fasilitas <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Tipe</th>
                    <th class="text-left">Lokasi</th>
                    <th class="text-center">Kapasitas</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facilities as $f):
                    $info = $labels[$f->type] ?? $labels['other'];
                    $imgs = $f->images ? json_decode($f->images, true) : [];
                    $thumb = (is_array($imgs) && !empty($imgs)) ? $imgs[0] : NULL;
                    $photo_count = is_array($imgs) ? count($imgs) : 0;
                ?>
                <tr class="group fac-row"
                    data-name="<?= html_escape(strtolower($f->name)) ?>"
                    data-location="<?= html_escape(strtolower($f->location ?? '')) ?>"
                    data-type="<?= html_escape($f->type) ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $f->id ?>" class="row-check accent-navy"></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-200 flex-shrink-0 overflow-hidden">
                                <?php if ($thumb): ?>
                                    <img src="<?= base_url('assets/uploads/' . $thumb) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center"><i class="fas <?= $info[1] ?> text-slate/40"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($f->name) ?></p>
                                <p class="text-xs text-slate mt-0.5 line-clamp-1"><?= html_escape(character_limiter($f->description ?? '-', 60)) ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-xs uppercase tracking-wider text-slate"><i class="fas <?= $info[1] ?> text-gold-muted mr-1"></i><?= $info[0] ?></span></td>
                    <td><span class="text-xs text-slate"><?= html_escape($f->location ?? '-') ?></span></td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= $f->capacity ?? '-' ?></span></td>
                    <td class="text-center">
                        <span class="inline-flex items-center gap-1 text-xs text-slate"><i class="fas fa-images text-gold-muted"></i><?= $photo_count ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($f->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="<?= base_url('admin/facilities/duplicate/' . $f->id) ?>" onclick="return confirm('Duplikat fasilitas ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/facilities/edit/' . $f->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <?= form_open('admin/facilities/delete/' . $f->id, ['class' => 'inline', 'onsubmit' => "return confirm('Yakin hapus fasilitas ini?')"]) ?>
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
</form>

<script>
(function(){
    // ===== Live Search =====
    var search = document.getElementById('searchFacilities');
    var rows = document.querySelectorAll('.fac-row');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = rows.length;
    
    if (search) {
        search.addEventListener('input', function(){
            var q = search.value.toLowerCase();
            var shown = 0;
            rows.forEach(function(r){
                var hit = !q || r.dataset.name.indexOf(q) > -1 || r.dataset.location.indexOf(q) > -1;
                r.style.display = hit ? '' : 'none';
                if (hit) shown++;
            });
            visibleCount.textContent = shown + ' / ' + totalItems + ' Items';
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
        if (a === 'deactivate' && !confirm('Nonaktifkan ' + document.querySelectorAll('.row-check:checked').length + ' fasilitas?')) return;
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
            var tbody = document.querySelector('#facTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.fac-row'));
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