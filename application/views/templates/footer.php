<?php
$CI =& get_instance();
$CI->load->model('Setting_model');
$site_name  = $CI->Setting_model->get('site_name', 'Fakultas Ilmu Komputer');
$site_logo  = $CI->Setting_model->get('site_logo', '');
$footer_tagline = $CI->Setting_model->get('footer_tagline', 'Mencetak generasi unggul dalam bidang teknologi dan ilmu komputer.');
$footer_copyright = $CI->Setting_model->get('footer_copyright', 'Fakultas Ilmu Komputer');
$contact_phone = $CI->Setting_model->get('contact_phone', '(021) 1234-5678');
$contact_email = $CI->Setting_model->get('contact_email', 'info@fakultas.ac.id');
$contact_address = $CI->Setting_model->get('contact_address', '');
$contact_map = $CI->Setting_model->get('contact_map_embed', '');
$facebook_url = $CI->Setting_model->get('facebook_url', '#');
$instagram_url = $CI->Setting_model->get('instagram_url', '#');
$linkedin_url = $CI->Setting_model->get('linkedin_url', '#');
$youtube_url = $CI->Setting_model->get('youtube_url', '#');
$twitter_url = $CI->Setting_model->get('twitter_url', '');
$tiktok_url = $CI->Setting_model->get('tiktok_url', '');
$EN = get_site_lang() == 'en';

// Helper t() fallback (jika belum ada di header)
if (!function_exists('t')) {
    function t($key) {
        $translations = [
            'footer_quick' => get_site_lang() == 'en' ? 'Quick Links' : 'Tautan Cepat',
            'footer_contact' => get_site_lang() == 'en' ? 'Contact Us' : 'Hubungi Kami',
            'footer_hours' => get_site_lang() == 'en' ? 'Mon-Fri: 08:00 - 16:00' : 'Sen-Jum: 08:00 - 16:00',
            'footer_rights' => get_site_lang() == 'en' ? 'All rights reserved.' : 'Hak cipta dilindungi.',
            'nav_profil' => get_site_lang() == 'en' ? 'Profile' : 'Profil',
            'nav_prodi' => get_site_lang() == 'en' ? 'Programs' : 'Program Studi',
            'nav_dosen' => get_site_lang() == 'en' ? 'Faculty' : 'Dosen',
            'nav_riset' => get_site_lang() == 'en' ? 'Research' : 'Riset',
            'nav_berita' => get_site_lang() == 'en' ? 'News' : 'Berita',
            'nav_download' => get_site_lang() == 'en' ? 'Downloads' : 'Unduhan',
            'nav_kalender' => get_site_lang() == 'en' ? 'Calendar' : 'Kalender',
            'nav_kemahasiswaan' => get_site_lang() == 'en' ? 'Student Affairs' : 'Kemahasiswaan',
            'nav_prestasi' => get_site_lang() == 'en' ? 'Achievements' : 'Prestasi',
            'nav_fasilitas' => get_site_lang() == 'en' ? 'Facilities' : 'Fasilitas',
            'nav_quick' => get_site_lang() == 'en' ? 'Quick Links' : 'Tautan Cepat',
        ];
        return $translations[$key] ?? $key;
    }
}
?>
</main>

<!-- Newsletter -->
<section class="bg-navy text-ivory py-20 border-t-4 border-gold">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="editorial-label text-gold mb-4">Stay Informed</p>
                <h2 class="font-serif text-4xl md:text-5xl font-light leading-tight mb-4">
                    <?= $EN ? 'Join our <em class="italic text-gold">academic community</em>' : 'Bergabunglah dengan <em class="italic text-gold">komunitas akademik</em> kami' ?>
                </h2>
                <p class="text-ivory/70 max-w-lg"><?= html_escape($footer_tagline) ?></p>
            </div>
            <form class="space-y-3 max-w-md md:ml-auto" id="newsletterForm">
                <input type="email" name="email" required placeholder="<?= $EN ? 'Your email' : 'Email Anda' ?>" class="w-full px-5 py-4 bg-transparent border border-ivory/30 text-ivory placeholder:text-ivory/50 focus:border-gold outline-none transition" aria-label="Email for newsletter">
                <button type="submit" class="w-full btn-gold py-4 font-semibold uppercase tracking-editorial text-xs" id="newsletterBtn">
                    <span id="btnText"><?= $EN ? 'Subscribe to Newsletter' : 'Berlangganan Newsletter' ?></span>
                    <span id="btnLoading" class="hidden"><i class="fas fa-spinner fa-spin mr-2"></i><?= $EN ? 'Subscribing...' : 'Berlangganan...' ?></span>
                </button>
                <p id="newsletterMsg" class="text-sm hidden"></p>
            </form>
        </div>
    </div>
</section>

<footer class="bg-navy-deep text-ivory/80 pt-20 pb-8">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-12 gap-12 pb-16 border-b border-ivory/10">
            <div class="md:col-span-4">
                <div class="flex items-center gap-3 mb-6">
                    <?php if ($site_logo): ?>
                        <img src="<?= base_url('assets/uploads/' . $site_logo) ?>" class="w-12 h-12 object-contain bg-white border border-gold/30 p-1" alt="Logo" loading="lazy">
                    <?php else: ?>
                        <div class="w-12 h-12 bg-gold text-navy flex items-center justify-center font-serif font-bold text-2xl">F</div>
                    <?php endif; ?>
                    <div class="leading-tight">
                        <h3 class="font-serif font-semibold text-ivory text-xl"><?= html_escape($site_name) ?></h3>
                        <p <p class="text-[10px] uppercase tracking-editorial text-ivory/50"><?= html_escape(get_site_lang() == 'en' ? site_tagline() : site_name()) ?></p>
                    </div>
                </div>
                <p class="text-sm text-ivory/60 leading-relaxed mb-6 max-w-sm"><?= html_escape($footer_tagline) ?></p>
                <div class="flex gap-3">
                    <?php if ($facebook_url && $facebook_url !== '#'): ?><a href="<?= html_escape($facebook_url) ?>" target="_blank" rel="noopener" class="w-10 h-10 border border-ivory/20 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition" aria-label="Facebook"><i class="fab fa-facebook-f text-sm"></i></a><?php endif; ?>
                    <?php if ($instagram_url && $instagram_url !== '#'): ?><a href="<?= html_escape($instagram_url) ?>" target="_blank" rel="noopener" class="w-10 h-10 border border-ivory/20 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition" aria-label="Instagram"><i class="fab fa-instagram text-sm"></i></a><?php endif; ?>
                    <?php if ($linkedin_url && $linkedin_url !== '#'): ?><a href="<?= html_escape($linkedin_url) ?>" target="_blank" rel="noopener" class="w-10 h-10 border border-ivory/20 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition" aria-label="LinkedIn"><i class="fab fa-linkedin-in text-sm"></i></a><?php endif; ?>
                    <?php if ($youtube_url && $youtube_url !== '#'): ?><a href="<?= html_escape($youtube_url) ?>" target="_blank" rel="noopener" class="w-10 h-10 border border-ivory/20 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition" aria-label="YouTube"><i class="fab fa-youtube text-sm"></i></a><?php endif; ?>
                    <?php if ($twitter_url): ?><a href="<?= html_escape($twitter_url) ?>" target="_blank" rel="noopener" class="w-10 h-10 border border-ivory/20 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition" aria-label="Twitter"><i class="fab fa-twitter text-sm"></i></a><?php endif; ?>
                    <?php if ($tiktok_url): ?><a href="<?= html_escape($tiktok_url) ?>" target="_blank" rel="noopener" class="w-10 h-10 border border-ivory/20 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition" aria-label="TikTok"><i class="fab fa-tiktok text-sm"></i></a><?php endif; ?>
                </div>
            </div>
            <div class="md:col-span-2">
                <p class="editorial-label text-gold mb-5"><?= t('footer_quick') ?></p>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= base_url('profil') ?>" class="hover:text-gold transition"><?= t('nav_profil') ?></a></li>
                    <li><a href="<?= base_url('akademik') ?>" class="hover:text-gold transition"><?= t('nav_prodi') ?></a></li>
                    <li><a href="<?= base_url('dosen') ?>" class="hover:text-gold transition"><?= t('nav_dosen') ?></a></li>
                    <li><a href="<?= base_url('riset') ?>" class="hover:text-gold transition"><?= t('nav_riset') ?></a></li>
                    <li><a href="<?= base_url('berita') ?>" class="hover:text-gold transition"><?= t('nav_berita') ?></a></li>
                </ul>
            </div>
            <div class="md:col-span-2">
                <p class="editorial-label text-gold mb-5"><?= t('nav_quick') ?></p>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= base_url('download') ?>" class="hover:text-gold transition"><?= t('nav_download') ?></a></li>
                    <li><a href="<?= base_url('akademik/kalender') ?>" class="hover:text-gold transition"><?= t('nav_kalender') ?></a></li>
                    <li><a href="<?= base_url('kemahasiswaan') ?>" class="hover:text-gold transition"><?= t('nav_kemahasiswaan') ?></a></li>
                    <li><a href="<?= base_url('prestasi') ?>" class="hover:text-gold transition"><?= t('nav_prestasi') ?></a></li>
                    <li><a href="<?= base_url('fasilitas') ?>" class="hover:text-gold transition"><?= t('nav_fasilitas') ?></a></li>
                </ul>
            </div>
            <div class="md:col-span-4">
                <p class="editorial-label text-gold mb-5"><?= t('footer_contact') ?></p>
                <address class="not-italic space-y-4 text-sm">
                    <?php if ($contact_address): ?>
                    <div class="flex gap-3"><i class="fas fa-map-marker-alt text-gold mt-1"></i><span><?= nl2br(html_escape($contact_address)) ?></span></div>
                    <?php endif; ?>
                    <?php if ($contact_phone): ?>
                    <div class="flex gap-3"><i class="fas fa-phone text-gold mt-1"></i><a href="tel:<?= preg_replace('/[^0-9+]/', '', $contact_phone) ?>" class="hover:text-gold transition"><?= html_escape($contact_phone) ?></a></div>
                    <?php endif; ?>
                    <?php if ($contact_email): ?>
                    <div class="flex gap-3"><i class="fas fa-envelope text-gold mt-1"></i><a href="mailto:<?= html_escape($contact_email) ?>" class="hover:text-gold transition"><?= html_escape($contact_email) ?></a></div>
                    <?php endif; ?>
                    <div class="flex gap-3"><i class="fas fa-clock text-gold mt-1"></i><span><?= t('footer_hours') ?></span></div>
                </address>
                <?php if ($contact_map): ?>
                <div class="mt-5 aspect-video bg-navy overflow-hidden">
                    <iframe src="<?= html_escape($contact_map) ?>" width="100%" height="100%" style="border:0;filter:invert(0.9) hue-rotate(180deg);" allowfullscreen="" loading="lazy" title="<?= $EN ? 'Campus location map' : 'Peta lokasi kampus' ?>"></iframe>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-ivory/50">
            <p>&copy; <?= date('Y') ?> <?= html_escape(site_name()) ?>. <?= t('footer_rights') ?></p>
            <div class="flex gap-6">
                <a href="<?= base_url('privacy-policy') ?>" class="hover:text-gold transition"><?= $EN ? 'Privacy Policy' : 'Kebijakan Privasi' ?></a>
                <a href="<?= base_url('terms') ?>" class="hover:text-gold transition"><?= $EN ? 'Terms of Use' : 'Syarat Penggunaan' ?></a>
                <a href="<?= base_url('sitemap') ?>" class="hover:text-gold transition"><?= $EN ? 'Sitemap' : 'Peta Situs' ?></a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/public.js') ?>"></script>

<script>
(function(){
    // ===== NEWSLETTER AJAX =====
    var form = document.getElementById('newsletterForm');
    var btn = document.getElementById('newsletterBtn');
    var btnText = document.getElementById('btnText');
    var btnLoading = document.getElementById('btnLoading');
    var msg = document.getElementById('newsletterMsg');

    if (form) {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var email = form.email.value;
            
            // Show loading
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            btn.disabled = true;
            msg.classList.add('hidden');

            // Simulate AJAX (replace with actual endpoint)
            setTimeout(function(){
                // Success
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btn.disabled = false;
                
                msg.textContent = '<?= $EN ? "✓ Thank you for subscribing!" : "✓ Terima kasih telah berlangganan!" ?>';
                msg.classList.remove('hidden');
                msg.classList.add('text-green-400');
                
                form.reset();
                
                setTimeout(function(){
                    msg.classList.add('hidden');
                }, 5000);
            }, 1500);

            /* 
            // Actual AJAX implementation (uncomment when endpoint ready)
            fetch('<?= base_url('newsletter/subscribe') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email: email})
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btn.disabled = false;
                
                if (data.success) {
                    msg.textContent = data.message;
                    msg.classList.remove('hidden');
                    msg.classList.add('text-green-400');
                    form.reset();
                    setTimeout(function(){ msg.classList.add('hidden'); }, 5000);
                } else {
                    msg.textContent = data.error || '<?= $EN ? "Something went wrong" : "Terjadi kesalahan" ?>';
                    msg.classList.remove('hidden');
                    msg.classList.add('text-red-400');
                }
            })
            .catch(function(err){
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btn.disabled = false;
                msg.textContent = '<?= $EN ? "Network error. Please try again." : "Error jaringan. Silakan coba lagi." ?>';
                msg.classList.remove('hidden');
                msg.classList.add('text-red-400');
            });
            */
        });
    }
})();
</script>

</body>
</html>