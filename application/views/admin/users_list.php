<?php
if (!function_exists('role_badge_cls')) {
    function role_badge_cls($r) {
        $r = strtolower((string)$r);
        if (strpos($r, 'super') !== FALSE) return 'bg-navy text-gold';
        if (strpos($r, 'admin') !== FALSE) return 'bg-gold text-navy';
        if (strpos($r, 'editor') !== FALSE) return 'bg-blue-500 text-white';
        return 'bg-gray-400 text-white';
    }
}
if (!function_exists('role_label')) {
    function role_label($r) { return ucfirst(str_replace('_', ' ', (string)$r)); }
}
?>

<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="editorial-label text-gold-muted mb-2">Mission Control</p>
            <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight">Manajemen <em class="italic text-gold-muted">Users</em></h1>
            <p class="text-slate mt-2 text-sm">Kelola akun administrator, editor, dan viewer.</p>
        </div>
        <a href="<?= base_url('admin/users/create') ?>" class="btn-gold px-6 py-3 text-xs uppercase tracking-editorial font-bold inline-flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Tambah User
        </a>
    </div>
</div>

<?php if ($this->session->flashdata('success')): ?>
<div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-3 mb-6 text-sm"><i class="fas fa-check-circle mr-2"></i><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
<div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3 mb-6 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<!-- Filter -->
<form action="<?= base_url('admin/users') ?>" method="GET" class="bg-white border border-gray-200 p-4 mb-6 flex flex-wrap gap-3 items-center">
    <div class="relative flex-1 min-w-[220px]">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate"></i>
        <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Cari username / nama / email..."
            class="w-full pl-11 pr-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy focus:border-gold outline-none transition">
    </div>
    <?php if ($has_role): ?>
    <select name="role" class="px-4 py-2.5 border border-navy/10 bg-ivory/50 text-navy text-sm">
        <option value="">Semua Role</option>
        <?php foreach ($roles as $r): ?>
        <option value="<?= html_escape($r) ?>" <?= $role == $r ? 'selected' : '' ?>><?= role_label($r) ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button type="submit" class="btn-primary px-6 py-2.5 text-xs uppercase tracking-editorial font-bold"><i class="fas fa-filter mr-1"></i> Filter</button>
</form>

<!-- Table -->
<div class="bg-white border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-navy text-ivory">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">#</th>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">User</th>
                    <?php if ($has_email): ?>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">Email</th>
                    <?php endif; ?>
                    <?php if ($has_role): ?>
                    <th class="px-6 py-4 text-left text-[10px] uppercase tracking-editorial">Role</th>
                    <?php endif; ?>
                    <th class="px-6 py-4 text-right text-[10px] uppercase tracking-editorial">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-slate">
                        <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>Belum ada user.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $i => $u): ?>
                <?php $rc = role_badge_cls($u->role ?? 'viewer'); ?>
                <tr class="hover:bg-ivory-warm/40 transition">
                    <td class="px-6 py-4 text-slate text-sm"><?= $i + 1 ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-navy text-gold flex items-center justify-center font-serif font-bold flex-shrink-0">
                                <?= strtoupper(substr($u->full_name ?: $u->username, 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-medium text-navy text-sm">
                                    <?= html_escape($u->full_name) ?>
                                    <?php if ((int)$u->id === (int)$uid): ?>
                                    <span class="ml-1 text-[9px] bg-gold/20 text-gold-muted px-1.5 py-0.5 font-bold uppercase">Anda</span>
                                    <?php endif; ?>
                                </p>
                                <p class="text-xs text-slate font-mono">@<?= html_escape($u->username) ?></p>
                            </div>
                        </div>
                    </td>
                    <?php if ($has_email): ?>
                    <td class="px-6 py-4 text-slate text-sm"><?= html_escape($u->email ?? '-') ?></td>
                    <?php endif; ?>
                    <?php if ($has_role): ?>
                    <td class="px-6 py-4">
                        <span class="<?= $rc ?> text-[10px] uppercase tracking-editorial font-bold px-3 py-1"><?= role_label($u->role ?? 'viewer') ?></span>
                    </td>
                    <?php endif; ?>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="<?= base_url('admin/users/edit/' . $u->id) ?>" class="inline-flex w-8 h-8 items-center justify-center border border-gray-200 text-slate hover:bg-navy hover:text-ivory transition mr-1" title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </a>
                        <?php if ((int)$u->id !== (int)$uid): ?>
                        <a href="<?= base_url('admin/users/delete/' . $u->id) ?>" onclick="return confirm('Hapus user ini?')" class="inline-flex w-8 h-8 items-center justify-center border border-gray-200 text-slate hover:bg-red-500 hover:text-white hover:border-red-500 transition" title="Hapus">
                            <i class="fas fa-trash text-xs"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>