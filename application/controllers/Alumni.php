<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alumni extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'text', 'site_lang']);
    }

    // ===== DIREKTORI ALUMNI (UPGRADE: peta + chart + hall of fame) =====
    public function index() {
        $q     = trim((string)$this->input->get('q'));
        $year  = $this->input->get('year');
        $prodi = $this->input->get('prodi');

        $this->db->select('alumni.*, study_programs.name as prodi_name')
                 ->join('study_programs', 'study_programs.id = alumni.study_program_id', 'left')
                 ->where('alumni.status', 'approved');
        if ($year)  $this->db->where('alumni.graduation_year', $year);
        if ($prodi) $this->db->where('alumni.study_program_id', $prodi);
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('alumni.full_name', $q);
            $this->db->or_like('alumni.company', $q);
            $this->db->or_like('alumni.current_position', $q);
            $this->db->group_end();
        }

        $data['alumni'] = $this->db->order_by('alumni.graduation_year', 'DESC')
                                   ->order_by('alumni.full_name', 'ASC')
                                   ->get('alumni')->result();

        $data['q'] = $q; $data['year'] = $year; $data['prodi'] = $prodi;
        $data['programs'] = $this->db->order_by('name', 'ASC')->get('study_programs')->result();
        $data['years'] = $this->db->select('graduation_year')->distinct()
                                   ->where('status', 'approved')
                                   ->order_by('graduation_year', 'DESC')
                                   ->get('alumni')->result();

        // ===== UPGRADE 1: AGREGASI PETA DUNIA =====
        $map_raw = $this->db
            ->select('country, city, COUNT(*) as count', FALSE)
            ->where('status', 'approved')
            ->where('country IS NOT NULL AND country != ""')
            ->group_by('country, city')
            ->get('alumni')->result();

        $map_data = [];
        foreach ($map_raw as $r) {
            $c = $r->country;
            if (!isset($map_data[$c])) {
                $map_data[$c] = ['country' => $c, 'count' => 0, 'cities' => []];
            }
            $map_data[$c]['count'] += (int)$r->count;
            if (!empty($r->city)) {
                $map_data[$c]['cities'][$r->city] = ($map_data[$c]['cities'][$r->city] ?? 0) + (int)$r->count;
            }
        }
        usort($map_data, function($a, $b){ return $b['count'] - $a['count']; });
        $data['map_data'] = array_slice($map_data, 0, 15); // top 15 negara

        // ===== UPGRADE 2: CHART ANGKATAN =====
        $year_raw = $this->db
            ->select('graduation_year, COUNT(*) as count', FALSE)
            ->where('status', 'approved')
            ->group_by('graduation_year')
            ->order_by('graduation_year', 'ASC')
            ->get('alumni')->result();
        $data['year_chart'] = $year_raw;

        // ===== UPGRADE 3: TOP COMPANIES =====
        $data['top_companies'] = $this->db
            ->select('company, COUNT(*) as count', FALSE)
            ->where('status', 'approved')
            ->where('company IS NOT NULL AND company != ""')
            ->group_by('company')
            ->order_by('count', 'DESC')
            ->limit(8)
            ->get('alumni')->result();

        // ===== UPGRADE 4: TOP INDUSTRIES =====
        $data['top_industries'] = $this->db
            ->select('industry, COUNT(*) as count', FALSE)
            ->where('status', 'approved')
            ->where('industry IS NOT NULL AND industry != ""')
            ->group_by('industry')
            ->order_by('count', 'DESC')
            ->limit(6)
            ->get('alumni')->result();

        // ===== UPGRADE 5: HALL OF FAME (alumni dengan prestasi + foto) =====
        $data['hall_of_fame'] = $this->db
            ->select('alumni.*, study_programs.name as prodi_name')
            ->join('study_programs', 'study_programs.id = alumni.study_program_id', 'left')
            ->where('alumni.status', 'approved')
            ->where('alumni.achievements IS NOT NULL AND alumni.achievements != ""')
            ->order_by('alumni.graduation_year', 'DESC')
            ->limit(4)
            ->get('alumni')->result();

        // ===== STATS DASAR =====
        $rows = $this->db->select('country, industry')->where('status', 'approved')->get('alumni')->result();
        $countries = array_unique(array_filter(array_map(function ($r) { return $r->country; }, $rows)));
        $industries = array_unique(array_filter(array_map(function ($r) { return $r->industry; }, $rows)));
        $data['stats'] = [
            'total' => count($rows),
            'countries' => count($countries),
            'industries' => count($industries),
        ];

        $data['title'] = 'Alumni — Fakultas Teknik & Ilmu Komputer';
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/alumni_index', $data);
        $this->load->view('templates/footer');
    }

    // ===== PROFIL DETAIL ALUMNI (unchanged) =====
    public function view($id = NULL) {
        $alumnus = $this->db->select('alumni.*, study_programs.name as prodi_name')
                            ->join('study_programs', 'study_programs.id = alumni.study_program_id', 'left')
                            ->where('alumni.id', $id)
                            ->where('alumni.status', 'approved')
                            ->get('alumni')->row();
        if (!$alumnus) show_404();

        $data['a'] = $alumnus;
        $data['achievement_lines'] = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string)$alumnus->achievements)), 'strlen'));
        $data['title'] = $alumnus->full_name . ' — Alumni';

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/alumni_detail', $data);
        $this->load->view('templates/footer');
    }

    // ===== FORM PENDAFTARAN (unchanged) =====
    public function register() {
        $data['programs'] = $this->db->order_by('name', 'ASC')->get('study_programs')->result();
        $data['title'] = 'Bergabung Direktori Alumni — Fakultas';
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/alumni_register', $data);
        $this->load->view('templates/footer');
    }

    // ===== PROSES PENDAFTARAN (unchanged) =====
    public function submit() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[alumni.email]');
        $this->form_validation->set_rules('study_program_id', 'Program Studi', 'required|integer');
        $this->form_validation->set_rules('graduation_year', 'Tahun Lulus', 'required|integer');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('register_errors', validation_errors('<p class="mb-1">• ', '</p>'));
            redirect('alumni/register');
            return;
        }

        $this->db->insert('alumni', [
            'full_name'        => $this->input->post('full_name', TRUE),
            'nim'              => $this->input->post('nim', TRUE),
            'study_program_id' => $this->input->post('study_program_id'),
            'graduation_year'  => $this->input->post('graduation_year'),
            'email'            => $this->input->post('email', TRUE),
            'phone'            => $this->input->post('phone', TRUE),
            'password_hash'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'current_position' => $this->input->post('current_position', TRUE),
            'company'          => $this->input->post('company', TRUE),
            'industry'         => $this->input->post('industry', TRUE),
            'city'             => $this->input->post('city', TRUE),
            'country'          => $this->input->post('country', TRUE) ?: 'Indonesia',
            'linkedin_url'     => $this->input->post('linkedin_url', TRUE),
            'website_url'      => $this->input->post('website_url', TRUE),
            'bio'              => $this->input->post('bio'),
            'achievements'     => $this->input->post('achievements'),
            'status'           => 'pending',
        ]);

        $this->session->set_flashdata('register_success', '1');
        redirect('alumni/register');
    }
}