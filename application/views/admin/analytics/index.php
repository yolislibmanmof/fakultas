<!-- Page Header -->
<div class="mb-10">
    <p class="editorial-label text-gold-muted mb-3">Performance Intelligence</p>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        Analytics <em class="italic text-gold-muted">Dashboard</em>
    </h1>
    <p class="text-slate mt-2 text-sm">Pantau performa website dengan Google Analytics 4 terintegrasi.</p>
</div>

<div class="grid lg:grid-cols-3 gap-8 items-start">

    <!-- ============ KONFIGURASI ============ -->
    <div class="lg:col-span-1">
        <?= form_open('admin/analytics/update', ['class' => 'bg-white border border-gray-200 p-6 md:p-8 lg:sticky lg:top-24']) ?>
            <p class="editorial-label text-gold-muted mb-5">1. Konfigurasi</p>

            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">GA4 Measurement ID</label>
                    <input type="text" name="ga_measurement_id" value="<?= html_escape($ga_id) ?>" placeholder="G-XXXXXXXXXX"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                    <p class="text-xs text-slate mt-2">Didapat dari Google Analytics → Admin → Data Streams</p>
                </div>

                <div>
                    <label class="block editorial-label text-navy mb-2">URL Dashboard GA4 (opsional)</label>
                    <input type="url" name="ga_dashboard_url" value="<?= html_escape($ga_dashboard) ?>" placeholder="https://analytics.google.com/..."
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-xs">
                    <p class="text-xs text-slate mt-2">Embed dashboard GA4 di panel admin</p>
                </div>

                <!-- 🔥 FITUR GILA: Test connection button -->
                <button type="button" id="testGA" class="btn-outline-navy w-full px-6 py-2.5 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fas fa-plug mr-2"></i>Test Connection
                </button>
                <div id="testResult" class="hidden mt-3 p-3 text-xs"></div>

                <button type="submit" class="btn-gold w-full px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="editorial-label text-gold-muted mb-3">Status</p>
                <?php if ($ga_id): ?>
                <div class="flex items-center gap-2 text-sm text-green-700 bg-green-50 p-3 border border-green-200">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="font-semibold">Analytics Aktif</span>
                </div>
                <p class="text-xs text-slate mt-2 font-mono break-all">ID: <?= html_escape($ga_id) ?></p>
                <?php else: ?>
                <div class="flex items-center gap-2 text-sm text-yellow-700 bg-yellow-50 p-3 border border-yellow-200">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                    <span class="font-semibold">Belum dikonfigurasi</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="mt-6 p-4 bg-navy text-ivory text-xs">
                <p class="font-semibold text-gold mb-2"><i class="fas fa-lightbulb mr-1"></i> Cara Setup GA4:</p>
                <ol class="list-decimal list-inside space-y-1 text-ivory/80">
                    <li>Buka <a href="https://analytics.google.com" target="_blank" class="text-gold underline">analytics.google.com</a></li>
                    <li>Buat property baru (GA4)</li>
                    <li>Copy Measurement ID (G-XXXX)</li>
                    <li>Paste di field di atas</li>
                    <li>Klik Simpan — otomatis aktif di website!</li>
                </ol>
            </div>
        <?= form_close() ?>
    </div>

    <!-- ============ DASHBOARD PREVIEW ============ -->
    <div class="lg:col-span-2">
        <p class="editorial-label text-gold-muted mb-4">2. Dashboard Preview</p>

        <?php if ($ga_dashboard): ?>
        <div class="bg-white border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-ivory-warm/50 border-b border-gray-200 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-chart-line text-gold-muted"></i>
                    <span class="editorial-label text-navy">Live Analytics</span>
                </div>
                <a href="<?= html_escape($ga_dashboard) ?>" target="_blank" class="text-xs text-navy hover:text-gold">
                    <i class="fas fa-external-link-alt mr-1"></i>Buka di Tab Baru
                </a>
            </div>
            <iframe src="<?= html_escape($ga_dashboard) ?>" class="w-full border-0" style="height:600px" allow="clipboard-write" sandbox="allow-scripts allow-same-origin"></iframe>
        </div>
        <?php else: ?>
        <div class="bg-white border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-ivory-warm mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-chart-area text-4xl text-gold-muted"></i>
            </div>
            <h3 class="font-serif text-2xl font-light text-navy mb-2">Belum Ada Dashboard</h3>
            <p class="text-slate text-sm max-w-md mx-auto">
                Tambahkan URL dashboard GA4 di form konfigurasi untuk menampilkan analytics live di sini.
            </p>
        </div>
        <?php endif; ?>

        <!-- ============ QUICK STATS INTERNAL ============ -->
        <?php
            $CI =& get_instance();
            $stats = [];
            // 🔥 FIX: Defensive query (cek tabel ada)
            if ($CI->db->table_exists('posts')) {
                $stats['Total Berita'] = $CI->db->count_all('posts');
            }
            if ($CI->db->table_exists('lecturers')) {
                $stats['Dosen Aktif'] = $CI->db->where('is_active', 1)->count_all_results('lecturers');
            }
            if ($CI->db->table_exists('study_programs')) {
                $stats['Program Studi'] = $CI->db->where('is_active', 1)->count_all_results('study_programs');
            }
            if ($CI->db->table_exists('research')) {
                $stats['Total Riset'] = $CI->db->count_all('research');
            }
            if ($CI->db->table_exists('documents')) {
                $stats['Total Dokumen'] = $CI->db->where('is_active', 1)->count_all_results('documents');
            }
            if ($CI->db->table_exists('alumni')) {
                $stats['Total Alumni'] = $CI->db->where('status', 'approved')->count_all_results('alumni');
            }
        ?>
        <div class="mt-6 bg-white border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <p class="editorial-label text-gold-muted">Quick Stats Internal (dari database)</p>
                <!-- 🔥 FITUR GILA: Refresh button -->
                <button type="button" id="refreshStats" class="text-xs text-navy hover:text-gold transition" title="Refresh stats">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4" id="statsGrid">
                <?php foreach ($stats as $label => $value): ?>
                <div class="stat-card bg-ivory/50 p-4 border border-gray-100 transition hover:border-gold/30">
                    <div class="font-serif text-3xl font-light text-navy"><?= number_format($value) ?></div>
                    <p class="text-[10px] uppercase tracking-wider text-slate mt-1"><?= $label ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 🔥 FITUR GILA: Export stats -->
        <div class="mt-6 bg-white border border-gray-200 p-6">
            <p class="editorial-label text-gold-muted mb-4">Data Export</p>
            <div class="flex gap-3">
                <button type="button" id="exportJSON" class="btn-outline-navy px-5 py-2.5 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fas fa-file-code mr-2"></i>Export JSON
                </button>
                <button type="button" id="exportCSV" class="btn-outline-navy px-5 py-2.5 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    // ===== Test GA Connection =====
    document.getElementById('testGA').addEventListener('click', function(){
        var btn = this;
        var result = document.getElementById('testResult');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
        result.classList.remove('hidden');
        result.className = 'mt-3 p-3 text-xs bg-blue-50 text-blue-700 border border-blue-200';
        result.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i>Mengecek koneksi...';

        fetch('<?= base_url('admin/analytics/test_ga') ?>', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'ok') {
                result.className = 'mt-3 p-3 text-xs bg-green-50 text-green-700 border border-green-200';
                result.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + data.message + ' (Format: ' + data.format + ')';
            } else {
                result.className = 'mt-3 p-3 text-xs bg-red-50 text-red-700 border border-red-200';
                result.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>' + data.message;
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plug mr-2"></i>Test Connection';
        })
        .catch(err => {
            result.className = 'mt-3 p-3 text-xs bg-red-50 text-red-700 border border-red-200';
            result.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Error: ' + err.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plug mr-2"></i>Test Connection';
        });
    });

    // ===== Refresh Stats =====
    document.getElementById('refreshStats').addEventListener('click', function(){
        var btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        fetch('<?= base_url('admin/dashboard/refresh_stats') ?>', {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            alert('Stats refreshed! Posts: ' + (data.posts_published || 0) + ', Alumni pending: ' + (data.alumni_pending || 0));
            // Dalam implementasi nyata, update DOM stats grid di sini
        })
        .catch(() => {
            btn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            alert('Gagal refresh stats.');
        });
    });

    // ===== Export JSON =====
    document.getElementById('exportJSON').addEventListener('click', function(){
        var stats = {};
        document.querySelectorAll('.stat-card').forEach(card => {
            var label = card.querySelector('p').textContent.trim();
            var value = card.querySelector('div').textContent.trim().replace(/,/g, '');
            stats[label] = parseInt(value) || 0;
        });
        var blob = new Blob([JSON.stringify(stats, null, 2)], {type: 'application/json'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'analytics-stats-' + new Date().toISOString().slice(0,10) + '.json';
        a.click();
    });

    // ===== Export CSV =====
    document.getElementById('exportCSV').addEventListener('click', function(){
        var lines = ['Metric,Value'];
        document.querySelectorAll('.stat-card').forEach(card => {
            var label = card.querySelector('p').textContent.trim();
            var value = card.querySelector('div').textContent.trim().replace(/,/g, '');
            lines.push('"' + label + '",' + value);
        });
        var blob = new Blob([lines.join('\n')], {type: 'text/csv'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'analytics-stats-' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
    });
})();
</script>