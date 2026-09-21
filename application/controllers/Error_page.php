<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_page extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'site_lang']);
    }

    /**
     * 403 Forbidden — Restricted Zone
     * Panggil dari controller mana saja:
     *   redirect('error_page/forbidden');
     * atau:
     *   $this->error_page->forbidden('Pesan khusus');
     */
    public function forbidden($custom_msg = NULL) {
        $data = [
            'title' => '403 - Access Denied',
            'message' => $custom_msg ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.',
        ];
        $this->output->set_status_header(403);
        $this->load->view('errors/html/error_403', $data);
    }

    /**
     * Helper: dipanggil langsung dari controller lain (tanpa redirect)
     * Contoh: $this->load->library('../controllers/Error_page'); 
     * (alternatif: buat helper function)
     */
    public function show_403($msg = NULL) {
        $this->forbidden($msg);
        exit;
    }
}