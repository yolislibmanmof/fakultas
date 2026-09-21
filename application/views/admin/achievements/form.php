<div class="mb-10">
    <a href="<?= base_url('admin/achievements') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
        <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
        Kembali ke Daftar
    </a>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        <?= $item ? 'Edit <em class="italic text-gold-muted">Prestasi</em>' : 'Tambah <em class="italic text-gold-muted">Prestasi</em>' ?>
    </h1>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 text-sm"><?= validation_errors() ?></div>
<?php endif; ?>

<?= form_open_multipart($item ? 'admin/achievements/update/' . $item->id : 'admin/achievements/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl space-y-8']) ?>

    <div>
        <p class="editorial-label text-gold-muted mb-5">Data Mahasiswa</p>
        <div class="grid md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
                <label class="block editorial-label text-navy mb-2">Nama Mahasiswa <span class="text-red-500">*</span></label>
                <input type="text" name="student_name" value="<?= set_value('student_name', $item ? $item->student_name : '') ?>" required class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">NIM</label>
                <input type="text" name="nim" value="<?= set_value('nim', $item ? $item->nim : '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
            </div>
            <div class="md:col-span-3">
                <label class="block editorial-label text-navy mb-2">Program Studi</label>
                <select name="study_program_id" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <option value="">- Pilih Prodi -</option>
                    <?php foreach ($programs as $p): ?>
                    <option value="<?= $p->id ?>" <?= set_select('study_program_id', (string)$p->id, $item && $item->study_program_id == $p->id) ?>><?= html_escape($p->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="pt-6 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Detail Prestasi</p>
        <div class="space-y-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Judul Prestasi <span class="text-red-500">*</span></label>
                <input type="text" name="achievement_title" value="<?= set_value('achievement_title', $item ? $item->achievement_title : '') ?>" required placeholder="Contoh: Juara 1 Hackathon Nasional" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Tingkat</label>
                    <select name="level" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <?php foreach ($levels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= set_select('level', $key, $item && $item->level == $key) ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Tahun <span class="text-red-500">*</span></label>
                    <!-- 🔥 FIX: Tahun dinamis (bukan hardcoded 2035) -->
                    <input type="number" name="year" min="1990" max="<?= date('Y') + 1 ?>" value="<?= set_value('year', $item ? $item->year : date('Y')) ?>" required class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Penyelenggara</label>
                    <!-- 🔥 FITUR GILA: Auto-suggest organizer (datalist) -->
                    <input type="text" name="organizer" list="organizerList" value="<?= set_value('organizer', $item ? $item->organizer : '') ?>" placeholder="Contoh: Kemdikbud" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <datalist id="organizerList">
                        <option value="Kemdikbud">
                        <option value="Kemendikbudristek">
                        <option value="Universitas">
                        <option value="Fakultas">
                        <option value="Pemerintah Daerah">
                        <option value="Swasta">
                        <option value="Internasional">
                    </datalist>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-6 border-t border-gray-200">
        <label class="block editorial-label text-navy mb-2">Bukti Dokumen / Foto (opsional)</label>
        <div class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-5 text-center">
            <i class="fas fa-certificate text-3xl text-slate mb-2"></i>
            <input type="file" name="document_proof" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                class="block w-full text-sm text-slate file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-xs hover:file:bg-gold hover:file:text-navy file:cursor-pointer">
            <p class="text-xs text-slate mt-2">PDF/DOC/JPG/PNG, maksimal 5MB.</p>
        </div>
        <?php if ($item && $item->document_proof): ?>
            <div class="mt-3 p-3 bg-ivory-warm/30 border border-gray-200 flex items-center justify-between">
                <p class="text-xs text-slate">
                    <i class="fas fa-paperclip text-gold-muted mr-1"></i>Bukti saat ini: 
                    <span class="font-mono font-semibold text-navy"><?= html_escape($item->document_proof) ?></span>
                </p>
                <a href="<?= base_url('assets/uploads/achievements/' . $item->document_proof) ?>" target="_blank" class="text-xs text-gold-muted hover:text-gold font-semibold">
                    <i class="fas fa-external-link-alt mr-1"></i>Lihat
                </a>
            </div>
            <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                <input type="checkbox" name="remove_doc" value="1" class="accent-red-600"> Hapus bukti
            </label>
        <?php endif; ?>
    </div>

    <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i><?= $item ? 'Update' : 'Simpan' ?>
        </button>
        <?php if ($item): ?>
        <!-- 🔥 FITUR GILA: Duplicate button (hanya muncul di edit mode) -->
        <a href="<?= base_url('admin/achievements/duplicate/' . $item->id) ?>" onclick="return confirm('Duplikat prestasi ini sebagai entry baru?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/achievements') ?>" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">Batal</a>
    </div>

<?= form_close() ?>