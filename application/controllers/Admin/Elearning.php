<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elearning extends CI_Controller {

    // Daftar ikon Font Awesome yang valid (whitelist)
    private $allowed_icons = [
        'fa-laptop', 'fa-laptop-code', 'fa-graduation-cap', 'fa-book', 'fa-book-open',
        'fa-chalkboard-teacher', 'fa-users', 'fa-video', 'fa-headset', 'fa-comments',
        'fa-cloud', 'fa-database', 'fa-flask', 'fa-microscope', 'fa-calculator',
        'fa-globe', 'fa-server', 'fa-code', 'fa-desktop', 'fa-tablet-alt',
        'fa-mobile-alt', 'fa-wifi', 'fa-satellite-dish', 'fa-rocket', 'fa-brain',
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        // Defensive: sort_order opsional (fitur reorder aktif hanya jika kolom ada)
        if ($this->db->field_exists('sort_order', 'elearning_links')) {
            $this->db->order_by('sort_order', 'ASC');
        }
        $this->db->order_by('id', 'ASC')->limit(100);

        $data = [
            'title' => 'E-Learning', 'active_menu' => 'elearning',
            'items' => $this->db->get('elearning_links')->result(),
            'allowed_icons' => $this->allowed_icons,
            'has_sort' => $this->db->field_exists('sort_order', 'elearning_links'),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/elearning/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = ['title' => 'Tambah Platform', 'active_menu' => 'elearning', 'item' => NULL, 'allowed_icons' => $this->allowed_icons];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/elearning/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->insert('elearning_links', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_elearning', 'Tambah e-learning: ' . $this->input->post('platform_name', TRUE));
        $this->session->set_flashdata('success', 'Platform e-learning berhasil ditambahkan!');
        redirect('admin/elearning');
    }

    public function edit($id) {
        $item = $this->db->get_where('elearning_links', ['id' => $id])->row();
        if (!$item) redirect('admin/elearning');
        $data = ['title' => 'Edit Platform', 'active_menu' => 'elearning', 'item' => $item, 'allowed_icons' => $this->allowed_icons];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/elearning/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $item = $this->db->get_where('elearning_links', ['id' => $id])->row();
        if (!$item) redirect('admin/elearning');
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->where('id', $id)->update('elearning_links', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_elearning', 'Edit e-learning: ' . $this->input->post('platform_name', TRUE));
        $this->session->set_flashdata('success', 'Platform e-learning berhasil diperbarui!');
        redirect('admin/elearning');
    }

    public function delete($id) {
        $item = $this->db->get_where('elearning_links', ['id' => $id])->row();
        if ($item) {
            $this->db->where('id', $id)->delete('elearning_links');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_elearning', 'Hapus e-learning: ' . $item->platform_name);
            $this->session->set_flashdata('success', 'Platform e-learning berhasil dihapus.');
        }
        redirect('admin/elearning');
    }

    // ===== 🔥 FITUR GILA: AJAX test connection =====
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
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_NOBODY => true, // HEAD request (lebih cepat)
            CURLOPT_USERAGENT => 'FacultyBot/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err) return ['ok' => false, 'message' => 'Tidak dapat terhubung: ' . $err, 'code' => 0];
        if ($code >= 200 && $code < 400) return ['ok' => true, 'message' => 'URL dapat diakses', 'code' => $code];
        return ['ok' => false, 'message' => 'Server merespons HTTP ' . $code, 'code' => $code];
    }

    // ===== 🔥 FITUR GILA: AJAX reorder (drag & drop) =====
    public function reorder() {
        if (!$this->input->is_ajax_request()) show_404();
        $ids = $this->input->post('ids');
        if (!is_array($ids)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false]));
            return;
        }
        // Pastikan kolom sort_order ada (defensive)
        if (!$this->db->field_exists('sort_order', 'elearning_links')) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false, 'message' => 'Kolom sort_order belum ada']));
            return;
        }
        foreach ($ids as $i => $id) {
            $this->db->where('id', (int)$id)->update('elearning_links', ['sort_order' => $i]);
        }
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['ok' => true]));
    }

    // ===== 🔥 FITUR GILA: Duplicate platform =====
    public function duplicate($id) {
        $item = $this->db->get_where('elearning_links', ['id' => $id])->row();
        if (!$item) { show_404(); }
        $new = clone $item;
        unset($new->id);
        $new->platform_name = $item->platform_name . ' (Salinan)';
        $this->db->insert('elearning_links', (array)$new);
        $this->session->set_flashdata('success', 'Platform berhasil diduplikasi.');
        redirect('admin/elearning');
    }

    private function _validate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('platform_name', 'Nama Platform', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('url', 'URL', 'required|trim|valid_url|max_length[500]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[500]');
        $this->form_validation->set_rules('icon', 'Icon', 'trim|max_length[50]|callback__validate_icon');
    }

    public function _validate_icon($icon) {
        if ($icon === '' || $icon === NULL) return TRUE;
        // Hanya izinkan karakter [a-z0-9-] (anti-XSS via icon class)
        if (!preg_match('/^[a-z0-9\-]+$/', $icon)) {
            $this->form_validation->set_message('_validate_icon', 'Format ikon tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    private function _payload() {
        $icon = $this->input->post('icon', TRUE) ?: 'fa-laptop';
        // Whitelist check
        if (!in_array($icon, $this->allowed_icons, true)) {
            $this->session->set_flashdata('error', 'Ikon tidak diizinkan. Pilih dari daftar.');
            redirect('admin/elearning/create');
            return FALSE;
        }
        return [
            'platform_name' => $this->input->post('platform_name', TRUE),
            'url'           => trim($this->input->post('url', TRUE)),
            'description'   => $this->input->post('description', TRUE),
            'icon'          => $icon,
            'is_active'     => $this->input->post('is_active') ? 1 : 0,
        ];
    }
}