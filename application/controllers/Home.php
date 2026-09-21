<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Setting_model');
    }

    public function index() {
        $data['title'] = 'Beranda - Fakultas Teknik & Ilmu Komputer';
        $data['description'] = 'Fakultas Teknik & Ilmu Komputer — pusat keunggulan akademik yang mencetak pemimpin teknologi dan ilmuwan kelas dunia.';

        // 1. Berita Terbaru (4 item)
        $data['latest_news'] = $this->db
            ->select('posts.*, categories.name as category_name, users.full_name as author')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.type', 'news')
            ->where('posts.status', 'published')
            ->order_by('posts.published_at', 'DESC')
            ->limit(4)
            ->get()
            ->result();

        // 2. Program Studi Aktif
        $data['programs'] = $this->db
            ->where('is_active', 1)
            ->order_by('degree', 'DESC')
            ->order_by('name', 'ASC')
            ->get('study_programs')
            ->result();

        // 3. Statistik Dinamis (+Alumni)
        $data['stats'] = [
            'lecturers' => $this->db->where('is_active', 1)->count_all_results('lecturers'),
            'students'  => 1250,
            'programs'  => $this->db->where('is_active', 1)->count_all_results('study_programs'),
            'research'  => $this->db->count_all('research'),
            'alumni'    => $this->db->where('status', 'approved')->count_all_results('alumni'),
        ];

        // 4. Data Dekan
        $data['dean'] = (object) [
            'name'    => $this->Setting_model->get('dean_name', 'Prof. Dr. H. Ahmad Fauzi, M.T.'),
            'message' => $this->Setting_model->get('dean_message', 'Kami tidak hanya mendidik mahasiswa untuk mendapatkan gelar, tetapi membentuk pemimpin yang mampu menciptakan dampak nyata bagi peradaban melalui ilmu pengetahuan dan teknologi.'),
            'photo'   => $this->Setting_model->get('dean_photo', ''),
        ];

        // 5. Data Riset untuk Spotlight
        $data['research_spotlight'] = $this->db
            ->select('research.*, lecturers.name as lecturer_name, lecturers.title_front')
            ->from('research')
            ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left')
            ->order_by('research.year', 'DESC')
            ->limit(3)
            ->get()
            ->result();

        // 6. Prestasi Terbaru
        $data['achievements'] = $this->db
            ->select('achievements.*, study_programs.name as prodi_name')
            ->from('achievements')
            ->join('study_programs', 'study_programs.id = achievements.study_program_id', 'left')
            ->order_by('achievements.year', 'DESC')
            ->limit(3)
            ->get()
            ->result();

        // 7. Video Tour Settings
        $data['video_enabled']  = $this->Setting_model->get('video_tour_enabled', '0') == '1';
        $data['video_type']     = $this->Setting_model->get('video_tour_type', 'youtube');
        $data['video_youtube']  = $this->Setting_model->get('video_tour_youtube', '');
        $data['video_file']     = $this->Setting_model->get('video_tour_file', '');
        $data['video_title']    = $this->Setting_model->get('video_tour_title', 'Explore Our Campus');
        $data['video_subtitle'] = $this->Setting_model->get('video_tour_subtitle', '');

        // 8. Ekstrak YouTube ID
        $data['video_yt_id'] = '';
        if ($data['video_type'] == 'youtube' && $data['video_youtube']) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $data['video_youtube'], $m);
            $data['video_yt_id'] = $m[1] ?? '';
        }

        // 9. 🆕 Featured Lecturers (3 dosen dengan foto)
        $data['featured_lecturers'] = $this->db
            ->where('is_active', 1)
            ->where('photo IS NOT NULL')
            ->where('photo !=', '')
            ->order_by('name', 'ASC')
            ->limit(3)
            ->get('lecturers')
            ->result();

        // 10. 🆕 Featured Alumni (3 alumni terbaru approved)
        $data['featured_alumni'] = $this->db
            ->select('alumni.*, study_programs.name as prodi_name')
            ->join('study_programs', 'study_programs.id = alumni.study_program_id', 'left')
            ->where('alumni.status', 'approved')
            ->order_by('alumni.graduation_year', 'DESC')
            ->limit(3)
            ->get('alumni')
            ->result();

        // 11. 🆕 Blog Dosen Terbaru — TABEL: lecturer_posts (FIXED)
        $data['latest_blogs'] = $this->db
            ->select('lecturer_posts.*, lecturers.name as lecturer_name, lecturers.photo as lecturer_photo')
            ->from('lecturer_posts')
            ->join('lecturers', 'lecturers.id = lecturer_posts.lecturer_id', 'left')
            ->where('lecturer_posts.status', 'published')
            ->order_by('lecturer_posts.published_at', 'DESC')
            ->limit(2)
            ->get()
            ->result();

        // 12. 🆕 Upcoming Events (3 event terdekat)
        $data['upcoming_events'] = $this->db
            ->where('start_date >=', date('Y-m-d'))
            ->where('is_active', 1)
            ->order_by('start_date', 'ASC')
            ->limit(3)
            ->get('academic_calendar')
            ->result();

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/home', $data);
        $this->load->view('templates/footer');
    }
}