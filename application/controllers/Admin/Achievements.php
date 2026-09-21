<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Achievements extends CI_Controller {

    private $levels = [
        'international' => 'Internasional',
        'national'      => 'Nasional',
        'regional'      => 'Regional',
        'university'    => 'Universitas',
        'faculty'       => 'Fakultas',
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->library('upload');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        $q     = trim((string)$this->input->get('q'));
        $level = $this->input->get('level');

        $this->db->select('achievements.*, study_programs.name as prodi_name')
            ->from('achievements')
            ->join('study_programs', 'study_programs.id = achievements.study_program_id', 'left');
        if ($level && array_key_exists($level, $this->levels)) $this->db->where('achievements.level', $level);
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('achievements.student_name', $q);
            $this->db->or_like('achievements.achievement_title', $q);
            $this->db->group_end();
        }
        $this->db->order_by('achievements.year', 'DESC')->order_by('achievements.id', 'DESC');
        $this->db->limit(500);

        $data = [
            'title' => 'Manajemen Prestasi', 'active_menu' => 'achievements',
            'items' => $this->db->get()->result(),
            'levels' => $this->levels, 'q' => $q, 'level' => $level,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/achievements/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title' => 'Tambah Prestasi', 'active_menu' => 'achievements',
            'item' => NULL, 'levels' => $this->levels,
            'programs' => $this->db->order_by('name', 'ASC')->get('study_programs')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/achievements/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return; // Redirect sudah dilakukan di _payload

        $this->db->insert('achievements', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_achievement', 'Tambah prestasi: ' . $this->input->post('achievement_title', TRUE));
        $this->session->set_flashdata('success', 'Prestasi berhasil ditambahkan!');
        redirect('admin/achievements');
    }

    public function edit($id) {
        $item = $this->db->get_where('achievements', ['id' => $id])->row();
        if (!$item) redirect('admin/achievements');
        $data = [
            'title' => 'Edit Prestasi', 'active_menu' => 'achievements',
            'item' => $item, 'levels' => $this->levels,
            'programs' => $this->db->order_by('name', 'ASC')->get('study_programs')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/achievements/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $item = $this->db->get_where('achievements', ['id' => $id])->row();
        if (!$item) redirect('admin/achievements');
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $payload = $this->_payload($item);
        if ($payload === FALSE) return; // Redirect sudah dilakukan di _payload

        $this->db->where('id', $id)->update('achievements', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_achievement', 'Edit prestasi: ' . $this->input->post('achievement_title', TRUE));
        $this->session->set_flashdata('success', 'Prestasi berhasil diperbarui!');
        redirect('admin/achievements');
    }

    public function delete($id) {
        $item = $this->db->get_where('achievements', ['id' => $id])->row();
        if ($item) {
            if ($item->document_proof && file_exists(FCPATH . 'assets/uploads/achievements/' . $item->document_proof)) {
                @unlink(FCPATH . 'assets/uploads/achievements/' . $item->document_proof);
            }
            $this->db->where('id', $id)->delete('achievements');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_achievement', 'Hapus prestasi: ' . $item->achievement_title);
            $this->session->set_flashdata('success', 'Prestasi berhasil dihapus.');
        }
        redirect('admin/achievements');
    }

    private function _validate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('student_name', 'Nama Mahasiswa', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('achievement_title', 'Judul Prestasi', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('nim', 'NIM', 'trim|max_length[30]');
        $this->form_validation->set_rules('year', 'Tahun', 'required|integer|greater_than_equal_to[1950]|less_than_equal_to[' . (date('Y') + 1) . ']');
        $this->form_validation->set_rules('organizer', 'Penyelenggara', 'trim|max_length[200]');
    }

    private function _payload($old = NULL) {
        $level = $this->input->post('level', TRUE);
        $doc = $old ? $old->document_proof : NULL;
        $redirect_url = $old ? 'admin/achievements/edit/' . $old->id : 'admin/achievements/create';

        if ($this->input->post('remove_doc')) {
            if ($doc && file_exists(FCPATH . 'assets/uploads/achievements/' . $doc)) {
                @unlink(FCPATH . 'assets/uploads/achievements/' . $doc);
            }
            $doc = NULL;
        }

        if (!empty($_FILES['document_proof']['name'])) {
            $path = FCPATH . 'assets/uploads/achievements/';
            if (!is_dir($path)) @mkdir($path, 0755, TRUE);
            $this->upload->initialize([
                'upload_path' => $path,
                'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
                'max_size' => 5120,
                'encrypt_name' => TRUE,
            ]);
            if ($this->upload->do_upload('document_proof')) {
                $updata = $this->upload->data();
                // Verifikasi MIME type asli (anti-extension spoofing)
                $allowed_mime = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg', 'image/jpg', 'image/png',
                ];
                if (!in_array($updata['file_type'], $allowed_mime, true)) {
                    @unlink($updata['full_path']);
                    $this->session->set_flashdata('error', 'Tipe file dokumen tidak valid. Gunakan PDF, DOC, DOCX, JPG, atau PNG.');
                    redirect($redirect_url);
                    return FALSE;
                }
                if ($doc && file_exists(FCPATH . 'assets/uploads/achievements/' . $doc)) {
                    @unlink(FCPATH . 'assets/uploads/achievements/' . $doc);
                }
                $doc = $updata['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Upload dokumen gagal: ' . strip_tags($this->upload->display_errors('', '')));
                redirect($redirect_url);
                return FALSE;
            }
        }

        return [
            'student_name'      => $this->input->post('student_name', TRUE),
            'nim'               => $this->input->post('nim', TRUE),
            'study_program_id'  => $this->input->post('study_program_id') ?: NULL,
            'achievement_title' => $this->input->post('achievement_title', TRUE),
            'level'             => array_key_exists($level, $this->levels) ? $level : 'national',
            'year'              => (int)$this->input->post('year'),
            'organizer'         => $this->input->post('organizer', TRUE),
            'document_proof'    => $doc,
        ];
    }
}