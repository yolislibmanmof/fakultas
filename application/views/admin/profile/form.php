<!-- Page Header -->
<div class="mb-10">
    <p class="editorial-label text-gold-muted mb-3">Faculty Profile</p>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        Profil <em class="italic text-gold-muted">Fakultas</em>
    </h1>
    <p class="text-slate mt-2 text-sm">Konten halaman profil publik. Perubahan langsung tersimpan ke website.</p>
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

<style>
    #pvOrgWrap { position: relative; background: #F7F5F0; border: 1px solid #e5e7eb; padding: 1.25rem; overflow: auto; max-height: 720px; }
    .pv-stage { position: relative; min-width: 480px; padding: 1rem; }
    .pv-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
    .pv-svg path { fill: none; stroke: #C9A227; stroke-width: 2; stroke-linecap: round; }
    .pv-level { display: flex; justify-content: center; gap: 1rem; margin-bottom: 2.5rem; position: relative; flex-wrap: wrap; }
    .pv-level:last-child { margin-bottom: 0; }
    .pv-node { width: 132px; background: #fff; border: 1px solid #e5e7eb; border-top: 3px solid #C9A227; padding: .85rem .5rem; text-align: center; box-shadow: 0 4px 12px rgba(11,34,57,.08); animation: pvNodeIn .5s cubic-bezier(.34,1.56,.64,1) both; }
    .pv-node.dean { background: #0B2239; width: 150px; }
    .pv-node.wadek { background: #F5EFE0; }
    .pv-node.tu { background: #E0F2FE; }
    @keyframes pvNodeIn { from { transform: scale(.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .pv-photo { width: 56px; height: 56px; border-radius: 50%; overflow: hidden; margin: 0 auto .5rem; border: 2px solid rgba(201,162,39,.5); background: #0B2239; }
    .pv-photo img { width: 100%; height: 100%; object-fit: cover; }
    .pv-init { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #C9A227; font-weight: 700; font-family: 'Fraunces', serif; font-size: 1.2rem; }
    .pv-role { font-size: .5rem; letter-spacing: .2em; text-transform: uppercase; color: #B8941F; font-weight: 700; margin: 0 0 2px; }
    .pv-node.dean .pv-role { color: #C9A227; }
    .pv-name { font-size: .72rem; font-weight: 600; color: #0B2239; line-height: 1.2; margin: 0; }
    .pv-node.dean .pv-name { color: #F7F5F0; }
    .pv-sub { font-size: .55rem; color: #64748b; margin: 2px 0 0; }
    .pv-node.dean .pv-sub { color: rgba(247,245,240,.7); }
    .layout-card { cursor: pointer; }
    .layout-card input:checked + div { border-color: #C9A227; background: rgba(201,162,39,.08); }
    .json-editor { font-family: 'JetBrains Mono', monospace; font-size: .75rem; }
    .json-status { font-size: .65rem; padding: .25rem .5rem; border-radius: 3px; display: inline-block; }
    .json-status.ok { background: #dcfce7; color: #166534; }
    .json-status.err { background: #fee2e2; color: #991b1b; }
    #autoSaveStatus { position: fixed; bottom: 20px; right: 20px; padding: .5rem 1rem; background: #0B2239; color: #C9A227; border-radius: 4px; font-size: .75rem; font-family: 'JetBrains Mono', monospace; z-index: 100; transition: all .3s; opacity: 0; pointer-events: none; }
    #autoSaveStatus.visible { opacity: 1; }
</style>

<div class="grid lg:grid-cols-2 gap-8 items-start">
<div class="lg:col-span-1">
<?= form_open_multipart('admin/profile/update', ['id' => 'profileForm', 'class' => 'bg-white border border-gray-200 p-6 md:p-8 space-y-10']) ?>

    <!-- Sejarah -->
    <div>
        <p class="editorial-label text-gold-muted mb-5">History</p>
        <div>
            <label class="block editorial-label text-navy mb-2">Sejarah Fakultas</label>
            <textarea name="faculty_history" rows="6" maxlength="5000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= html_escape($settings['faculty_history'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Visi & Misi -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Vision & Mission</p>
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Visi Fakultas</label>
                <textarea name="faculty_vision" rows="5" maxlength="1000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed italic"><?= html_escape($settings['faculty_vision'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Misi Fakultas</label>
                <textarea name="faculty_mission" rows="5" maxlength="2000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= html_escape($settings['faculty_mission'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- JSON Fields -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Dynamic Content (JSON)</p>
        <p class="text-xs text-slate mb-5"><i class="fas fa-info-circle mr-1"></i>Data terstruktur untuk section khusus di halaman publik.</p>
        <div class="space-y-5">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block editorial-label text-navy">Milestone Sejarah</label>
                    <button type="button" onclick="validateJSON('faculty_milestones')" class="text-[10px] px-2 py-1 bg-navy text-ivory uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition">Validate</button>
                </div>
                <textarea name="faculty_milestones" id="faculty_milestones" rows="4" class="json-editor w-full px-3 py-2 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['faculty_milestones'] ?? '[]') ?></textarea>
                <div id="faculty_milestones_status" class="mt-1"></div>
                <p class="text-[10px] text-slate mt-1 italic">Format: [{"year": "1990", "title": "Didirikan", "desc": "..."}]</p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block editorial-label text-navy">Core Values</label>
                    <button type="button" onclick="validateJSON('core_values')" class="text-[10px] px-2 py-1 bg-navy text-ivory uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition">Validate</button>
                </div>
                <textarea name="core_values" id="core_values" rows="4" class="json-editor w-full px-3 py-2 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['core_values'] ?? '[]') ?></textarea>
                <div id="core_values_status" class="mt-1"></div>
                <p class="text-[10px] text-slate mt-1 italic">Format: [{"title": "Integritas", "icon": "fa-shield", "desc": "..."}]</p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block editorial-label text-navy">Prioritas Dekan</label>
                    <button type="button" onclick="validateJSON('dean_priorities')" class="text-[10px] px-2 py-1 bg-navy text-ivory uppercase tracking-wider font-semibold hover:bg-gold hover:text-navy transition">Validate</button>
                </div>
                <textarea name="dean_priorities" id="dean_priorities" rows="4" class="json-editor w-full px-3 py-2 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['dean_priorities'] ?? '[]') ?></textarea>
                <div id="dean_priorities_status" class="mt-1"></div>
                <p class="text-[10px] text-slate mt-1 italic">Format: [{"title": "Digitalisasi", "desc": "..."}]</p>
            </div>
        </div>
    </div>

    <!-- Dekan -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Dean's Message</p>
        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label class="block editorial-label text-navy mb-2">Nama Dekan</label>
                <input type="text" name="dean_name" id="in_dean_name" value="<?= html_escape($settings['dean_name'] ?? '') ?>" maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                <label class="block editorial-label text-navy mb-2 mt-4">Foto Dekan</label>
                <div class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-3 text-center">
                    <input type="file" name="dean_photo" id="in_dean_photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-[10px] text-slate file:mr-2 file:py-1 file:px-2 file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-[9px] hover:file:bg-gold file:cursor-pointer">
                </div>
                <?php if (!empty($settings['dean_photo'])): ?>
                    <div class="mt-3 flex items-center gap-3">
                        <img id="pv_dean_thumb" src="<?= base_url('assets/uploads/' . $settings['dean_photo']) ?>" class="w-16 h-16 rounded-full object-cover border-2 border-gold/40" alt="">
                        <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_dean_photo" value="1" class="accent-red-600"> Hapus
                        </label>
                    </div>
                <?php else: ?>
                    <img id="pv_dean_thumb" src="" class="hidden w-16 h-16 rounded-full object-cover border-2 border-gold/40 mt-3" alt="">
                <?php endif; ?>
            </div>
            <div class="md:col-span-2">
                <label class="block editorial-label text-navy mb-2">Sambutan Dekan</label>
                <textarea name="dean_message" rows="8" maxlength="3000" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy leading-relaxed"><?= html_escape($settings['dean_message'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Wakil Dekan 1, 2, 3 -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Wakil Dekan</p>
        <?php
        $wadeks = [
            ['key' => 'wadek_1', 'label' => 'Wakil Dekan I'],
            ['key' => 'wadek_2', 'label' => 'Wakil Dekan II'],
            ['key' => 'wadek_3', 'label' => 'Wakil Dekan III'],
        ];
        foreach ($wadeks as $w):
            $nk = $w['key'] . '_name';
            $pk = $w['key'] . '_photo';
            $rk = 'remove_' . $pk;
        ?>
        <div class="grid md:grid-cols-3 gap-5 mb-6 pb-6 border-b border-gray-100">
            <div class="md:col-span-2">
                <label class="block editorial-label text-navy mb-2"><?= $w['label'] ?></label>
                <input type="text" name="<?= $nk ?>" id="in_<?= $w['key'] ?>_name" value="<?= html_escape($settings[$nk] ?? '') ?>" maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy" placeholder="Kosongkan jika tidak ada">
                <?php if ($w['key'] === 'wadek_1'): ?>
                <label class="block editorial-label text-navy mb-2 mt-4">Label Jabatan</label>
                <input type="text" name="wadek_label" id="in_wadek_label" value="<?= html_escape($settings['wadek_label'] ?? '') ?>" maxlength="50" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy" placeholder="Wakil Dekan">
                <?php endif; ?>
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Foto</label>
                <div class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-3 text-center">
                    <input type="file" name="<?= $pk ?>" id="in_<?= $w['key'] ?>_photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-[10px] text-slate file:mr-2 file:py-1 file:px-2 file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-[9px] hover:file:bg-gold file:cursor-pointer">
                </div>
                <?php if (!empty($settings[$pk])): ?>
                    <div class="mt-3 flex items-center gap-2">
                        <img id="pv_<?= $w['key'] ?>_thumb" src="<?= base_url('assets/uploads/' . $settings[$pk]) ?>" class="w-16 h-16 rounded-full object-cover border-2 border-gold/30" alt="">
                        <label class="flex items-center gap-1 text-[10px] text-red-600 cursor-pointer">
                            <input type="checkbox" name="<?= $rk ?>" value="1" class="accent-red-600"> Hapus
                        </label>
                    </div>
                <?php else: ?>
                    <img id="pv_<?= $w['key'] ?>_thumb" src="" class="hidden w-16 h-16 rounded-full object-cover border-2 border-gold/30 mt-3" alt="">
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Kepala TU -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Kepala Tata Usaha</p>
        <div class="grid md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
                <label class="block editorial-label text-navy mb-2">Nama Kepala TU</label>
                <input type="text" name="tu_head_name" id="in_tu_head_name" value="<?= html_escape($settings['tu_head_name'] ?? '') ?>" maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy" placeholder="Kosongkan jika tidak ada">
            </div>
            <div class="flex items-end">
                <p class="text-xs text-slate italic">Tampil di struktur organisasi (opsional)</p>
            </div>
        </div>
    </div>

    <!-- Layout -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Organization Chart Layout</p>
        <div class="grid md:grid-cols-2 gap-4">
            <label class="layout-card">
                <input type="radio" name="org_layout" id="in_layout_tiered" value="tiered" class="sr-only" <?= ($settings['org_layout'] ?? 'tiered') == 'tiered' ? 'checked' : '' ?>>
                <div class="border-2 border-gray-200 p-4 transition">
                    <p class="font-semibold text-navy text-sm mb-1">🏛️ Berjenjang</p>
                    <p class="text-xs text-slate">Dekan → Wakil Dekan → Ketua Prodi</p>
                </div>
            </label>
            <label class="layout-card">
                <input type="radio" name="org_layout" id="in_layout_flat" value="flat" class="sr-only" <?= ($settings['org_layout'] ?? '') == 'flat' ? 'checked' : '' ?>>
                <div class="border-2 border-gray-200 p-4 transition">
                    <p class="font-semibold text-navy text-sm mb-1">📐 Langsung</p>
                    <p class="text-xs text-slate">Dekan → (Wadek + Kaprodi sejajar)</p>
                </div>
            </label>
        </div>
    </div>

    <!-- Kaprodi -->
    <div class="pt-8 border-t border-gray-200">
        <p class="editorial-label text-gold-muted mb-5">Heads of Study Programs (Kaprodi)</p>
        <?php foreach ($programs as $p): ?>
        <div class="grid md:grid-cols-3 gap-5 mb-6 pb-6 border-b border-gray-100">
            <div class="md:col-span-2">
                <label class="block editorial-label text-navy mb-2"><?= html_escape($p->name) ?></label>
                <input type="text" name="kaprodi[<?= $p->id ?>][name]" data-kaprodi="<?= $p->id ?>" value="<?= html_escape($p->head_of_study_program ?? '') ?>" maxlength="150" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy" placeholder="Nama Ketua Prodi">
            </div>
            <div>
                <label class="block editorial-label text-navy mb-2">Foto</label>
                <div class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-3 text-center">
                    <input type="file" name="kaprodi_photo[<?= $p->id ?>]" data-kphoto="<?= $p->id ?>" accept="image/jpeg,image/png,image/webp" class="block w-full text-[10px] text-slate file:mr-2 file:py-1 file:px-2 file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-[9px] hover:file:bg-gold file:cursor-pointer">
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <img id="pv_kaprodi_<?= $p->id ?>" src="<?= !empty($p->head_photo) ? base_url('assets/uploads/' . $p->head_photo) : '' ?>" class="w-12 h-12 rounded-full object-cover border-2 border-gold/30 <?= empty($p->head_photo) ? 'hidden' : '' ?>" alt="">
                    <?php if (!empty($p->head_photo)): ?>
                    <label class="flex items-center gap-1 text-[10px] text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_kaprodi_photo[<?= $p->id ?>]" value="1" class="accent-red-600"> Hapus
                    </label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <p class="text-xs text-slate italic"><i class="fas fa-info-circle text-gold-muted mr-1"></i>Kaprodi yang dikosongkan tidak tampil di bagan publik.</p>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 pt-8 mt-8 border-t border-gray-200">
        <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-save mr-2"></i>Simpan Perubahan Profil
        </button>
        <a href="<?= base_url('profil/struktur') ?>" target="_blank" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
            <i class="fas fa-sitemap mr-2"></i>Lihat Struktur Publik
        </a>
        <button type="button" id="loadDraftBtn" class="btn-outline-navy px-6 py-3 font-semibold uppercase tracking-editorial text-xs hidden">
            <i class="fas fa-undo mr-2"></i>Muat Draft
        </button>
        <button type="button" id="clearDraftBtn" class="text-xs text-red-600 hover:text-red-800 px-3 py-3 font-semibold uppercase tracking-editorial hidden">
            <i class="fas fa-trash mr-1"></i>Hapus Draft
        </button>
    </div>

<?= form_close() ?>
</div>

<!-- LIVE PREVIEW -->
<div class="lg:col-span-1 lg:sticky lg:top-24">
    <p class="editorial-label text-gold-muted mb-4">Live Preview — Struktur Organisasi</p>
    <div id="pvOrgWrap">
        <div class="pv-stage" id="pvStage">
            <svg class="pv-svg" id="pvSvg"></svg>
            <div id="pvLevels"></div>
        </div>
    </div>
    <p class="text-xs text-slate mt-3 italic"><i class="fas fa-bolt text-gold-muted mr-1"></i>Preview real-time: ketik nama, pilih foto, ganti layout — bagan langsung berubah.</p>

    <div class="mt-6 bg-white border border-gray-200 p-5">
        <p class="editorial-label text-gold-muted mb-3">Dynamic Content Preview</p>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-ivory-warm/50 p-3 border border-gray-100">
                <div class="font-serif text-2xl text-navy" id="milestoneCount">0</div>
                <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Milestones</p>
            </div>
            <div class="bg-ivory-warm/50 p-3 border border-gray-100">
                <div class="font-serif text-2xl text-navy" id="valueCount">0</div>
                <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Core Values</p>
            </div>
            <div class="bg-ivory-warm/50 p-3 border border-gray-100">
                <div class="font-serif text-2xl text-navy" id="priorityCount">0</div>
                <p class="text-[10px] uppercase tracking-wider text-slate mt-1">Priorities</p>
            </div>
        </div>
    </div>
</div>
</div>

<div id="autoSaveStatus"><i class="fas fa-save mr-1"></i>Draft tersimpan</div>

<script>
(function(){
    var $ = function(id){ return document.getElementById(id); };
    // 🔥 FIX: helper null-safe — return '' jika elemen tidak ada (anti-crash)
    function val(id){ var el = $(id); return el ? el.value : ''; }
    function esc(s){ var d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }

    var KAPRODI_LIST = <?= json_encode(array_map(function($p){ return [
        'id' => (int)$p->id,
        'sub' => $p->name,
        'photo' => !empty($p->head_photo) ? base_url('assets/uploads/' . $p->head_photo) : '',
    ]; }, $programs)) ?>;

    var state = { kaprodiPhotos: {} };
    KAPRODI_LIST.forEach(function(k){ state.kaprodiPhotos[k.id] = k.photo; });
    state.deanPhoto   = <?= !empty($settings['dean_photo'])    ? json_encode(base_url('assets/uploads/' . $settings['dean_photo']))    : "''" ?>;
    state.wadek1Photo = <?= !empty($settings['wadek_1_photo']) ? json_encode(base_url('assets/uploads/' . $settings['wadek_1_photo'])) : "''" ?>;
    state.wadek2Photo = <?= !empty($settings['wadek_2_photo']) ? json_encode(base_url('assets/uploads/' . $settings['wadek_2_photo'])) : "''" ?>;
    state.wadek3Photo = <?= !empty($settings['wadek_3_photo']) ? json_encode(base_url('assets/uploads/' . $settings['wadek_3_photo'])) : "''" ?>;

    function nodeHTML(photo, name, role, sub, cls){
        var inner = photo ? '<img src="'+photo+'" alt="">' : '<div class="pv-init">'+esc((name||'?').charAt(0).toUpperCase())+'</div>';
        return '<div class="pv-node '+cls+'"><div class="pv-photo">'+inner+'</div>'+
            '<p class="pv-role">'+esc(role)+'</p><p class="pv-name">'+esc(name)+'</p>'+
            (sub?'<p class="pv-sub">'+esc(sub)+'</p>':'')+'</div>';
    }

    function render(){
        // 🔥 FIX: semua ID sinkron dengan HTML (in_wadek_1_name, bukan in_wadek_name)
        var deanName   = val('in_dean_name')   || 'Dekan';
        var wadek1     = val('in_wadek_1_name');
        var wadek2     = val('in_wadek_2_name');
        var wadek3     = val('in_wadek_3_name');
        var tuName     = val('in_tu_head_name');
        var wadekLabel = val('in_wadek_label') || 'Wakil Dekan';
        var flatEl     = $('in_layout_flat');
        var layout     = (flatEl && flatEl.checked) ? 'flat' : 'tiered';

        var html = '<div class="pv-level" id="pvDean">'+nodeHTML(state.deanPhoto, deanName, 'Dekan', '', 'dean')+'</div>';

        var wadeks = '';
        if (wadek1) wadeks += nodeHTML(state.wadek1Photo, wadek1, wadekLabel, '', 'wadek');
        if (wadek2) wadeks += nodeHTML(state.wadek2Photo, wadek2, 'Wadek II', '', 'wadek');
        if (wadek3) wadeks += nodeHTML(state.wadek3Photo, wadek3, 'Wadek III', '', 'wadek');
        if (tuName) wadeks += nodeHTML('', tuName, 'Kasubag TU', '', 'tu');

        if (wadeks && layout == 'tiered') {
            html += '<div class="pv-level" id="pvWadek">'+wadeks+'</div>';
        }

        var kids = '';
        if (wadeks && layout == 'flat') kids += wadeks;
        KAPRODI_LIST.forEach(function(k){
            var inp = document.querySelector('input[data-kaprodi="'+k.id+'"]');
            var name = inp ? inp.value : '';
            if (!name) return;
            kids += nodeHTML(state.kaprodiPhotos[k.id] || '', name, 'KAPRODI', k.sub, '');
        });
        if (kids) html += '<div class="pv-level" id="pvKids">'+kids+'</div>';

        var levels = $('pvLevels');
        if (levels) levels.innerHTML = html;
        requestAnimationFrame(drawLines);
    }

    function box(el, base){
        var r = el.getBoundingClientRect();
        return { cx: r.left-base.left+r.width/2, top: r.top-base.top, bot: r.bottom-base.top };
    }

    function drawLines(){
        var stage = $('pvStage'), svg = $('pvSvg');
        if (!stage || !svg) return;
        var base = stage.getBoundingClientRect();
        svg.setAttribute('viewBox','0 0 '+stage.offsetWidth+' '+stage.offsetHeight);
        var paths = '';
        var dean = document.querySelector('#pvDean .pv-node');
        var wadek = document.querySelector('#pvWadek .pv-node');
        var kids = document.querySelectorAll('#pvKids .pv-node');
        if (dean && wadek) {
            var a=box(dean,base), b=box(wadek,base), m=(a.bot+b.top)/2;
            paths += '<path d="M '+a.cx+' '+a.bot+' L '+a.cx+' '+m+' L '+b.cx+' '+m+' L '+b.cx+' '+b.top+'"/>';
        }
        var parent = wadek || dean;
        if (parent && kids.length) {
            var p=box(parent,base), f=box(kids[0],base), m2=(p.bot+f.top)/2;
            var minX=p.cx, maxX=p.cx;
            kids.forEach(function(k){ var bb=box(k,base); if(bb.cx<minX)minX=bb.cx; if(bb.cx>maxX)maxX=bb.cx; });
            paths += '<path d="M '+p.cx+' '+p.bot+' L '+p.cx+' '+m2+'"/>';
            paths += '<path d="M '+minX+' '+m2+' L '+maxX+' '+m2+'"/>';
            kids.forEach(function(k){ var bb=box(k,base); paths += '<path d="M '+bb.cx+' '+m2+' L '+bb.cx+' '+bb.top+'"/>'; });
        }
        svg.innerHTML = paths;
    }

    var form = $('profileForm');
    if (form) {
        form.addEventListener('input', render);
        form.addEventListener('change', render);
    }

    // 🔥 FIX: bind file dengan guard — semua ID sinkron dengan HTML
    function bindFile(inputId, stateKey, thumbId){
        var inp = $(inputId);
        if (!inp) return; // skip jika elemen tidak ada
        inp.addEventListener('change', function(e){
            var f = e.target.files[0]; if(!f) return;
            if (f.size > 3 * 1024 * 1024) { alert('Ukuran foto maksimal 3MB.'); e.target.value = ''; return; }
            if (!/image\/(jpeg|png|webp)/.test(f.type)) { alert('Format harus JPG/PNG/WEBP.'); e.target.value = ''; return; }
            var r = new FileReader();
            r.onload = function(ev){
                state[stateKey] = ev.target.result;
                var t = $(thumbId);
                if (t) { t.src = ev.target.result; t.classList.remove('hidden'); }
                render();
            };
            r.readAsDataURL(f);
        });
    }

    bindFile('in_dean_photo',    'deanPhoto',   'pv_dean_thumb');
    bindFile('in_wadek_1_photo', 'wadek1Photo', 'pv_wadek_1_thumb');
    bindFile('in_wadek_2_photo', 'wadek2Photo', 'pv_wadek_2_thumb');
    bindFile('in_wadek_3_photo', 'wadek3Photo', 'pv_wadek_3_thumb');

    if (form) {
        form.querySelectorAll('input[data-kphoto]').forEach(function(inp){
            var id = inp.getAttribute('data-kphoto');
            inp.addEventListener('change', function(e){
                var f = e.target.files[0]; if(!f) return;
                if (f.size > 3 * 1024 * 1024) { alert('Ukuran foto maksimal 3MB.'); e.target.value = ''; return; }
                if (!/image\/(jpeg|png|webp)/.test(f.type)) { alert('Format harus JPG/PNG/WEBP.'); e.target.value = ''; return; }
                var r = new FileReader();
                r.onload = function(ev){
                    state.kaprodiPhotos[id] = ev.target.result;
                    var t = $('pv_kaprodi_' + id);
                    if (t) { t.src = ev.target.result; t.classList.remove('hidden'); }
                    render();
                };
                r.readAsDataURL(f);
            });
        });
    }

    // Character counter
    if (form) {
        form.querySelectorAll('textarea').forEach(function(ta){
            var counter = document.createElement('p');
            counter.className = 'text-[10px] text-slate mt-1';
            var max = ta.getAttribute('maxlength');
            counter.innerHTML = '<span>' + ta.value.length + '</span>' + (max ? ' / ' + max : '') + ' karakter';
            ta.parentNode.appendChild(counter);
            ta.addEventListener('input', function(){
                counter.querySelector('span').textContent = ta.value.length;
                if (max && ta.value.length > parseInt(max) * 0.9) {
                    counter.classList.add('text-amber-600');
                    counter.classList.remove('text-slate');
                } else {
                    counter.classList.remove('text-amber-600');
                    counter.classList.add('text-slate');
                }
            });
        });
    }

    // JSON validation
    window.validateJSON = function(key){
        var ta = $(key);
        var statusEl = $(key + '_status');
        if (!ta || !statusEl) return;
        var v = ta.value.trim();
        if (!v || v === '[]') {
            statusEl.innerHTML = '<span class="json-status ok"><i class="fas fa-check mr-1"></i>Valid (0 item)</span>';
            updateJSONCounts();
            return;
        }
        try {
            var arr = JSON.parse(v);
            if (!Array.isArray(arr)) throw new Error();
            statusEl.innerHTML = '<span class="json-status ok"><i class="fas fa-check mr-1"></i>Valid (' + arr.length + ' item)</span>';
        } catch(e) {
            statusEl.innerHTML = '<span class="json-status err"><i class="fas fa-times mr-1"></i>JSON tidak valid</span>';
        }
        updateJSONCounts();
    };

    function updateJSONCounts(){
        var map = [['faculty_milestones','milestoneCount'],['core_values','valueCount'],['dean_priorities','priorityCount']];
        map.forEach(function(pair){
            var ta = $(pair[0]), c = $(pair[1]);
            if (!ta || !c) return;
            try { var a = JSON.parse(ta.value); c.textContent = Array.isArray(a) ? a.length : 0; }
            catch(e){ c.textContent = 0; }
        });
    }
    ['faculty_milestones','core_values','dean_priorities'].forEach(function(key){
        var ta = $(key);
        if (ta) ta.addEventListener('input', updateJSONCounts);
    });
    updateJSONCounts();

    // Auto-save draft
    var draftKey = 'profile_draft_v2_' + window.location.pathname;
    var lastSave = {};
    var autoSaveStatus = $('autoSaveStatus');
    var loadDraftBtn = $('loadDraftBtn');
    var clearDraftBtn = $('clearDraftBtn');

    function showAutoSave(){
        if (!autoSaveStatus) return;
        autoSaveStatus.classList.add('visible');
        setTimeout(function(){ autoSaveStatus.classList.remove('visible'); }, 2000);
    }

    function getCurrentData(){
        var data = {};
        if (!form) return data;
        form.querySelectorAll('input[type="text"], textarea').forEach(function(el){
            if (el.name && el.name.indexOf('remove_') !== 0) data[el.name] = el.value;
        });
        form.querySelectorAll('input[type="radio"]:checked').forEach(function(el){
            if (el.name) data[el.name] = el.value;
        });
        return data;
    }

    try {
        var saved = JSON.parse(localStorage.getItem(draftKey) || 'null');
        if (saved && saved.timestamp > Date.now() - 7*86400000 && loadDraftBtn && clearDraftBtn) {
            loadDraftBtn.classList.remove('hidden');
            clearDraftBtn.classList.remove('hidden');
            loadDraftBtn.addEventListener('click', function(){
                if (confirm('Muat draft dari ' + new Date(saved.timestamp).toLocaleString() + '?')) {
                    Object.keys(saved.data).forEach(function(key){
                        var el = form.elements[key];
                        if (el && el.type !== 'file') el.value = saved.data[key];
                    });
                    render();
                    updateJSONCounts();
                    loadDraftBtn.classList.add('hidden');
                    clearDraftBtn.classList.add('hidden');
                }
            });
            clearDraftBtn.addEventListener('click', function(){
                if (confirm('Hapus draft tersimpan?')) {
                    localStorage.removeItem(draftKey);
                    loadDraftBtn.classList.add('hidden');
                    clearDraftBtn.classList.add('hidden');
                }
            });
        }
    } catch(e) {}

    setInterval(function(){
        if (!form) return;
        var data = getCurrentData();
        if (JSON.stringify(data) !== JSON.stringify(lastSave)) {
            try {
                localStorage.setItem(draftKey, JSON.stringify({timestamp: Date.now(), data: data}));
                lastSave = data;
                showAutoSave();
            } catch(e) {}
        }
    }, 30000);

    if (form) form.addEventListener('submit', function(){
        try { localStorage.removeItem(draftKey); } catch(e){}
    });

    window.addEventListener('resize', drawLines);
    render();
})();
</script>