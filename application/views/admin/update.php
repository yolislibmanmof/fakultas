<div class="p-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-navy">🔄 Update Sistem</h1>
            <p class="text-sm text-slate mt-1">Versi saat ini: <span class="font-mono font-bold text-gold">v<?= html_escape($version) ?></span></p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] uppercase tracking-wider font-bold px-3 py-1.5 bg-green-100 text-green-700 rounded-full">
                <i class="fas fa-shield-alt mr-1"></i>Auto-Backup Aktif
            </span>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-3.5 mb-6 flex items-start gap-3">
        <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
        <div class="flex-1 text-sm"><?= $this->session->flashdata('success') ?></div>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div class="flex-1 text-sm"><?= $this->session->flashdata('error') ?></div>
    </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- URL Manifest -->
        <div class="bg-white border border-gray-200 p-5">
            <p class="text-xs font-bold uppercase text-slate mb-3"><i class="fas fa-link text-gold-muted mr-1"></i>Server Update</p>
            <form method="post" action="<?= base_url('admin/update/save_manifest') ?>">
                <input type="url" name="url" value="<?= html_escape($manifest_url) ?>" placeholder="https://server.com/manifest.json" class="w-full border-2 border-gray-200 px-3 py-2 text-sm focus:border-gold outline-none mb-3">
                <button class="w-full bg-navy text-ivory px-4 py-2 text-xs font-bold uppercase hover:bg-gold hover:text-navy transition">
                    <i class="fas fa-save mr-1"></i>Simpan URL
                </button>
            </form>
            <p class="text-[10px] text-slate mt-2"><i class="fas fa-info-circle mr-1"></i>File JSON di server Anda yang berisi info versi terbaru.</p>
        </div>

        <!-- Cek + Jalankan -->
        <div class="bg-white border border-gray-200 p-5">
            <p class="text-xs font-bold uppercase text-slate mb-3"><i class="fas fa-cloud-download-alt text-gold-muted mr-1"></i>Cek & Update</p>
            <div class="flex gap-2 mb-3">
                <button id="btnCheck" class="flex-1 bg-gold text-navy px-4 py-2 text-xs font-bold uppercase hover:bg-navy hover:text-gold transition">
                    <i class="fas fa-sync mr-1"></i>Cek Update
                </button>
                <button id="btnRun" class="hidden flex-1 bg-green-600 text-white px-4 py-2 text-xs font-bold uppercase hover:bg-green-700 transition">
                    <i class="fas fa-rocket mr-1"></i>Update
                </button>
            </div>
            <div id="checkResult" class="text-xs text-slate bg-ivory p-3 min-h-[60px]"></div>
        </div>
    </div>

    <!-- Upload Manual -->
    <form method="post" action="<?= base_url('admin/update/upload') ?>" enctype="multipart/form-data" class="bg-white border border-gray-200 p-5 mb-6">
        <p class="text-xs font-bold uppercase text-slate mb-3"><i class="fas fa-upload text-gold-muted mr-1"></i>Update Manual (ZIP)</p>
        <p class="text-[11px] text-slate mb-3">Untuk update offline atau darurat. Upload paket ZIP dari pengembang.</p>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="file" name="pkg" accept=".zip" required class="flex-1 text-sm border border-gray-200 px-3 py-2">
            <input type="text" name="version" placeholder="v1.2.0 (opsional)" class="border border-gray-200 px-3 py-2 text-sm sm:w-32">
            <button class="bg-navy text-ivory px-5 py-2 text-xs font-bold uppercase hover:bg-gold hover:text-navy transition">
                <i class="fas fa-cloud-upload-alt mr-1"></i>Terapkan
            </button>
        </div>
    </form>

    <!-- Riwayat Update -->
    <div class="bg-white border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold uppercase text-slate"><i class="fas fa-history text-gold-muted mr-1"></i>Riwayat Update</p>
            <div class="flex gap-2">
                <?php if (!empty($log)): ?>
                <button id="btnClearLog" class="text-[10px] uppercase text-red-600 hover:text-red-800 font-bold">
                    <i class="fas fa-trash mr-1"></i>Hapus Log
                </button>
                <a href="<?= base_url('admin/update/download_log') ?>" class="text-[10px] uppercase text-blue-600 hover:text-blue-800 font-bold">
                    <i class="fas fa-download mr-1"></i>Unduh
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($log)): ?>
        <div class="text-center py-8 text-slate">
            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm">Belum ada riwayat update.</p>
        </div>
        <?php else: ?>
        <div class="space-y-1">
            <?php foreach ($log as $e):
                $success = strpos($e['status'], 'SUCCESS') !== false;
            ?>
            <div class="flex items-center justify-between text-xs border-b border-gray-100 py-2 hover:bg-ivory-warm px-2 -mx-2 transition">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <i class="fas fa-<?= $success ? 'check-circle text-green-500' : 'times-circle text-red-500' ?>"></i>
                    <span class="font-mono font-semibold text-navy">v<?= html_escape($e['version']) ?></span>
                    <span class="text-slate truncate flex-1"><?= html_escape(substr($e['status'], 0, 60)) ?></span>
                </div>
                <span class="text-slate text-[10px] ml-2 whitespace-nowrap"><?= html_escape($e['time']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function(){
    var btnCheck = document.getElementById('btnCheck');
    var btnRun = document.getElementById('btnRun');
    var res = document.getElementById('checkResult');
    var btnClear = document.getElementById('btnClearLog');

    if (btnCheck) {
        btnCheck.addEventListener('click', function(){
            res.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Memeriksa server update...';
            btnCheck.disabled = true;
            btnCheck.classList.add('opacity-50');

            fetch('<?= base_url('admin/update/check') ?>')
                .then(function(r){ return r.json(); })
                .then(function(d){
                    btnCheck.disabled = false;
                    btnCheck.classList.remove('opacity-50');

                    if (!d.ok) {
                        res.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>' + d.msg + '</span>';
                        return;
                    }
                    if (d.has_update) {
                        res.innerHTML = '<span class="text-green-700"><i class="fas fa-check-circle mr-1"></i>Update tersedia: <b>v' + d.latest + '</b>'
                            + (d.released ? ' (' + d.released + ')' : '') + '</span>'
                            + (d.notes && d.notes.length ? '<div class="mt-2 text-[11px]">' + d.notes.map(function(n){ return '• ' + n; }).join('<br>') + '</div>' : '');
                        btnRun.classList.remove('hidden');
                    } else {
                        res.innerHTML = '<span class="text-slate"><i class="fas fa-info-circle mr-1"></i>Sudah versi terbaru (v' + d.current + ')</span>';
                        btnRun.classList.add('hidden');
                    }
                })
                .catch(function(){
                    btnCheck.disabled = false;
                    btnCheck.classList.remove('opacity-50');
                    res.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal menghubungi server update.</span>';
                });
        });
    }

    if (btnRun) {
        btnRun.addEventListener('click', function(){
            if (confirm('Jalankan update?\n\nSistem akan:\n1. Backup database otomatis\n2. Snapshot kode\n3. Unduh & terapkan update\n4. Rollback jika gagal\n\nLanjutkan?')) {
                location.href = '<?= base_url('admin/update/run') ?>';
            }
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', function(){
            if (confirm('Hapus semua riwayat update?')) {
                location.href = '<?= base_url('admin/update/clear_log') ?>';
            }
        });
    }
})();
</script>