<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Content Management</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Berita & <em class="italic text-gold-muted">Pengumuman</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Kelola seluruh konten editorial website.</p>
    </div>
    <a href="<?= base_url('admin/posts/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-pen-nib"></i>Tulis Berita
    </a>
</div>

<!-- Filters + Search -->
<div class="flex flex-wrap items-center gap-4 mb-6">
    <div class="flex items-center gap-2">
        <span class="editorial-label text-slate">Status:</span>
        <a href="<?= base_url('admin/posts') ?>" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 transition <?= !$status ? 'bg-navy text-ivory' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">Semua</a>
        <a href="<?= base_url('admin/posts?status=published') ?>" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 transition <?= $status == 'published' ? 'bg-green-600 text-ivory border-green-600' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">Published</a>
        <a href="<?= base_url('admin/posts?status=draft') ?>" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 transition <?= $status == 'draft' ? 'bg-yellow-500 text-navy border-yellow-500' : 'border border-navy/20 text-navy hover:bg-navy hover:text-ivory' ?>">Draft</a>
    </div>
    <div class="flex-1"></div>
    <input type="text" id="searchPosts" placeholder="Cari judul / author..." class="px-4 py-2 border border-navy/20 bg-white text-navy text-sm focus:border-gold outline-none transition w-full max-w-xs">
</div>

<!-- Bulk Form -->
<form id="bulkForm" action="<?= base_url('admin/posts/bulk_action') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate"><?= $status ? ucfirst($status) : 'All' ?> Posts</p>
        <p class="font-mono text-xs text-slate" id="visibleCount"><?= count($posts) ?> Articles</p>
    </div>

    <?php if (empty($posts)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-newspaper text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2"><?= $status ? 'Tidak ada artikel ' . $status . '.' : 'Belum ada berita.' ?></p>
            <a href="<?= base_url('admin/posts/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tulis Berita Pertama
            </a>
        </div>
    <?php else: ?>
    
    <!-- Bulk bar -->
    <div id="bulkBar" class="hidden px-6 py-3 bg-navy text-ivory flex items-center gap-4 border-b border-gold/30">
        <span class="text-sm"><strong id="bulkCount">0</strong> dipilih</span>
        <button type="button" onclick="setBulk('publish')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-green-600 hover:bg-green-500 transition">
            <i class="fas fa-check mr-1"></i>Publish
        </button>
        <button type="button" onclick="setBulk('draft')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-yellow-600 hover:bg-yellow-500 transition">
            <i class="fas fa-file mr-1"></i>Draft
        </button>
        <button type="button" onclick="setBulk('archive')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-gray-600 hover:bg-gray-500 transition">
            <i class="fas fa-archive mr-1"></i>Arsipkan
        </button>
        <button type="button" onclick="setBulk('delete')" class="text-xs uppercase tracking-editorial font-bold px-3 py-1.5 bg-red-600 hover:bg-red-500 transition">
            <i class="fas fa-trash mr-1"></i>Hapus
        </button>
        <button type="button" onclick="clearSel()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 border border-ivory/30 hover:bg-ivory/10 transition ml-auto">Batal</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="postsTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="title">Judul <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Kategori</th>
                    <th class="text-left">Tipe</th>
                    <th class="text-center">Status</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="views">Views <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $p): ?>
                <tr class="group post-row"
                    data-title="<?= html_escape(strtolower($p->title)) ?>"
                    data-views="<?= (int)($p->views ?? 0) ?>"
                    data-date="<?= html_escape($p->created_at) ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $p->id ?>" class="row-check accent-navy"></td>
                    <td class="max-w-md">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 bg-gray-200 flex-shrink-0 overflow-hidden">
                                <?php if ($p->featured_image): ?>
                                    <img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center"><i class="fas fa-newspaper text-slate/40"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <p class="font-serif font-medium text-navy group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($p->title) ?></p>
                                <p class="text-xs text-slate mt-0.5">oleh <?= html_escape($p->author ?? 'Admin') ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-xs uppercase tracking-wider text-slate"><?= html_escape($p->category_name ?? '-') ?></span></td>
                    <td><span class="text-xs text-slate"><?= ucfirst($p->type) ?></span></td>
                    <td class="text-center">
                        <?php if ($p->status == 'published'): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Published</span>
                        <?php elseif ($p->status == 'archived'): ?>
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Archived</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-yellow-50 text-yellow-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Draft</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= number_format($p->views ?? 0) ?></span></td>
                    <td><span class="font-mono text-xs text-slate"><?= date('d M Y', strtotime($p->created_at)) ?></span></td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Pin/Unpin (jika kolom ada) -->
                            <button type="button" onclick="togglePin(<?= $p->id ?>, this)" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-gold hover:text-navy hover:border-gold transition" title="<?= ($p->is_pinned ?? 0) ? 'Unpin' : 'Pin' ?>">
                                <i class="fas fa-thumbtack text-xs <?= ($p->is_pinned ?? 0) ? 'text-gold' : '' ?>"></i>
                            </button>
                            <!-- Preview -->
                            <a href="<?= base_url('admin/posts/preview/' . $p->id) ?>" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-amber-500 hover:text-ivory hover:border-amber-500 transition" title="Preview">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/posts/duplicate/' . $p->id) ?>" onclick="return confirm('Duplikat artikel ini sebagai draft?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <?php if ($p->status == 'published'): ?>
                            <a href="<?= base_url('berita/detail/' . $p->slug) ?>" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-gold hover:text-navy hover:border-gold transition" title="Lihat di website"><i class="fas fa-external-link-alt text-xs"></i></a>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/posts/edit/' . $p->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                            <button type="button" onclick="hapusBerita(<?= $p->id ?>)" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
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
    var searchInput = document.getElementById('searchPosts');
    var rows = document.querySelectorAll('.post-row');
    var visibleCount = document.getElementById('visibleCount');
    var totalItems = rows.length;
    
    searchInput.addEventListener('input', function(){
        var q = searchInput.value.toLowerCase();
        var shown = 0;
        rows.forEach(function(r){
            var hit = !q || r.dataset.title.indexOf(q) > -1;
            r.style.display = hit ? '' : 'none';
            if (hit) shown++;
        });
        visibleCount.textContent = shown + ' / ' + totalItems + ' Articles';
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
        var n = document.querySelectorAll('.row-check:checked').length;
        if (a === 'delete' && !confirm('Hapus ' + n + ' artikel? Tindakan ini tidak dapat dibatalkan!')) return;
        document.getElementById('bulkAction').value = a;
        document.getElementById('bulkForm').submit();
    };
    
    window.handleBulk = function(e){
        if (!document.getElementById('bulkAction').value) { e.preventDefault(); return false; }
        return true;
    };
    
    // ===== Pin/Unpin (AJAX) =====
    window.togglePin = function(id, btn){
        fetch('<?= base_url('admin/posts/toggle_pin/') ?>' + id, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            if (data.ok) {
                var icon = btn.querySelector('i');
                if (data.pinned) {
                    icon.classList.add('text-gold');
                    btn.title = 'Unpin';
                } else {
                    icon.classList.remove('text-gold');
                    btn.title = 'Pin';
                }
            } else {
                alert(data.message || 'Gagal toggle pin.');
            }
        })
        .catch(function(){ alert('Gagal toggle pin.'); });
    };
    
    // ===== Table Sorting =====
    document.querySelectorAll('[data-sort]').forEach(function(th){
        th.addEventListener('click', function(){
            var col = this.dataset.sort;
            var tbody = document.querySelector('#postsTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.post-row'));
            var asc = this.classList.contains('sort-asc');
            
            document.querySelectorAll('[data-sort]').forEach(function(h){
                h.classList.remove('sort-asc', 'sort-desc');
                h.querySelector('i').className = 'fas fa-sort text-xs ml-1 opacity-30';
            });
            
            rs.sort(function(a, b){
                var av = a.dataset[col] || '', bv = b.dataset[col] || '';
                if (col === 'views') {
                    av = parseInt(av) || 0; bv = parseInt(bv) || 0;
                    return asc ? bv - av : av - bv;
                }
                return asc ? bv.localeCompare(av) : av.localeCompare(bv);
            });
            
            rs.forEach(function(r){ tbody.appendChild(r); });
            this.classList.add(asc ? 'sort-desc' : 'sort-asc');
            this.querySelector('i').className = 'fas fa-sort-' + (asc ? 'down' : 'up') + ' text-xs ml-1';
        });
    });

    // ===== 🗑️ HAPUS BERITA (anti nested-form + CSRF aman) =====
    window.hapusBerita = function(id){
        if (!confirm('Yakin hapus berita ini? Tindakan tidak dapat dibatalkan.')) return;
        var f = document.createElement('form');
        f.method = 'post';
        f.action = '<?= base_url('admin/posts/delete'); ?>/' + id;
        f.style.display = 'none';
        // Sertakan CSRF token bila aktif
        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        if (csrfName && csrfHash) {
            var ci = document.createElement('input');
            ci.type = 'hidden'; ci.name = csrfName; ci.value = csrfHash;
            f.appendChild(ci);
        }
        document.body.appendChild(f);
        f.submit();   // submit programmatik: melewati handler admin.js yang crash
    };
})();
</script>