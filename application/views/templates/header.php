<?php
$current_uri = uri_string();
$CI =& get_instance();
$CI->load->model('Setting_model');

// ===== FIX: Helper t() fallback =====
if (!function_exists('t')) {
    function t($key) {
        $translations = [
            'nav_home' => get_site_lang() == 'en' ? 'Home' : 'Beranda',
            'nav_profil' => get_site_lang() == 'en' ? 'Profile' : 'Profil',
            'nav_sejarah' => get_site_lang() == 'en' ? 'History' : 'Sejarah',
            'nav_visimisi' => get_site_lang() == 'en' ? 'Vision & Mission' : 'Visi & Misi',
            'nav_struktur' => get_site_lang() == 'en' ? 'Structure' : 'Struktur',
            'nav_sambutan' => get_site_lang() == 'en' ? "Dean's Welcome" : 'Sambutan Dekan',
            'nav_akademik' => get_site_lang() == 'en' ? 'Academic' : 'Akademik',
            'nav_prodi' => get_site_lang() == 'en' ? 'Programs' : 'Program Studi',
            'nav_kurikulum' => get_site_lang() == 'en' ? 'Curriculum' : 'Kurikulum',
            'nav_kalender' => get_site_lang() == 'en' ? 'Calendar' : 'Kalender',
            'nav_akreditasi' => get_site_lang() == 'en' ? 'Accreditation' : 'Akreditasi',
            'nav_dosen' => get_site_lang() == 'en' ? 'Faculty' : 'Dosen',
            'nav_riset' => get_site_lang() == 'en' ? 'Research' : 'Riset',
            'nav_prestasi' => get_site_lang() == 'en' ? 'Achievements' : 'Prestasi',
            'nav_kemahasiswaan' => get_site_lang() == 'en' ? 'Student Affairs' : 'Kemahasiswaan',
            'nav_beasiswa' => get_site_lang() == 'en' ? 'Scholarships' : 'Beasiswa',
            'nav_berita' => get_site_lang() == 'en' ? 'News' : 'Berita',
            'nav_download' => get_site_lang() == 'en' ? 'Downloads' : 'Unduhan',
            'nav_fasilitas' => get_site_lang() == 'en' ? 'Facilities' : 'Fasilitas',
            'nav_mahasiswa' => get_site_lang() == 'en' ? 'For Students' : 'Untuk Mahasiswa',
            'nav_dosen_label' => get_site_lang() == 'en' ? 'For Faculty' : 'Untuk Dosen',
            'nav_elearning' => get_site_lang() == 'en' ? 'E-Learning' : 'E-Learning',
            'nav_quick' => get_site_lang() == 'en' ? 'Quick Links' : 'Tautan Cepat',
            'nav_login' => get_site_lang() == 'en' ? 'Login' : 'Masuk',
            'topbar_follow' => get_site_lang() == 'en' ? 'Follow Us' : 'Ikuti Kami',
            'topbar_portal' => get_site_lang() == 'en' ? 'Portal' : 'Portal',
            'search_placeholder' => get_site_lang() == 'en' ? 'Search news, faculty, programs...' : 'Cari berita, dosen, program studi...',
            'footer_quick' => get_site_lang() == 'en' ? 'Quick Links' : 'Tautan Cepat',
            'footer_contact' => get_site_lang() == 'en' ? 'Contact Us' : 'Hubungi Kami',
            'footer_hours' => get_site_lang() == 'en' ? 'Mon-Fri: 08:00 - 16:00' : 'Sen-Jum: 08:00 - 16:00',
            'footer_rights' => get_site_lang() == 'en' ? 'All rights reserved.' : 'Hak cipta dilindungi.',
        ];
        return $translations[$key] ?? $key;
    }
}

$app_font       = $CI->Setting_model->get('font_family', 'editorial');
$app_scale      = $CI->Setting_model->get('font_scale', 'md');
$site_lang      = get_site_lang();
$EN             = ($site_lang == 'en');
$site_name      = $CI->Setting_model->get('site_name', 'Fakultas Ilmu Komputer');
$site_logo      = $CI->Setting_model->get('site_logo', '');
$site_favicon   = $CI->Setting_model->get('site_favicon', '');
$ga_id          = $CI->Setting_model->get('ga_measurement_id', '');
$contact_phone  = $CI->Setting_model->get('contact_phone', '(021) 1234-5678');
$contact_email  = $CI->Setting_model->get('contact_email', 'info@fakultas.ac.id');
$contact_address= $CI->Setting_model->get('contact_address', 'Jakarta, Indonesia');
$facebook_url   = $CI->Setting_model->get('facebook_url', '#');
$instagram_url  = $CI->Setting_model->get('instagram_url', '#');
$linkedin_url   = $CI->Setting_model->get('linkedin_url', '#');
$youtube_url    = $CI->Setting_model->get('youtube_url', '#');
$twitter_url    = $CI->Setting_model->get('twitter_url', '');
$tiktok_url     = $CI->Setting_model->get('tiktok_url', '');
$hero_al        = $CI->Setting_model->get('hero_alignment', 'left');
$section_al     = $CI->Setting_model->get('section_alignment', 'left');
$content_al     = $CI->Setting_model->get('content_alignment', 'left');

$font_map = [
    'editorial' => ['Fraunces', 'Inter', 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Inter:wght@300;400;500;600;700;800&display=swap'],
    'classic'   => ['Playfair Display', 'Source Sans 3', 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Source+Sans+3:wght@300;400;500;600;700;800&display=swap'],
    'modern'    => ['Sora', 'Inter', 'https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap'],
    'elegant'   => ['Cormorant Garamond', 'Jost', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap'],
    'architect' => ['Bricolage Grotesque', 'Inter', 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300..800&family=Inter:wght@300;400;500;600;700;800&display=swap'],
    'journal'   => ['DM Serif Display', 'Atkinson Hyperlegible', 'https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap'],
    'tech'      => ['Space Grotesk', 'IBM Plex Mono', 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;1,400&display=swap'],
    'heritage'  => ['Libre Caslon Text', 'Lato', 'https://fonts.googleapis.com/css2?family=Libre+Caslon+Text:ital,wght@0,400;0,700;1,400&family=Lato:ital,wght@0,300;0,400;0,700;1,400&display=swap'],
    'humanist'  => ['Spectral', 'Source Sans 3', 'https://fonts.googleapis.com/css2?family=Spectral:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Source+Sans+3:wght@300;400;500;600;700;800&display=swap'],
    'swiss'     => ['Helvetica Neue', 'Inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap'],
    'monument'  => ['Bodoni Moda', 'Inter', 'https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;0,6..96,700;0,6..96,900;1,6..96,400&family=Inter:wght@300;400;500;600;700;800&display=swap'],
];
$font = $font_map[$app_font] ?? $font_map['editorial'];
$name_parts = preg_split('/\s*[&]\s*/', $site_name, 2);
$name_line1 = trim($name_parts[0] ?? $site_name);
$name_line2 = isset($name_parts[1]) ? '& ' . trim($name_parts[1]) : '';

// ===== SEO: Dynamic meta tags =====
$default_faculty = 'Fakultas Ilmu Komputer';
$page_title = isset($title) ? str_replace($default_faculty, $site_name, $title) : $site_name;
$page_desc  = isset($description) ? str_replace($default_faculty, $site_name, $description) : 'Website Resmi ' . $site_name;
$page_image = isset($featured_image) ? base_url('assets/uploads/' . $featured_image) : ($site_logo ? base_url('assets/uploads/' . $site_logo) : '');
$page_url = current_url();
?>
<!DOCTYPE html>
<html lang="<?= $site_lang ?>" class="fs-<?= html_escape($app_scale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== SEO META TAGS ===== -->
    <title><?= html_escape($page_title) ?></title>
    <meta name="description" content="<?= html_escape($page_desc) ?>">
    <link rel="canonical" href="<?= html_escape($page_url) ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= html_escape($page_url) ?>">
    <meta property="og:title" content="<?= html_escape($page_title) ?>">
    <meta property="og:description" content="<?= html_escape($page_desc) ?>">
    <?php if ($page_image): ?>
    <meta property="og:image" content="<?= html_escape($page_image) ?>">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= html_escape($page_url) ?>">
    <meta property="twitter:title" content="<?= html_escape($page_title) ?>">
    <meta property="twitter:description" content="<?= html_escape($page_desc) ?>">
    <?php if ($page_image): ?>
    <meta property="twitter:image" content="<?= html_escape($page_image) ?>">
    <?php endif; ?>
    
    <!-- Theme Color (Mobile Browser) -->
    <meta name="theme-color" content="#0B2239">
    
    <?php if ($site_favicon): ?>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/uploads/' . $site_favicon) ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/uploads/' . $site_favicon) ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/uploads/' . $site_favicon) ?>">
    <?php endif; ?>
    
    <?php if ($ga_id && strlen($ga_id) > 5): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= html_escape($ga_id) ?>"></script>
    <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '<?= html_escape($ga_id) ?>');</script>
    <?php endif; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= $font[2] ?>" rel="stylesheet">
    
    <!-- MODE GELAP PUBLIK: terapkan sebelum render (anti flash putih) -->
    <script>
    (function(){
        if (localStorage.getItem('pub_dark') === '1') document.documentElement.classList.add('pub-dark');
    })();
    </script>

    <script>
    /* ==== CONSOLE GUARD: saring warning Tailwind CDN ==== */
    (function(){
        if (!window.console || !console.warn) return;
        var _warn = console.warn.bind(console);
        console.warn = function(){
            var m = Array.prototype.slice.call(arguments).join(' ');
            if (m.indexOf('tailwindcss.com') !== -1 || m.indexOf('Tailwind CSS') !== -1) return;
            _warn.apply(console, arguments);
        };
    })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { 'navy': '#0B2239', 'navy-deep': '#061420', 'navy-light': '#13334F', 'ivory': '#F7F5F0', 'ivory-warm': '#EDE8DE', 'gold': '#C9A227', 'gold-soft': '#D4AF37', 'gold-muted': '#B8941F', 'slate': '#475569' },
            fontFamily: { 'serif': ['<?= $font[0] ?>', 'Georgia', 'serif'], 'sans': ['<?= $font[1] ?>', 'system-ui', 'sans-serif'] },
            letterSpacing: { 'editorial': '0.2em', 'wide-xl': '0.35em' }
        }}}
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>">
    
    <!-- ===== JSON-LD ORGANIZATION SCHEMA ===== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "EducationalOrganization",
        "name": <?= json_encode($site_name) ?>,
        "url": <?= json_encode(base_url()) ?>,
        "logo": <?= json_encode($site_logo ? base_url('assets/uploads/' . $site_logo) : '') ?>,
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": <?= json_encode($contact_phone) ?>,
            "email": <?= json_encode($contact_email) ?>,
            "contactType": "customer service",
            "availableLanguage": ["Indonesian", "English"]
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": <?= json_encode($contact_address) ?>,
            "addressCountry": "ID"
        },
        "sameAs": [
            <?php $socials = [];
            if ($facebook_url && $facebook_url !== '#') $socials[] = $facebook_url;
            if ($instagram_url && $instagram_url !== '#') $socials[] = $instagram_url;
            if ($linkedin_url && $linkedin_url !== '#') $socials[] = $linkedin_url;
            if ($youtube_url && $youtube_url !== '#') $socials[] = $youtube_url;
            if ($twitter_url) $socials[] = $twitter_url;
            if ($tiktok_url) $socials[] = $tiktok_url;
            echo json_encode($socials);
            ?>
        ]
    }
    </script>
    
    <style>
        :root { --font-serif: '<?= $font[0] ?>', Georgia, serif; --font-sans: '<?= $font[1] ?>', system-ui, sans-serif; }
        html.fs-sm { font-size: 15px; } html.fs-md { font-size: 16px; }
        html.fs-lg { font-size: 17.5px; } html.fs-xl { font-size: 19px; }
        body { font-family: '<?= $font[1] ?>', sans-serif; background: #F7F5F0; color: #1C1917; }
        h1, h2, h3, h4, .font-serif { font-family: '<?= $font[0] ?>', Georgia, serif; }
        .nav-link { position: relative; transition: color 0.2s ease; white-space: nowrap; }
        .nav-link::after { content: ''; position: absolute; bottom: -6px; left: 50%; width: 0; height: 2px; background: #C9A227; transition: all 0.4s cubic-bezier(0.19, 1, 0.22, 1); transform: translateX(-50%); }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .dropdown > a.nav-link { padding-top: 14px; padding-bottom: 14px; }
        .dropdown-menu { display: block !important; opacity: 0; visibility: hidden; pointer-events: none; transform: translateY(12px); transition: opacity .22s ease, transform .22s ease, visibility 0s linear .25s; }
        .dropdown-menu::before { content: ''; position: absolute; left: 0; right: 0; top: -24px; height: 24px; background: transparent; }
        .dropdown:hover > .dropdown-menu, .dropdown:focus-within > .dropdown-menu { opacity: 1; visibility: visible; pointer-events: auto; transform: translateY(0); transition: opacity .22s ease, transform .22s ease; }
        .dropdown > a.nav-link i { transition: transform .25s ease; }
        .dropdown:hover > a.nav-link i { transform: rotate(180deg); }
        .oversize-num { font-family: '<?= $font[0] ?>', serif; font-size: clamp(4rem, 10vw, 8rem); line-height: 0.9; font-weight: 300; letter-spacing: -0.04em; }
        .editorial-label { font-size: 0.68rem; letter-spacing: 0.25em; text-transform: uppercase; font-weight: 600; }
        .fade-in { transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
        .link-arrow { display: inline-flex; align-items: center; gap: 0.5rem; transition: gap 0.3s ease; }
        .link-arrow:hover { gap: 0.85rem; }
        .hero-pattern { background-image: radial-gradient(circle at 20% 30%, rgba(201, 162, 39, 0.08) 0%, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.04) 0%, transparent 40%); }
        .btn-primary { background: #0B2239; color: #F7F5F0; border: 1px solid #0B2239; transition: all 0.35s ease; }
        .btn-primary:hover { background: #C9A227; border-color: #C9A227; color: #0B2239; }
        .btn-outline { background: transparent; color: #F7F5F0; border: 1px solid rgba(247, 245, 240, 0.4); transition: all 0.35s ease; }
        .btn-outline:hover { background: #F7F5F0; color: #0B2239; border-color: #F7F5F0; }
        .btn-gold { background: #C9A227; color: #0B2239; border: 1px solid #C9A227; transition: all 0.35s ease; }
        .btn-gold:hover { background: #B8941F; border-color: #B8941F; }
        .news-card:hover .news-image { transform: scale(1.05); }
        .news-image { transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1); }
        #mainNav.nav-scrolled { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(12px); box-shadow: 0 1px 0 rgba(11, 34, 57, 0.08); }
        #mainNav.nav-scrolled #navInner { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: #F7F5F0; }
        ::-webkit-scrollbar-thumb { background: #0B2239; } ::-webkit-scrollbar-thumb:hover { background: #C9A227; }
        ::selection { background: #C9A227; color: #0B2239; }
        .text-balance { text-wrap: balance; }
        .lang-btn { padding: 2px 8px; border: 1px solid rgba(247,245,240,.25); transition: all .2s ease; }
        .lang-btn.active { background: #C9A227; color: #0B2239; border-color: #C9A227; font-weight: 700; }

        /* ===== SKIP TO CONTENT (Accessibility) ===== */
        .skip-link {
            position: absolute; top: -40px; left: 0; background: #C9A227;
            color: #0B2239; padding: 8px 16px; text-decoration: none;
            z-index: 9999; font-weight: 700; transition: top 0.3s;
        }
        .skip-link:focus { top: 0; }

        /* ===== COMMAND PALETTE (Ctrl+K) ===== */
        .cmd-palette {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(6,20,32,.85); backdrop-filter: blur(8px);
            display: none; align-items: flex-start; justify-content: center;
            padding-top: 15vh;
        }
        .cmd-palette.open { display: flex; animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .cmd-palette-box {
            background: #fff; width: 90%; max-width: 600px;
            box-shadow: 0 24px 64px rgba(0,0,0,.3); border-radius: 8px;
            overflow: hidden;
        }
        .cmd-palette-input {
            width: 100%; padding: 20px 24px; border: none; font-size: 18px;
            background: transparent; outline: none;
        }
        .cmd-palette-results { max-height: 400px; overflow-y: auto; border-top: 1px solid #e5e7eb; }
        .cmd-palette-item {
            padding: 12px 24px; display: flex; align-items: center; gap: 12px;
            cursor: pointer; transition: background .15s;
        }
        .cmd-palette-item:hover, .cmd-palette-item.active { background: #F7F5F0; }
        .cmd-palette-item i { width: 20px; color: #C9A227; }
        .cmd-palette-hint {
            padding: 12px 24px; background: #F7F5F0; font-size: 11px;
            color: #64748b; text-align: center;
        }

        /* ===== BACK TO TOP ===== */
        .back-to-top {
            position: fixed; bottom: 24px; right: 24px; z-index: 90;
            width: 48px; height: 48px; background: #0B2239; color: #C9A227;
            border: none; border-radius: 50%; cursor: pointer;
            display: none; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(11,34,57,.3); transition: all .3s;
        }
        .back-to-top.show { display: flex; animation: slideUp .3s ease; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: none; opacity: 1; } }
        .back-to-top:hover { background: #C9A227; color: #0B2239; transform: translateY(-4px); }

        /* ===== CREATIVE MAX ===== */
        .brand-logo-wrap { position: relative; transition: transform .45s cubic-bezier(.34,1.56,.64,1); }
        .brand-logo-wrap::after {
            content: ''; position: absolute; inset: -4px;
            border: 1px solid rgba(201,162,39,.0); border-radius: 50%;
            transition: all .4s ease;
        }
        a.brand-link:hover .brand-logo-wrap { transform: rotate(-6deg) scale(1.06); }
        a.brand-link:hover .brand-logo-wrap::after { inset: -5px; border-color: rgba(201,162,39,.55); transform: rotate(90deg); border-radius: 12px; }
        .brand-name {
            position: relative; display: inline-block;
            font-family: var(--font-serif); font-weight: 700;
            letter-spacing: -0.015em; line-height: 1.05; color: #0B2239;
            transition: color .3s ease;
        }
        .brand-name::after {
            content: ''; position: absolute; left: 0; bottom: -3px; height: 2px; width: 0;
            background: linear-gradient(90deg, #C9A227 0%, rgba(201,162,39,0) 100%);
            transition: width .55s cubic-bezier(.22,1,.36,1);
        }
        a.brand-link:hover .brand-name { color: #B8941F; }
        a.brand-link:hover .brand-name::after { width: 100%; }
        .brand-sub {
            display: flex; align-items: center; gap: .45rem;
            font-size: 9.5px; letter-spacing: .32em; text-transform: uppercase;
            font-weight: 600; color: #64748b; margin-top: 3px; transition: color .3s ease;
        }
        .brand-sub::before { content: ''; width: 14px; height: 1px; background: #C9A227; }
        a.brand-link:hover .brand-sub { color: #B8941F; }
        .brand-name { font-size: 17px; }
        @media (min-width: 1280px) { .brand-name { font-size: 20px; } }
        @media (min-width: 1536px) { .brand-name { font-size: 23px; } }
        @media (min-width: 1280px) { .brand-sub { font-size: 10.5px; } }
        .nav-divider {
            width: 1px; height: 34px; flex-shrink: 0;
            background: linear-gradient(180deg, transparent, rgba(201,162,39,.6), transparent);
        }
        #mainNav ul.nav-desktop > li { animation: navIn .55s cubic-bezier(.22,1,.36,1) both; }
        #mainNav ul.nav-desktop > li:nth-child(1) { animation-delay: .04s; }
        #mainNav ul.nav-desktop > li:nth-child(2) { animation-delay: .08s; }
        #mainNav ul.nav-desktop > li:nth-child(3) { animation-delay: .12s; }
        #mainNav ul.nav-desktop > li:nth-child(4) { animation-delay: .16s; }
        #mainNav ul.nav-desktop > li:nth-child(5) { animation-delay: .20s; }
        #mainNav ul.nav-desktop > li:nth-child(6) { animation-delay: .24s; }
        #mainNav ul.nav-desktop > li:nth-child(7) { animation-delay: .28s; }
        #mainNav ul.nav-desktop > li:nth-child(8) { animation-delay: .32s; }
        #mainNav ul.nav-desktop > li:nth-child(9) { animation-delay: .36s; }
        #mainNav ul.nav-desktop > li:nth-child(10) { animation-delay: .40s; }
        #mainNav ul.nav-desktop > li:nth-child(11) { animation-delay: .44s; }
        #mainNav ul.nav-desktop > li:nth-child(12) { animation-delay: .48s; }
        #mainNav ul.nav-desktop > li:nth-child(13) { animation-delay: .52s; }
        @keyframes navIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
        #mainNav ul.nav-desktop > li > a.nav-link { transition: color .2s ease, letter-spacing .3s ease; }
        #mainNav ul.nav-desktop > li > a.nav-link:hover { letter-spacing: .03em; }
        .pmb-cta { animation: pmbGlow 2.8s ease-in-out infinite; }
        @keyframes pmbGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(201,162,39,0); }
            50% { box-shadow: 0 0 16px 2px rgba(201,162,39,.45); }
        }
        #mainNav ul.nav-desktop { white-space: nowrap; }
        #mainNav ul.nav-desktop > li { flex-shrink: 0; }
        @media (min-width: 1024px) and (max-width: 1279px) {
            #mainNav ul.nav-desktop { gap: 0.7rem; font-size: 12.5px; }
            #mainNav .pmb-cta { padding: 0.5rem 0.7rem; font-size: 10px; }
            .nav-divider { display: none; }
        }
        @media (min-width: 1280px) and (max-width: 1535px) {
            #mainNav ul.nav-desktop { gap: 1rem; font-size: 13.5px; }
        }
        @media (min-width: 1536px) {
            #mainNav ul.nav-desktop { gap: 1.25rem; font-size: 14.5px; }
        }

        /* ===== MOBILE MENU ANIMATION ===== */
        #mobile-menu {
            max-height: 0; overflow: hidden;
            transition: max-height .4s cubic-bezier(.22,1,.36,1);
        }
        #mobile-menu.open { max-height: 800px; }

        /* ===== GLOBAL TEXT ALIGNMENT ===== */
        <?php if ($hero_al == 'center'): ?>
        main > section:first-child .container > div > div:first-child,
        main > section:first-child .max-w-3xl,
        main > section:first-child .max-w-4xl { margin-left: auto !important; margin-right: auto !important; text-align: center !important; }
        main > section:first-child h1, main > section:first-child .editorial-label, main > section:first-child p:not(nav *) { text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
        main > section:first-child .flex-wrap.gap-4, main > section:first-child .flex.gap-4 { justify-content: center !important; }
        <?php elseif ($hero_al == 'right'): ?>
        main > section:first-child .container > div > div:first-child, main > section:first-child .max-w-3xl, main > section:first-child .max-w-4xl { margin-left: auto !important; text-align: right !important; }
        main > section:first-child h1, main > section:first-child .editorial-label, main > section:first-child p:not(nav *) { text-align: right !important; }
        main > section:first-child .flex-wrap.gap-4, main > section:first-child .flex.gap-4 { justify-content: flex-end !important; }
        <?php else: ?>
        main > section:first-child .container > div > div:first-child, main > section:first-child h1, main > section:first-child .editorial-label, main > section:first-child p:not(nav *) { text-align: left !important; }
        main > section:first-child .flex-wrap.gap-4, main > section:first-child .flex.gap-4 { justify-content: flex-start !important; }
        <?php endif; ?>

        <?php if ($section_al == 'center'): ?>
        main > section:not(:first-child) h1, main > section:not(:first-child) h2 { text-align: center !important; }
        <?php elseif ($section_al == 'right'): ?>
        main > section:not(:first-child) h1, main > section:not(:first-child) h2 { text-align: right !important; }
        <?php else: ?>
        main > section:not(:first-child) h1, main > section:not(:first-child) h2 { text-align: left !important; }
        <?php endif; ?>

        <?php if ($content_al == 'center'): ?>
        main > section:not(:first-child) p:not(.editorial-label):not(nav *), main > section:not(:first-child) .text-gray-700, main > section:not(:first-child) .text-slate.leading-relaxed, main > section:not(:first-child) .leading-relaxed, main > section:not(:first-child) .leading-loose { text-align: center !important; }
        <?php elseif ($content_al == 'right'): ?>
        main > section:not(:first-child) p:not(.editorial-label):not(nav *), main > section:not(:first-child) .text-gray-700, main > section:not(:first-child) .text-slate.leading-relaxed, main > section:not(:first-child) .leading-relaxed, main > section:not(:first-child) .leading-loose { text-align: right !important; }
        <?php else: ?>
        main > section:not(:first-child) p:not(.editorial-label):not(nav *), main > section:not(:first-child) .text-gray-700, main > section:not(:first-child) .text-slate.leading-relaxed, main > section:not(:first-child) .leading-relaxed, main > section:not(:first-child) .leading-loose { text-align: left !important; }
        <?php endif; ?>

        main table *, main ul li, main ol li, main .refined-table *, main form input, main form textarea, main form select, main form label, main .mission-card *, main .grid > div, main .news-card h3, main .news-card p { text-align: inherit !important; }

        /* ============ MODE GELAP PUBLIK ============ */
        html.pub-dark { color-scheme: dark; }
        html.pub-dark body { background: #0B2239 !important; color: #F7F5F0; transition: background .35s, color .35s; }

        /* ===== FIX VISIBILITAS NAVBAR DARK ===== */
        html.pub-dark #mainNav { background: #0d1a2e !important; border-color: rgba(255,255,255,.08) !important; }
        html.pub-dark #mainNav ul.nav-desktop,
        html.pub-dark #mainNav ul.nav-desktop > li > a.nav-link { color: rgba(247,245,240,.82) !important; }
        /* varian opacity (text-navy/70 dll) */
        html.pub-dark .text-navy\/70, html.pub-dark .text-navy\/60, html.pub-dark .text-navy\/80 { color: rgba(247,245,240,.78) !important; }
        html.pub-dark #mainNav .nav-link:hover,
        html.pub-dark #mainNav ul.nav-desktop > li > a:hover { color: #C9A227 !important; }
        html.pub-dark .brand-name { color: #F7F5F0 !important; }
        html.pub-dark .brand-sub { color: #a9b8c8 !important; }
        html.pub-dark #searchToggle, html.pub-dark #mobile-menu-btn { color: rgba(247,245,240,.85) !important; border-color: rgba(255,255,255,.15) !important; }
        html.pub-dark #searchToggle:hover, html.pub-dark #mobile-menu-btn:hover { border-color: #C9A227 !important; color: #C9A227 !important; }
        html.pub-dark #mobile-menu { border-color: rgba(255,255,255,.08) !important; }
        html.pub-dark #mobile-menu a { color: #F7F5F0 !important; }
        html.pub-dark #mobile-menu a:hover { background: #13334F !important; color: #C9A227 !important; }
        html.pub-dark #searchBar input { background: #0d1a2e !important; color: #F7F5F0 !important; border-color: rgba(255,255,255,.2) !important; }

        /* Background utilitas */
        html.pub-dark .bg-white, html.pub-dark #mainNav { background: #0d1a2e !important; }
        html.pub-dark .bg-ivory { background: #0B2239 !important; }
        html.pub-dark .bg-ivory-warm { background: #10203a !important; }
        html.pub-dark .bg-gray-50, html.pub-dark .bg-gray-100, html.pub-dark .bg-gray-200 { background: #13334F !important; }

        /* Teks */
        html.pub-dark .text-navy { color: #F7F5F0 !important; }
        html.pub-dark .text-slate { color: #a9b8c8 !important; }
        html.pub-dark .text-gray-500, html.pub-dark .text-gray-600, html.pub-dark .text-gray-700 { color: #c2cedb !important; }
        html.pub-dark h1, html.pub-dark h2, html.pub-dark h3, html.pub-dark h4 { color: #F7F5F0 !important; }

        /* Border */
        html.pub-dark .border-gray-100, html.pub-dark .border-gray-200,
        html.pub-dark .border-gray-200\/60 { border-color: rgba(255,255,255,.08) !important; }

        /* Kartu & panel (override halaman) */
        html.pub-dark .fac-card, html.pub-dark .dzl-card, html.pub-dark .tr-card, html.pub-dark .rel-card,
        html.pub-dark .related-card, html.pub-dark .trending-card, html.pub-dark .list-row, html.pub-dark .author-card,
        html.pub-dark .fac-stat-card, html.pub-dark .nz-month, html.pub-dark .read-toolbar, html.pub-dark .reading-stats,
        html.pub-dark .fac-map-wrap, html.pub-dark .share-panel, html.pub-dark .tr-champ-card,
        html.pub-dark .dzl-modal-card, html.pub-dark .tr-modal-card, html.pub-dark .dz-modal-card,
        html.pub-dark .fac-modal-card, html.pub-dark .kb-modal-card, html.pub-dark .cmd-palette-box,
        html.pub-dark .tr-stat, html.pub-dark .tr-modal-card {
            background: #13334F !important; border-color: rgba(255,255,255,.08) !important;
        }

        /* Dropdown nav */
        html.pub-dark .dropdown-menu { background: #13334F !important; border-top-color: #C9A227 !important; }
        html.pub-dark .dropdown-menu li a { color: #c2cedb !important; border-color: rgba(255,255,255,.05) !important; }
        html.pub-dark .dropdown-menu li a:hover { background: #0B2239 !important; color: #C9A227 !important; }

        /* Input */
        html.pub-dark input, html.pub-dark textarea, html.pub-dark select {
            background: #0d1a2e !important; color: #F7F5F0 !important; border-color: rgba(255,255,255,.15) !important;
        }
        html.pub-dark input::placeholder, html.pub-dark textarea::placeholder { color: #7a8a9a !important; }

        /* Artikel */
        html.pub-dark .article-body { color: #dbe4ee !important; }
        html.pub-dark .article-body h2, html.pub-dark .article-body h3, html.pub-dark .article-body h4 { color: #C9A227 !important; }
        html.pub-dark .article-body blockquote { background: linear-gradient(135deg,#13334F,#0B2239) !important; color: #F7F5F0 !important; }

        /* Command palette */
        html.pub-dark .cmd-palette-item { color: #F7F5F0; }
        html.pub-dark .cmd-palette-item:hover, html.pub-dark .cmd-palette-item.active { background: #0B2239 !important; }
        html.pub-dark .cmd-palette-hint { background: #0B2239 !important; color: #a9b8c8 !important; }

        /* Chip, tag, tombol outline */
        html.pub-dark .tr-chip, html.pub-dark .dzl-chip, html.pub-dark .tc-chip, html.pub-dark .fac-chip {
            background: #13334F !important; color: #c2cedb !important; border-color: rgba(255,255,255,.1) !important;
        }
        html.pub-dark .tr-chip.active, html.pub-dark .dzl-chip.active, html.pub-dark .tc-chip.active {
            background: #C9A227 !important; color: #0B2239 !important;
        }

        /* Gambar sedikit diredupkan */
        html.pub-dark img { filter: brightness(.92); }

        /* Scrollbar gelap */
        html.pub-dark ::-webkit-scrollbar-track { background: #0B2239; }
        html.pub-dark ::-webkit-scrollbar-thumb { background: #C9A227; }

        /* Toggle ikon dinamis */
        html.pub-dark #pubDarkToggle { background: #C9A227 !important; color: #0B2239 !important; border-color: #C9A227 !important; }
    </style>
</head>
<body class="bg-ivory">

<!-- SKIP TO CONTENT (Accessibility) -->
<a href="#main-content" class="skip-link"><?= $EN ? 'Skip to content' : 'Langsung ke konten' ?></a>

<!-- Top Bar -->
<div class="bg-navy text-ivory/80 text-xs py-2.5 border-b border-navy-light">
    <div class="container mx-auto px-6 flex justify-between items-center">
        <div class="flex gap-5 items-center">
            <?php if ($contact_phone): ?>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $contact_phone) ?>" class="hidden sm:inline-flex items-center gap-1.5 hover:text-gold transition" aria-label="<?= $EN ? 'Call us' : 'Telepon kami' ?>">
                <i class="fas fa-phone text-[10px]"></i><span><?= html_escape($contact_phone) ?></span>
            </a>
            <?php endif; ?>
            <?php if ($contact_email): ?>
            <a href="mailto:<?= html_escape($contact_email) ?>" class="hidden md:inline-flex items-center gap-1.5 hover:text-gold transition" aria-label="<?= $EN ? 'Email us' : 'Email kami' ?>">
                <i class="fas fa-envelope text-[10px]"></i><span><?= html_escape($contact_email) ?></span>
            </a>
            <?php endif; ?>
            <?php if ($contact_address): ?>
            <span class="hidden lg:inline-flex items-center gap-1.5">
                <i class="fas fa-map-marker-alt text-[10px]"></i><span class="max-w-xs truncate"><?= html_escape($contact_address) ?></span>
            </span>
            <?php endif; ?>
        </div>
        <div class="flex gap-4 items-center">
            <span class="hidden md:inline text-[10px] uppercase tracking-editorial text-ivory/50 mr-1"><?= t('topbar_follow') ?>:</span>
            <?php if ($facebook_url && $facebook_url !== '#'): ?><a href="<?= html_escape($facebook_url) ?>" target="_blank" rel="noopener" class="hover:text-gold transition" aria-label="Facebook"><i class="fab fa-facebook-f text-[11px]"></i></a><?php endif; ?>
            <?php if ($instagram_url && $instagram_url !== '#'): ?><a href="<?= html_escape($instagram_url) ?>" target="_blank" rel="noopener" class="hover:text-gold transition" aria-label="Instagram"><i class="fab fa-instagram text-[11px]"></i></a><?php endif; ?>
            <?php if ($linkedin_url && $linkedin_url !== '#'): ?><a href="<?= html_escape($linkedin_url) ?>" target="_blank" rel="noopener" class="hover:text-gold transition" aria-label="LinkedIn"><i class="fab fa-linkedin-in text-[11px]"></i></a><?php endif; ?>
            <?php if ($youtube_url && $youtube_url !== '#'): ?><a href="<?= html_escape($youtube_url) ?>" target="_blank" rel="noopener" class="hover:text-gold transition" aria-label="YouTube"><i class="fab fa-youtube text-[11px]"></i></a><?php endif; ?>
            <?php if ($twitter_url): ?><a href="<?= html_escape($twitter_url) ?>" target="_blank" rel="noopener" class="hover:text-gold transition" aria-label="Twitter"><i class="fab fa-twitter text-[11px]"></i></a><?php endif; ?>
            <?php if ($tiktok_url): ?><a href="<?= html_escape($tiktok_url) ?>" target="_blank" rel="noopener" class="hover:text-gold transition" aria-label="TikTok"><i class="fab fa-tiktok text-[11px]"></i></a><?php endif; ?>
            <span class="hidden md:inline text-ivory/30">|</span>
            <span class="inline-flex items-center gap-1">
                <i class="fas fa-globe text-[10px] text-gold"></i>
                <a href="<?= site_url('language/switch/id') ?>" class="lang-btn <?= $site_lang == 'id' ? 'active' : '' ?>" aria-label="Bahasa Indonesia">ID</a>
                <a href="<?= site_url('language/switch/en') ?>" class="lang-btn <?= $site_lang == 'en' ? 'active' : '' ?>" aria-label="English">EN</a>
            </span>
            <span class="hidden md:inline text-ivory/30">|</span>
            <a href="<?= base_url('auth/login') ?>" class="hidden md:inline-flex items-center gap-1.5 hover:text-gold transition" aria-label="<?= t('nav_login') ?>">
                <i class="fas fa-lock text-[10px]"></i><span class="uppercase tracking-editorial text-[10px]"><?= t('topbar_portal') ?></span>
            </a>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav id="mainNav" class="bg-white sticky top-0 z-50 transition-all duration-300 border-b border-gray-200/60" aria-label="Main navigation">
    <div class="container mx-auto px-4 xl:px-6">
        <div id="navInner" class="flex justify-between items-center gap-3 xl:gap-4 py-4 xl:py-5 transition-all duration-300">

            <a href="<?= base_url() ?>" class="brand-link flex items-center gap-3 shrink-0" title="<?= html_escape($site_name) ?>" aria-label="<?= html_escape($site_name) ?> - Home">
                <span class="brand-logo-wrap inline-flex">
                    <?php if ($site_logo): ?>
                        <img src="<?= base_url('assets/uploads/' . $site_logo) ?>" class="w-11 h-11 xl:w-12 xl:h-12 object-contain bg-white border border-gray-200 p-1" alt="Logo">
                    <?php else: ?>
                        <span class="w-11 h-11 xl:w-12 xl:h-12 bg-navy flex items-center justify-center text-ivory font-serif font-bold text-xl tracking-tight">F</span>
                    <?php endif; ?>
                </span>
                <span class="hidden lg:block leading-tight">
                    <span class="brand-name whitespace-nowrap"><?= html_escape($name_line1) ?></span>
                    <?php if ($name_line2): ?>
                    <span class="brand-sub whitespace-nowrap"><?= html_escape(ltrim($name_line2, '& ')) ?></span>
                    <?php endif; ?>
                </span>
            </a>

            <span class="nav-divider hidden xl:block"></span>

            <ul class="nav-desktop hidden lg:flex items-center text-[12px] font-semibold text-navy">
                <li><a href="<?= base_url() ?>" class="nav-link <?= $current_uri == '' ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>"><?= t('nav_home') ?></a></li>
                <li class="dropdown relative">
                    <a href="<?= base_url('profil') ?>" class="nav-link <?= strpos($current_uri, 'profil') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?> inline-flex items-center gap-1" aria-haspopup="true" aria-expanded="false"><?= t('nav_profil') ?> <i class="fas fa-chevron-down text-[9px] ml-0.5"></i></a>
                    <ul class="dropdown-menu hidden absolute left-0 mt-4 w-64 bg-white border-t-2 border-gold shadow-2xl z-50" role="menu">
                        <li><a href="<?= base_url('profil/sejarah') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-landmark text-gold-muted w-4"></i><?= t('nav_sejarah') ?></a></li>
                        <li><a href="<?= base_url('profil/visi-misi') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-bullseye text-gold-muted w-4"></i><?= t('nav_visimisi') ?></a></li>
                        <li><a href="<?= base_url('profil/struktur') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-sitemap text-gold-muted w-4"></i><?= t('nav_struktur') ?></a></li>
                        <li><a href="<?= base_url('profil/sambutan') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy transition" role="menuitem"><i class="fas fa-comment-dots text-gold-muted w-4"></i><?= t('nav_sambutan') ?></a></li>
                    </ul>
                </li>
                <li class="dropdown relative">
                    <a href="<?= base_url('akademik') ?>" class="nav-link <?= strpos($current_uri, 'akademik') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?> inline-flex items-center gap-1" aria-haspopup="true" aria-expanded="false"><?= t('nav_akademik') ?> <i class="fas fa-chevron-down text-[9px] ml-0.5"></i></a>
                    <ul class="dropdown-menu hidden absolute left-0 mt-4 w-64 bg-white border-t-2 border-gold shadow-2xl z-50" role="menu">
                        <li><a href="<?= base_url('akademik') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-graduation-cap text-gold-muted w-4"></i><?= t('nav_prodi') ?></a></li>
                        <li><a href="<?= base_url('akademik/kurikulum') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-book-open text-gold-muted w-4"></i><?= t('nav_kurikulum') ?></a></li>
                        <li><a href="<?= base_url('akademik/kalender') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-calendar-alt text-gold-muted w-4"></i><?= t('nav_kalender') ?></a></li>
                        <li><a href="<?= base_url('akademik/akreditasi') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy transition" role="menuitem"><i class="fas fa-certificate text-gold-muted w-4"></i><?= t('nav_akreditasi') ?></a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url('dosen') ?>" class="nav-link <?= strpos($current_uri, 'dosen') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>"><?= t('nav_dosen') ?></a></li>
                <li><a href="<?= base_url('riset') ?>" class="nav-link <?= strpos($current_uri, 'riset') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>"><?= t('nav_riset') ?></a></li>
                <li><a href="<?= base_url('prestasi') ?>" class="nav-link <?= strpos($current_uri, 'prestasi') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>"><?= t('nav_prestasi') ?></a></li>
                <li class="dropdown relative">
                    <a href="<?= base_url('kemahasiswaan') ?>" class="nav-link <?= strpos($current_uri, 'kemahasiswaan') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?> inline-flex items-center gap-1" aria-haspopup="true" aria-expanded="false"><?= t('nav_kemahasiswaan') ?> <i class="fas fa-chevron-down text-[9px] ml-0.5"></i></a>
                    <ul class="dropdown-menu hidden absolute left-0 mt-4 w-64 bg-white border-t-2 border-gold shadow-2xl z-50" role="menu">
                        <li><a href="<?= base_url('kemahasiswaan') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-users text-gold-muted w-4"></i><?= t('nav_kemahasiswaan') ?></a></li>
                        <li><a href="<?= base_url('kemahasiswaan') ?>" class="flex items-center gap-3 px-5 py-3.5 text-sm text-slate hover:bg-ivory hover:text-navy transition" role="menuitem"><i class="fas fa-award text-gold-muted w-4"></i><?= t('nav_beasiswa') ?></a></li>
                    </ul>
                </li>
                <li><a href="<?= base_url('alumni') ?>" class="nav-link <?= strpos($current_uri, 'alumni') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>">Alumni</a></li>
                <li><a href="<?= base_url('berita') ?>" class="nav-link <?= strpos($current_uri, 'berita') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>"><?= t('nav_berita') ?></a></li>
                <li><a href="<?= base_url('kontak') ?>" class="nav-link <?= strpos($current_uri, 'kontak') === 0 ? 'active text-navy' : 'text-navy/70 hover:text-navy' ?>"><?= $EN ? 'Contact' : 'Kontak' ?></a></li>
                <li>
                    <a href="<?= base_url('pmb') ?>" class="pmb-cta btn-gold px-3 xl:px-4 py-2 text-[10px] uppercase tracking-editorial font-bold inline-flex items-center gap-1.5 whitespace-nowrap <?= strpos($current_uri, 'pmb') === 0 ? 'ring-2 ring-offset-1 ring-gold' : '' ?>" aria-label="<?= $EN ? 'Apply for admission' : 'Daftar PMB' ?>">
                        <i class="fas fa-user-plus text-[9px]"></i><?= $EN ? 'Apply' : 'PMB' ?>
                    </a>
                </li>
                <li class="dropdown relative">
                    <button class="nav-link text-navy/70 hover:text-navy w-9 h-9 flex items-center justify-center border border-gray-200 hover:border-gold transition" aria-label="More menu" aria-haspopup="true" aria-expanded="false"><i class="fas fa-th-large text-[11px]"></i></button>
                    <ul class="dropdown-menu hidden absolute right-0 mt-4 w-64 bg-white border-t-2 border-gold shadow-2xl z-50" role="menu">
                        <li class="px-5 pt-4 pb-2"><span class="editorial-label text-gold-muted"><?= t('nav_mahasiswa') ?></span></li>
                        <li><a href="<?= base_url('download') ?>" class="flex items-center gap-3 px-5 py-2.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-download text-gold-muted w-4"></i><?= t('nav_download') ?></a></li>
                        <li><a href="<?= base_url('kemahasiswaan') ?>" class="flex items-center gap-3 px-5 py-2.5 text-sm text-slate hover:bg-ivory hover:text-navy transition" role="menuitem"><i class="fas fa-award text-gold-muted w-4"></i><?= t('nav_beasiswa') ?></a></li>
                        <li class="px-5 pt-3 pb-2 border-t border-gray-100"><span class="editorial-label text-gold-muted"><?= $EN ? 'For Alumni' : 'Untuk Alumni' ?></span></li>
                        <li><a href="<?= base_url('alumni') ?>" class="flex items-center gap-3 px-5 py-2.5 text-sm text-slate hover:bg-ivory hover:text-navy border-b border-gray-100 transition" role="menuitem"><i class="fas fa-id-badge text-gold-muted w-4"></i><?= $EN ? 'Alumni Directory' : 'Direktori Alumni' ?></a></li>
                        <li><a href="<?= base_url('alumni/register') ?>" class="flex items-center gap-3 px-5 py-2.5 text-sm text-slate hover:bg-ivory hover:text-navy transition" role="menuitem"><i class="fas fa-user-plus text-gold-muted w-4"></i><?= $EN ? 'Registration' : 'Pendaftaran' ?></a></li>
                        <li class="px-5 pt-3 pb-2 border-t border-gray-100"><span class="editorial-label text-gold-muted"><?= t('nav_dosen_label') ?></span></li>
                        <li><a href="#" class="flex items-center gap-3 px-5 py-2.5 text-sm text-slate hover:bg-ivory hover:text-navy transition" role="menuitem"><i class="fas fa-chalkboard-teacher text-gold-muted w-4"></i><?= t('nav_elearning') ?></a></li>
                    </ul>
                </li>
            </ul>

            <div class="flex items-center gap-2 shrink-0">
                <!-- 🌙 DARK MODE TOGGLE PUBLIK -->
                <button id="pubDarkToggle" class="hidden md:flex w-10 h-10 items-center justify-center text-navy/70 hover:text-navy border border-gray-200 hover:border-gold transition" aria-label="Toggle dark mode" title="Mode Gelap">
                    <i class="fas fa-moon text-[13px]" id="pubDarkIcon"></i>
                </button>
                <button id="searchToggle" class="hidden md:flex w-10 h-10 items-center justify-center text-navy/70 hover:text-navy border border-gray-200 hover:border-gold transition" aria-label="<?= $EN ? 'Open search' : 'Buka pencarian' ?>" title="Search (Ctrl+K)"><i class="fas fa-search text-[13px]"></i></button>
                <button id="mobile-menu-btn" class="lg:hidden w-10 h-10 flex items-center justify-center text-navy border border-gray-200" aria-label="Toggle mobile menu" aria-expanded="false"><i class="fas fa-bars text-sm"></i></button>
            </div>
        </div>

        <div id="searchBar" class="hidden pb-6">
            <form action="<?= base_url('search') ?>" method="GET" class="relative">
                <input type="text" name="q" placeholder="<?= t('search_placeholder') ?>" class="w-full px-6 py-4 border-2 border-navy text-navy placeholder:text-slate font-serif text-lg focus:border-gold outline-none transition bg-white" autocomplete="off" aria-label="Search">
                <button type="submit" class="absolute right-0 top-0 bottom-0 w-16 bg-navy text-ivory hover:bg-gold hover:text-navy transition" aria-label="Submit search"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <ul id="mobile-menu" class="lg:hidden space-y-1 border-t border-gray-200 pt-4" role="navigation" aria-label="Mobile menu">
            <li><a href="<?= base_url() ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_home') ?></a></li>
            <li><a href="<?= base_url('profil') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_profil') ?></a></li>
            <li><a href="<?= base_url('akademik') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_akademik') ?></a></li>
            <li><a href="<?= base_url('dosen') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_dosen') ?></a></li>
            <li><a href="<?= base_url('riset') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_riset') ?></a></li>
            <li><a href="<?= base_url('prestasi') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_prestasi') ?></a></li>
            <li><a href="<?= base_url('kemahasiswaan') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_kemahasiswaan') ?></a></li>
            <li><a href="<?= base_url('alumni') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg">Alumni</a></li>
            <li><a href="<?= base_url('berita') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><?= t('nav_berita') ?></a></li>
            <li><a href="<?= base_url('kontak') ?>" class="block py-3 px-4 text-navy hover:bg-ivory font-serif text-lg"><i class="fas fa-envelope text-gold-muted mr-2 text-sm"></i><?= $EN ? 'Contact' : 'Kontak' ?></a></li>
            <li><a href="<?= base_url('pmb') ?>" class="block py-3 px-4 bg-gold text-navy font-bold uppercase tracking-editorial text-sm"><i class="fas fa-user-plus mr-2"></i><?= $EN ? 'Apply Now' : 'Daftar PMB' ?></a></li>
            <li class="pt-4 mt-4 border-t border-gray-200">
                <span class="block px-4 pb-2 editorial-label text-gold-muted"><?= t('nav_quick') ?></span>
                <a href="<?= base_url('download') ?>" class="block py-2 px-4 text-slate hover:bg-ivory"><?= t('nav_download') ?></a>
                <a href="<?= base_url('alumni/register') ?>" class="block py-2 px-4 text-slate hover:bg-ivory"><?= $EN ? 'Alumni Registration' : 'Pendaftaran Alumni' ?></a>
                <a href="<?= base_url('auth/login') ?>" class="block py-2 px-4 text-slate hover:bg-ivory"><?= t('nav_login') ?></a>
            </li>
        </ul>
    </div>
</nav>

<!-- COMMAND PALETTE (Ctrl+K) -->
<div class="cmd-palette" id="cmdPalette" role="dialog" aria-label="Quick search">
    <div class="cmd-palette-box">
        <input type="text" class="cmd-palette-input" id="cmdInput" placeholder="<?= t('search_placeholder') ?>" autocomplete="off" aria-label="Search">
        <div class="cmd-palette-results" id="cmdResults"></div>
        <div class="cmd-palette-hint">
            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded text-[10px]">Ctrl</kbd> + <kbd class="px-2 py-1 bg-white border border-gray-300 rounded text-[10px]">K</kbd> to open · <kbd class="px-2 py-1 bg-white border border-gray-300 rounded text-[10px]">Esc</kbd> to close
        </div>
    </div>
</div>

<!-- BACK TO TOP -->
<button class="back-to-top" id="backToTop" aria-label="<?= $EN ? 'Back to top' : 'Kembali ke atas' ?>" title="<?= $EN ? 'Back to top' : 'Kembali ke atas' ?>">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
(function(){
    // ===== 🌙 MODE GELAP PUBLIK =====
    (function(){
        var btn = document.getElementById('pubDarkToggle');
        var icon = document.getElementById('pubDarkIcon');
        if (!btn || !icon) return;
        function paint(){
            var on = document.documentElement.classList.contains('pub-dark');
            icon.className = on ? 'fas fa-sun text-[13px]' : 'fas fa-moon text-[13px]';
            btn.title = on ? '<?= $EN ? "Light Mode" : "Mode Terang" ?>' : '<?= $EN ? "Dark Mode" : "Mode Gelap" ?>';
        }
        btn.addEventListener('click', function(){
            var on = document.documentElement.classList.toggle('pub-dark');
            localStorage.setItem('pub_dark', on ? '1' : '0');
            paint();
        });
        paint();
    })();

    // ===== MOBILE MENU TOGGLE =====
    var mobileBtn = document.getElementById('mobile-menu-btn');
    var mobileMenu = document.getElementById('mobile-menu');
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', function(){
            var isOpen = mobileMenu.classList.toggle('open');
            mobileBtn.setAttribute('aria-expanded', isOpen);
            mobileBtn.innerHTML = isOpen ? '<i class="fas fa-times text-sm"></i>' : '<i class="fas fa-bars text-sm"></i>';
        });
    }

    // ===== SEARCH BAR TOGGLE =====
    var searchToggle = document.getElementById('searchToggle');
    var searchBar = document.getElementById('searchBar');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', function(){
            var isHidden = searchBar.classList.toggle('hidden');
            if (!isHidden) {
                var input = searchBar.querySelector('input');
                if (input) input.focus();
            }
        });
    }

    // ===== NAVBAR SCROLL EFFECT =====
    var mainNav = document.getElementById('mainNav');
    window.addEventListener('scroll', function(){
        if (window.scrollY > 50) {
            mainNav.classList.add('nav-scrolled');
        } else {
            mainNav.classList.remove('nav-scrolled');
        }
    });

    // ===== COMMAND PALETTE (Ctrl+K) =====
    var cmdPalette = document.getElementById('cmdPalette');
    var cmdInput = document.getElementById('cmdInput');
    var cmdResults = document.getElementById('cmdResults');
    var cmdItems = [
        { title: '<?= $EN ? "Home" : "Beranda" ?>', url: '<?= base_url() ?>', icon: 'fa-home' },
        { title: '<?= t('nav_profil') ?>', url: '<?= base_url('profil') ?>', icon: 'fa-building' },
        { title: '<?= t('nav_prodi') ?>', url: '<?= base_url('akademik') ?>', icon: 'fa-graduation-cap' },
        { title: '<?= t('nav_dosen') ?>', url: '<?= base_url('dosen') ?>', icon: 'fa-chalkboard-teacher' },
        { title: '<?= t('nav_riset') ?>', url: '<?= base_url('riset') ?>', icon: 'fa-flask' },
        { title: '<?= t('nav_prestasi') ?>', url: '<?= base_url('prestasi') ?>', icon: 'fa-trophy' },
        { title: '<?= t('nav_kemahasiswaan') ?>', url: '<?= base_url('kemahasiswaan') ?>', icon: 'fa-users' },
        { title: 'Alumni', url: '<?= base_url('alumni') ?>', icon: 'fa-user-graduate' },
        { title: '<?= t('nav_berita') ?>', url: '<?= base_url('berita') ?>', icon: 'fa-newspaper' },
        { title: '<?= $EN ? 'Contact' : 'Kontak' ?>', url: '<?= base_url('kontak') ?>', icon: 'fa-envelope' },
        { title: '<?= $EN ? 'Apply (PMB)' : 'Daftar PMB' ?>', url: '<?= base_url('pmb') ?>', icon: 'fa-user-plus' },
        { title: '<?= t('nav_download') ?>', url: '<?= base_url('download') ?>', icon: 'fa-download' },
    ];

    function renderCmdResults(filter) {
        filter = (filter || '').toLowerCase();
        var filtered = cmdItems.filter(function(item) {
            return item.title.toLowerCase().indexOf(filter) > -1;
        });
        cmdResults.innerHTML = filtered.map(function(item, idx) {
            return '<a href="' + item.url + '" class="cmd-palette-item" data-idx="' + idx + '"><i class="fas ' + item.icon + '"></i><span>' + item.title + '</span></a>';
        }).join('');
    }

    function openCmdPalette() {
        cmdPalette.classList.add('open');
        cmdInput.value = '';
        renderCmdResults('');
        setTimeout(function(){ cmdInput.focus(); }, 100);
    }

    function closeCmdPalette() {
        cmdPalette.classList.remove('open');
    }

    document.addEventListener('keydown', function(e){
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openCmdPalette();
        }
        if (e.key === 'Escape' && cmdPalette.classList.contains('open')) {
            closeCmdPalette();
        }
    });

    cmdInput.addEventListener('input', function(){
        renderCmdResults(this.value);
    });

    cmdPalette.addEventListener('click', function(e){
        if (e.target === cmdPalette) closeCmdPalette();
    });

    renderCmdResults('');

    // ===== BACK TO TOP =====
    var backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', function(){
        if (window.scrollY > 500) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });

    backToTop.addEventListener('click', function(){
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ===== KEYBOARD NAVIGATION FOR DROPDOWNS =====
    document.querySelectorAll('.dropdown').forEach(function(dropdown) {
        var trigger = dropdown.querySelector('a[aria-haspopup]');
        var menu = dropdown.querySelector('[role="menu"]');
        if (!trigger || !menu) return;

        trigger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                trigger.click();
            }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                var firstItem = menu.querySelector('[role="menuitem"]');
                if (firstItem) firstItem.focus();
            }
        });

        menu.addEventListener('keydown', function(e) {
            var items = Array.from(menu.querySelectorAll('[role="menuitem"]'));
            var idx = items.indexOf(document.activeElement);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                var next = items[(idx + 1) % items.length];
                if (next) next.focus();
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                var prev = items[(idx - 1 + items.length) % items.length];
                if (prev) prev.focus();
            }
            if (e.key === 'Escape') {
                trigger.focus();
            }
        });
    });
})();
</script>

<script>
(function(){
    function bindSearchToggle(){
        var t = document.getElementById('searchToggle');
        var bar = document.getElementById('searchBar');
        if (!t || !bar || t.dataset.bound) return;   // cegah double-bind
        t.dataset.bound = '1';

        t.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            bar.classList.toggle('hidden');
            if (!bar.classList.contains('hidden')) {
                var inp = bar.querySelector('input[name="q"]');
                if (inp) setTimeout(function(){ inp.focus(); }, 60);
            }
        });

        // Tutup kalau klik di luar
        document.addEventListener('click', function(e){
            if (!bar.classList.contains('hidden') && !bar.contains(e.target) && !t.contains(e.target)) {
                bar.classList.add('hidden');
            }
        });

        // Tutup dengan ESC
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && !bar.classList.contains('hidden')) bar.classList.add('hidden');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindSearchToggle);
    } else {
        bindSearchToggle();
    }
})();
</script>

<main id="main-content">