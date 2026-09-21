<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_site_lang')) {
    function get_site_lang() {
        $CI =& get_instance();
        $lang = $CI->session->userdata('site_lang');
        if (!$lang) {
            $CI->load->helper('cookie');          // load dulu, baru aman dipakai
            $lang = get_cookie('site_lang');
        }
        if (!$lang) $lang = 'id';                 // fallback terakhir
        return ($lang === 'en') ? 'en' : 'id';
    }
}

if (!function_exists('t')) {
    function t($key) {
        static $lang = NULL;
        if ($lang === NULL) $lang = get_site_lang();
        $dict = site_lang_dict();
        if (isset($dict[$key][$lang])) return $dict[$key][$lang];
        if (isset($dict[$key]['id'])) return $dict[$key]['id'];
        return $key;
    }
}

if (!function_exists('site_lang_dict')) {
    function site_lang_dict() {
        return array(
            // ===== TOPBAR =====
            'topbar_follow'  => array('id' => 'Ikuti Kami', 'en' => 'Follow Us'),
            'topbar_portal'  => array('id' => 'Portal', 'en' => 'Portal'),

            // ===== NAV =====
            'nav_home'         => array('id' => 'Beranda', 'en' => 'Home'),
            'nav_profil'       => array('id' => 'Profil', 'en' => 'About'),
            'nav_akademik'     => array('id' => 'Akademik', 'en' => 'Academics'),
            'nav_dosen'        => array('id' => 'Dosen', 'en' => 'Faculty'),
            'nav_riset'        => array('id' => 'Riset', 'en' => 'Research'),
            'nav_fasilitas'    => array('id' => 'Fasilitas', 'en' => 'Facilities'),
            'nav_prestasi'     => array('id' => 'Prestasi', 'en' => 'Achievements'),
            'nav_kemahasiswaan'=> array('id' => 'Kemahasiswaan', 'en' => 'Student Life'),
            'nav_berita'       => array('id' => 'Berita', 'en' => 'News'),
            'nav_sejarah'      => array('id' => 'Sejarah', 'en' => 'History'),
            'nav_visimisi'     => array('id' => 'Visi & Misi', 'en' => 'Vision & Mission'),
            'nav_struktur'     => array('id' => 'Struktur Organisasi', 'en' => 'Organizational Structure'),
            'nav_sambutan'     => array('id' => 'Sambutan Dekan', 'en' => "Dean's Welcome"),
            'nav_prodi'        => array('id' => 'Program Studi', 'en' => 'Study Programs'),
            'nav_kurikulum'    => array('id' => 'Kurikulum', 'en' => 'Curriculum'),
            'nav_kalender'     => array('id' => 'Kalender Akademik', 'en' => 'Academic Calendar'),
            'nav_akreditasi'   => array('id' => 'Akreditasi', 'en' => 'Accreditation'),
            'nav_mahasiswa'    => array('id' => 'Untuk Mahasiswa', 'en' => 'For Students'),
            'nav_dosen_label'  => array('id' => 'Untuk Dosen', 'en' => 'For Faculty'),
            'nav_download'     => array('id' => 'Download Center', 'en' => 'Download Center'),
            'nav_beasiswa'     => array('id' => 'Beasiswa', 'en' => 'Scholarships'),
            'nav_elearning'    => array('id' => 'E-Learning', 'en' => 'E-Learning'),
            'nav_quick'        => array('id' => 'Akses Cepat', 'en' => 'Quick Access'),
            'nav_login'        => array('id' => 'Portal Login', 'en' => 'Portal Login'),
            'search_placeholder'=> array('id' => 'Cari berita, dosen, program studi, atau dokumen...', 'en' => 'Search news, faculty, programs, or documents...'),

            // ===== FOOTER =====
            'footer_tagline' => array('id' => 'Mencetak generasi unggul dalam bidang teknologi dan ilmu komputer.', 'en' => 'Shaping outstanding generations in technology and computer science.'),
            'footer_quick'   => array('id' => 'Tautan Cepat', 'en' => 'Quick Links'),
            'footer_contact' => array('id' => 'Kontak', 'en' => 'Contact'),
            'footer_follow'  => array('id' => 'Ikuti Kami', 'en' => 'Follow Us'),
            'footer_hours'   => array('id' => 'Senin - Jumat: 08.00 - 16.00 WIB', 'en' => 'Monday - Friday: 08.00 - 16.00 (WIB)'),
            'footer_rights'  => array('id' => 'Hak cipta dilindungi undang-undang.', 'en' => 'All rights reserved.'),

            // ===== HOME =====
            'btn_explore' => array('id' => 'Jelajahi Program Studi', 'en' => 'Explore Programs'),
            'btn_news'    => array('id' => 'Berita Terkini', 'en' => 'Latest News'),
            'home_stats_lecturers' => array('id' => 'Dosen Aktif', 'en' => 'Active Faculty'),
            'home_stats_students'  => array('id' => 'Mahasiswa', 'en' => 'Students'),
            'home_stats_programs'  => array('id' => 'Program Studi', 'en' => 'Study Programs'),
            'home_stats_research'  => array('id' => 'Penelitian', 'en' => 'Research'),
            'home_news_label' => array('id' => 'Kabar Terbaru', 'en' => 'Latest Stories'),
            'home_news_title' => array('id' => 'Berita & Pengumuman', 'en' => 'News & Announcements'),
            'home_see_all'    => array('id' => 'Lihat Semua Berita', 'en' => 'View All News'),
            'home_read_more'  => array('id' => 'Baca Selengkapnya', 'en' => 'Read More'),
            'home_prog_label' => array('id' => 'Program Akademik', 'en' => 'Academic Programs'),
            'home_prog_title' => array('id' => 'Program Studi unggulan kami', 'en' => 'Our outstanding study programs'),
            'home_prog_desc'  => array('id' => 'Kurikulum dirancang mengikuti standar internasional, dipandu oleh dosen berkualifikasi doktor dari universitas terkemuka dunia.', 'en' => 'Curriculum designed to international standards, guided by doctoral-qualified faculty from world-leading universities.'),
            'home_prog_all'   => array('id' => 'Semua Program', 'en' => 'All Programs'),
            'home_prog_accred'=> array('id' => 'Akreditasi', 'en' => 'Accreditation'),
            'home_dean_title' => array('id' => 'Dekan Fakultas', 'en' => 'Dean of Faculty'),
            'home_res_label'  => array('id' => 'Riset & Inovasi', 'en' => 'Research & Innovation'),
            'home_res_title'  => array('id' => 'Riset kami membentuk masa depan', 'en' => 'Our research shapes the future'),
            'home_res_1' => array('id' => 'Proyek Riset Aktif', 'en' => 'Active Research Projects'),
            'home_res_1d'=> array('id' => 'Penelitian kolaboratif dengan mitra industri dan universitas internasional di bidang AI, IoT, dan sistem cerdas.', 'en' => 'Collaborative research with industry partners and international universities in AI, IoT, and intelligent systems.'),
            'home_res_2' => array('id' => 'Publikasi Ilmiah', 'en' => 'Scientific Publications'),
            'home_res_2d'=> array('id' => 'Artikel di jurnal bereputasi internasional (Scopus, WoS) dan konferensi tingkat dunia setiap tahunnya.', 'en' => 'Articles in internationally reputable journals (Scopus, WoS) and world-class conferences every year.'),
            'home_res_3' => array('id' => 'Kerjasama Global', 'en' => 'Global Partnerships'),
            'home_res_3d'=> array('id' => 'Mitra universitas dan lembaga riset dari 15+ negara dalam program pertukaran dan riset bersama.', 'en' => 'University and research partners from 15+ countries in exchange and joint research programs.'),
            'home_res_current' => array('id' => 'Riset Terkini', 'en' => 'Current Research'),
            'home_res_all'     => array('id' => 'Jelajahi Semua Riset', 'en' => 'Explore All Research'),
            'home_ach_label' => array('id' => 'Keunggulan Mahasiswa', 'en' => 'Student Excellence'),
            'home_ach_title' => array('id' => 'Prestasi mahasiswa kami', 'en' => 'Our students\' achievements'),
            'home_ach_all'   => array('id' => 'Semua Prestasi', 'en' => 'All Achievements'),
            'home_video_label' => array('id' => 'Tur Kampus Virtual', 'en' => 'Virtual Campus Tour'),
        );
    }
}
if (!function_exists('site_name')) {
    function site_name() {
        $CI =& get_instance();
        $CI->load->model('Setting_model');
        return $CI->Setting_model->get('site_name', 'Fakultas Teknik & Ilmu Komputer');
    }
}