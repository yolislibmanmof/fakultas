<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Setting_model');
    }

    public function index() {
        $data = [
            'title' => 'Analytics Dashboard',
            'active_menu' => 'analytics',
            'ga_id' => $this->Setting_model->get('ga_measurement_id', ''),
            'ga_dashboard' => $this->Setting_model->get('ga_dashboard_url', ''),
            'stats' => $this->_quick_stats(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/analytics/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ga_measurement_id', 'Measurement ID', 'trim|regex_match[/^(G-[A-Z0-9]+|UA-\d+-\d+|AW-\d+)?$/]');
        $this->form_validation->set_rules('ga_dashboard_url', 'Dashboard URL', 'trim|valid_url');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors('<p>• ', '</p>'));
            redirect('admin/analytics'); return;
        }

        $this->Setting_model->set('ga_measurement_id', $this->input->post('ga_measurement_id', TRUE));
        $this->Setting_model->set('ga_dashboard_url', $this->input->post('ga_dashboard_url', TRUE));
        $this->session->set_flashdata('success', 'Pengaturan Analytics berhasil disimpan!');
        redirect('admin/analytics');
    }

    // ===== 🔥 FITUR GILA: Quick Stats untuk dashboard analytics =====
    private function _quick_stats() {
        $tables = ['posts', 'documents', 'lecturers', 'alumni', 'achievements'];
        $stats = [];
        foreach ($tables as $t) {
            if ($this->db->table_exists($t)) {
                $stats[$t] = $this->db->count_all_results($t);
            } else {
                $stats[$t] = 0;
            }
        }
        return $stats;
    }

    // ===== 🔥 FITUR GILA: AJAX endpoint untuk test GA connection =====
    public function test_ga() {
        $ga_id = $this->Setting_model->get('ga_measurement_id', '');
        $response = [
            'status' => 'error',
            'message' => 'Measurement ID belum dikonfigurasi.',
            'ga_id' => $ga_id,
        ];

        if ($ga_id && preg_match('/^(G-[A-Z0-9]+|UA-\d+-\d+|AW-\d+)$/', $ga_id)) {
            $response['status'] = 'ok';
            $response['message'] = 'Measurement ID valid & tersimpan.';
            $response['format'] = strpos($ga_id, 'G-') === 0 ? 'GA4' : (strpos($ga_id, 'UA-') === 0 ? 'Universal Analytics' : 'Google Ads');
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // ===== 🔥 FITUR GILA: AJAX endpoint untuk live stats =====
    public function live_stats() {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->_quick_stats()));
    }
}