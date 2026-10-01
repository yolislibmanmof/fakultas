<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Student Excellence</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Prestasi <em class="italic text-gold-muted">Mahasiswa</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Pencapaian mahasiswa di berbagai tingkat kompetisi.</p>
    </div>
    <div class="flex gap-2 self-start">
        <!-- 🔥 FITUR GILA: Export CSV -->
        <a href="<?= base_url('admin/achievements/export' . ($q || $level ? '?' . http_build_query(array_filter(['q' => $q, 'level' => $level])) : '')) ?>" 
           class="btn-outline-navy px-4 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2" title="Export ke CSV">
            <i class="fas fa-file-csv"></i>
        </a>
        <a href="<?= base_url('admin/achievements/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2">
            <i class="fas fa-trophy"></i>Tambah Prestasi
        </a>
    </div>
</div>

<!-- Search + Filter -->
<div class="flex flex-col md:flex-row gap-4 mb-6">
    <form action="<?= base_url('admin/achievements') ?>" method="GET" class="relative flex-1 max-w-md">
        <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Cari mahasiswa / judul prestasi..."
            class="w-full px-5 py-3 pr-12 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
        <button type="submit" class="absolute right-0 top-0 bottom-0 w-12 text-navy hover:text-gold transition"><i class="fas fa-search"></i></button>
        <?php if ($level): ?><input type="hidden" name="level" value="<?= html_escape($level) ?>"><?php endif; ?>
    </form>
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('admin/achievements') ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= !$level ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">Semua</a>
        <?php foreach ($levels as $key => $label): ?>
        <a href="<?= base_url('admin/achievements?level=' . $key) ?>" class="text-xs uppercase tracking-editorial font-semibold px-4 py-2 transition <?= $level == $key ? 'bg-gold text-navy border-gold' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Table -->
<div id="bulkForm" class="bulk-wrapper">
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-4">
            <p class="editorial-label text-slate">All Achievements</p>
            <p class="font-mono text-xs text-slate"><?= count($items) ?> Records</p>
        </div>
        
        <!-- 🔥 FITUR GILA: Bulk actions -->
        <div id="bulkActions" class="flex items-center gap-2 opacity-50 pointer-events-none transition-all">
            <span class="text-xs text-slate"><span id="selectedCount">0</span> dipilih</span>
<button type="button" onclick="bulkHapusPrestasi()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition">
    <i class="fas fa-trash mr-1"></i>Hapus
</button>
            <button type="button" onclick="clearSelection()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100 transition">
                Batal
            </button>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-trophy text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2"><?= ($q || $level) ? 'Tidak ada prestasi yang cocok.' : 'Belum ada prestasi.' ?></p>
            <?php if (!$q && !$level): ?>
            <a href="<?= base_url('admin/achievements/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tambah Prestasi Pertama
            </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="achievementsTable">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" id="selectAll" class="accent-navy" title="Pilih semua">
                    </th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="student_name">
                        Mahasiswa <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="achievement_title">
                        Prestasi <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-left">Prodi</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="level">
                        Tingkat <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="year">
                        Tahun <i class="fas fa-sort text-xs ml-1 opacity-30"></i>
                    </th>
                    <th class="text-center">Bukti</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $r):
                    $badge = [
                        'international' => 'bg-gold text-navy',
                        'national'      => 'bg-blue-50 text-blue-700',
                        'regional'      => 'bg-green-50 text-green-700',
                        'university'    => 'bg-purple-50 text-purple-700',
                        'faculty'       => 'bg-gray-100 text-gray-600',
                    ][$r->level] ?? 'bg-gray-100 text-gray-600';
                ?>
                <tr class="group hover:bg-ivory-warm/30 transition">
                    <td>
                        <input type="checkbox" name="ids[]" value="<?= $r->id ?>" class="row-checkbox accent-navy">
                    </td>
                    <td>
                        <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition"><?= html_escape($r->student_name) ?></p>
                        <p class="text-xs text-slate mt-0.5 font-mono"><?= html_escape($r->nim ?? '-') ?></p>
                    </td>
                    <td class="max-w-xs">
                        <p class="text-sm text-navy line-clamp-2"><?= html_escape($r->achievement_title) ?></p>
                        <?php if ($r->organizer): ?><p class="text-xs text-slate mt-0.5"><?= html_escape($r->organizer) ?></p><?php endif; ?>
                    </td>
                    <td><span class="text-xs text-slate"><?= html_escape($r->prodi_name ?? '-') ?></span></td>
                    <td class="text-center"><span class="<?= $badge ?> px-2.5 py-1 text-[10px] uppercase tracking-wider font-bold"><?= $levels[$r->level] ?? ucfirst($r->level) ?></span></td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= $r->year ?></span></td>
                    <td class="text-center">
                        <?php if ($r->document_proof): ?>
                            <a href="<?= base_url('assets/uploads/achievements/' . $r->document_proof) ?>" target="_blank" class="inline-flex items-center gap-1 text-gold-muted hover:text-gold text-xs font-semibold" title="Lihat bukti">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        <?php else: ?><span class="text-xs text-slate">-</span><?php endif; ?>
                    </td>
<td class="text-center">
    <div class="flex items-center justify-center gap-1">
        <!-- Preview button -->
        <button type="button" onclick="previewAchievement(<?= $r->id ?>)" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Preview">
            <i class="fas fa-eye text-xs"></i>
        </button>
        <!-- Duplicate button -->
        <a href="<?= base_url('admin/achievements/duplicate/' . $r->id) ?>" onclick="return confirm('Duplikat prestasi ini?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
            <i class="fas fa-copy text-xs"></i>
        </a>
        <!-- Edit -->
        <a href="<?= base_url('admin/achievements/edit/' . $r->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit">
            <i class="fas fa-pen text-xs"></i>
        </a>
        <!-- ✅ HAPUS: pakai button + JS submit (anti form-nesting) -->
        <button type="button" onclick="hapusPrestasi(<?= $r->id ?>)" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
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
</div>

<!-- 🔥 FITUR GILA: Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="font-serif text-xl text-navy">Preview Prestasi</h3>
            <button onclick="closePreview()" class="text-slate hover:text-navy text-xl">&times;</button>
        </div>
        <div id="previewContent" class="p-6">
            <div class="animate-pulse space-y-4">
                <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded w-full"></div>
            </div>
        </div>
    </div>
</div>

<script>
// ===== BULK SELECT =====
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.row-checkbox');
const bulkActions = document.getElementById('bulkActions');
const selectedCount = document.getElementById('selectedCount');

function updateBulkActions() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    selectedCount.textContent = checked.length;
    if (checked.length > 0) {
        bulkActions.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        bulkActions.classList.add('opacity-50', 'pointer-events-none');
    }
}

if (selectAll) {
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });
}

checkboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
});

function clearSelection() {
    checkboxes.forEach(cb => cb.checked = false);
    if (selectAll) selectAll.checked = false;
    updateBulkActions();
}

// ===== TABLE SORTING =====
document.querySelectorAll('[data-sort]').forEach(th => {
    th.addEventListener('click', function() {
        const column = this.dataset.sort;
        const table = document.getElementById('achievementsTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        const isAsc = this.classList.contains('sort-asc');
        
        // Reset all headers
        document.querySelectorAll('[data-sort]').forEach(h => {
            h.classList.remove('sort-asc', 'sort-desc');
            h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
        });
        
        // Sort rows
        rows.sort((a, b) => {
            const aIdx = Array.from(th.parentNode.children).indexOf(th);
            const aCell = a.children[aIdx];
            const bCell = b.children[aIdx];
            
            let aVal = aCell.textContent.trim();
            let bVal = bCell.textContent.trim();
            
            // Handle numeric columns (year)
            if (column === 'year') {
                aVal = parseInt(aVal) || 0;
                bVal = parseInt(bVal) || 0;
                return isAsc ? bVal - aVal : aVal - bVal;
            }
            
            // Handle text columns
            return isAsc ? bVal.localeCompare(aVal) : aVal.localeCompare(bVal);
        });
        
        // Reorder DOM
        rows.forEach(row => tbody.appendChild(row));
        
        // Update header
        this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
        this.querySelector('i').className = `fas fa-sort-${isAsc ? 'down' : 'up'} text-xs ml-1`;
    });
});

// ===== PREVIEW MODAL =====
function previewAchievement(id) {
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="animate-pulse space-y-4"><div class="h-6 bg-gray-200 rounded w-3/4"></div><div class="h-4 bg-gray-200 rounded w-1/2"></div></div>';
    
    // Fetch data (dalam implementasi nyata, pakai AJAX)
    fetch(`<?= base_url('admin/achievements/preview/') ?>${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const a = data.data;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <p class="editorial-label text-gold-muted mb-2">Mahasiswa</p>
                            <p class="font-serif text-xl text-navy">${a.student_name}</p>
                            <p class="text-sm text-slate font-mono">${a.nim || '-'}</p>
                        </div>
                        <div>
                            <p class="editorial-label text-gold-muted mb-2">Prestasi</p>
                            <p class="font-serif text-lg text-navy">${a.achievement_title}</p>
                            ${a.organizer ? `<p class="text-sm text-slate mt-1">Penyelenggara: ${a.organizer}</p>` : ''}
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <p class="editorial-label text-gold-muted mb-1">Tingkat</p>
                                <p class="text-sm text-navy font-semibold">${a.level_label}</p>
                            </div>
                            <div>
                                <p class="editorial-label text-gold-muted mb-1">Tahun</p>
                                <p class="text-sm text-navy font-mono">${a.year}</p>
                            </div>
                        </div>
                        ${a.prodi_name ? `
                        <div class="pt-4 border-t border-gray-200">
                            <p class="editorial-label text-gold-muted mb-1">Program Studi</p>
                            <p class="text-sm text-navy">${a.prodi_name}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
            }
        })
        .catch(err => {
            content.innerHTML = '<p class="text-red-600">Gagal memuat preview.</p>';
        });
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Close modal on backdrop click
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});

// ===== 🔥 FUNGSI HAPUS PRESTASI (ANTI-FORM-NESTING) =====
function hapusPrestasi(id){
    if (!confirm('Yakin hapus prestasi ini? Tindakan tidak dapat dibatalkan.')) return;
    
    // Buat form programmatik (melewati handler admin.js yang crash)
    var f = document.createElement('form');
    f.method = 'post';
    f.action = '<?= base_url('admin/achievements/delete'); ?>/' + id;
    f.style.display = 'none';
    
    // Tambah CSRF token kalau ada
    var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
    if (csrfName && csrfHash) {
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = csrfName;
        csrf.value = csrfHash;
        f.appendChild(csrf);
    }
    
    document.body.appendChild(f);
    f.submit();
}

// ===== BULK DELETE yang juga aman =====
function bulkHapusPrestasi(){
    var checked = document.querySelectorAll('.row-checkbox:checked');
    if (!checked.length) {
        alert('Pilih data terlebih dahulu.');
        return;
    }
    if (!confirm('Hapus ' + checked.length + ' prestasi terpilih? Tindakan tidak dapat dibatalkan.')) return;
    
    var f = document.createElement('form');
    f.method = 'post';
    f.action = '<?= base_url('admin/achievements/bulk_delete'); ?>';
    f.style.display = 'none';
    
    // Tambah CSRF
    var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
    if (csrfName && csrfHash) {
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = csrfName;
        csrf.value = csrfHash;
        f.appendChild(csrf);
    }
    
    // Tambah ID terpilih
    checked.forEach(function(cb){
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'ids[]';
        inp.value = cb.value;
        f.appendChild(inp);
    });
    
    document.body.appendChild(f);
    f.submit();
}
</script>