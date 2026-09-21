<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/courses') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $course ? 'Edit <em class="italic text-gold-muted">Mata Kuliah</em>' : 'Tambah <em class="italic text-gold-muted">Mata Kuliah</em>' ?>
        </h1>
    </div>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open_multipart($course ? 'admin/courses/update/' . $course->id : 'admin/courses/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-4xl']) ?>

    <div class="space-y-8">

        <!-- Identitas MK -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Identitas Mata Kuliah</p>
            <div class="grid md:grid-cols-4 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Kode MK <span class="text-red-500">*</span></label>
                    <!-- 🔥 Auto-generate button -->
                    <div class="flex gap-2">
                        <input type="text" name="course_code" id="courseCode" value="<?= set_value('course_code', $course ? $course->course_code : '') ?>" required placeholder="IF101"
                            class="flex-1 px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono uppercase">
                        <?php if (!$course): ?>
                        <button type="button" id="autoCode" class="px-3 py-2.5 bg-navy text-ivory text-xs uppercase tracking-editorial font-semibold hover:bg-gold hover:text-navy transition" title="Auto-generate">
                            <i class="fas fa-magic"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block editorial-label text-navy mb-2">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="courseName" value="<?= set_value('name', $course ? $course->name : '') ?>" required
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
            </div>
        </div>

        <!-- Bobot & Penempatan -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Bobot & Penempatan</p>
            <div class="grid md:grid-cols-3 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">SKS <span class="text-red-500">*</span></label>
                    <!-- 🔥 FIX: Sinkron max dengan controller (12) -->
                    <input type="number" name="sks" min="1" max="12" value="<?= set_value('sks', $course ? $course->sks : 3) ?>" required
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Semester <span class="text-red-500">*</span></label>
                    <input type="number" name="semester" min="1" max="14" value="<?= set_value('semester', $course ? $course->semester : 1) ?>" required
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Jenis <span class="text-red-500">*</span></label>
                    <select name="course_type" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <option value="wajib" <?= set_select('course_type', 'wajib', !$course || $course->course_type == 'wajib') ?>>Wajib</option>
                        <option value="pilihan" <?= set_select('course_type', 'pilihan', $course && $course->course_type == 'pilihan') ?>>Pilihan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Afiliasi Prodi (multi) -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-4">Program Studi (bisa lebih dari satu)</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-ivory-warm/30 p-5 border border-navy/10">
                <?php foreach ($programs as $p): ?>
                <label class="flex items-center gap-3 p-3 bg-white border border-navy/10 hover:border-navy/30 cursor-pointer transition">
                    <input type="checkbox" name="prodi_ids[]" value="<?= $p->id ?>" <?= in_array($p->id, $checked) ? 'checked' : '' ?> class="accent-navy">
                    <span class="text-sm text-navy"><?= html_escape($p->name) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Deskripsi Mata Kuliah</label>
            <textarea name="description" rows="4" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('description', $course ? $course->description : '') ?></textarea>
            <p class="text-xs text-slate mt-1">Maksimal 2000 karakter.</p>
        </div>

        <!-- Silabus -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">File Silabus (opsional)</label>
            <div class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-5 text-center">
                <i class="fas fa-file-pdf text-3xl text-slate mb-2"></i>
                <input type="file" name="syllabus_file" id="syllabusInput" accept=".pdf,.doc,.docx"
                    class="block w-full text-sm text-slate file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-xs hover:file:bg-gold hover:file:text-navy file:cursor-pointer">
                <p class="text-xs text-slate mt-2">PDF/DOC/DOCX, maksimal 8MB.</p>
            </div>
            <?php if ($course && $course->syllabus_file): ?>
                <div class="mt-3 p-3 bg-ivory-warm/30 border border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-slate">
                        <i class="fas fa-paperclip text-gold-muted mr-1"></i>
                        Silabus saat ini: <span class="font-mono font-semibold text-navy"><?= html_escape($course->syllabus_file) ?></span>
                    </p>
                    <a href="<?= base_url('assets/uploads/syllabus/' . $course->syllabus_file) ?>" target="_blank" class="text-xs text-gold-muted hover:text-gold font-semibold">
                        <i class="fas fa-external-link-alt mr-1"></i>Lihat
                    </a>
                </div>
                <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                    <input type="checkbox" name="remove_syllabus" value="1" class="accent-red-600"> Hapus silabus
                </label>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i><?= $course ? 'Update' : 'Simpan' ?> Mata Kuliah
        </button>
        <?php if ($course): ?>
        <!-- 🔥 Duplicate button (edit mode only) -->
        <a href="<?= base_url('admin/courses/duplicate/' . $course->id) ?>" onclick="return confirm('Duplikat MK ini sebagai entry baru?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/courses') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function(){
    // ===== Auto-generate course code dari nama MK =====
    var autoBtn = document.getElementById('autoCode');
    var codeInput = document.getElementById('courseCode');
    var nameInput = document.getElementById('courseName');
    
    if (autoBtn && codeInput && nameInput) {
        autoBtn.addEventListener('click', function(){
            var name = nameInput.value.trim();
            if (!name) {
                alert('Isi nama mata kuliah terlebih dahulu.');
                return;
            }
            // Extract initials (max 3 huruf) + random 3 digit
            var words = name.split(/\s+/).filter(function(w){ return w.length > 2; });
            var initials = words.slice(0, 3).map(function(w){ return w[0].toUpperCase(); }).join('');
            if (initials.length < 2) initials = name.substring(0, 2).toUpperCase();
            var num = Math.floor(100 + Math.random() * 900);
            codeInput.value = initials + num;
            codeInput.focus();
        });
    }
})();
</script>