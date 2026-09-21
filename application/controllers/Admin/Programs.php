<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Programs extends CI_Controller {

    private $valid_degrees = ['D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis'];
    private $valid_accreditations = ['Unggul', 'Baik Sekali', 'Baik', 'A', 'B', 'C', 'Terakreditasi', 'Belum'];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        $q = trim((string)$this->input->get('q'));
        $degree = $this->input->get('degree');
        $active = $this->input->get('active');

        $this->db->select('*')->from('study_programs');
        if ($q !== '') {
            $this->db->group_start()
                ->like('name', $q)
                ->or_like('head_of_study_program', $q)
                ->or_like('description', $q)
                ->group_end();
        }
        if ($degree && in_array($degree, $this->valid_degrees)) $this->db->where('degree', $degree);
        if ($active !== '' && $active !== NULL) $this->db->where('is_active', (int)$active);
        $this->db->order_by('degree', 'DESC')->order_by('name', 'ASC')->limit(100);

        $all = $this->db->get()->result();

        // Hitung mahasiswa per prodi (jika ada tabel students)
        if ($this->db->table_exists('students')) {
            $student_counts = [];
            $counts_raw = $this->db->select('study_program_id, COUNT(*) as c', FALSE)
                ->group_by('study_program_id')->get('students')->result();
            foreach ($counts_raw as $r) $student_counts[(int)$r->study_program_id] = (int)$r->c;
            foreach ($all as $p) $p->student_count = $student_counts[$p->id] ?? 0;
        }

        $data = [
            'title' => 'Manajemen Program Studi', 'active_menu' => 'programs',
            'programs' => $all, 'q' => $q, 'degree' => $degree, 'active' => $active,
            'degrees' => $this->valid_degrees, 'accreditations' => $this->valid_accreditations,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/programs/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = ['title' => 'Tambah Program Studi', 'active_menu' => 'programs', 'program' => NULL,
                 'degrees' => $this->valid_degrees, 'accreditations' => $this->valid_accreditations];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/programs/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $photo = $this->_handle_photo();
        if ($photo === 'error') { $this->create(); return; }
        if ($photo) $payload['head_photo'] = $photo;

        $this->db->insert('study_programs', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_program', 'Tambah prodi: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Program studi berhasil ditambahkan!');
        redirect('admin/programs');
    }

    public function edit($id) {
        $program = $this->db->get_where('study_programs', ['id' => $id])->row();
        if (!$program) redirect('admin/programs');
        $data = ['title' => 'Edit Program Studi', 'active_menu' => 'programs', 'program' => $program,
                 'degrees' => $this->valid_degrees, 'accreditations' => $this->valid_accreditations];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/programs/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $program = $this->db->get_where('study_programs', ['id' => $id])->row();
        if (!$program) redirect('admin/programs');
        $this->_validate($id);
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $payload = $this->_payload($program->slug);
        if ($payload === FALSE) { $this->edit($id); return; }

        if ($this->input->post('remove_head_photo')) {
            $this->_safe_unlink($program->head_photo);
            $payload['head_photo'] = NULL;
        }

        $photo = $this->_handle_photo();
        if ($photo === 'error') { $this->edit($id); return; }
        if ($photo) {
            $this->_safe_unlink($program->head_photo);
            $payload['head_photo'] = $photo;
        }

        $this->db->where('id', $id)->update('study_programs', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_program', 'Edit prodi: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Program studi berhasil diperbarui!');
        redirect('admin/programs');
    }

    public function delete($id) {
        $program = $this->db->get_where('study_programs', ['id' => $id])->row();
        if ($program) {
            $this->_safe_unlink($program->head_photo);
            $this->db->where('id', $id)->delete('study_programs');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_program', 'Hapus prodi: ' . $program->name);
            $this->session->set_flashdata('success', 'Program studi berhasil dihapus.');
        }
        redirect('admin/programs');
    }

    // ===== 🔥 FITUR GILA: Duplicate prodi =====
    public function duplicate($id) {
        $program = $this->db->get_where('study_programs', ['id' => $id])->row();
        if (!$program) show_404();
        $new = clone $program;
        unset($new->id);
        $new->name = $program->name . ' (Salinan)';
        $new->slug = $this->_unique_slug($new->name);
        $new->head_photo = null;
        $this->db->insert('study_programs', (array)$new);
        $this->session->set_flashdata('success', 'Prodi berhasil diduplikasi.');
        redirect('admin/programs');
    }

    // ===== 🔥 FITUR GILA: Public page preview (lihat tampilan publik) =====
    public function preview($id) {
        $program = $this->db->get_where('study_programs', ['id' => $id])->row();
        if (!$program) show_404();
        redirect('akademik/detail/' . $program->slug);
    }

    // ===== 🔥 FITUR GILA: Bulk toggle active =====
    public function bulk_toggle() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');
        if (!is_array($ids) || !in_array($action, ['activate', 'deactivate'])) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/programs'); return;
        }
        $status = ($action === 'activate') ? 1 : 0;
        $this->db->where_in('id', $ids)->update('study_programs', ['is_active' => $status]);
        $count = $this->db->affected_rows();
        $this->session->set_flashdata('success', "{$count} prodi berhasil di-{$action}.");
        redirect('admin/programs');
    }

    // ===== 🔥 FITUR GILA: AJAX check accreditation expiring =====
    public function check_expiry() {
        if (!$this->input->is_ajax_request()) show_404();
        $threshold = date('Y-m-d', strtotime('+90 days')); // 3 bulan ke depan
        $expiring = $this->db
            ->where('accreditation_until IS NOT NULL')
            ->where('accreditation_until <=', $threshold)
            ->where('accreditation_until >=', date('Y-m-d'))
            ->where('is_active', 1)
            ->order_by('accreditation_until', 'ASC')
            ->limit(10)
            ->get('study_programs')->result();
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'count' => count($expiring),
            'items' => array_map(function ($p) {
                $days = (strtotime($p->accreditation_until) - time()) / 86400;
                return [
                    'name' => $p->name,
                    'until' => $p->accreditation_until,
                    'days_left' => (int)$days,
                ];
            }, $expiring),
        ]));
    }

    private function _validate($exclude_id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama Prodi', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('degree', 'Jenjang', 'required|callback__validate_degree');
        $this->form_validation->set_rules('accreditation', 'Akreditasi', 'trim|callback__validate_accreditation');
        $this->form_validation->set_rules('accreditation_until', 'Berlaku Sampai', 'callback__validate_date');
        $this->form_validation->set_rules('head_of_study_program', 'Kaprodi', 'trim|max_length[150]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[3000]');
        $this->form_validation->set_rules('vision', 'Visi', 'trim|max_length[1000]');
        $this->form_validation->set_rules('mission', 'Misi', 'trim|max_length[2000]');
    }

    public function _validate_degree($degree) {
        if (!in_array($degree, $this->valid_degrees, true)) {
            $this->form_validation->set_message('_validate_degree', 'Jenjang tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    public function _validate_accreditation($acc) {
        if ($acc === '' || $acc === NULL) return TRUE;
        if (!in_array($acc, $this->valid_accreditations, true)) {
            $this->form_validation->set_message('_validate_accreditation', 'Nilai akreditasi tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    public function _validate_date($date) {
        if ($date === '' || $date === NULL) return TRUE;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->form_validation->set_message('_validate_date', 'Format tanggal tidak valid (YYYY-MM-DD).');
            return FALSE;
        }
        return TRUE;
    }

    private function _payload($old_slug = NULL) {
        $name = $this->input->post('name', TRUE);
        $slug = $old_slug ?: $this->_unique_slug($name);

        // Pastikan degree valid
        $degree = $this->input->post('degree');
        if (!in_array($degree, $this->valid_degrees, true)) {
            $this->session->set_flashdata('error', 'Jenjang tidak valid.');
            return FALSE;
        }

        // Accreditation validation
        $acc = $this->input->post('accreditation', TRUE);
        if ($acc !== '' && $acc !== NULL && !in_array($acc, $this->valid_accreditations, true)) {
            $acc = '';
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'degree' => $degree,
            'accreditation' => $acc,
            'accreditation_until' => $this->input->post('accreditation_until') ?: NULL,
            'head_of_study_program' => $this->input->post('head_of_study_program', TRUE),
            'description' => $this->input->post('description'),
            'vision' => $this->input->post('vision'),
            'mission' => $this->input->post('mission'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ];
    }

    private function _unique_slug($name, $exclude_id = NULL) {
        $base = url_title(strtolower(trim($name)), '-', TRUE);
        if ($base === '') $base = 'program';
        $slug = $base; $i = 2;
        $max = 1000;
        while ($max-- > 0) {
            $this->db->where('slug', $slug);
            if ($exclude_id) $this->db->where('id !=', $exclude_id);
            if ($this->db->count_all_results('study_programs') == 0) return $slug;
            $slug = $base . '-' . $i; $i++;
        }
        return $base . '-' . time();
    }

    private function _handle_photo() {
        if (empty($_FILES['head_photo']['name'])) return NULL;
        $path = FCPATH . 'assets/uploads/programs/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 2048,
            'encrypt_name' => TRUE,
        ]);
        if ($this->upload->do_upload('head_photo')) {
            $updata = $this->upload->data();
            $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($updata['file_type'], $allowed_mime, true)) {
                @unlink($updata['full_path']);
                $this->session->set_flashdata('error', 'Tipe file foto tidak valid.');
                return 'error';
            }
            return 'programs/' . $updata['file_name'];
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