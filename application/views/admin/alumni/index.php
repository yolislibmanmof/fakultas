<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Alumni Network</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Manajemen <em class="italic text-gold-muted">Alumni</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Verifikasi dan kelola profil alumni direktori.</p>
    </div>
    <a href="<?= base_url('admin/alumni/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-user-plus"></i>Tambah Alumni
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <a href="<?= base_url('admin/alumni') ?>" class="mission-card bg-white p-5 text-center <?= !$status ? 'border-gold' : '' ?>">
        <div class="font-serif text-3xl font-light text-navy"><?= $counts['all'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total</p>
    </a>
    <a href="<?= base_url('admin/alumni?status=pending') ?>" class="mission-card bg-white p-5 text-center <?= $status == 'pending' ? 'border-gold' : '' ?>">
        <div class="font-serif text-3xl font-light text-yellow-600"><?= $counts['pending'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Pending</p>
    </a>
    <a href="<?= base_url('admin/alumni?status=approved') ?>" class="mission-card bg-white p-5 text-center <?= $status == 'approved' ? 'border-gold' : '' ?>">
        <div class="font-serif text-3xl font-light text-green-600"><?= $counts['approved'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Approved</p>
    </a>
    <a href="<?= base_url('admin/alumni?status=rejected') ?>" class="mission-card bg-white p-5 text-center <?= $status == 'rejected' ? 'border-gold' : '' ?>">
        <div class="font-serif text-3xl font-light text-red-500"><?= $counts['rejected'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Rejected</p>
    </a>
</div>

<!-- Search + Filter + Export -->
<div class="flex flex-col md:flex-row gap-3 mb-6">
    <form action="<?= base_url('admin/alumni') ?>" method="GET" class="relative flex-1 max-w-md">
        <input type="text" name="q" id="alumniSearch" value="<?= html_escape($q) ?>" placeholder="Cari nama, perusahaan, email..."
            class="w-full px-5 py-3 pr-12 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
        <button type="submit" class="absolute right-0 top-0 bottom-0 w-12 text-navy hover:text-gold transition"><i class="fas fa-search"></i></button>
        <?php if ($status): ?><input type="hidden" name="status" value="<?= html_escape($status) ?>"><?php endif; ?>
    </form>

    <!-- 🔥 Filter client-side: Tahun & Prodi -->
    <select id="filterYear" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
        <option value="">Semua Tahun</option>
    </select>
    <select id="filterProdi" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
        <option value="">Semua Prodi</option>
    </select>
    <select id="sortBy" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
        <option value="newest">Terbaru</option>
        <option value="name">Nama A-Z</option>
        <option value="year_desc">Tahun ↓</option>
        <option value="year_asc">Tahun ↑</option>
    </select>
    <!-- 🔥 Export CSV (client-side) -->
    <button type="button" id="exportCsv" class="btn-outline-navy px-5 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
        <i class="fas fa-file-csv"></i>Export
    </button>
</div>

<!-- 🔥 Bulk action bar -->
<form id="bulkForm" action="<?= base_url('admin/alumni/bulk_status') ?>" method="POST"
      onsubmit="return confirm('Terapkan aksi ke ' + document.querySelectorAll('.row-check:checked').length + ' alumni?')">
    <input type="hidden" name="return_status" value="<?= html_escape($status) ?>">
    <div id="bulkBar" class="hidden mb-4 bg-navy text-ivory px-5 py-3 flex flex-wrap items-center gap-3">
        <span class="text-sm"><strong id="bulkCount">0</strong> dipilih</span>
        <input type="hidden" name="action" id="bulkAction" value="approve">
        <button type="submit" onclick="document.getElementById('bulkAction').value='approve'" class="text-xs uppercase tracking-editorial font-bold px-4 py-2 bg-green-600 hover:bg-green-500 transition">
            <i class="fas fa-check mr-1"></i>Approve
        </button>
        <button type="submit" onclick="document.getElementById('bulkAction').value='reject'" class="text-xs uppercase tracking-editorial font-bold px-4 py-2 bg-yellow-600 hover:bg-yellow-500 transition">
            <i class="fas fa-times mr-1"></i>Reject
        </button>
        <button type="button" id="bulkClear" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 border border-ivory/30 hover:bg-ivory/10 transition ml-auto">Batal</button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full refined-table" id="alumniTable">
                <thead>
                    <tr>
                        <th class="w-10"><input type="checkbox" id="checkAll" class="accent-navy" title="Pilih semua"></th>
                        <th class="text-left">Alumni</th>
                        <th class="text-left">Kelas</th>
                        <th class="text-left">Karir</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="alumniBody">
                    <?php if (empty($alumni)): ?>
                    <tr><td colspan="6" class="text-center py-12 text-slate">Belum ada data alumni.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($alumni as $a): ?>
                    <tr class="alumni-row group"
                        data-name="<?= html_escape(strtolower($a->full_name)) ?>"
                        data-email="<?= html_escape(strtolower($a->email)) ?>"
                        data-company="<?= html_escape(strtolower($a->company ?? '')) ?>"
                        data-year="<?= (int)$a->graduation_year ?>"
                        data-prodi="<?= html_escape($a->prodi_name ?? '') ?>"
                        data-status="<?= html_escape($a->status) ?>"
                        data-created="<?= html_escape($a->created_at ?? '') ?>"
                        data-csv-name="<?= html_escape($a->full_name) ?>"
                        data-csv-email="<?= html_escape($a->email) ?>"
                        data-csv-nim="<?= html_escape($a->nim ?? '') ?>"
                        data-csv-position="<?= html_escape($a->current_position ?? '') ?>"
                        data-csv-company="<?= html_escape($a->company ?? '') ?>"
                        data-csv-city="<?= html_escape($a->city ?? '') ?>"
                        data-csv-country="<?= html_escape($a->country ?? '') ?>">
                        <td><input type="checkbox" name="ids[]" value="<?= $a->id ?>" class="row-check accent-navy"></td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full overflow-hidden bg-navy text-gold flex items-center justify-center font-serif flex-shrink-0 border border-gold/30">
                                    <?php if ($a->photo): ?><img src="<?= base_url('assets/uploads/' . $a->photo) ?>" class="w-full h-full object-cover" alt=""><?php else: ?><?= strtoupper(substr($a->full_name, 0, 1)) ?><?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($a->full_name) ?></p>
                                    <p class="text-xs text-slate truncate"><?= html_escape($a->email) ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="bg-navy text-ivory text-[10px] uppercase tracking-editorial px-2.5 py-1 font-semibold"><?= $a->graduation_year ?></span>
                            <p class="text-xs text-slate mt-1"><?= html_escape($a->prodi_name ?? '-') ?></p>
                        </td>
                        <td class="max-w-[200px]">
                            <p class="text-sm text-navy truncate"><?= html_escape($a->current_position ?? '-') ?></p>
                            <p class="text-xs text-slate truncate"><?= html_escape($a->company ?? '') ?> <?= $a->city ? '• ' . html_escape($a->city) : '' ?></p>
                        </td>
                        <td class="text-center">
                            <?php if ($a->status == 'approved'): ?>
                                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Approved</span>
                            <?php elseif ($a->status == 'pending'): ?>
                                <span class="inline-flex items-center gap-1.5 bg-yellow-50 text-yellow-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Pending</span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <!-- 🔥 Lihat profil publik -->
                                <?php if ($a->status == 'approved'): ?>
                                <a href="<?= base_url('alumni/view/' . $a->id) ?>" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-gold hover:text-navy hover:border-gold transition" title="Lihat profil publik">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                                <?php endif; ?>
                                <!-- 🔥 FIX: Approve/Reject via POST (bukan GET link) -->
                                <?php if ($a->status != 'approved'): ?>
                                <?= form_open('admin/alumni/approve/' . $a->id, ['class' => 'inline']) ?>
                                    <button type="submit" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-green-600 hover:bg-green-600 hover:text-ivory hover:border-green-600 transition" title="Approve">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                <?= form_close() ?>
                                <?php endif; ?>
                                <?php if ($a->status != 'rejected'): ?>
                                <?= form_open('admin/alumni/reject/' . $a->id, ['class' => 'inline', 'onsubmit' => "return confirm('Tolak profil ini?')"]) ?>
                                    <button type="submit" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-yellow-600 hover:bg-yellow-500 hover:text-ivory hover:border-yellow-500 transition" title="Reject">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                <?= form_close() ?>
                                <?php endif; ?>
                                <a href="<?= base_url('admin/alumni/edit/' . $a->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <button type="button" onclick="if(confirm('Yakin hapus alumni ini? Profil dan foto akan hilang permanen.')){ fetch('<?= base_url('admin/alumni/delete/' . $a->id) ?>', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(){ location.reload(); }).catch(function(){ alert('Gagal hapus'); }); }" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-ivory-warm/40 border-t border-gray-200 text-xs text-slate">
            Menampilkan <strong id="visibleCount"><?= count($alumni) ?></strong> dari <?= count($alumni) ?> alumni
        </div>
    </div>
</form>

<script>
(function(){
    var rows = Array.prototype.slice.call(document.querySelectorAll('.alumni-row'));
    var yearSel = document.getElementById('filterYear');
    var prodiSel = document.getElementById('filterProdi');
    var sortBy = document.getElementById('sortBy');
    var visibleCount = document.getElementById('visibleCount');

    // ===== Populate filter options dari data rows =====
    var years = [], prodis = [];
    rows.forEach(function(r){
        if (r.dataset.year && years.indexOf(r.dataset.year) === -1) years.push(r.dataset.year);
        if (r.dataset.prodi && prodis.indexOf(r.dataset.prodi) === -1) prodis.push(r.dataset.prodi);
    });
    years.sort(function(a,b){ return b - a; });
    prodis.sort();
    years.forEach(function(y){ var o = document.createElement('option'); o.value = y; o.textContent = y; yearSel.appendChild(o); });
    prodis.forEach(function(p){ var o = document.createElement('option'); o.value = p; o.textContent = p; prodiSel.appendChild(o); });

    // ===== Live filter =====
    function applyFilters() {
        var q = (document.getElementById('alumniSearch').value || '').toLowerCase();
        var yr = yearSel.value, pr = prodiSel.value;
        var shown = 0;
        rows.forEach(function(r){
            var hitQ = !q || r.dataset.name.indexOf(q) > -1 || r.dataset.email.indexOf(q) > -1 || r.dataset.company.indexOf(q) > -1;
            var hitY = !yr || r.dataset.year === yr;
            var hitP = !pr || r.dataset.prodi === pr;
            var show = hitQ && hitY && hitP;
            r.style.display = show ? '' : 'none';
            if (show) shown++;
        });
        visibleCount.textContent = shown;
    }
    yearSel.addEventListener('change', applyFilters);
    prodiSel.addEventListener('change', applyFilters);
    document.getElementById('alumniSearch').addEventListener('input', applyFilters);

    // ===== Sorting =====
    sortBy.addEventListener('change', function(){
        var tbody = document.getElementById('alumniBody');
        var sorted = rows.slice();
        var mode = sortBy.value;
        sorted.sort(function(a, b){
            if (mode === 'name') return a.dataset.name.localeCompare(b.dataset.name);
            if (mode === 'year_desc') return (+b.dataset.year) - (+a.dataset.year);
            if (mode === 'year_asc') return (+a.dataset.year) - (+b.dataset.year);
            return (b.dataset.created || '').localeCompare(a.dataset.created || '');
        });
        sorted.forEach(function(r){ tbody.appendChild(r); });
    });

    // ===== Bulk select =====
    var checkAll = document.getElementById('checkAll');
    var bulkBar = document.getElementById('bulkBar');
    var bulkCount = document.getElementById('bulkCount');
    function refreshBulk() {
        var checked = document.querySelectorAll('.row-check:checked');
        bulkCount.textContent = checked.length;
        bulkBar.classList.toggle('hidden', checked.length === 0);
    }
    checkAll.addEventListener('change', function(){
        rows.forEach(function(r){
            if (r.style.display !== 'none') {
                var cb = r.querySelector('.row-check');
                if (cb) cb.checked = checkAll.checked;
            }
        });
        refreshBulk();
    });
    document.addEventListener('change', function(e){
        if (e.target.classList.contains('row-check')) refreshBulk();
    });
    document.getElementById('bulkClear').addEventListener('click', function(){
        document.querySelectorAll('.row-check').forEach(function(cb){ cb.checked = false; });
        checkAll.checked = false;
        refreshBulk();
    });

    // ===== Export CSV (client-side) =====
    function csvEsc(v){ v = v || ''; return '"' + String(v).replace(/"/g, '""') + '"'; }
    document.getElementById('exportCsv').addEventListener('click', function(){
        var lines = [['Nama','Email','NIM','Tahun','Prodi','Posisi','Perusahaan','Kota','Negara','Status'].join(',')];
        rows.forEach(function(r){
            if (r.style.display === 'none') return;
            lines.push([
                csvEsc(r.dataset.csvName), csvEsc(r.dataset.csvEmail), csvEsc(r.dataset.csvNim),
                csvEsc(r.dataset.year), csvEsc(r.dataset.prodi), csvEsc(r.dataset.csvPosition),
                csvEsc(r.dataset.csvCompany), csvEsc(r.dataset.csvCity), csvEsc(r.dataset.csvCountry),
                csvEsc(r.dataset.status)
            ].join(','));
        });
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'alumni-' + new Date().toISOString().slice(0,10) + '.csv';
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
    });
})();
</script>