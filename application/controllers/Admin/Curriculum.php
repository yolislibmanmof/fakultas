<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Curriculum extends CI_Controller {

    // Standar beban SKS menurut jenjang (acuan umum Dikti)
    private $standards = [
        'D3' => ['min' => 110, 'max' => 120, 'label' => '110–120 SKS'],
        'D4' => ['min' => 144, 'max' => 160, 'label' => '144–160 SKS'],
        'S1' => ['min' => 144, 'max' => 160, 'label' => '144–160 SKS'],
        'S2' => ['min' => 36,  'max' => 72,  'label' => '36–72 SKS'],
        'S3' => ['min' => 40,  'max' => 72,  'label' => '40–72 SKS'],
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model(['Study_program_model', 'Course_model']);
        $this->load->helper(['url', 'text', 'download']);
    }

    public function index() {
        $programs = $this->Study_program_model->get_all();
        $chart_data = [];
        foreach ($programs as $p) {
            $this->_enrich($p);
            // 🔥 FITUR GILA: Chart data distribusi SKS per semester
            $chart_data[$p->slug] = $this->_build_chart_data($p);
        }

        $data = [
            'title' => 'Dashboard Kurikulum', 'active_menu' => 'curriculum',
            'programs' => $programs,
            'standards' => $this->standards,
            'chart_data' => $chart_data,
            'overall_summary' => $this->_overall_summary($programs),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/curriculum/index', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== Halaman cetak / export PDF (via browser print) =====
    public function cetak($slug = NULL) {
        if (!$slug || strlen($slug) > 100) redirect('admin/curriculum');
        $program = $this->Study_program_model->get_by_slug($slug);
        if (!$program) redirect('admin/curriculum');
        $this->_enrich($program);

        $data = ['program' => $program, 'standards' => $this->standards];
        $this->load->view('admin/curriculum/print', $data);
    }

    // ===== 🔥 FITUR GILA: Export CSV =====
    public function export_csv($slug = NULL) {
        if (!$slug) redirect('admin/curriculum');
        $program = $this->Study_program_model->get_by_slug($slug);
        if (!$program) redirect('admin/curriculum');
        $this->_enrich($program);

        $filename = 'kurikulum-' . $program->slug . '-' . date('Y-m-d') . '.csv';
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Semester', 'Kode MK', 'Nama MK', 'SKS', 'Jenis', 'Prodi']);

        foreach ($program->curriculum as $sem => $courses) {
            foreach ($courses as $c) {
                fputcsv($output, [
                    $sem,
                    $c->course_code,
                    $c->name,
                    $c->sks,
                    $c->course_type ?? 'wajib',
                    $program->name,
                ]);
            }
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        force_download($filename, $csv);
    }

    // ===== 🔥 FITUR GILA: Compare antar prodi =====
    public function compare() {
        $slugs = $this->input->get('slugs');
        if (!$slugs) redirect('admin/curriculum');
        $slugs_arr = array_filter(explode(',', $slugs));
        if (count($slugs_arr) < 2 || count($slugs_arr) > 5) {
            $this->session->set_flashdata('error', 'Pilih 2-5 prodi untuk dibandingkan.');
            redirect('admin/curriculum'); return;
        }

        $programs = [];
        foreach ($slugs_arr as $slug) {
            $p = $this->Study_program_model->get_by_slug(trim($slug));
            if ($p) {
                $this->_enrich($p);
                $programs[] = $p;
            }
        }

        $data = [
            'title' => 'Perbandingan Kurikulum', 'active_menu' => 'curriculum',
            'programs' => $programs, 'standards' => $this->standards,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/curriculum/compare', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== 🔥 FITUR GILA: AJAX refresh data per prodi =====
    public function refresh($slug = NULL) {
        if (!$this->input->is_ajax_request() || !$slug) show_404();
        $program = $this->Study_program_model->get_by_slug($slug);
        if (!$program) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false, 'error' => 'Prodi tidak ditemukan']));
            return;
        }
        $this->_enrich($program);
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'ok' => true,
            'total_sks' => $program->total_sks,
            'total_courses' => $program->total_courses,
            'validation' => $program->validation,
            'chart' => $this->_build_chart_data($program),
        ]));
    }

    // ===== Lengkapi data prodi + validasi =====
    private function _enrich(&$p) {
        $courses = $this->Course_model->get_by_prodi($p->id);
        if (!$courses) $courses = [];

        $grouped = [];
        foreach ($courses as $c) {
            $sem = (int)$c->semester;
            $grouped[$sem][] = $c;
        }
        ksort($grouped);

        $p->curriculum    = $grouped;
        $p->total_sks     = array_sum(array_map(function ($c) { return (int)$c->sks; }, $courses));
        $p->total_courses = count($courses);
        $p->validation    = $this->_validate_sks($p->degree ?? '', $p->total_sks);

        // 🔥 FITUR GILA: Deteksi gap semester + MK tanpa SKS
        $p->warnings = [];
        if (!empty($grouped)) {
            $semesters = array_keys($grouped);
            $min_sem = min($semesters);
            $max_sem = max($semesters);
            for ($s = $min_sem; $s <= $max_sem; $s++) {
                if (!isset($grouped[$s])) {
                    $p->warnings[] = "Semester {$s} kosong (tidak ada MK).";
                }
            }
        }
        $no_sks = array_filter($courses, function ($c) { return empty($c->sks) || (int)$c->sks == 0; });
        if (count($no_sks) > 0) {
            $p->warnings[] = count($no_sks) . " MK tanpa SKS.";
        }
    }

    private function _validate_sks($degree, $sks) {
        $std = $this->standards[$degree] ?? NULL;
        if (!$std) return ['status' => 'unknown', 'label' => 'Standar tidak diketahui', 'range' => '-'];
        if ($sks < $std['min'])  return ['status' => 'low',  'label' => 'Kurang dari standar', 'range' => $std['label']];
        if ($sks > $std['max'])  return ['status' => 'high', 'label' => 'Melebihi standar',    'range' => $std['label']];
        return ['status' => 'ok', 'label' => 'Sesuai standar', 'range' => $std['label']];
    }

    // 🔥 Chart data: SKS per semester (untuk bar chart)
    private function _build_chart_data($p) {
        $data = [];
        if (!empty($p->curriculum)) {
            foreach ($p->curriculum as $sem => $courses) {
                $sks_sum = array_sum(array_map(function ($c) { return (int)$c->sks; }, $courses));
                $data[] = ['semester' => $sem, 'sks' => $sks_sum, 'courses' => count($courses)];
            }
        }
        return $data;
    }

    // 🔥 Overall summary: total SKS semua prodi, rata-rata, dll
    private function _overall_summary($programs) {
        $total_sks = 0; $total_courses = 0; $ok_count = 0;
        foreach ($programs as $p) {
            $total_sks += $p->total_sks ?? 0;
            $total_courses += $p->total_courses ?? 0;
            if (($p->validation['status'] ?? '') === 'ok') $ok_count++;
        }
        $avg_sks = count($programs) > 0 ? round($total_sks / count($programs), 1) : 0;
        return [
            'total_programs' => count($programs),
            'total_sks' => $total_sks,
            'total_courses' => $total_courses,
            'avg_sks_per_program' => $avg_sks,
            'compliant_count' => $ok_count,
        ];
    }
}