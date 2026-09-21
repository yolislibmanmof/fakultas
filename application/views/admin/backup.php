<div class="p-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div><h1 class="text-2xl font-bold text-navy">🛡️ Backup & Restore</h1>
        <p class="text-sm text-slate">Backup lengkap database dalam file SQL — aman disimpan offline.</p></div>
        <a href="<?= base_url('admin/backup/create') ?>" class="bg-gold text-navy px-5 py-3 text-xs font-bold uppercase tracking-wider hover:bg-navy hover:text-gold transition"><i class="fas fa-database mr-2"></i>Buat Backup Sekarang</a>
    </div>

    <?php if ($this->session->flashdata('ok')): ?><div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-4"><?= $this->session->flashdata('ok') ?></div><?php endif; ?>
    <?php if ($this->session->flashdata('err')): ?><div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4"><?= $this->session->flashdata('err') ?></div><?php endif; ?>

    <div class="bg-white border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-navy text-ivory text-left text-xs uppercase tracking-wider"><tr><th class="p-3">File</th><th class="p-3">Ukuran</th><th class="p-3">Tanggal</th><th class="p-3 text-right">Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($files)): ?><tr><td colspan="4" class="p-6 text-center text-slate">Belum ada backup.</td></tr><?php endif; ?>
                <?php foreach ($files as $f): ?>
                <tr class="border-t border-gray-100 hover:bg-ivory">
                    <td class="p-3 font-mono text-xs"><?= $f['name'] ?></td>
                    <td class="p-3"><?= $f['size'] ?></td>
                    <td class="p-3"><?= $f['date'] ?></td>
                    <td class="p-3 text-right space-x-2">
                        <a href="<?= base_url('admin/backup/download/' . $f['name']) ?>" class="text-blue-600 hover:underline text-xs"><i class="fas fa-download"></i> Unduh</a>
                        <a href="<?= base_url('admin/backup/restore/' . $f['name']) ?>" onclick="return confirm('PULIHKAN dari <?= $f['name'] ?>? Data saat ini akan DIGANTI.')" class="text-orange-600 hover:underline text-xs"><i class="fas fa-undo"></i> Pulihkan</a>
                        <a href="<?= base_url('admin/backup/delete/' . $f['name']) ?>" onclick="return confirm('Hapus backup ini?')" class="text-red-600 hover:underline text-xs"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-xs text-slate mt-4"><i class="fas fa-info-circle mr-1"></i>Auto-backup harian aktif. Folder backup dilindungi <code>.htaccess</code> (tidak bisa diakses dari browser).</p>
</div>