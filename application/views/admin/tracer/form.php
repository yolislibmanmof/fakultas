<!-- Page Header -->
<div class="mb-10">
    <p class="editorial-label text-gold-muted mb-3">Alumni Engagement</p>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        Pengaturan <em class="italic text-gold-muted">Tracer Study</em>
    </h1>
    <p class="text-slate mt-2 text-sm">Atur formulir tracer study dan banner ajakan untuk alumni.</p>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open('admin/tracer/update', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-3xl']) ?>

    <div class="space-y-8">

        <!-- Link Form -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Formulir Tracer</p>
            <div>
                <label class="block editorial-label text-navy mb-2">URL Form Tracer Study <span class="text-red-500">*</span></label>
                <input type="url" name="form_url" value="<?= set_value('form_url', $settings ? $settings->form_url : '') ?>" required placeholder="https://forms.gle/..."
                    class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                <p class="text-xs text-slate mt-2 italic">Tempel URL lengkap dari Google Forms, Microsoft Forms, atau platform sejenis.</p>
            </div>
        </div>

        <!-- Banner Konten -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Banner Ajakan</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Judul Banner</label>
                    <input type="text" name="title" value="<?= set_value('title', $settings ? $settings->title : '') ?>"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('description', $settings ? $settings->description : '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Visibilitas</p>
            <label class="block">
                <label class="block editorial-label text-navy mb-2">Status Tampilan</label>
                <select name="is_active" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="1" <?= set_select('is_active', '1', !$settings || $settings->is_active) ?>>Aktif (tampilkan banner di halaman Kemahasiswaan)</option>
                    <option value="0" <?= set_select('is_active', '0', $settings && !$settings->is_active) ?>>Nonaktif (sembunyikan banner)</option>
                </select>
            </label>
        </div>

        <!-- Preview -->
        <?php if ($settings): ?>
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Preview Banner</p>
            <div class="bg-navy text-ivory p-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 hero-pattern"></div>
                <div class="relative">
                    <i class="fas fa-graduation-cap text-3xl text-gold mb-3"></i>
                    <h3 class="font-serif text-xl font-light mb-2"><?= html_escape($settings->title) ?></h3>
                    <p class="text-sm text-ivory/70 mb-4"><?= html_escape(character_limiter($settings->description ?? '', 100)) ?></p>
                    <span class="inline-block bg-gold text-navy px-4 py-2 text-[10px] uppercase tracking-editorial font-bold">Isi Tracer Study</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Perubahan
        </button>
    </div>

<?= form_close() ?>