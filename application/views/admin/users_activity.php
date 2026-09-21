<div class="mb-8">
    <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <div class="flex items-center gap-4 mb-4">
        <div class="w-14 h-14 rounded-full bg-navy text-gold flex items-center justify-center font-serif font-bold text-xl">
            <?= strtoupper(substr($user->full_name ?: $user->username, 0, 1)) ?>
        </div>
        <div>
            <h1 class="font-serif text-3xl font-light text-navy"><?= html_escape($user->full_name) ?></h1>
            <p class="text-sm text-slate font-mono">@<?= html_escape($user->username) ?></p>
        </div>
    </div>
</div>

<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-navy text-ivory">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">Waktu</th>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">Aksi</th>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">Deskripsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($logs)): ?>
                <tr><td colspan="3" class="px-6 py-12 text-center text-slate">Belum ada aktivitas tercatat.</td></tr>
                <?php else: foreach ($logs as $l): ?>
                <tr class="hover:bg-ivory-warm/40 transition">
                    <td class="px-6 py-3 text-xs text-slate font-mono whitespace-nowrap"><?= date('d M Y H:i', strtotime($l->created_at)) ?></td>
                    <td class="px-6 py-3"><span class="bg-gold/10 text-gold-muted text-[10px] uppercase tracking-editorial font-bold px-2 py-1"><?= html_escape($l->action ?? '-') ?></span></td>
                    <td class="px-6 py-3 text-sm text-navy"><?= html_escape($l->description ?? '-') ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>