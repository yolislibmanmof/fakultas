<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alumni extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->helper(['url', 'text']);
    }

    // ===== LIST + FILTER STATUS =====
    public function index() {
        $status = $this->input->get('status');
        $q = trim((string)$this->input->get('q'));

        $this->db->select('alumni.*, study_programs.name as prodi_name')
                 ->join('study_programs', 'study_programs.id = alumni.study_program_id', 'left');
        if ($status) $this->db->where('alumni.status', $status);
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('alumni.full_name', $q);
            $this->db->or_like('alumni.company', $q);
            $this->db->or_like('alumni.email', $q);
            $this->db->group_end();
        }

        $data['alumni'] = $this->db->order_by('alumni.created_at', 'DESC')->limit(500)->get('alumni')->result();
        $data['status'] = $status;
        $data['q'] = $q;
        $raw_counts = $this->db
            ->select('status, COUNT(*) AS c', FALSE)
            ->group_by('status')
            ->get('alumni')->result();
        $status_counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($raw_counts as $rc) {
            if (isset($status_counts[$rc->status])) $status_counts[$rc->status] = (int)$rc->c;
        }
        $data['counts'] = [
            'all'      => array_sum($status_counts),
            'pending'  => $status_counts['pending'],
            'approved' => $status_counts['approved'],
            'rejected' => $status_counts['rejected'],
        ];
        $data['title'] = 'Manajemen Alumni';
        $data['active_menu'] = 'alumni';

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/alumni/index', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== APPROVE / REJECT =====
    public function approve($id = NULL) {
        $this->db->where('id', $id)->update('alumni', ['status' => 'approved']);
        $this->session->set_flashdata('success', 'Profil alumni disetujui & tampil di direktori publik.');
        redirect('admin/alumni' . ($this->input->get('status') ? '?status=' . $this->input->get('status') : ''));
    }

    public function reject($id = NULL) {
        $this->db->where('id', $id)->update('alumni', ['status' => 'rejected']);
        $this->session->set_flashdata('success', 'Profil alumni ditolak.');
        redirect('admin/alumni' . ($this->input->get('status') ? '?status=' . $this->input->get('status') : ''));
    }

    // ===== FORM TAMBAH =====
    public function create() {
        $data['programs'] = $this->db->order_by('name', 'ASC')->get('study_programs')->result();
        $data['a'] = NULL;
        $data['title'] = 'Tambah Alumni';
        $data['active_menu'] = 'alumni';
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/alumni/form', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== SIMPAN BARU =====
    public function store() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Nama', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[alumni.email]');
        $this->form_validation->set_rules('graduation_year', 'Tahun Lulus', 'trim|integer|greater_than_equal_to[1950]|less_than_equal_to[' . (date('Y') + 1) . ']');
        $this->form_validation->set_rules('phone', 'No. HP', 'trim|regex_match[/^[0-9+\-\s()]{6,20}$/]');
        $this->form_validation->set_rules('nim', 'NIM', 'trim|max_length[30]');
        $this->form_validation->set_rules('linkedin_url', 'LinkedIn', 'trim|valid_url');
        $this->form_validation->set_rules('website_url', 'Website', 'trim|valid_url');
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $this->db->insert('alumni', $this->_payload(TRUE));
        $this->session->set_flashdata('success', 'Alumni berhasil ditambahkan.');
        redirect('admin/alumni');
    }

    // ===== FORM EDIT =====
    public function edit($id = NULL) {
        $a = $this->db->get_where('alumni', ['id' => $id])->row();
        if (!$a) redirect('admin/alumni');
        $data['programs'] = $this->db->order_by('name', 'ASC')->get('study_programs')->result();
        $data['a'] = $a;
        $data['title'] = 'Edit Alumni';
        $data['active_menu'] = 'alumni';
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/alumni/form', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== UPDATE =====
    public function update($id = NULL) {
        $a = $this->db->get_where('alumni', ['id' => $id])->row();
        if (!$a) redirect('admin/alumni');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Nama', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|callback__check_email_unique[' . $id . ']');
        $this->form_validation->set_rules('graduation_year', 'Tahun Lulus', 'trim|integer|greater_than_equal_to[1950]|less_than_equal_to[' . (date('Y') + 1) . ']');
        $this->form_validation->set_rules('phone', 'No. HP', 'trim|regex_match[/^[0-9+\-\s()]{6,20}$/]');
        $this->form_validation->set_rules('nim', 'NIM', 'trim|max_length[30]');
        $this->form_validation->set_rules('linkedin_url', 'LinkedIn', 'trim|valid_url');
        $this->form_validation->set_rules('website_url', 'Website', 'trim|valid_url');
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $this->db->where('id', $id)->update('alumni', $this->_payload(FALSE, $a));
        $this->session->set_flashdata('success', 'Profil alumni berhasil diperbarui.');
        redirect('admin/alumni');
    }

    // ===== HAPUS =====
    public function delete($id = NULL) {
        $a = $this->db->get_where('alumni', ['id' => $id])->row();
        if ($a) {
            if ($a->photo && file_exists(FCPATH . 'assets/uploads/' . $a->photo)) {
                @unlink(FCPATH . 'assets/uploads/' . $a->photo);
            }
            $this->db->where('id', $id)->delete('alumni');
            $this->session->set_flashdata('success', 'Alumni berhasil dihapus.');
        }
        redirect('admin/alumni');
    }

    // ===== VALIDASI CALLBACK: CEK UNIK EMAIL SAAT EDIT =====
    public function _check_email_unique($email, $id) {
        $exists = $this->db->where('email', $email)->where('id !=', $id)->get('alumni')->row();
        if ($exists) {
            $this->form_validation->set_message('_check_email_unique', 'Email sudah dipakai alumni lain.');
            return FALSE;
        }
        return TRUE;
    }

    // ===== PAYLOAD =====
    private function _payload($is_new = FALSE, $old = NULL) {
        $p = [
            'full_name'        => $this->input->post('full_name', TRUE),
            'nim'              => $this->input->post('nim', TRUE),
            'study_program_id' => $this->input->post('study_program_id') ?: NULL,
            'graduation_year'  => $this->input->post('graduation_year') ?: NULL,
            'email'            => $this->input->post('email', TRUE),
            'phone'            => $this->input->post('phone', TRUE),
            'current_position' => $this->input->post('current_position', TRUE),
            'company'          => $this->input->post('company', TRUE),
            'industry'         => $this->input->post('industry', TRUE),
            'city'             => $this->input->post('city', TRUE),
            'country'          => $this->input->post('country', TRUE) ?: 'Indonesia',
            'linkedin_url'     => $this->input->post('linkedin_url', TRUE),
            'website_url'      => $this->input->post('website_url', TRUE),
            'bio'              => $this->input->post('bio'),
            'achievements'     => $this->input->post('achievements'),
            'status'           => $this->input->post('status') ?: 'pending',
        ];

        // Password (opsional saat edit)
        $pw = $this->input->post('password');
        if ($pw !== NULL && $pw !== '') {
            $p['password_hash'] = password_hash($pw, PASSWORD_DEFAULT);
        } elseif ($is_new) {
            $p['password_hash'] = password_hash('alumni123', PASSWORD_DEFAULT);
        }

        // Foto upload
        if (!empty($_FILES['photo']['name'])) {
            $path = FCPATH . 'assets/uploads/alumni/';
            if (!is_dir($path)) @mkdir($path, 0755, TRUE);
            $this->load->library('upload');
            $this->upload->initialize([
                'upload_path' => $path,
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size' => 2048,
                'encrypt_name' => TRUE,
            ]);
            if ($this->upload->do_upload('photo')) {
                $updata = $this->upload->data();
                // Verifikasi MIME type asli (anti-extension spoofing)
                $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!in_array($updata['file_type'], $allowed_mime, true)) {
                    @unlink($updata['full_path']);
                    $this->session->set_flashdata('error', 'Tipe file foto tidak valid. Gunakan JPG, PNG, atau WEBP.');
                    redirect($is_new ? 'admin/alumni/create' : 'admin/alumni/edit/' . ($old->id ?? ''));
                    return;
                }
                if ($old && $old->photo && file_exists(FCPATH . 'assets/uploads/' . $old->photo)) {
                    @unlink(FCPATH . 'assets/uploads/' . $old->photo);
                }
                $p['photo'] = 'alumni/' . $updata['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Upload foto gagal: ' . strip_tags($this->upload->display_errors('', '')));
                redirect($is_new ? 'admin/alumni/create' : 'admin/alumni/edit/' . ($old->id ?? ''));
                return;
            }
        }

        return $p;
    }
}