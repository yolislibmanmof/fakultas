<?php
$categories = $categories ?? ['pmb' => 'PMB', 'akademik' => 'Akademik', 'ujian' => 'Ujian', 'libur' => 'Libur', 'wisuda' => 'Wisuda', 'seminar' => 'Seminar', 'lainnya' => 'Lainnya'];
?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <a href="<?= base_url('admin/kalender') ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-editorial text-slate hover:text-navy transition mb-3">
            <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M20 5H2M6 1L2 5l4 4" stroke="currentColor" stroke-width="1.5"/></svg>
            Kembali ke Daftar
        </a>
        <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
            <?= $event ? 'Edit <em class="italic text-gold-muted">Agenda</em>' : 'Jadwalkan <em class="italic text-gold-muted">Agenda</em>' ?>
        </h1>
    </div>
</div>

<?php if (validation_errors()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flex items-start gap-3 text-sm">
        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
        <div><?= validation_errors() ?></div>
    </div>
<?php endif; ?>

<?= form_open($event ? 'admin/kalender/update/' . $event->id : 'admin/kalender/store', ['class' => 'bg-white border border-gray-200 p-6 md:p-10 max-w-3xl', 'id' => 'eventForm']) ?>

    <div class="space-y-8">

        <!-- Informasi Event -->
        <div>
            <p class="editorial-label text-gold-muted mb-5">Informasi Agenda</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Nama Agenda <span class="text-red-500">*</span></label>
                    <input type="text" name="event_name" id="eventName" value="<?= set_value('event_name', $event ? $event->event_name : '') ?>" required
                        placeholder="Contoh: Ujian Tengah Semester Genap" maxlength="255"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <!-- 🔥 Quick suggestions -->
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" onclick="fillName('Ujian Tengah Semester')" class="text-[10px] px-2 py-1 bg-ivory-warm border border-navy/10 text-navy hover:bg-gold hover:text-navy transition">UTS</button>
                        <button type="button" onclick="fillName('Ujian Akhir Semester')" class="text-[10px] px-2 py-1 bg-ivory-warm border border-navy/10 text-navy hover:bg-gold hover:text-navy transition">UAS</button>
                        <button type="button" onclick="fillName('Wisuda')" class="text-[10px] px-2 py-1 bg-ivory-warm border border-navy/10 text-navy hover:bg-gold hover:text-navy transition">Wisuda</button>
                        <button type="button" onclick="fillName('Libur Semester')" class="text-[10px] px-2 py-1 bg-ivory-warm border border-navy/10 text-navy hover:bg-gold hover:text-navy transition">Libur</button>
                        <button type="button" onclick="fillName('Penerimaan Mahasiswa Baru')" class="text-[10px] px-2 py-1 bg-ivory-warm border border-navy/10 text-navy hover:bg-gold hover:text-navy transition">PMB</button>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block editorial-label text-navy mb-2">Kategori</label>
                        <select name="category" id="eventCategory" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= $key ?>" <?= set_select('category', $key, $event && $event->category == $key) ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Status</label>
                        <select name="is_active" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <option value="1" <?= set_select('is_active', '1', !$event || $event->is_active) ?>>Aktif (tampil di publik)</option>
                            <option value="0" <?= set_select('is_active', '0', $event && !$event->is_active) ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Periode -->
        <div class="pt-6 border-t border-gray-200">
            <p class="editorial-label text-gold-muted mb-5">Periode Kegiatan</p>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="startDate" value="<?= set_value('start_date', $event ? $event->start_date : '') ?>" required
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="endDate" value="<?= set_value('end_date', $event ? $event->end_date : '') ?>"
                        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono">
                    <p class="text-[10px] text-slate mt-1 italic">Kosongkan jika hanya 1 hari.</p>
                </div>
            </div>
            
            <!-- 🔥 Date validation feedback -->
            <div id="dateError" class="hidden mt-3 p-3 bg-red-50 border border-red-200 text-red-700 text-xs"></div>
            
            <!-- 🔥 Overlap warning -->
            <div id="overlapWarning" class="hidden mt-3 p-3 bg-amber-50 border border-amber-200 text-amber-800 text-xs"></div>
        </div>

        <!-- Deskripsi -->
        <div class="pt-6 border-t border-gray-200">
            <label class="block editorial-label text-navy mb-2">Deskripsi (opsional)</label>
            <textarea name="description" rows="3" maxlength="1000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= set_value('description', $event ? $event->description : '') ?></textarea>
            <p class="text-[10px] text-slate mt-1">Maks 1000 karakter.</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Agenda
        </button>
        <?php if ($event): ?>
        <a href="<?= base_url('admin/kalender/duplicate/' . $event->id) ?>" onclick="return confirm('Duplikat agenda ini?')" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-copy mr-2"></i>Duplikat
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/kalender') ?>" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            Batal
        </a>
    </div>

<?= form_close() ?>

<script>
(function(){
    var nameInput = document.getElementById('eventName');
    var startDate = document.getElementById('startDate');
    var endDate = document.getElementById('endDate');
    var dateError = document.getElementById('dateError');
    var overlapWarning = document.getElementById('overlapWarning');
    var form = document.getElementById('eventForm');
    var excludeId = <?= json_encode($event ? $event->id : 0) ?>;
    
    // ===== Quick fill name =====
    window.fillName = function(name){
        nameInput.value = name;
        nameInput.focus();
    };
    
    // ===== Date validation & overlap check =====
    function validateDates(){
        dateError.classList.add('hidden');
        overlapWarning.classList.add('hidden');
        
        var s = startDate.value;
        var e = endDate.value;
        
        if (!s) return true;
        
        // Check valid date
        if (!/^\d{4}-\d{2}-\d{2}$/.test(s)) {
            showError('Format tanggal mulai tidak valid.');
            return false;
        }
        
        if (e) {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(e)) {
                showError('Format tanggal selesai tidak valid.');
                return false;
            }
            if (e < s) {
                showError('Tanggal selesai tidak boleh sebelum tanggal mulai.');
                return false;
            }
            
            var days = Math.floor((new Date(e) - new Date(s)) / 86400000) + 1;
            if (days > 365) {
                showError('Durasi agenda tidak boleh lebih dari 365 hari.');
                return false;
            }
        }
        
        // 🔥 AJAX overlap check
        checkOverlap(s, e || s);
        return true;
    }
    
    function showError(msg){
        dateError.textContent = msg;
        dateError.classList.remove('hidden');
    }
    
    var debounceTimer;
    function checkOverlap(s, e){
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function(){
            overlapWarning.classList.add('hidden');
            
            fetch('<?= base_url('admin/kalender/check_overlap') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'start=' + s + '&end=' + e + '&exclude_id=' + excludeId
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (data.overlap) {
                    var msg = '⚠️ Tanggal ini overlap dengan ' + data.count + ' agenda lain: ';
                    msg += data.events.map(function(ev){ return ev.name + ' (' + ev.start + ' → ' + ev.end + ')'; }).join(', ');
                    overlapWarning.textContent = msg;
                    overlapWarning.classList.remove('hidden');
                }
            })
            .catch(function(){/* silent */});
        }, 500);
    }
    
    if (startDate) startDate.addEventListener('change', validateDates);
    if (endDate) endDate.addEventListener('change', validateDates);
    
    // ===== Auto-set end_date = start_date jika kosong =====
    if (startDate) {
        startDate.addEventListener('change', function(){
            if (!endDate.value) endDate.value = startDate.value;
        });
    }
    
    // ===== Block submit if date invalid =====
    if (form) {
        form.addEventListener('submit', function(e){
            if (!validateDates()) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // ===== Auto-fill end date on first load =====
    if (startDate.value && !endDate.value) {
        endDate.value = startDate.value;
    }
})();
</script>