<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracer extends CI_Controller {

    private $keys = ['tracer_title', 'tracer_description', 'tracer_form_url', 'tracer_is_active'];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model(['Setting_model', 'Auth_model']);
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        // Statistik alumni
        $alumni_total = 0; $alumni_approved = 0; $alumni_pending = 0;
        if ($this->db->table_exists('alumni')) {
            $alumni_total = $this->db->count_all('alumni');
            $alumni_approved = $this->db->where('status', 'approved')->count_all_results('alumni');
            $alumni_pending = $this->db->where('status', 'pending')->count_all_results('alumni');
        }

        $data = [
            'title' => 'Tracer Study', 'active_menu' => 'tracer',
            's' => [
                'title'       => $this->Setting_model->get('tracer_title', 'Tracer Study Alumni'),
                'description' => $this->Setting_model->get('tracer_description', ''),
                'form_url'    => $this->Setting_model->get('tracer_form_url', ''),
                'is_active'   => $this->Setting_model->get('tracer_is_active', '0'),
            ],
            'alumni_total' => $alumni_total,
            'alumni_count' => $alumni_approved,
            'alumni_pending' => $alumni_pending,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/tracer/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tracer_title', 'Judul', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('tracer_description', 'Deskripsi', 'trim|max_length[2000]');
        $this->form_validation->set_rules('tracer_form_url', 'URL Formulir', 'trim|valid_url|max_length[500]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors('<p>• ', '</p>'));
            redirect('admin/tracer'); return;
        }

        $this->Setting_model->set('tracer_title', $this->input->post('tracer_title', TRUE));
        $this->Setting_model->set('tracer_description', $this->input->post('tracer_description', TRUE));
        $this->Setting_model->set('tracer_form_url', trim($this->input->post('tracer_form_url', TRUE)));
        $this->Setting_model->set('tracer_is_active', $this->input->post('tracer_is_active') ? '1' : '0');

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_tracer', 'Perbarui pengaturan Tracer Study');
        $this->session->set_flashdata('success', 'Pengaturan Tracer Study berhasil disimpan!');
        redirect('admin/tracer');
    }

    // ===== 🔥 FITUR GILA: AJAX test URL =====
    public function test_url() {
        if (!$this->input->is_ajax_request()) show_404();
        $url = trim($this->input->post('url'));
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false, 'message' => 'URL tidak valid']));
            return;
        }
        $status = $this->_check_url_status($url);
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($status));
    }

    private function _check_url_status($url) {
        if (!function_exists('curl_init')) {
            return ['ok' => true, 'message' => 'cURL tidak tersedia, skip validasi', 'code' => 0];
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_NOBODY => true,
            CURLOPT_USERAGENT => 'FacultyBot/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($err) return ['ok' => false, 'message' => 'Tidak dapat terhubung: ' . $err, 'code' => 0];
        if ($code >= 200 && $code < 400) return ['ok' => true, 'message' => 'URL dapat diakses', 'code' => $code];
        return ['ok' => false, 'message' => 'HTTP ' . $code, 'code' => $code];
    }

    // ===== 🔥 FITUR GILA: Preview tracer di frontend =====
    public function preview() {
        redirect(base_url('kemahasiswaan'));
    }

    // ===== 🔥 FITUR GILA: Export alumni list (untuk dikirim ke Google Form) =====
    public function export_alumni() {
        if (!$this->db->table_exists('alumni')) {
            $this->session->set_flashdata('error', 'Tabel alumni tidak tersedia.');
            redirect('admin/tracer'); return;
        }

        $alumni = $this->db->select('alumni.*, study_programs.name as prodi_name')
            ->join('study_programs', 'study_programs.id = alumni.study_program_id', 'left')
            ->where('alumni.status', 'approved')
            ->order_by('alumni.graduation_year', 'DESC')
            ->get('alumni')->result();

        $filename = 'alumni-tracer-' . date('Y-m-d') . '.csv';
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['NIM', 'Nama', 'Email', 'No. HP', 'Prodi', 'Tahun Lulus', 'Pekerjaan', 'Perusahaan']);
        foreach ($alumni as $a) {
            fputcsv($output, [
                $a->nim ?? '-',
                $a->full_name,
                $a->email ?? '-',
                $a->phone ?? '-',
                $a->prodi_name ?? '-',
                $a->graduation_year ?? '-',
                $a->current_position ?? '-',
                $a->company ?? '-',
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        force_download($filename, $csv);
    }

    // ===== 🔥 FITUR GILA: Generate reminder message (untuk WhatsApp/Email) =====
    public function reminder_message() {
        if (!$this->input->is_ajax_request()) show_404();
        $name = $this->input->post('name');
        $url = $this->Setting_model->get('tracer_form_url', '');
        $site_name = $this->Setting_model->get('site_name', 'Fakultas');

        $msg = "Halo {$name},\n\n" .
               "Kami dari {$site_name} mengundang Anda untuk mengisi Tracer Study alumni.\n" .
               "Partisipasi Anda sangat berharga untuk pengembangan almamater.\n\n" .
               "Link formulir: {$url}\n\n" .
               "Terima kasih atas kontribusi Anda! 🎓";

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['message' => $msg, 'wa_link' => 'https://wa.me/?text=' . urlencode($msg)]));
    }
}