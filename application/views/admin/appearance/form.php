<!-- Page Header -->
<div class="mb-10">
    <p class="editorial-label text-gold-muted mb-3">Design Control Center</p>
    <h1 class="font-serif text-4xl md:text-5xl font-light text-navy tracking-tight leading-tight">
        Tampilan <em class="italic text-gold-muted">Website</em>
    </h1>
    <p class="text-slate mt-2 text-sm">Atur identitas, font, animasi, video tour, sosial media, footer, dan posisi teks — dengan live preview.</p>
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
    @keyframes pvWalk { from { left: 0; } to { left: calc(100% - 36px); } }
    @keyframes pvStepL { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
    @keyframes pvStepR { 0%,100% { transform: translateY(-3px); } 50% { transform: translateY(0); } }
    @keyframes pvFloat { from { transform: translateY(0) rotate(-4deg); } to { transform: translateY(-20px) rotate(5deg); } }
    @keyframes pvAurora { from { transform: translate(0,0) scale(1); } to { transform: translate(40px,-30px) scale(1.2); } }
    @keyframes pvPulse { 0%,100% { opacity: 1; } 50% { opacity: .4; } }
    @keyframes pvMatrix { from { transform: translateY(-20px); opacity: 0; } 50% { opacity: 1; } to { transform: translateY(60px); opacity: 0; } }
    @keyframes pvGeoSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .pv-robot { position: absolute; bottom: 6px; left: 0; width: 36px; height: 36px; animation: pvWalk 6s linear infinite; }
    .pv-robot .antenna { position: absolute; top: 0; left: 17px; width: 2px; height: 6px; background: #C9A227; }
    .pv-robot .antenna::after { content: ''; position: absolute; top: -5px; left: -2px; width: 6px; height: 6px; border-radius: 50%; background: #C9A227; animation: pvPulse 1s infinite; }
    .pv-robot .head { position: absolute; top: 6px; left: 6px; width: 24px; height: 12px; background: #F7F5F0; border-radius: 3px; }
    .pv-robot .head::before, .pv-robot .head::after { content: ''; position: absolute; top: 4px; width: 4px; height: 4px; border-radius: 50%; background: #0B2239; }
    .pv-robot .head::before { left: 5px; } .pv-robot .head::after { right: 5px; }
    .pv-robot .body { position: absolute; top: 19px; left: 8px; width: 20px; height: 10px; background: #C9A227; border-radius: 2px; }
    .pv-robot .leg { position: absolute; top: 29px; width: 4px; height: 6px; background: #F7F5F0; }
    .pv-robot .leg.l { left: 11px; animation: pvStepL .35s infinite; }
    .pv-robot .leg.r { right: 11px; animation: pvStepR .35s infinite; }
    .pv-campus-icon { position: absolute; animation: pvFloat ease-in-out infinite alternate; }
    .pv-aurora { position: absolute; border-radius: 9999px; filter: blur(30px); opacity: .5; animation: pvAurora 6s ease-in-out infinite alternate; }
    .pv-dot { position: absolute; width: 3px; height: 3px; border-radius: 50%; background: rgba(201,162,39,.7); }
    .pv-matrix-char { position: absolute; font-family: 'JetBrains Mono', monospace; color: #C9A227; font-size: 10px; animation: pvMatrix 2s linear infinite; }
    .pv-geo { position: absolute; border: 1px solid rgba(201,162,39,.5); animation: pvGeoSpin 8s linear infinite; }
    .pv-wave-path { fill: none; stroke: rgba(201,162,39,.6); stroke-width: 1.5; }
    .pv-text .lit { color: #C9A227; } .pv-text .dim { color: rgba(247,245,240,.15); }
</style>

<!-- 🔥 FITUR GILA: Theme Presets -->
<div class="mb-8 bg-gradient-to-r from-gold/10 via-ivory-warm/30 to-navy/5 border border-gold/30 p-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="editorial-label text-gold-muted mb-2"><i class="fas fa-magic mr-1"></i>Quick Start</p>
            <h3 class="font-serif text-2xl font-light text-navy">Preset <em class="italic text-gold-muted">Themes</em></h3>
            <p class="text-sm text-slate mt-1">Pilih template siap pakai, lalu customize sesuai kebutuhan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= base_url('admin/appearance/apply_preset/editorial') ?>" onclick="return confirm('Terapkan tema Editorial?')" class="btn-outline-navy px-4 py-2 text-xs uppercase tracking-editorial font-semibold">Editorial</a>
            <a href="<?= base_url('admin/appearance/apply_preset/classic') ?>" onclick="return confirm('Terapkan tema Classic?')" class="btn-outline-navy px-4 py-2 text-xs uppercase tracking-editorial font-semibold">Classic</a>
            <a href="<?= base_url('admin/appearance/apply_preset/modern') ?>" onclick="return confirm('Terapkan tema Modern?')" class="btn-outline-navy px-4 py-2 text-xs uppercase tracking-editorial font-semibold">Modern</a>
            <a href="<?= base_url('admin/appearance/apply_preset/tech') ?>" onclick="return confirm('Terapkan tema Tech?')" class="btn-outline-navy px-4 py-2 text-xs uppercase tracking-editorial font-semibold">Tech</a>
        </div>
    </div>
</div>

<!-- FITUR GILA: Export/Import Settings -->
<div class="mb-8 grid md:grid-cols-2 gap-4">
    <a href="<?= base_url('admin/appearance/export') ?>" class="bg-white border border-gray-200 p-5 hover:border-gold hover:shadow-md transition flex items-center gap-4 group">
        <div class="w-12 h-12 bg-gold/10 text-gold flex items-center justify-center flex-shrink-0 group-hover:bg-gold group-hover:text-navy transition">
            <i class="fas fa-download text-xl"></i>
        </div>
        <div>
            <p class="font-serif text-lg text-navy">Export Settings</p>
            <p class="text-xs text-slate mt-1">Download backup konfigurasi (JSON)</p>
        </div>
    </a>
    <label class="bg-white border border-gray-200 p-5 hover:border-gold hover:shadow-md transition flex items-center gap-4 cursor-pointer group">
        <div class="w-12 h-12 bg-navy/10 text-navy flex items-center justify-center flex-shrink-0 group-hover:bg-navy group-hover:text-ivory transition">
            <i class="fas fa-upload text-xl"></i>
        </div>
        <div class="flex-1">
            <p class="font-serif text-lg text-navy">Import Settings</p>
            <p class="text-xs text-slate mt-1">Restore dari file backup</p>
            <input type="file" id="importFile" accept=".json" class="hidden">
        </div>
    </label>
</div>

<div class="grid lg:grid-cols-5 gap-8 items-start">
    <div class="lg:col-span-3 space-y-8">
        <?= form_open_multipart('admin/appearance/update', ['class' => 'space-y-8']) ?>

<!-- FIELD BARU: Nama Fakultas (English) -->
<div>
    <label class="block editorial-label text-navy mb-2">
        Nama Fakultas <span class="text-gold-muted font-normal">(English)</span>
    </label>
    <input type="text" name="site_name_en" value="<?= html_escape($settings['site_name_en'] ?? '') ?>" 
        placeholder="Faculty of Engineering & Computer Science"
        class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
    <p class="text-[10px] text-slate mt-1">
        <i class="fas fa-info-circle text-gold-muted mr-1"></i>
        Kosongkan agar subtitle footer EN otomatis mengikuti nama utama. Terpisah untuk kebutuhan bilingual.
    </p>
</div>

        <!-- 1. TYPOGRAPHY -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">1. Tipografi Publik</p>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Pasangan Font (11 pilihan)</label>
                    <select name="font_family" id="setFont" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                        <?php
                        $font_options = [
                            'editorial' => '01 — Editorial · Fraunces + Inter',
                            'classic'   => '02 — Classic · Playfair Display + Source Sans 3',
                            'modern'    => '03 — Modern · Sora + Inter',
                            'elegant'   => '04 — Elegant · Cormorant Garamond + Jost',
                            'architect' => '05 — Architect · Bricolage Grotesque + Inter',
                            'journal'   => '06 — Journal · DM Serif Display + Atkinson Hyperlegible',
                            'tech'      => '07 — Tech · Space Grotesk + IBM Plex Mono',
                            'heritage'  => '08 — Heritage · Libre Caslon Text + Lato',
                            'humanist'  => '09 — Humanist · Spectral + Source Sans 3',
                            'swiss'     => '10 — Swiss · Helvetica Neue + Neue Haas',
                            'monument'  => '11 — Monument · Bodoni Moda + Inter',
                        ];
                        foreach ($font_options as $k => $label): ?>
                        <option value="<?= $k ?>" <?= set_select('font_family', $k, ($settings['font_family'] ?? 'editorial') == $k) ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Ukuran Font Global</label>
                    <select name="font_scale" id="setScale" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        <option value="sm" <?= set_select('font_scale', 'sm', ($settings['font_scale'] ?? '') == 'sm') ?>>Kecil (15px)</option>
                        <option value="md" <?= set_select('font_scale', 'md', ($settings['font_scale'] ?? 'md') == 'md') ?>>Normal (16px)</option>
                        <option value="lg" <?= set_select('font_scale', 'lg', ($settings['font_scale'] ?? '') == 'lg') ?>>Besar (17.5px)</option>
                        <option value="xl" <?= set_select('font_scale', 'xl', ($settings['font_scale'] ?? '') == 'xl') ?>>Sangat Besar (19px)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. HERO CONTENT -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">2. Konten Beranda</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Label Kecil</label>
                    <input type="text" name="hero_label" id="setLabel" value="<?= html_escape($settings['hero_label'] ?? '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Judul Hero</label>
                    <input type="text" name="hero_title" id="setTitle" value="<?= html_escape($settings['hero_title'] ?? '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    <p class="text-xs text-slate mt-2 italic">Tip: apit kata dengan * untuk italic emas. Contoh: Shaping the *future*</p>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Sub-judul</label>
                    <textarea name="hero_subtitle" id="setSubtitle" rows="3" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['hero_subtitle'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Teks Robot Welcome</label>
                    <input type="text" name="welcome_text" id="setWelcome" value="<?= html_escape($settings['welcome_text'] ?? '') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                </div>
            </div>
        </div>

        <!-- 3. ANIMATION TEMPLATE -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">3. Template Animasi Beranda (8 Pilihan)</p>
            <div class="grid md:grid-cols-2 gap-4">
                <?php
                $templates = [
                    'robot'     => ['🤖 Robot Walker', 'Robot berjalan mengetik WELCOME'],
                    'particles' => ['✨ Particles', 'Jaringan partikel konstelasi emas'],
                    'aurora'    => ['🌌 Aurora', 'Blob gradien bergerak halus'],
                    'campus'    => ['🎓 Campus Life', 'Ikon mahasiswa melayang'],
                    'minimal'   => ['◽ Minimal', 'Bersih tanpa animasi'],
                    'waves'     => ['🌊 Waves', 'Gelombang sin emas yang mengalir'],
                    'matrix'    => ['💾 Matrix', 'Karakter digital berjatuhan ala kode'],
                    'geometric' => ['◆ Geometric', 'Bentuk geometri berputar artistik'],
                ];
                foreach ($templates as $key => $t): ?>
                <label class="template-card relative border-2 p-4 cursor-pointer transition <?= ($settings['hero_animation'] ?? '') == $key ? 'border-gold bg-gold/5' : 'border-gray-200 hover:border-navy/30' ?>">
                    <input type="radio" name="hero_animation" value="<?= $key ?>" <?= set_radio('hero_animation', $key, ($settings['hero_animation'] ?? '') == $key) ?> class="sr-only">
                    <div class="h-20 mb-3 relative overflow-hidden bg-navy">
                        <?php if ($key == 'robot'): ?>
                            <div class="absolute bottom-2 left-0 right-0 h-px bg-ivory/20"></div>
                            <div class="pv-robot" style="animation-duration:4s"><span class="antenna"></span><span class="head"></span><span class="body"></span><span class="leg l"></span><span class="leg r"></span></div>
                        <?php elseif ($key == 'particles'): ?>
                            <span class="pv-dot" style="left:15%;top:25%"></span><span class="pv-dot" style="left:40%;top:60%"></span>
                            <span class="pv-dot" style="left:65%;top:30%"></span><span class="pv-dot" style="left:80%;top:70%"></span>
                        <?php elseif ($key == 'aurora'): ?>
                            <div class="pv-aurora" style="width:70px;height:70px;background:rgba(201,162,39,.5);left:10%;top:10%"></div>
                            <div class="pv-aurora" style="width:90px;height:90px;background:rgba(19,51,79,.8);left:50%;top:30%;animation-delay:1s"></div>
                        <?php elseif ($key == 'campus'): ?>
                            <span class="pv-campus-icon" style="left:12%;top:20%;color:rgba(201,162,39,.6);font-size:14px"><i class="fas fa-graduation-cap"></i></span>
                            <span class="pv-campus-icon" style="left:45%;top:50%;color:rgba(247,245,240,.4);font-size:12px;animation-delay:1s"><i class="fas fa-laptop"></i></span>
                        <?php elseif ($key == 'waves'): ?>
                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 60" preserveAspectRatio="none">
                                <path class="pv-wave-path" d="M0,30 Q25,10 50,30 T100,30"></path>
                            </svg>
                        <?php elseif ($key == 'matrix'): ?>
                            <span class="pv-matrix-char" style="left:10%">0</span>
                            <span class="pv-matrix-char" style="left:40%;animation-delay:.6s">1</span>
                            <span class="pv-matrix-char" style="left:70%;animation-delay:1.2s">F</span>
                        <?php elseif ($key == 'geometric'): ?>
                            <div class="pv-geo" style="width:30px;height:30px;left:15%;top:20%;border-radius:50%"></div>
                            <div class="pv-geo" style="width:36px;height:36px;left:72%;top:15%;transform:rotate(45deg);animation-duration:12s"></div>
                        <?php endif; ?>
                    </div>
                    <p class="font-semibold text-navy text-sm"><?= $t[0] ?></p>
                    <p class="text-xs text-slate"><?= $t[1] ?></p>
                    <span class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 <?= ($settings['hero_animation'] ?? '') == $key ? 'border-gold bg-gold' : 'border-gray-300' ?> tpl-dot"></span>
                </label>
                <?php endforeach; ?>
            </div>

            <label class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-200 cursor-pointer">
                <input type="checkbox" name="hero_particles" value="1" id="setParticles" <?= (($settings['hero_particles'] ?? '0') == '1') ? 'checked' : '' ?> class="w-5 h-5 accent-navy">
                <div>
                    <p class="text-sm font-medium text-navy">Aktifkan Partikel Latar</p>
                    <p class="text-xs text-slate">Partikel konstelasi emas di belakang hero</p>
                </div>
            </label>
        </div>

        <!-- 4. HERO PHOTO -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">4. Foto Latar Beranda</p>
            <div class="bg-ivory-warm/50 border-2 border-dashed border-navy/20 p-5 text-center">
                <i class="fas fa-camera text-3xl text-slate mb-2"></i>
                <input type="file" name="hero_photo" id="setPhoto" accept="image/*" class="block w-full text-sm text-slate file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-navy file:text-ivory file:font-semibold file:uppercase file:tracking-editorial file:text-xs hover:file:bg-gold hover:file:text-navy file:cursor-pointer">
                <p class="text-xs text-slate mt-2">Foto gedung/lab kampus. Akan diberi overlay navy otomatis.</p>
            </div>
            <?php if (!empty($settings['hero_photo'])): ?>
                <div class="mt-4 flex items-center gap-4">
                    <img src="<?= base_url('assets/uploads/' . $settings['hero_photo']) ?>" class="w-32 h-20 object-cover border border-gray-200" alt="">
                    <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_photo" value="1" class="accent-red-600"> Hapus foto saat ini
                    </label>
                </div>
            <?php endif; ?>
        </div>

        <!-- 5. VIDEO TOUR -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">5. Video Tour Kampus</p>
            <div class="space-y-5">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="video_tour_enabled" value="1" <?= (($settings['video_tour_enabled'] ?? '0') == '1') ? 'checked' : '' ?> class="w-5 h-5 accent-navy">
                    <div>
                        <p class="text-sm font-medium text-navy">Aktifkan Section Video Tour</p>
                        <p class="text-xs text-slate">Tampilkan video tour di beranda</p>
                    </div>
                </label>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block editorial-label text-navy mb-2">Judul Section</label>
                        <input type="text" name="video_tour_title" value="<?= html_escape($settings['video_tour_title'] ?? 'Explore Our Campus') ?>" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2">Tipe Video</label>
                        <select name="video_tour_type" class="w-full px-3 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                            <option value="youtube" <?= ($settings['video_tour_type'] ?? 'youtube') == 'youtube' ? 'selected' : '' ?>>YouTube</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block editorial-label text-navy mb-2">URL YouTube</label>
                        <input type="url" name="video_tour_youtube" value="<?= html_escape($settings['video_tour_youtube'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block editorial-label text-navy mb-2">Sub-judul</label>
                        <textarea name="video_tour_subtitle" rows="2" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['video_tour_subtitle'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. SOSIAL MEDIA & KONTAK -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">6. Sosial Media & Kontak Fakultas</p>
            <div class="space-y-5">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block editorial-label text-navy mb-2"><i class="fab fa-facebook text-blue-600 mr-1"></i>Facebook URL</label>
                        <input type="url" name="facebook_url" value="<?= html_escape($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><i class="fab fa-instagram text-pink-600 mr-1"></i>Instagram URL</label>
                        <input type="url" name="instagram_url" value="<?= html_escape($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><i class="fab fa-linkedin text-blue-700 mr-1"></i>LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="<?= html_escape($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/school/..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><i class="fab fa-youtube text-red-600 mr-1"></i>YouTube URL</label>
                        <input type="url" name="youtube_url" value="<?= html_escape($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><i class="fab fa-twitter text-sky-500 mr-1"></i>Twitter/X URL</label>
                        <input type="url" name="twitter_url" value="<?= html_escape($settings['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                    </div>
                    <div>
                        <label class="block editorial-label text-navy mb-2"><i class="fab fa-tiktok text-black mr-1"></i>TikTok URL</label>
                        <input type="url" name="tiktok_url" value="<?= html_escape($settings['tiktok_url'] ?? '') ?>" placeholder="https://tiktok.com/@..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy text-sm">
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <p class="text-sm font-medium text-navy mb-4">Informasi Kontak</p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block editorial-label text-navy mb-2">No. Telepon</label>
                            <input type="text" name="contact_phone" value="<?= html_escape($settings['contact_phone'] ?? '') ?>" placeholder="(021) 1234-5678" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        </div>
                        <div>
                            <label class="block editorial-label text-navy mb-2">Email</label>
                            <input type="email" name="contact_email" value="<?= html_escape($settings['contact_email'] ?? '') ?>" placeholder="info@fakultas.ac.id" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block editorial-label text-navy mb-2">Alamat Lengkap</label>
                        <textarea name="contact_address" rows="2" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['contact_address'] ?? '') ?></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block editorial-label text-navy mb-2">Google Maps Embed URL <span class="text-slate font-normal">(opsional)</span></label>
                        <input type="url" name="contact_map_embed" value="<?= html_escape($settings['contact_map_embed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=..." class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy font-mono text-xs">
                        <p class="text-xs text-slate mt-1 italic">Ambil dari Google Maps → Bagikan → Sematkan peta → salin src iframe</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. FOOTER CUSTOM -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">7. Konten Footer</p>
            <div class="space-y-5">
                <div>
                    <label class="block editorial-label text-navy mb-2">Tagline Fakultas</label>
                    <textarea name="footer_tagline" rows="2" class="w-full px-4 py-3 bg-ivory/50 border border-navy/10 text-navy"><?= html_escape($settings['footer_tagline'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block editorial-label text-navy mb-2">Teks Copyright</label>
                    <input type="text" name="footer_copyright" value="<?= html_escape($settings['footer_copyright'] ?? '') ?>" placeholder="Fakultas Teknik & Ilmu Komputer" class="w-full px-4 py-2.5 bg-ivory/50 border border-navy/10 text-navy">
                </div>
            </div>
        </div>

        <!-- 8. TEXT ALIGNMENT GLOBAL -->
        <div class="bg-white border border-gray-200 p-6 md:p-8">
            <p class="editorial-label text-gold-muted mb-5">8. Posisi Teks Publik (Alignment)</p>
            <p class="text-sm text-slate mb-6">Atur perataan teks di seluruh halaman publik website.</p>

            <div class="grid md:grid-cols-3 gap-6">
                <?php
                $alignment_options = [
                    ['key' => 'hero_alignment', 'label' => 'Hero (Beranda)', 'current' => $settings['hero_alignment'] ?? 'left'],
                    ['key' => 'section_alignment', 'label' => 'Judul Section', 'current' => $settings['section_alignment'] ?? 'left'],
                    ['key' => 'content_alignment', 'label' => 'Isi Konten', 'current' => $settings['content_alignment'] ?? 'left'],
                ];
                foreach ($alignment_options as $opt): ?>
                <div>
                    <label class="block editorial-label text-navy mb-3"><?= $opt['label'] ?></label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach (['left','center','right'] as $al):
                            $icon = $al == 'left' ? 'fa-align-left' : ($al == 'center' ? 'fa-align-center' : 'fa-align-right');
                            $label = $al == 'left' ? 'Kiri' : ($al == 'center' ? 'Tengah' : 'Kanan');
                        ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="<?= $opt['key'] ?>" value="<?= $al ?>" <?= set_radio($opt['key'], $al, $opt['current'] == $al) ?> class="sr-only peer">
                            <div class="text-center p-3 border-2 peer-checked:border-gold peer-checked:bg-gold/10 border-gray-200 transition">
                                <i class="fas <?= $icon ?> text-navy"></i>
                                <p class="text-[10px] uppercase tracking-wider mt-1 text-slate"><?= $label ?></p>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 p-4 bg-ivory-warm/50 border border-gold/20">
                <p class="text-xs text-slate">
                    <i class="fas fa-info-circle text-gold-muted mr-1"></i>
                    <strong>Preview:</strong> Pengaturan ini mempengaruhi <em>Hero</em> (judul + tombol), <em>Judul Section</em> (h2 di tiap halaman), dan <em>Isi Konten</em> (paragraf).
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-gold px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
                <i class="fas fa-rocket mr-2"></i>Publikasikan Tampilan
            </button>
            <a href="<?= base_url() ?>" target="_blank" class="btn-outline-navy px-8 py-3 font-semibold uppercase tracking-editorial text-xs">
                <i class="fas fa-external-link-alt mr-2"></i>Lihat Website
            </a>
        </div>

        <?= form_close() ?>
    </div>

    <!-- LIVE PREVIEW -->
    <div class="lg:col-span-2 lg:sticky lg:top-24">
        <p class="editorial-label text-gold-muted mb-4">Live Preview</p>
        <div id="pvHero" class="relative bg-navy text-ivory overflow-hidden border border-gray-200 shadow-xl" style="min-height:420px">
            <div id="pvPhoto" class="hero-photo hidden" style="position:absolute;inset:0;background-size:cover;background-position:center"></div>
            <div id="pvAurora" class="absolute inset-0 hidden overflow-hidden"></div>
            <div id="pvCampus" class="absolute inset-0 hidden overflow-hidden"></div>
            <div id="pvWaves" class="absolute inset-0 hidden overflow-hidden">
                <svg class="w-full h-full" viewBox="0 0 400 400" preserveAspectRatio="none">
                    <path id="pvWavePath1" class="pv-wave-path" d="" stroke="rgba(201,162,39,.6)" stroke-width="1.5" fill="none"></path>
                    <path id="pvWavePath2" class="pv-wave-path" d="" stroke="rgba(247,245,240,.3)" stroke-width="1" fill="none"></path>
                </svg>
            </div>
            <div id="pvMatrix" class="absolute inset-0 hidden overflow-hidden"></div>
            <div id="pvGeometric" class="absolute inset-0 hidden overflow-hidden"></div>
            <canvas id="pvCanvas" class="absolute inset-0 w-full h-full"></canvas>
            <div class="relative z-10 p-8" id="pvContent">
                <div class="flex items-center gap-3 mb-6">
                    <div id="pvLogo" class="w-9 h-9 bg-gold text-navy flex items-center justify-center font-serif font-bold text-sm">F</div>
                    <span id="pvSiteName" class="font-serif font-semibold text-sm tracking-tight text-ivory">Fakultas Teknik</span>
                </div>
                <p class="editorial-label text-gold mb-4" id="pvLabel"><?= html_escape($settings['hero_label'] ?? '') ?></p>
                <h3 class="font-serif font-light text-3xl leading-tight mb-4" id="pvTitle"></h3>
                <p class="text-sm text-ivory/80 mb-6" id="pvSubtitle"><?= html_escape(character_limiter($settings['hero_subtitle'] ?? '', 90)) ?></p>
                <div class="flex gap-2">
                    <span class="btn-gold px-4 py-2 text-[10px]">Tombol Emas</span>
                    <span class="btn-outline px-4 py-2 text-[10px]">Outline</span>
                </div>
            </div>
            <div id="pvRobotStrip" class="relative z-10" style="height:56px;border-top:1px solid rgba(247,245,240,.12);overflow:hidden">
                <div class="pv-text" id="pvText" style="position:absolute;left:16px;right:16px;top:12px;font-family:'JetBrains Mono',monospace;font-size:.6rem;letter-spacing:.25em;white-space:nowrap;overflow:hidden"></div>
                <div class="pv-robot"><span class="antenna"></span><span class="head"></span><span class="body"></span><span class="leg l"></span><span class="leg r"></span></div>
            </div>
        </div>
        <p class="text-xs text-slate mt-3 italic"><i class="fas fa-info-circle text-gold-muted mr-1"></i>Preview berubah real-time saat Anda mengubah pengaturan.</p>
    </div>
</div>

<script>
(function () {
    var fontMap = {
        editorial: ['Fraunces', 'Inter'],
        classic:   ['Playfair Display', 'Source Sans 3'],
        modern:    ['Sora', 'Inter'],
        elegant:   ['Cormorant Garamond', 'Jost'],
        architect: ['Bricolage Grotesque', 'Inter'],
        journal:   ['DM Serif Display', 'Atkinson Hyperlegible'],
        tech:      ['Space Grotesk', 'IBM Plex Mono'],
        heritage:  ['Libre Caslon Text', 'Lato'],
        humanist:  ['Spectral', 'Source Sans 3'],
        swiss:     ['Helvetica Neue', 'Neue Haas Grotesk'],
        monument:  ['Bodoni Moda', 'Inter'],
    };
    var sizeMap = { sm: '13px', md: '15px', lg: '17px', xl: '19px' };
    var $ = function (id) { return document.getElementById(id); };
    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function formatTitle(t) { return esc(t).replace(/\*(.+?)\*/g, '<em style="color:#C9A227;font-style:italic">$1</em>'); }

    function loadGoogleFont(family) {
        if (!family || family === 'Helvetica Neue' || family === 'Neue Haas Grotesk') return;
        var link = document.createElement('link');
        link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(family) + ':wght@300;400;500;600;700;800;900&display=swap';
        link.rel = 'stylesheet';
        document.head.appendChild(link);
    }

    function spawnCampus(container, count) {
        container.innerHTML = '';
        var icons = ['fa-graduation-cap','fa-laptop','fa-book','fa-flask','fa-microscope','fa-lightbulb','fa-user-graduate','fa-book-open'];
        for (var i = 0; i < count; i++) {
            var s = document.createElement('span');
            s.className = 'pv-campus-icon';
            s.innerHTML = '<i class="fas ' + icons[i % icons.length] + '"></i>';
            s.style.left = (3 + Math.random() * 90) + '%';
            s.style.top = (8 + Math.random() * 80) + '%';
            s.style.fontSize = (12 + Math.random() * 20) + 'px';
            s.style.color = (i % 2 === 0) ? 'rgba(201,162,39,.35)' : 'rgba(247,245,240,.2)';
            s.style.animationDuration = (4 + Math.random() * 6) + 's';
            s.style.animationDelay = (-Math.random() * 6) + 's';
            container.appendChild(s);
        }
    }

    function spawnAurora() {
        var layer = $('pvAurora'); if (!layer) return; layer.dataset.spawned = '1';
        var cols = ['rgba(201,162,39,.5)','rgba(19,51,79,.8)','rgba(212,175,55,.4)'];
        for (var i = 0; i < 3; i++) {
            var d = document.createElement('div');
            d.className = 'pv-aurora';
            d.style.width = (70 + i * 30) + 'px'; d.style.height = d.style.width;
            d.style.background = cols[i];
            d.style.left = (10 + i * 28) + '%'; d.style.top = (15 + (i % 2) * 40) + '%';
            d.style.animationDelay = (i * 1.2) + 's';
            layer.appendChild(d);
        }
    }

    function spawnMatrix() {
        var layer = $('pvMatrix'); if (!layer || layer.dataset.spawned) return;
        layer.dataset.spawned = '1';
        var chars = '01FTIKACDEFGHIJKLMN';
        for (var i = 0; i < 15; i++) {
            var s = document.createElement('span');
            s.className = 'pv-matrix-char';
            s.textContent = chars[Math.floor(Math.random() * chars.length)];
            s.style.left = (Math.random() * 95) + '%';
            s.style.animationDelay = (Math.random() * 3) + 's';
            s.style.animationDuration = (2 + Math.random() * 2) + 's';
            layer.appendChild(s);
        }
    }

    function spawnGeometric() {
        var layer = $('pvGeometric'); if (!layer || layer.dataset.spawned) return;
        layer.dataset.spawned = '1';
        for (var i = 0; i < 8; i++) {
            var s = document.createElement('div');
            s.className = 'pv-geo';
            var size = 20 + Math.random() * 40;
            s.style.width = size + 'px'; s.style.height = size + 'px';
            s.style.left = (Math.random() * 90) + '%';
            s.style.top = (Math.random() * 80) + '%';
            s.style.borderRadius = (i % 3 === 1) ? '50%' : (i % 3 === 2 ? '0' : '2px');
            s.style.animationDuration = (6 + Math.random() * 8) + 's';
            s.style.animationDirection = (i % 2 === 0) ? 'normal' : 'reverse';
            if (i % 3 === 2) s.style.transform = 'rotate(45deg)';
            layer.appendChild(s);
        }
    }

    function animateWaves() {
        var t = Date.now() / 1000;
        var w = 400, h = 400;
        function path(amp, freq, yOff, phase) {
            var d = 'M 0 ' + (yOff + Math.sin(phase) * amp);
            for (var x = 0; x <= w; x += 10) {
                var y = yOff + Math.sin((x / w) * Math.PI * freq + t + phase) * amp;
                d += ' L ' + x + ' ' + y;
            }
            return d;
        }
        var p1 = $('pvWavePath1'), p2 = $('pvWavePath2');
        if (p1) p1.setAttribute('d', path(40, 3, 200, 0));
        if (p2) p2.setAttribute('d', path(30, 2, 250, 1));
    }

    function apply() {
        var fontKey = $('setFont').value;
        var f = fontMap[fontKey] || fontMap.editorial;
        loadGoogleFont(f[0]);
        loadGoogleFont(f[1]);
        var content = $('pvContent');
        content.style.fontFamily = f[1] + ', sans-serif';
        content.querySelectorAll('.font-serif, h3').forEach(function (el) { el.style.fontFamily = f[0] + ', serif'; });
        $('pvHero').style.fontSize = sizeMap[$('setScale').value] || '15px';
        $('pvSiteName').textContent = $('setSiteName').value || 'Fakultas Teknik';
        $('pvSiteName').style.fontFamily = f[0] + ', serif';
        $('pvLabel').textContent = $('setLabel').value;
        $('pvTitle').innerHTML = formatTitle($('setTitle').value);
        $('pvSubtitle').textContent = $('setSubtitle').value.slice(0, 90);
        $('pvText').innerHTML = '<span class="lit">' + esc($('setWelcome').value.slice(0, 20)) + '</span><span class="dim">' + esc($('setWelcome').value.slice(20)) + '</span>';

        var tplEl = document.querySelector('input[name="hero_animation"]:checked');
        if (!tplEl) return;
        var tpl = tplEl.value;
        $('pvRobotStrip').style.display = (tpl === 'robot') ? 'block' : 'none';
        $('pvAurora').classList.toggle('hidden', tpl !== 'aurora');
        if (tpl === 'aurora' && !$('pvAurora').dataset.spawned) spawnAurora();
        $('pvCampus').classList.toggle('hidden', tpl !== 'campus');
        if (tpl === 'campus' && !$('pvCampus').dataset.spawned) { spawnCampus($('pvCampus'), 14); $('pvCampus').dataset.spawned = '1'; }
        $('pvWaves').classList.toggle('hidden', tpl !== 'waves');
        $('pvMatrix').classList.toggle('hidden', tpl !== 'matrix');
        if (tpl === 'matrix') spawnMatrix();
        $('pvGeometric').classList.toggle('hidden', tpl !== 'geometric');
        if (tpl === 'geometric') spawnGeometric();
        $('pvCanvas').classList.toggle('hidden', !($('setParticles').checked || tpl === 'particles'));

        document.querySelectorAll('.template-card').forEach(function (card) {
            var input = card.querySelector('input');
            card.classList.toggle('border-gold', input.checked);
            card.classList.toggle('bg-gold/5', input.checked);
            card.classList.toggle('border-gray-200', !input.checked);
            var dot = card.querySelector('.tpl-dot');
            dot.classList.toggle('border-gold', input.checked);
            dot.classList.toggle('bg-gold', input.checked);
            dot.classList.toggle('border-gray-300', !input.checked);
        });
    }

    ['setFont','setScale','setSiteName','setLabel','setTitle','setSubtitle','setWelcome','setParticles'].forEach(function (id) {
        var el = $(id); if (el) el.addEventListener('input', apply);
    });
    document.querySelectorAll('input[name="hero_animation"]').forEach(function (r) { r.addEventListener('change', apply); });

    var logoInput = $('setLogo');
    if (logoInput) logoInput.addEventListener('change', function (e) {
        var file = e.target.files[0]; if (!file) return;
        var reader = new FileReader();
        reader.onload = function (ev) {
            var logo = $('pvLogo');
            logo.innerHTML = '';
            var img = document.createElement('img');
            img.src = ev.target.result;
            img.className = 'w-full h-full object-contain';
            logo.appendChild(img);
            logo.style.background = 'white';
            logo.style.padding = '4px';
        };
        reader.readAsDataURL(file);
    });

    var photoInput = $('setPhoto');
    if (photoInput) photoInput.addEventListener('change', function (e) {
        var file = e.target.files[0]; if (!file) return;
        var reader = new FileReader();
        reader.onload = function (ev) {
            var ph = $('pvPhoto');
            ph.classList.remove('hidden');
            ph.style.backgroundImage = 'url(' + ev.target.result + ')';
        };
        reader.readAsDataURL(file);
    });

    var canvas = $('pvCanvas'), ctx = canvas.getContext('2d'), pts = [];
    function resize() { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; }
    resize(); window.addEventListener('resize', resize);
    for (var i = 0; i < 20; i++) pts.push({ x: Math.random() * 400, y: Math.random() * 400, vx: (Math.random() - .5) * .5, vy: (Math.random() - .5) * .5 });
    (function tick() {
        requestAnimationFrame(tick);
        if (document.hidden) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (!canvas.classList.contains('hidden')) {
            pts.forEach(function (p) {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
                ctx.beginPath(); ctx.arc(p.x, p.y, 1.2, 0, 6.28); ctx.fillStyle = 'rgba(201,162,39,.6)'; ctx.fill();
            });
        }
        if (!$('pvWaves').classList.contains('hidden')) animateWaves();
    })();

    // ===== 🔥 FITUR GILA: Import Settings =====
    var importInput = document.getElementById('importFile');
    if (importInput) {
        importInput.addEventListener('change', function(e){
            var file = e.target.files[0];
            if (!file) return;
            if (!file.name.endsWith('.json')) {
                alert('File harus berformat .json');
                importInput.value = '';
                return;
            }
            if (!confirm('Import settings dari ' + file.name + '? Pengaturan saat ini akan ditimpa.')) {
                importInput.value = '';
                return;
            }
            var formData = new FormData();
            formData.append('import_file', file);

            fetch('<?= base_url('admin/appearance/import') ?>', {
                method: 'POST',
                body: formData
            })
            .then(function(r){ return r.text(); })
            .then(function(){
                alert('Settings berhasil diimport! Halaman akan refresh.');
                window.location.reload();
            })
            .catch(function(err){
                alert('Gagal import: ' + err.message);
                importInput.value = '';
            });
        });
    }

    apply();
})();
</script>