<?php 
$q = $q ?? ''; 
$degree = $degree ?? null;
$active = $active ?? null;
$degrees = $degrees ?? ['D3','D4','S1','S2','S3','Profesi','Spesialis'];
?>
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Academic Programs</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Manajemen <em class="italic text-gold-muted">Program Studi</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Data program studi beserta akreditasi dan struktur.</p>
    </div>
    <div class="flex gap-2 self-start">
        <!-- 🔥 Export CSV -->
        <button type="button" id="exportCSV" class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-file-csv"></i>
        </button>
        <a href="<?= base_url('admin/programs/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Prodi
        </a>
    </div>
</div>

<!-- Stats + Filter Jenjang -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="<?= base_url('admin/programs') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$degree ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
        Semua Jenjang
    </a>
    <?php foreach ($degrees as $d): ?>
    <a href="<?= base_url('admin/programs?degree=' . $d) ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $degree == $d ? 'bg-gold text-navy border-gold' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">
        <?= $d ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Search -->
<div class="flex flex-col md:flex-row gap-3 mb-6">
    <input type="text" id="searchProdi" value="<?= html_escape($q) ?>" placeholder="Cari nama prodi / kaprodi / deskripsi..."
        class="flex-1 max-w-md px-5 py-3 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
    <select id="filterActive" class="px-4 py-3 border border-navy/20 bg-white text-navy text-sm">
        <option value="">Semua Status</option>
        <option value="1" <?= $active === '1' ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= $active === '0' ? 'selected' : '' ?>>Nonaktif</option>
    </select>
    <!-- 🔥 Accreditation expiry warning -->
    <button type="button" id="checkExpiry" class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
        <i class="fas fa-exclamation-triangle"></i>Cek Expired
    </button>
</div>

<!-- Bulk Form -->
<form id="bulkForm" action="<?= base_url('admin/programs/bulk_toggle') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate">All Programs<?= $q ? ' — hasil untuk "' . html_escape($q) . '"' : '' ?></p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= count($programs) ?> Programs</p>
    </div>

    <?php if (empty($programs)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-graduation-cap text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2"><?= $q ? 'Tidak ada prodi yang cocok.' : 'Belum ada program studi.' ?></p>
            <a href="<?= base_url('admin/programs/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah Prodi Pertama
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
        <table class="min-w-full refined-table" id="prodiTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="name">Program Studi <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-center">Jenjang</th>
                    <th class="text-center">Akreditasi</th>
                    <th class="text-left">Kaprodi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($programs as $idx => $p): ?>
                <tr class="group prodi-row"
                    data-name="<?= html_escape(strtolower($p->name)) ?>"
                    data-head="<?= html_escape(strtolower($p->head_of_study_program ?? '')) ?>"
                    data-desc="<?= html_escape(strtolower($p->description ?? '')) ?>"
                    data-degree="<?= html_escape($p->degree) ?>"
                    data-active="<?= $p->is_active ? '1' : '0' ?>"
                    data-csv-name="<?= html_escape($p->name) ?>"
                    data-csv-degree="<?= html_escape($p->degree) ?>"
                    data-csv-acc="<?= html_escape($p->accreditation ?? '') ?>"
                    data-csv-head="<?= html_escape($p->head_of_study_program ?? '') ?>"
                    data-csv-until="<?= html_escape($p->accreditation_until ?? '') ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $p->id ?>" class="row-check accent-navy"></td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-navy text-gold flex items-center justify-center flex-shrink-0 font-serif font-bold text-sm">
                                <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($p->name) ?></p>
                                <p class="text-xs text-slate mt-0.5 line-clamp-1"><?= html_escape(character_limiter($p->description ?? '-', 70)) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="bg-navy text-ivory text-[10px] uppercase tracking-editorial px-2.5 py-1 font-bold"><?= html_escape($p->degree) ?></span>
                    </td>
                    <td class="text-center">
                        <?php if (($p->accreditation ?? '') == 'Unggul' || ($p->accreditation ?? '') == 'A'): ?>
                            <span class="inline-flex items-center gap-1.5 bg-gold text-navy px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold">
                                <i class="fas fa-star text-[9px]"></i><?= html_escape($p->accreditation) ?>
                            </span>
                        <?php elseif (!empty($p->accreditation)): ?>
                            <span class="bg-ivory-warm text-navy px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold"><?= html_escape($p->accreditation) ?></span>
                        <?php else: ?>
                            <span class="text-slate text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="text-sm text-slate"><?= html_escape($p->head_of_study_program ?? '-') ?></span></td>
                    <td class="text-center">
                        <?php if ($p->is_active): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Preview publik -->
                            <a href="<?= base_url('admin/programs/preview/' . $p->id) ?>" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-amber-500 hover:text-ivory hover:border-amber-500 transition" title="Lihat di website">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/programs/duplicate/' . $p->id) ?>" onclick="return confirm('Duplikat prodi ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <a href="<?= base_url('admin/programs/edit/' . $p->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <button type="button" onclick="hapusProdi(<?= $p->id ?>)" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
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

<!-- 🔥 Accreditation Expiry Modal -->
<div id="expiryModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white max-w-lg w-full max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="font-serif text-xl text-navy">Akreditasi Segera Expired</h3>
            <button onclick="closeExpiry()" class="text-slate hover:text-navy text-xl">&times;</button>
        </div>
        <div id="expiryContent" class="p-6"></div>
    </div>
</div>

<script>
(function(){
    // ===== Live Search + Filter =====
    var searchInput = document.getElementById('searchProdi');
    var filterActive = document.getElementById('filterActive');
    var rows = document.querySelectorAll('.prodi-row');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = rows.length;
    var currentDegree = <?= json_encode($degree ?? '') ?>;
    
    function applyFilters(){
        var q = (searchInput.value || '').toLowerCase();
        var act = filterActive.value;
        var shown = 0;
        rows.forEach(function(r){
            var hitQ = !q || r.dataset.name.indexOf(q) > -1 || r.dataset.head.indexOf(q) > -1 || r.dataset.desc.indexOf(q) > -1;
            var hitA = !act || r.dataset.active === act;
            var hitD = !currentDegree || r.dataset.degree === currentDegree;
            var show = hitQ && hitA && hitD;
            r.style.display = show ? '' : 'none';
            if (show) shown++;
        });
        visibleCount.textContent = shown + ' / ' + totalItems + ' Programs';
    }
    
    searchInput.addEventListener('input', applyFilters);
    filterActive.addEventListener('change', function(){
        window.location.href = '<?= base_url('admin/programs') ?>?active=' + this.value + (currentDegree ? '&degree=' + currentDegree : '');
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
        if (a === 'deactivate' && !confirm('Nonaktifkan ' + n + ' prodi?')) return;
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
            var tbody = document.querySelector('#prodiTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.prodi-row'));
            var asc = this.classList.contains('sort-asc');
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            rs.sort(function(a, b){
                return asc ? b.dataset[col].localeCompare(a.dataset[col]) : a.dataset[col].localeCompare(b.dataset[col]);
            });
            rs.forEach(function(r){ tbody.appendChild(r); });
            this.classList.add(asc ? 'sort-desc' : 'sort-asc');
            this.querySelector('i').className = 'fas fa-sort-' + (asc ? 'down' : 'up') + ' text-xs ml-1';
        });
    });
    
    // ===== Export CSV =====
    function csvEsc(v){ return '"' + String(v||'').replace(/"/g, '""') + '"'; }
    document.getElementById('exportCSV').addEventListener('click', function(){
        var lines = [['Nama','Jenjang','Akreditasi','Kaprodi','Berlaku Sampai'].join(',')];
        rows.forEach(function(r){
            if (r.style.display === 'none') return;
            lines.push([
                csvEsc(r.dataset.csvName), csvEsc(r.dataset.csvDegree), csvEsc(r.dataset.csvAcc),
                csvEsc(r.dataset.csvHead), csvEsc(r.dataset.csvUntil)
            ].join(','));
        });
        var blob = new Blob([lines.join('\n')], {type: 'text/csv;charset=utf-8;'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'prodi-' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
    });
    
    // ===== Check Accreditation Expiry =====
    document.getElementById('checkExpiry').addEventListener('click', function(){
        var btn = this;
        var modal = document.getElementById('expiryModal');
        var content = document.getElementById('expiryContent');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
        
        fetch('<?= base_url('admin/programs/check_expiry') ?>', {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Cek Expired';
            
            if (data.count === 0) {
                content.innerHTML = '<div class="text-center py-8"><i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i><p class="text-navy font-serif text-xl">Semua aman!</p><p class="text-sm text-slate mt-2">Tidak ada akreditasi yang akan expired dalam 90 hari ke depan.</p></div>';
            } else {
                var html = '<div class="space-y-3">';
                data.items.forEach(function(item){
                    var urgency = item.days_left <= 30 ? 'text-red-600' : 'text-amber-600';
                    html += '<div class="p-4 border border-gray-200 hover:border-gold/30 transition">';
                    html += '<p class="font-serif text-navy font-medium">' + item.name + '</p>';
                    html += '<p class="text-xs text-slate mt-1">Expired: ' + item.until + '</p>';
                    html += '<p class="text-sm font-bold mt-2 ' + urgency + '">' + item.days_left + ' hari lagi</p>';
                    html += '</div>';
                });
                html += '</div>';
                content.innerHTML = html;
            }
            modal.classList.remove('hidden');
        })
        .catch(function(){
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Cek Expired';
            content.innerHTML = '<p class="text-red-600">Gagal memeriksa.</p>';
            modal.classList.remove('hidden');
        });
    });
    
    window.closeExpiry = function(){ document.getElementById('expiryModal').classList.add('hidden'); };
    document.getElementById('expiryModal').addEventListener('click', function(e){ if (e.target === this) closeExpiry(); });

    // ===== 🗑️ HAPUS PRODI (anti nested-form + CSRF aman) =====
    window.hapusProdi = function(id){
        if (!confirm('Yakin hapus prodi ini? Tindakan tidak dapat dibatalkan.')) return;
        var f = document.createElement('form');
        f.method = 'post';
        f.action = '<?= base_url('admin/programs/delete'); ?>/' + id;
        f.style.display = 'none';
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        if (csrfName && csrfHash) {
            var ci = document.createElement('input');
            ci.type = 'hidden'; ci.name = csrfName; ci.value = csrfHash;
            f.appendChild(ci);
        }
        document.body.appendChild(f);
        f.submit();
    };
})();
</script>