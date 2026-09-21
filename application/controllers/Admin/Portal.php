<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {

    private $colors = ['blue', 'navy', 'gold', 'green', 'red', 'purple', 'teal', 'orange', 'pink', 'cyan'];
    private $cats   = ['umum' => 'Umum', 'akademik' => 'Akademik', 'kemahasiswaan' => 'Kemahasiswaan', 'keuangan' => 'Keuangan'];

    // Icon whitelist (anti-XSS)
    private $allowed_icons = [
        'fa-link', 'fa-graduation-cap', 'fa-book', 'fa-calendar-alt', 'fa-file-pdf',
        'fa-users', 'fa-id-card', 'fa-id-badge', 'fa-money-bill', 'fa-credit-card',
        'fa-award', 'fa-trophy', 'fa-clipboard-list', 'fa-clipboard-check',
        'fa-envelope', 'fa-phone', 'fa-map-marker-alt', 'fa-wifi', 'fa-laptop',
        'fa-download', 'fa-upload', 'fa-print', 'fa-search', 'fa-bell',
        'fa-comments', 'fa-question-circle', 'fa-info-circle', 'fa-external-link-alt',
        'fa-university', 'fa-building', 'fa-home', 'fa-user', 'fa-user-graduate',
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        // 🔥 FIX: Defensive — sort_order opsional
        if ($this->db->field_exists('sort_order', 'portal_content')) {
            $this->db->order_by('sort_order', 'ASC');
        }
        $this->db->order_by('id', 'ASC')->limit(100);
        $items = $this->db->get('portal_content')->result();

        $data = [
            'title' => 'Portal Mahasiswa', 'active_menu' => 'portal',
            'items' => $items, 'colors' => $this->colors, 'cats' => $this->cats,
            'allowed_icons' => $this->allowed_icons,
            'has_sort' => $this->db->field_exists('sort_order', 'portal_content'),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/portal/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = ['title' => 'Tambah Layanan Portal', 'active_menu' => 'portal', 'item' => NULL,
                 'colors' => $this->colors, 'cats' => $this->cats, 'allowed_icons' => $this->allowed_icons];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/portal/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->insert('portal_content', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_portal', 'Tambah portal: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Layanan portal berhasil ditambahkan!');
        redirect('admin/portal');
    }

    public function edit($id) {
        $item = $this->db->get_where('portal_content', ['id' => $id])->row();
        if (!$item) redirect('admin/portal');
        $data = ['title' => 'Edit Layanan Portal', 'active_menu' => 'portal', 'item' => $item,
                 'colors' => $this->colors, 'cats' => $this->cats, 'allowed_icons' => $this->allowed_icons];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/portal/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $item = $this->db->get_where('portal_content', ['id' => $id])->row();
        if (!$item) redirect('admin/portal');
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->where('id', $id)->update('portal_content', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_portal', 'Edit portal: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Layanan portal berhasil diperbarui!');
        redirect('admin/portal');
    }

    public function delete($id) {
        $item = $this->db->get_where('portal_content', ['id' => $id])->row();
        if ($item) {
            $this->db->where('id', $id)->delete('portal_content');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_portal', 'Hapus portal: ' . $item->title);
            $this->session->set_flashdata('success', 'Layanan portal berhasil dihapus.');
        }
        redirect('admin/portal');
    }

    // ===== 🔥 FITUR GILA: Duplicate item =====
    public function duplicate($id) {
        $item = $this->db->get_where('portal_content', ['id' => $id])->row();
        if (!$item) { show_404(); }
        $new = clone $item;
        unset($new->id);
        $new->title = $item->title . ' (Salinan)';
        $this->db->insert('portal_content', (array)$new);
        $this->session->set_flashdata('success', 'Layanan berhasil diduplikasi.');
        redirect('admin/portal');
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

    // ===== 🔥 FITUR GILA: AJAX reorder (kalau kolom sort_order ada) =====
    public function reorder() {
        if (!$this->input->is_ajax_request() || !$this->db->field_exists('sort_order', 'portal_content')) show_404();
        $ids = $this->input->post('ids');
        if (!is_array($ids)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => false]));
            return;
        }
        foreach ($ids as $i => $id) {
            $this->db->where('id', (int)$id)->update('portal_content', ['sort_order' => $i]);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true]));
    }

    // ===== 🔥 FITUR GILA: Bulk toggle active =====
    public function bulk_toggle() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');
        if (!is_array($ids) || !in_array($action, ['activate', 'deactivate'])) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/portal'); return;
        }
        $status = ($action === 'activate') ? 1 : 0;
        $this->db->where_in('id', $ids)->update('portal_content', ['is_active' => $status]);
        $count = $this->db->affected_rows();
        $this->session->set_flashdata('success', "{$count} layanan berhasil di-{$action}.");
        redirect('admin/portal');
    }

    private function _validate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Judul', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('link_url', 'URL', 'required|trim|valid_url|max_length[500]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[500]');
        $this->form_validation->set_rules('icon', 'Icon', 'trim|max_length[50]|callback__validate_icon');
        $this->form_validation->set_rules('sort_order', 'Urutan', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[999]');
    }

    public function _validate_icon($icon) {
        if ($icon === '' || $icon === NULL) return TRUE;
        if (!preg_match('/^[a-z0-9\-]+$/', $icon)) {
            $this->form_validation->set_message('_validate_icon', 'Format ikon tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    private function _payload() {
        $color = $this->input->post('color', TRUE);
        $cat   = $this->input->post('category', TRUE);
        $icon  = $this->input->post('icon', TRUE) ?: 'fa-link';

        // Icon whitelist check
        if (!in_array($icon, $this->allowed_icons, true)) {
            $this->session->set_flashdata('error', 'Ikon tidak diizinkan. Pilih dari daftar.');
            redirect('admin/portal/create');
            return FALSE;
        }

        $payload = [
            'title'       => $this->input->post('title', TRUE),
            'description' => $this->input->post('description', TRUE),
            'link_url'    => trim($this->input->post('link_url', TRUE)),
            'icon'        => $icon,
            'color'       => in_array($color, $this->colors) ? $color : 'blue',
            'category'    => array_key_exists($cat, $this->cats) ? $cat : 'umum',
            'is_active'   => $this->input->post('is_active') ? 1 : 0,
        ];

        // sort_order opsional (kalau kolom ada)
        if ($this->db->field_exists('sort_order', 'portal_content')) {
            $payload['sort_order'] = (int)$this->input->post('sort_order') ?: 0;
        }

        return $payload;
    }
}