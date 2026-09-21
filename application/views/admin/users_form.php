<?php
if (!function_exists('role_label')) {
    function role_label($r) { return ucfirst(str_replace('_', ' ', (string)$r)); }
}
$old = $this->session->flashdata('old') ?: [];
$is_edit = !empty($user);
?>

<div class="mb-8">
    <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="font-serif text-4xl font-light text-navy tracking-tight"><?= $is_edit ? 'Edit <em class="italic text-gold-muted">User</em>' : 'Tambah <em class="italic text-gold-muted">User</em>' ?></h1>
</div>

<?php if ($this->session->flashdata('form_errors')): ?>
<div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3 mb-6 text-sm"><?= $this->session->flashdata('form_errors') ?></div>
<?php endif; ?>

<form action="<?= $is_edit ? base_url('admin/users/update/' . $user->id) : base_url('admin/users/store') ?>" method="POST"
      class="bg-white border border-gray-200 p-8 md:p-10 max-w-2xl space-y-6">

    <div class="grid md:grid-cols-2 gap-5">
        <div>
            <label class="block editorial-label text-navy mb-2">Username *</label>
            <input type="text" name="username" required value="<?= html_escape($is_edit ? $user->username : ($old['username'] ?? '')) ?>"
                class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy focus:border-gold outline-none transition">
        </div>
        <div>
            <label class="block editorial-label text-navy mb-2">Nama Lengkap *</label>
            <input type="text" name="full_name" required value="<?= html_escape($is_edit ? $user->full_name : ($old['full_name'] ?? '')) ?>"
                class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy focus:border-gold outline-none transition">
        </div>
    </div>

    <?php if ($has_email): ?>
    <div>
        <label class="block editorial-label text-navy mb-2">Email</label>
        <input type="email" name="email" value="<?= html_escape($is_edit ? ($user->email ?? '') : ($old['email'] ?? '')) ?>"
            class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy focus:border-gold outline-none transition">
    </div>
    <?php endif; ?>

    <?php if ($has_role): ?>
    <div>
        <label class="block editorial-label text-navy mb-2">Role</label>
        <select name="role" class="w-full px-4 py-3 border border-navy/10 bg-ivory/50 text-navy">
            <?php foreach ($roles as $rv): ?>
            <option value="<?= html_escape($rv) ?>" <?= ($is_edit ? ($user->role ?? '') : '') == $rv ? 'selected' : '' ?>><?= role_label($rv) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <div>
        <label class="block editorial-label text-navy mb-2">Password <?= $is_edit ? '(kosongkan jika tidak diubah)' : '*' ?></label>
        <input type="password" name="password" <?= $is_edit ? '' : 'required' ?> minlength="6"
            class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy focus:border-gold outline-none transition">
    </div>

    <div class="flex gap-3 pt-4 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 text-xs uppercase tracking-editorial font-bold"><i class="fas fa-save mr-2"></i> Simpan</button>
        <a href="<?= base_url('admin/users') ?>" class="px-8 py-3 text-xs uppercase tracking-editorial font-bold border border-navy/20 text-navy hover:bg-navy hover:text-ivory transition">Batal</a>
    </div>
</form>