<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <p class="editorial-label text-gold-muted mb-3">Faculty Voices</p>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            Blog <em class="italic text-gold-muted">Dosen</em>
        </h1>
        <p class="text-slate mt-2 text-sm">Kelola artikel blog yang ditulis oleh dosen fakultas.</p>
    </div>
    <a href="<?= base_url('admin/lecturerblog/create') ?>" class="btn-navy px-6 py-3 text-xs uppercase tracking-editorial font-semibold flex items-center gap-2 self-start">
        <i class="fas fa-pen-nib"></i>Tulis Artikel
    </a>
</div>

<!-- Stats Chips -->
<div class="grid grid-cols-3 gap-3 mb-6">
    <a href="<?= base_url('admin/lecturerblog') ?>" class="bg-white border-2 <?= !$status ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-navy"><?= $counts['all'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total</p>
    </a>
    <a href="<?= base_url('admin/lecturerblog?status=published') ?>" class="bg-white border-2 <?= $status == 'published' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-green-600"><?= $counts['published'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Published</p>
    </a>
    <a href="<?= base_url('admin/lecturerblog?status=draft') ?>" class="bg-white border-2 <?= $status == 'draft' ? 'border-gold' : 'border-gray-200 hover:border-navy/30' ?> p-4 text-center transition">
        <div class="font-serif text-2xl text-yellow-600"><?= $counts['draft'] ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Draft</p>
    </a>
</div>

<!-- Search -->
<div class="mb-6">
    <form action="<?= base_url('admin/lecturerblog') ?>" method="GET" class="relative max-w-md">
        <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Cari judul / nama dosen..."
            class="w-full px-5 py-3 pr-12 border border-navy/20 bg-white text-navy placeholder:text-slate text-sm focus:border-gold outline-none transition">
        <button type="submit" class="absolute right-0 top-0 bottom-0 w-12 text-navy hover:text-gold transition"><i class="fas fa-search"></i></button>
        <?php if ($status): ?><input type="hidden" name="status" value="<?= html_escape($status) ?>"><?php endif; ?>
    </form>
</div>

<!-- Bulk Form -->
<form id="bulkForm" action="<?= base_url('admin/lecturerblog/bulk_status') ?>" method="POST" onsubmit="return handleBulk(event)">
<input type="hidden" name="action" id="bulkAction" value="">

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
        <p class="editorial-label text-slate"><?= $status ? ucfirst($status) . ' Articles' : 'All Articles' ?></p>
        <p class="font-mono text-xs text-slate"><?= count($posts) ?> Articles</p>
    </div>

    <?php if (empty($posts)): ?>
        <div class="p-16 text-center">
            <i class="fas fa-pen-nib text-5xl text-gray-300 mb-4"></i>
            <p class="font-serif text-xl text-navy font-light mb-2">Belum ada artikel blog.</p>
            <a href="<?= base_url('admin/lecturerblog/create') ?>" class="inline-block mt-4 btn-gold px-6 py-2 text-xs uppercase tracking-editorial font-semibold">
                <i class="fas fa-plus mr-1"></i>Tulis Artikel Pertama
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
            <i class="fas fa-file mr-1"></i>Jadikan Draft
        </button>
        <button type="button" onclick="clearSel()" class="text-xs uppercase tracking-editorial font-semibold px-3 py-1.5 border border-ivory/30 hover:bg-ivory/10 transition ml-auto">Batal</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full refined-table" id="blogTable">
            <thead>
                <tr>
                    <th class="w-10"><input type="checkbox" id="selectAll" class="accent-navy"></th>
                    <th class="text-left cursor-pointer hover:text-gold transition" data-sort="title">Judul <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Penulis</th>
                    <th class="text-center">Status</th>
                    <th class="text-center cursor-pointer hover:text-gold transition" data-sort="views">Views <i class="fas fa-sort text-xs ml-1 opacity-30"></i></th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $p): ?>
                <tr class="group blog-row"
                    data-title="<?= html_escape(strtolower($p->title)) ?>"
                    data-views="<?= (int)($p->views ?? 0) ?>">
                    <td><input type="checkbox" name="ids[]" value="<?= $p->id ?>" class="row-check accent-navy"></td>
                    <td class="max-w-xs">
                        <div class="flex items-center gap-3">
                            <?php if ($p->featured_image): ?>
                                <img src="<?= base_url('assets/uploads/' . $p->featured_image) ?>" class="w-12 h-12 object-cover flex-shrink-0 border border-gray-100" alt="">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-ivory-warm flex items-center justify-center flex-shrink-0 text-gold-muted"><i class="fas fa-pen-nib"></i></div>
                            <?php endif; ?>
                            <span class="font-serif font-medium text-navy group-hover:text-gold-muted transition line-clamp-2"><?= html_escape($p->title) ?></span>
                        </div>
                    </td>
                    <td><span class="text-sm text-slate"><?= html_escape(trim(($p->title_front ?? '') . ' ' . ($p->lecturer_name ?? ''))) ?></span></td>
                    <td class="text-center">
                        <?php if ($p->status == 'published'): ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Published
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-yellow-50 text-yellow-700 px-2.5 py-1 text-[10px] uppercase tracking-wider font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Draft
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="font-mono text-xs text-slate"><?= number_format($p->views ?? 0) ?></span></td>
                    <td><span class="font-mono text-xs text-slate"><?= $p->published_at ? date('d M Y', strtotime($p->published_at)) : '-' ?></span></td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Preview -->
                            <button type="button" onclick="previewPost(<?= $p->id ?>, '<?= html_escape(addslashes($p->title)) ?>')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-amber-500 hover:text-ivory hover:border-amber-500 transition" title="Preview">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                            <!-- Duplicate -->
                            <a href="<?= base_url('admin/lecturerblog/duplicate/' . $p->id) ?>" onclick="return confirm('Duplikat artikel ini sebagai draft baru?')" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-blue-600 hover:text-ivory hover:border-blue-600 transition" title="Duplikat">
                                <i class="fas fa-copy text-xs"></i>
                            </a>
                            <?php if ($p->status == 'published'): ?>
                            <a href="<?= base_url('lecturerblog/post/' . $p->slug) ?>" target="_blank" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-gold hover:text-navy hover:border-gold transition" title="Lihat di website">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/lecturerblog/edit/' . $p->id) ?>" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-navy hover:text-ivory hover:border-navy transition" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <button type="button" onclick="hapusArtikel(<?= $p->id ?>)" class="w-8 h-8 border border-gray-200 flex items-center justify-center text-slate hover:bg-red-600 hover:text-ivory hover:border-red-600 transition" title="Hapus">
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

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 id="previewTitle" class="font-serif text-xl text-navy truncate">Preview</h3>
            <button onclick="closePreview()" class="text-slate hover:text-navy text-xl">&times;</button>
        </div>
        <div id="previewContent" class="p-6"></div>
    </div>
</div>

<script>
(function(){
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
        if (a === 'draft' && !confirm('Jadikan ' + n + ' artikel sebagai draft?')) return;
        if (a === 'publish' && !confirm('Publish ' + n + ' artikel?')) return;
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
            var tbody = document.querySelector('#blogTable tbody');
            var rs = Array.from(tbody.querySelectorAll('.blog-row'));
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
    
    // ===== Preview Modal =====
    window.previewPost = function(id, title){
        var modal = document.getElementById('previewModal');
        var content = document.getElementById('previewContent');
        var titleEl = document.getElementById('previewTitle');
        titleEl.textContent = title;
        modal.classList.remove('hidden');
        content.innerHTML = '<div class="animate-pulse space-y-4"><div class="h-6 bg-gray-200 rounded w-3/4"></div><div class="h-4 bg-gray-200 rounded w-1/2"></div><div class="h-4 bg-gray-200 rounded w-full"></div></div>';
        
        fetch('<?= base_url('admin/lecturerblog/preview/') ?>' + id)
            .then(function(r){ return r.text(); })
            .then(function(html){
                content.innerHTML = html || '<p class="text-slate">Gagal memuat preview.</p>';
            })
            .catch(function(){
                content.innerHTML = '<p class="text-red-600">Gagal memuat preview.</p>';
            });
    };
    
    window.closePreview = function(){
        document.getElementById('previewModal').classList.add('hidden');
    };
    
    document.getElementById('previewModal').addEventListener('click', function(e){
        if (e.target === this) closePreview();
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closePreview();
    });

    // ===== ️ HAPUS ARTIKEL (anti nested-form + CSRF aman) =====
    window.hapusArtikel = function(id){
        if (!confirm('Yakin hapus artikel ini? Tindakan tidak dapat dibatalkan.')) return;
        var f = document.createElement('form');
        f.method = 'post';
        f.action = '<?= base_url('admin/lecturerblog/delete'); ?>/' + id;
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