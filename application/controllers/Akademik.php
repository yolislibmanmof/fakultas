<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akademik extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Study_program_model', 'Course_model']);
    }

    // ==================== INDEX ====================
    public function index() {
        $data['title'] = 'Program Studi - ' . site_name();
        $data['meta_description'] = 'Daftar program studi terakreditasi di ' . site_name() . '. Pilih jalur akademik Anda.';
        $data['breadcrumb'] = [
            ['label' => 'Home', 'url' => base_url()],
            ['label' => 'Akademik', 'url' => base_url('akademik')],
            ['label' => 'Program Studi', 'url' => ''],
        ];
        
        $data['programs'] = $this->Study_program_model->get_active();
        
        // 🔥 FIX: Batch query untuk hindari N+1
        if (!empty($data['programs'])) {
            $prodi_ids = array_column($data['programs'], 'id');
            
            // Hitung courses & SKS per prodi dalam 1 query
            $stats = $this->_get_prodi_stats($prodi_ids);
            
            foreach ($data['programs'] as $p) {
                $p->total_courses = $stats[$p->id]['courses'] ?? 0;
                $p->total_sks = $stats[$p->id]['sks'] ?? 0;
            }
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/akademik_list', $data);
        $this->load->view('templates/footer');
    }

    // ==================== DETAIL ====================
    public function detail($slug = NULL) {
        $program = $this->Study_program_model->get_by_slug($slug);
        if (!$program) show_404();

        // 🔥 View counter (increment di background)
        $this->_increment_view($program->id);

        $courses = $this->Course_model->get_by_prodi($program->id);
        $grouped = [];
        foreach ($courses as $c) {
            $grouped[$c->semester][] = $c;
        }

        // ===== 🔥 FIX: DOSEN PENGAMPU (defensive) =====
        $lecturers = [];
        if ($this->db->table_exists('course_lecturers') && $this->db->table_exists('lecturers')) {
            $cl_fields = $this->db->list_fields('course_lecturers');
            $lc = in_array('lecturer_id', $cl_fields) ? 'lecturer_id' : 'lecturers_id';
            $cc = in_array('course_id', $cl_fields) ? 'course_id' : 'courses_id';
            
            // Ambil course IDs untuk prodi ini
            $course_ids = array_column($courses, 'id');
            
            if (!empty($course_ids)) {
                $lecturers = $this->db
                    ->select('lecturers.*')
                    ->from('lecturers')
                    ->join('course_lecturers', 'course_lecturers.' . $lc . ' = lecturers.id')
                    ->where_in('course_lecturers.' . $cc, $course_ids)
                    ->where('lecturers.is_active', 1)
                    ->group_by('lecturers.id')
                    ->limit(6)
                    ->get('lecturers')->result();
            }
        }

        // ===== 🔥 FIX: LAB TERKAIT (tanpa kolom 'category') =====
        $labs = [];
        if ($this->db->table_exists('facilities')) {
            $fac_fields = $this->db->list_fields('facilities');
            
            $this->db->where('is_active', 1);
            $this->db->group_start();
            $this->db->where('type', 'lab');
            $this->db->or_where('type', 'laboratory');
            if (in_array('category', $fac_fields)) {
                $this->db->or_like('category', 'lab');
            }
            $this->db->group_end();
            $labs = $this->db->limit(4)->get('facilities')->result();
            
            // Parse photo dari JSON images jika photo kosong
            foreach ($labs as $lab) {
                if (empty($lab->photo) && !empty($lab->images)) {
                    $imgs = json_decode($lab->images, true);
                    if (is_array($imgs) && !empty($imgs)) {
                        $lab->photo = $imgs[0];
                    }
                }
            }
        }

        // ===== 🔥 FIX: PROSPEK KARIR (prioritas dedicated column) =====
        $career_prospects = [];
        $sp_fields = $this->db->list_fields('study_programs');
        
        // Priority 1: dedicated column
        if (in_array('career_prospects', $sp_fields) && !empty($program->career_prospects)) {
            $decoded = json_decode($program->career_prospects, true);
            if (is_array($decoded) && !empty($decoded)) {
                $career_prospects = $decoded;
            }
        }
        
        // Priority 2: parse dari description (fallback)
        if (empty($career_prospects) && !empty($program->description)) {
            if (preg_match_all('/(?:^|\n)\s*[-*•]\s*(.+?)(?=\n|$)/', $program->description, $matches)) {
                $career_prospects = array_slice($matches[1], 0, 6);
            }
        }
        
        // Priority 3: fallback careers
        if (empty($career_prospects)) {
            $fallback_careers = [
                'Software Engineer', 'Data Analyst', 'System Architect',
                'IT Consultant', 'Project Manager', 'Research Analyst'
            ];
            $career_prospects = array_slice($fallback_careers, 0, 4);
        }

        // ===== 🔥 FIX: ALUMNI COUNT (defensive) =====
        $alumni_count = 0;
        if ($this->db->table_exists('alumni')) {
            $alumni_fields = $this->db->list_fields('alumni');
            if (in_array('study_program_id', $alumni_fields)) {
                $alumni_count = $this->db
                    ->where('study_program_id', $program->id)
                    ->where('status', 'approved')
                    ->count_all_results('alumni');
            }
        }

        // ===== 🔥 RELATED PROGRAMS =====
        $related_programs = $this->Study_program_model->get_active();
        $related_programs = array_filter($related_programs, function($p) use ($program) {
            return $p->id != $program->id;
        });
        // Prioritaskan same degree
        usort($related_programs, function($a, $b) use ($program) {
            $a_match = ($a->degree === $program->degree) ? 0 : 1;
            $b_match = ($b->degree === $program->degree) ? 0 : 1;
            return $a_match - $b_match;
        });
        $related_programs = array_slice($related_programs, 0, 3);

        $data = [
            'title' => $program->name . ' - ' . site_name(),
            'meta_description' => character_limiter(strip_tags($program->description ?? ''), 160),
            'breadcrumb' => [
                ['label' => 'Home', 'url' => base_url()],
                ['label' => 'Akademik', 'url' => base_url('akademik')],
                ['label' => 'Program Studi', 'url' => base_url('akademik')],
                ['label' => $program->name, 'url' => ''],
            ],
            'program' => $program,
            'curriculum' => $grouped,
            'total_sks' => array_sum(array_map(function ($c) { return $c->sks; }, $courses)),
            'lecturers' => $lecturers,
            'labs' => $labs,
            'career_prospects' => $career_prospects,
            'alumni_count' => $alumni_count,
            'related_programs' => $related_programs,
            'programs' => $this->Study_program_model->get_active(), // untuk view
        ];
        
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/akademik_detail', $data);
        $this->load->view('templates/footer');
    }

    // ==================== KURIKULUM ====================
    public function kurikulum() {
        $data['title'] = 'Kurikulum - ' . site_name();
        $data['meta_description'] = 'Kurikulum lengkap semua program studi di ' . site_name() . '.';
        $data['breadcrumb'] = [
            ['label' => 'Home', 'url' => base_url()],
            ['label' => 'Akademik', 'url' => base_url('akademik')],
            ['label' => 'Kurikulum', 'url' => ''],
        ];
        
        $data['programs'] = $this->Study_program_model->get_active();
        $grand_total_sks = 0;
        $grand_total_courses = 0;
        
        // 🔥 FIX: Batch query
        if (!empty($data['programs'])) {
            $prodi_ids = array_column($data['programs'], 'id');
            $stats = $this->_get_prodi_stats($prodi_ids);
            
            foreach ($data['programs'] as $p) {
                $courses = $this->Course_model->get_by_prodi($p->id);
                $grouped = [];
                foreach ($courses as $c) $grouped[$c->semester][] = $c;
                $p->curriculum = $grouped;
                $p->total_sks = $stats[$p->id]['sks'] ?? 0;
                $p->total_courses = $stats[$p->id]['courses'] ?? 0;
                $grand_total_sks += $p->total_sks;
                $grand_total_courses += $p->total_courses;
            }
        }
        
        $data['grand_sks'] = $grand_total_sks;
        $data['grand_courses'] = $grand_total_courses;
        
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/akademik_kurikulum', $data);
        $this->load->view('templates/footer');
    }

    // ==================== KALENDER ====================
    public function kalender() {
        $data['title'] = 'Kalender Akademik - ' . site_name();
        $data['meta_description'] = 'Kalender akademik ' . site_name() . ' - jadwal kegiatan, ujian, dan libur.';
        $data['breadcrumb'] = [
            ['label' => 'Home', 'url' => base_url()],
            ['label' => 'Akademik', 'url' => base_url('akademik')],
            ['label' => 'Kalender Akademik', 'url' => ''],
        ];
        
        // 🔥 FIX: Defensive table check
        $data['events'] = [];
        if ($this->db->table_exists('academic_calendar')) {
            $data['events'] = $this->db
                ->where('is_active', 1)
                ->order_by('start_date', 'ASC')
                ->get('academic_calendar')
                ->result();
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/akademik_kalender', $data);
        $this->load->view('templates/footer');
    }

    // ==================== AKREDITASI ====================
    public function akreditasi() {
        $data['title'] = 'Akreditasi - ' . site_name();
        $data['meta_description'] = 'Status akreditasi program studi di ' . site_name() . ' oleh BAN-PT.';
        $data['breadcrumb'] = [
            ['label' => 'Home', 'url' => base_url()],
            ['label' => 'Akademik', 'url' => base_url('akademik')],
            ['label' => 'Akreditasi', 'url' => ''],
        ];
        
        // 🔥 FIX: Pakai get_active() bukan get_all()
        $data['programs'] = $this->Study_program_model->get_active();
        
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/akademik_akreditasi', $data);
        $this->load->view('templates/footer');
    }

    // ==================== 🔥 EXPORT ICAL ====================
    public function export_ical() {
        if (!$this->db->table_exists('academic_calendar')) {
            show_404();
        }
        
        $events = $this->db->where('is_active', 1)->get('academic_calendar')->result();
        
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//" . site_name() . "//Academic Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:" . site_name() . " Academic Calendar\r\n";
        $ical .= "X-WR-TIMEZONE:Asia/Jakarta\r\n";
        
        foreach ($events as $e) {
            $start = date('Ymd', strtotime($e->start_date));
            $end = date('Ymd', strtotime($e->end_date ?? $e->start_date));
            $end_dt = date('Ymd', strtotime($end . ' +1 day')); // iCal end is exclusive
            
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $e->id . "@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $start . "\r\n";
            $ical .= "DTEND;VALUE=DATE:" . $end_dt . "\r\n";
            $ical .= "SUMMARY:" . $this->_ical_escape($e->event_name) . "\r\n";
            if (!empty($e->description)) {
                $ical .= "DESCRIPTION:" . $this->_ical_escape($e->description) . "\r\n";
            }
            $ical .= "CATEGORIES:" . strtoupper($e->category) . "\r\n";
            $ical .= "END:VEVENT\r\n";
        }
        
        $ical .= "END:VCALENDAR\r\n";
        
        $this->output
            ->set_content_type('text/calendar; charset=utf-8')
            ->set_header('Content-Disposition: attachment; filename="academic-calendar.ics"')
            ->set_output($ical);
    }

    // ==================== PRIVATE HELPERS ====================
    
    /**
     * 🔥 Batch query untuk stats prodi (hindari N+1)
     */
    private function _get_prodi_stats($prodi_ids) {
        $stats = [];
        if (empty($prodi_ids) || !$this->db->table_exists('courses') || !$this->db->table_exists('course_study_program')) {
            return $stats;
        }
        
        $f = $this->db->list_fields('course_study_program');
        $cc = in_array('course_id', $f) ? 'course_id' : 'courses_id';
        $pc = in_array('study_program_id', $f) ? 'study_program_id' : 'program_id';
        
        $rows = $this->db
            ->select("course_study_program.{$pc} as prodi_id, COUNT(courses.id) as course_count, SUM(courses.sks) as total_sks")
            ->from('courses')
            ->join('course_study_program', "course_study_program.{$cc} = courses.id")
            ->where_in("course_study_program.{$pc}", $prodi_ids)
            ->group_by("course_study_program.{$pc}")
            ->get()->result();
        
        foreach ($rows as $r) {
            $stats[$r->prodi_id] = [
                'courses' => (int)$r->course_count,
                'sks' => (int)$r->total_sks,
            ];
        }
        
        return $stats;
    }
    
    /**
     * 🔥 Increment view counter
     */
    private function _increment_view($program_id) {
        // Cek apakah tabel punya kolom views
        if (!$this->db->table_exists('study_programs')) return;
        
        $fields = $this->db->list_fields('study_programs');
        if (!in_array('views', $fields)) return;
        
        // Gunakan session untuk prevent duplicate counts per session
        $view_key = 'viewed_prodi_' . $program_id;
        if (!$this->session->userdata($view_key)) {
            $this->db->set('views', 'views + 1', FALSE)
                     ->where('id', $program_id)
                     ->update('study_programs');
            $this->session->set_userdata($view_key, true);
        }
    }
    
    /**
     * Escape string untuk iCal format
     */
    private function _ical_escape($str) {
        $str = str_replace(['\\', ',', ';', "\n", "\r"], ['\\\\', '\\,', '\\;', '\\n', ''], $str);
        return $str;
    }
}