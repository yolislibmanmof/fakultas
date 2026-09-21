<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Document_model');
    }

    public function index() {
        $data['title'] = 'Download Center - Fakultas';
        $data['q'] = $this->input->get('q', TRUE);
        $data['cat'] = $this->input->get('cat', TRUE);
        $data['documents'] = $this->Document_model->get_active($data['cat'] ?: NULL, $data['q'] ?: NULL);
        $data['categories'] = ['akademik' => 'Akademik', 'kemahasiswaan' => 'Kemahasiswaan', 'keuangan' => 'Keuangan', 'umum' => 'Umum'];
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/download_list', $data);
        $this->load->view('templates/footer');
    }

    public function file($id) {
        $doc = $this->Document_model->get_by_id($id);
        if (!$doc || !$doc->is_active) show_404();
        $path = FCPATH . 'assets/uploads/documents/' . $doc->file_path;
        if (!file_exists($path)) show_404();

        $this->Document_model->increment_download($id);
        $this->load->helper('download');
        force_download($doc->file_name, file_get_contents($path));
    }
}