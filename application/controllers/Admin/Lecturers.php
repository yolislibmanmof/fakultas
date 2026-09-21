<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lecturers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model(['Lecturer_model', 'Auth_model']);
        $this->load->library('upload');
        $this->load->helper(['url', 'text', 'download']);
    }

    public function index() {
        $q = trim((string)$this->input->get('q'));
        $active = $this->input->get('active');

        $this->db->select('*')->from('lecturers');
        if ($q !== '') {
            $this->db->group_start()
                ->like('name', $q)
                ->or_like('nidn', $q)
                ->or_like('email', $q)
                ->or_like('expertise', $q)
                ->group_end();
        }
        if ($active !== '' && $active !== NULL) {
            $this->db->where('is_active', (int)$active);
        }
        $this->db->order_by('name', 'ASC')->limit(300);
        $lecturers = $this->db->get()->result();

        foreach ($lecturers as $l) {
            $l->prodi_names = implode(', ', $this->Lecturer_model->get_prodi_names($l->id)) ?: '-';
        }

        $data = [
            'title' => 'Manajemen Dosen', 'active_menu' => 'lecturers',
            'lecturers' => $lecturers, 'q' => $q, 'active' => $active,
            'total_active' => $this->db->where('is_active', 1)->count_all_results('lecturers'),
            'total_inactive' => $this->db->where('is_active', 0)->count_all_results('lecturers'),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/lecturers/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title' => 'Tambah Dosen', 'active_menu' => 'lecturers',
            'prodi_list' => $this->Lecturer_model->get_all_prodi(),
            'checked' => [], 'lecturer' => NULL,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/lecturers/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $photo = $this->_handle_upload();
        if ($photo === 'error') redirect('admin/lecturers/create');

        $lid = $this->Lecturer_model->insert([
            'nidn' => $this->input->post('nidn', TRUE),
            'name' => $this->input->post('name', TRUE),
            'title_front' => $this->input->post('title_front', TRUE),
            'title_back' => $this->input->post('title_back', TRUE),
            'email' => $this->input->post('email', TRUE),
            'phone' => $this->input->post('phone', TRUE),
            'expertise' => $this->input->post('expertise', TRUE),
            'education' => $this->input->post('education', TRUE),
            'google_scholar_url' => $this->input->post('google_scholar_url', TRUE),
            'sinta_url' => $this->input->post('sinta_url', TRUE),
            'photo' => $photo,
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ]);
        $this->Lecturer_model->sync_prodi($lid, $this->input->post('prodi_ids') ?: []);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_lecturer', 'Tambah dosen: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Dosen berhasil ditambahkan!');
        redirect('admin/lecturers');
    }

    public function edit($id) {
        $lecturer = $this->Lecturer_model->get_by_id($id);
        if (!$lecturer) redirect('admin/lecturers');

        $data = [
            'title' => 'Edit Dosen', 'active_menu' => 'lecturers',
            'prodi_list' => $this->Lecturer_model->get_all_prodi(),
            'checked' => array_map(function ($r) { return $r->study_program_id; }, $this->Lecturer_model->get_prodi_of($id)),
            'lecturer' => $lecturer,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/lecturers/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $lecturer = $this->Lecturer_model->get_by_id($id);
        if (!$lecturer) redirect('admin/lecturers');

        $this->_validate($id);
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $photo = $lecturer->photo;

        if ($this->input->post('remove_photo')) {
            $this->_safe_unlink($photo);
            $photo = NULL;
        }

        if (!empty($_FILES['photo']['name'])) {
            $new = $this->_handle_upload();
            if ($new === 'error') redirect('admin/lecturers/edit/' . $id);
            $this->_safe_unlink($photo);
            $photo = $new;
        }

        $this->Lecturer_model->update($id, [
            'nidn' => $this->input->post('nidn', TRUE),
            'name' => $this->input->post('name', TRUE),
            'title_front' => $this->input->post('title_front', TRUE),
            'title_back' => $this->input->post('title_back', TRUE),
            'email' => $this->input->post('email', TRUE),
            'phone' => $this->input->post('phone', TRUE),
            'expertise' => $this->input->post('expertise', TRUE),
            'education' => $this->input->post('education', TRUE),
            'google_scholar_url' => $this->input->post('google_scholar_url', TRUE),
            'sinta_url' => $this->input->post('sinta_url', TRUE),
            'photo' => $photo,
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ]);
        $this->Lecturer_model->sync_prodi($id, $this->input->post('prodi_ids') ?: []);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_lecturer', 'Edit dosen: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Data dosen berhasil diperbarui!');
        redirect('admin/lecturers');
    }

    public function delete($id) {
        $lecturer = $this->Lecturer_model->get_by_id($id);
        if ($lecturer) {
            $this->_safe_unlink($lecturer->photo);
            $this->Lecturer_model->delete($id);
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_lecturer', 'Hapus dosen: ' . $lecturer->name);
            $this->session->set_flashdata('success', 'Dosen berhasil dihapus.');
        }
        redirect('admin/lecturers');
    }

    // ===== 🔥 FITUR GILA: Duplicate dosen =====
    public function duplicate($id) {
        $lecturer = $this->Lecturer_model->get_by_id($id);
        if (!$lecturer) { show_404(); }
        $new = clone $lecturer;
        unset($new->id);
        $new->name = $lecturer->name . ' (Salinan)';
        $new->nidn = $lecturer->nidn . '-COPY';
        $new->photo = null;

        // Pastikan NIDN unik
        $counter = 1;
        while ($this->db->get_where('lecturers', ['nidn' => $new->nidn])->row()) {
            $new->nidn = $lecturer->nidn . '-COPY' . $counter++;
        }

        $this->Lecturer_model->insert((array)$new);
        $new_id = $this->db->insert_id();

        // Copy relasi prodi
        $prodi_ids = array_map(function ($r) { return $r->study_program_id; }, $this->Lecturer_model->get_prodi_of($id));
        $this->Lecturer_model->sync_prodi($new_id, $prodi_ids);

        $this->session->set_flashdata('success', 'Dosen berhasil diduplikasi.');
        redirect('admin/lecturers/edit/' . $new_id);
    }

    // ===== 🔥 FITUR GILA: Export vCard (untuk 1 dosen) =====
    public function vcard($id) {
        $l = $this->Lecturer_model->get_by_id($id);
        if (!$l) show_404();

        $site_name = 'Fakultas';
        try {
            $row = $this->db->get_where('site_settings', ['key' => 'site_name'])->row();
            if ($row) $site_name = $row->value;
        } catch (Exception $e) {}

        $vcard  = "BEGIN:VCARD\r\nVERSION:3.0\r\n";
        $vcard .= "FN:" . trim(($l->title_front ? $l->title_front . ' ' : '') . $l->name . ($l->title_back ? ', ' . $l->title_back : '')) . "\r\n";
        $vcard .= "N:" . $l->name . ";;;;\r\n";
        if ($l->email) $vcard .= "EMAIL;TYPE=WORK:" . $l->email . "\r\n";
        if ($l->phone) $vcard .= "TEL;TYPE=WORK,VOICE:" . $l->phone . "\r\n";
        $vcard .= "ORG:" . $site_name . "\r\n";
        $vcard .= "TITLE:Dosen\r\n";
        if ($l->google_scholar_url) $vcard .= "URL:" . $l->google_scholar_url . "\r\n";
        if ($l->expertise) $vcard .= "NOTE:Keahlian: " . $l->expertise . "\r\n";
        $vcard .= "END:VCARD\r\n";

        $filename = url_title($l->name, '-', true) . '.vcf';
        $this->output
            ->set_content_type('text/vcard; charset=utf-8')
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_output($vcard);
    }

    // ===== 🔥 FITUR GILA: Bulk toggle active =====
    public function bulk_toggle() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');
        if (!is_array($ids) || !in_array($action, ['activate', 'deactivate'])) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/lecturers'); return;
        }
        $status = ($action === 'activate') ? 1 : 0;
        $this->db->where_in('id', $ids)->update('lecturers', ['is_active' => $status]);
        $count = $this->db->affected_rows();
        $this->session->set_flashdata('success', "{$count} dosen berhasil di-{$action}.");
        redirect('admin/lecturers');
    }

    // ===== 🔥 FITUR GILA: AJAX cek NIDN unik =====
    public function check_nidn() {
        if (!$this->input->is_ajax_request()) show_404();
        $nidn = trim($this->input->post('nidn'));
        $exclude_id = (int)$this->input->post('exclude_id');
        $this->db->where('nidn', $nidn);
        if ($exclude_id > 0) $this->db->where('id !=', $exclude_id);
        $exists = $this->db->get('lecturers')->row();
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['unique' => !$exists]));
    }

    private function _validate($exclude_id = NULL) {
        $this->load->library('form_validation');

        // NIDN: 10 digit angka (format standar Dikti)
        $nidn_rule = 'required|trim|regex_match[/^\d{10}$/]';
        if ($exclude_id) {
            $nidn_rule .= '|callback__check_nidn_unique[' . $exclude_id . ']';
        } else {
            $nidn_rule .= '|is_unique[lecturers.nidn]';
        }

        $this->form_validation->set_rules('nidn', 'NIDN', $nidn_rule);
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('phone', 'No. HP', 'trim|regex_match[/^[0-9+\-\s()]{6,20}$/]');
        $this->form_validation->set_rules('title_front', 'Gelar Depan', 'trim|max_length[50]');
        $this->form_validation->set_rules('title_back', 'Gelar Belakang', 'trim|max_length[50]');
        $this->form_validation->set_rules('expertise', 'Keahlian', 'trim|max_length[300]');
        $this->form_validation->set_rules('education', 'Pendidikan', 'trim|max_length[500]');
        $this->form_validation->set_rules('google_scholar_url', 'Google Scholar', 'trim|valid_url|max_length[500]');
        $this->form_validation->set_rules('sinta_url', 'SINTA', 'trim|valid_url|max_length[500]');
    }

    public function _check_nidn_unique($nidn, $id) {
        $exists = $this->db->where('nidn', $nidn)->where('id !=', $id)->get('lecturers')->row();
        if ($exists) {
            $this->form_validation->set_message('_check_nidn_unique', 'NIDN sudah dipakai dosen lain.');
            return FALSE;
        }
        return TRUE;
    }

    private function _handle_upload() {
        if (empty($_FILES['photo']['name'])) return NULL;
        $path = FCPATH . 'assets/uploads/lecturers/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 2048, // 2MB
            'encrypt_name' => TRUE,
        ]);
        if ($this->upload->do_upload('photo')) {
            $updata = $this->upload->data();
            $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($updata['file_type'], $allowed_mime, true)) {
                @unlink($updata['full_path']);
                $this->session->set_flashdata('error', 'Tipe file foto tidak valid.');
                return 'error';
            }
            // Simpan dengan prefix folder supaya konsisten
            return 'lecturers/' . $updata['file_name'];
        }
        $this->session->set_flashdata('error', 'Upload foto gagal: ' . strip_tags($this->upload->display_errors('', '')));
        return 'error';
    }

    private function _safe_unlink($rel_path) {
        if (!$rel_path) return;
        $upload_base = realpath(FCPATH . 'assets/uploads');
        $full = realpath(FCPATH . 'assets/uploads/' . $rel_path);
        if ($full && $upload_base && strpos($full, $upload_base) === 0 && file_exists($full)) {
            @unlink($full);
        }
    }
}