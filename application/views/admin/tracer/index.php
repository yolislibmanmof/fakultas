<div class="mb-10">
    <p class="editorial-label text-gold-muted mb-3">Alumni Tracking</p>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        Tracer <em class="italic text-gold-muted">Study</em>
    </h1>
    <p class="text-slate mt-2 text-sm">Konfigurasi formulir pelacakan alumni yang tampil di halaman Kemahasiswaan.</p>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-3.5 mb-6 flash-anim text-sm">
        <i class="fas fa-check-circle mr-2"></i><?= $this->session->flashdata('success') ?>
    </div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-3.5 mb-6 flash-anim text-sm">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= $this->session->flashdata('error') ?>
    </div>
<?php endif; ?>

<!-- 🔥 Stats Alumni -->
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white border border-gray-200 p-5 text-center">
        <div class="font-serif text-3xl text-navy"><?= $alumni_total ?? 0 ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Total Alumni</p>
    </div>
    <div class="bg-white border border-green-200 p-5 text-center">
        <div class="font-serif text-3xl text-green-700"><?= $alumni_count ?? 0 ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Approved</p>
    </div>
    <div class="bg-white border border-yellow-200 p-5 text-center">
        <div class="font-serif text-3xl text-yellow-700"><?= $alumni_pending ?? 0 ?></div>
        <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Pending</p>
    </div>
</div>

<!-- 🔥 Quick Actions -->
<div class="grid md:grid-cols-3 gap-4 mb-8">
    <a href="<?= base_url('admin/tracer/export_alumni') ?>" class="bg-white border border-gray-200 p-5 hover:border-gold hover:shadow-md transition flex items-center gap-4 group">
        <div class="w-12 h-12 bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0 group-hover:bg-green-600 group-hover:text-ivory transition">
            <i class="fas fa-file-csv text-xl"></i>
        </div>
        <div>
            <p class="font-serif text-lg text-navy">Export Alumni</p>
            <p class="text-xs text-slate mt-1">Download CSV untuk Google Form</p>
        </div>
    </a>
    <a href="<?= base_url('admin/tracer/preview') ?>" target="_blank" class="bg-white border border-gray-200 p-5 hover:border-gold hover:shadow-md transition flex items-center gap-4 group">
        <div class="w-12 h-12 bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-ivory transition">
            <i class="fas fa-external-link-alt text-xl"></i>
        </div>
        <div>
            <p class="font-serif text-lg text-navy">Preview Publik</p>
            <p class="text-xs text-slate mt-1">Lihat tampilan di halaman Kemahasiswaan</p>
        </div>
    </a>
    <a href="<?= base_url('admin/alumni') ?>" class="bg-white border border-gray-200 p-5 hover:border-gold hover:shadow-md transition flex items-center gap-4 group">
        <div class="w-12 h-12 bg-gold/10 text-gold flex items-center justify-center flex-shrink-0 group-hover:bg-gold group-hover:text-navy transition">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <p class="font-serif text-lg text-navy">Kelola Alumni</p>
            <p class="text-xs text-slate mt-1">Approve/reject pendaftaran alumni</p>
        </div>
    </a>
</div>

<div class="grid lg:grid-cols-2 gap-8 items-start">

    <!-- Form -->
    <?= form_open('admin/tracer/update', ['class' => 'bg-white border border-gray-200 p-6 md:p-8 space-y-6', 'id' => 'tracerForm']) ?>
        <div>
            <label class="block editorial-label text-navy mb-2">Judul Section <span class="text-red-500">*</span></label>
            <input type="text" name="tracer_title" id="titleInput" value="<?= html_escape($s['title']) ?>" required maxlength="255" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
        </div>
        <div>
            <label class="block editorial-label text-navy mb-2">Deskripsi</label>
            <textarea name="tracer_description" id="descInput" rows="4" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= html_escape($s['description']) ?></textarea>
            <p class="text-[10px] text-slate mt-1"><span id="descCount">0</span>/2000 karakter</p>
        </div>
        <div>
            <label class="block editorial-label text-navy mb-2">URL Formulir (Google Form / lainnya)</label>
            <div class="flex gap-2">
                <input type="url" name="tracer_form_url" id="urlInput" value="<?= html_escape($s['form_url']) ?>" placeholder="https://forms.gle/..." maxlength="500" class="flex-1 px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                <!-- 🔥 Test URL button -->
                <button type="button" id="testBtn" class="px-4 py-2.5 bg-navy text-ivory text-xs uppercase tracking-editorial font-semibold hover:bg-gold hover:text-navy transition">
                    <i class="fas fa-plug mr-1"></i>Test
                </button>
            </div>
            <div id="urlTestResult" class="text-xs mt-2 hidden"></div>
        </div>
        <label class="flex items-center gap-3 pt-4 border-t border-gray-200 cursor-pointer">
            <input type="checkbox" name="tracer_is_active" id="activeInput" value="1" <?= $s['is_active'] == '1' ? 'checked' : '' ?> class="w-5 h-5 accent-navy">
            <div>
                <p class="text-sm font-medium text-navy">Section Aktif</p>
                <p class="text-xs text-slate">Tampilkan di halaman Kemahasiswaan publik</p>
            </div>
        </label>
        <div class="pt-4 flex gap-3">
            <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs"><i class="fas fa-save mr-2"></i>Simpan Pengaturan</button>
            <!-- 🔥 Generate reminder template -->
            <button type="button" id="genReminder" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs">
                <i class="fas fa-comment-dots mr-2"></i>Template Pesan
            </button>
        </div>
    <?= form_close() ?>

    <!-- Live Preview -->
    <div class="lg:sticky lg:top-24">
        <p class="editorial-label text-gold-muted mb-4">Preview Tampilan Publik</p>
        <div class="bg-navy text-ivory p-8 md:p-10 text-center relative overflow-hidden">
            <div class="absolute inset-0 hero-pattern"></div>
            <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-gold/40"></div>
            <div class="absolute bottom-0 right-0 w-20 h-20 border-b-2 border-r-2 border-gold/40"></div>
            <div class="relative">
                <i class="fas fa-graduation-cap text-4xl text-gold mb-5"></i>
                <p class="editorial-label text-ivory/60 mb-3">Alumni Tracking</p>
                <h3 id="pvTitle" class="font-serif text-2xl md:text-3xl font-light tracking-tight mb-4"><?= html_escape($s['title']) ?></h3>
                <p id="pvDesc" class="text-ivory/70 text-sm leading-relaxed mb-6"><?= html_escape(character_limiter($s['description'], 140)) ?: '(Deskripsi akan tampil di sini)' ?></p>
                <span id="pvBtn" class="btn-gold inline-block px-8 py-3 font-semibold uppercase tracking-editorial text-xs <?= $s['is_active'] == '1' ? 'opacity-100' : 'opacity-40' ?>">
                    <i class="fas fa-external-link-alt mr-2"></i>Isi Tracer Study
                </span>
                <p class="text-[10px] text-ivory/50 mt-4 font-mono"><?= $alumni_count ?? 0 ?> alumni terdaftar</p>
            </div>
        </div>
        
        <!-- 🔥 Reminder Modal -->
        <div id="reminderModal" class="hidden mt-4 bg-white border border-gray-200 p-5">
            <p class="editorial-label text-gold-muted mb-3">Template Pesan Reminder</p>
            <textarea id="reminderText" rows="6" class="w-full px-3 py-2 bg-ivory/50 border border-navy/10 text-navy text-sm font-mono"></textarea>
            <div class="flex gap-2 mt-3">
                <button type="button" onclick="copyReminder()" class="btn-outline-navy px-4 py-2 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fas fa-copy mr-1"></i>Copy
                </button>
                <a id="waLink" href="#" target="_blank" class="btn-navy px-4 py-2 text-xs uppercase tracking-editorial font-semibold">
                    <i class="fab fa-whatsapp mr-1"></i>Buka WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    var titleInput = document.getElementById('titleInput');
    var descInput = document.getElementById('descInput');
    var urlInput = document.getElementById('urlInput');
    var activeInput = document.getElementById('activeInput');
    var testBtn = document.getElementById('testBtn');
    var urlTestResult = document.getElementById('urlTestResult');
    var descCount = document.getElementById('descCount');
    var genReminder = document.getElementById('genReminder');
    var reminderModal = document.getElementById('reminderModal');
    var reminderText = document.getElementById('reminderText');
    var waLink = document.getElementById('waLink');
    
    // ===== Live preview =====
    function updatePreview(){
        document.getElementById('pvTitle').textContent = titleInput.value || 'Judul Tracer Study';
        document.getElementById('pvDesc').textContent = descInput.value.substring(0, 140) || '(Deskripsi akan tampil di sini)';
        document.getElementById('pvBtn').style.opacity = activeInput.checked ? '1' : '0.4';
    }
    
    titleInput.addEventListener('input', updatePreview);
    descInput.addEventListener('input', function(){
        descCount.textContent = descInput.value.length;
        updatePreview();
    });
    activeInput.addEventListener('change', updatePreview);
    descCount.textContent = descInput.value.length;
    
    // ===== Test URL =====
    testBtn.addEventListener('click', function(){
        var url = urlInput.value.trim();
        if (!url) { alert('Masukkan URL terlebih dahulu.'); return; }
        
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i>Testing...';
        urlTestResult.classList.remove('hidden');
        urlTestResult.innerHTML = '<span class="text-blue-600"><i class="fas fa-circle-notch fa-spin mr-1"></i>Mengecek koneksi...</span>';
        
        fetch('<?= base_url('admin/tracer/test_url') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'url=' + encodeURIComponent(url)
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test';
            
            if (data.ok) {
                urlTestResult.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>URL dapat diakses (HTTP ' + data.code + ')</span>';
            } else {
                urlTestResult.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>' + data.message + '</span>';
            }
        })
        .catch(function(err){
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test';
            urlTestResult.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Error</span>';
        });
    });
    
    // ===== Generate reminder template =====
    genReminder.addEventListener('click', function(){
        var url = urlInput.value.trim() || '[URL Tracer Study]';
        var alumniCount = <?= json_encode($alumni_count ?? 0) ?>;
        
        fetch('<?= base_url('admin/tracer/reminder_message') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'name=[Nama Alumni]'
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            reminderText.value = data.message || 'Halo [Nama Alumni],\n\nKami mengundang Anda untuk mengisi Tracer Study.\n\nLink: ' + url + '\n\nTerima kasih!';
            waLink.href = data.wa_link || 'https://wa.me/?text=' + encodeURIComponent(reminderText.value);
            reminderModal.classList.remove('hidden');
        })
        .catch(function(){
            reminderText.value = 'Halo [Nama Alumni],\n\nKami mengundang Anda untuk mengisi Tracer Study.\n\nLink: ' + url + '\n\nTerima kasih!';
            waLink.href = 'https://wa.me/?text=' + encodeURIComponent(reminderText.value);
            reminderModal.classList.remove('hidden');
        });
    });
    
    window.copyReminder = function(){
        reminderText.select();
        document.execCommand('copy');
        alert('Template pesan berhasil dicopy ke clipboard!');
    };
})();
</script>