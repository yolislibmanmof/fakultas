<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courses extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->library('upload');
        $this->load->helper(['url', 'text']);
    }

    // ===== Deteksi kolom tabel pivot course_study_program =====
    private function _pivot() {
        static $p = NULL;
        if ($p === NULL) {
            $f = $this->db->list_fields('course_study_program');
            $p = [
                'course' => in_array('course_id', $f) ? 'course_id' : 'courses_id',
                'prodi'  => in_array('study_program_id', $f) ? 'study_program_id' : 'program_id',
            ];
        }
        return $p;
    }

    private function _prodi_names($course_id) {
        $pv = $this->_pivot();
        $rows = $this->db->select('study_programs.name')
            ->from('course_study_program')
            ->join('study_programs', 'study_programs.id = course_study_program.' . $pv['prodi'])
            ->where('course_study_program.' . $pv['course'], $course_id)
            ->get()->result();
        $names = [];
        foreach ($rows as $r) $names[] = $r->name;
        return implode(', ', $names) ?: '-';
    }

    private function _sync_prodi($course_id, $ids) {
        $pv = $this->_pivot();
        $this->db->where($pv['course'], $course_id)->delete('course_study_program');
        foreach ((array)$ids as $pid) {
            if ($pid) $this->db->insert('course_study_program', [$pv['course'] => $course_id, $pv['prodi'] => $pid]);
        }
    }

    public function index() {
        $q    = trim((string)$this->input->get('q'));
        $prog = $this->input->get('prodi');
        $pv   = $this->_pivot();

        $this->db->select('courses.*')->from('courses');
        if ($prog) {
            $this->db->join('course_study_program', 'course_study_program.' . $pv['course'] . ' = courses.id')
                     ->where('course_study_program.' . $pv['prodi'], $prog);
        }
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('courses.name', $q);
            $this->db->or_like('courses.course_code', $q);
            $this->db->group_end();
        }
        $this->db->order_by('courses.semester', 'ASC')->order_by('courses.course_code', 'ASC');
        $this->db->limit(500); // Safety limit
        $courses = $this->db->get()->result();

        foreach ($courses as $c) $c->prodi_names = $this->_prodi_names($c->id);

        $data = [
            'title' => 'Manajemen Mata Kuliah', 'active_menu' => 'courses',
            'courses' => $courses,
            'programs' => $this->db->order_by('name', 'ASC')->get('study_programs')->result(),
            'q' => $q, 'prodi' => $prog,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/courses/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title' => 'Tambah Mata Kuliah', 'active_menu' => 'courses',
            'course' => NULL, 'checked' => [],
            'programs' => $this->db->order_by('name', 'ASC')->get('study_programs')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/courses/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate(NULL);
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->insert('courses', $payload);
        $id = $this->db->insert_id();
        $this->_sync_prodi($id, $this->input->post('prodi_ids') ?: []);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_course', 'Tambah MK: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Mata kuliah berhasil ditambahkan!');
        redirect('admin/courses');
    }

    public function edit($id) {
        $course = $this->db->get_where('courses', ['id' => $id])->row();
        if (!$course) redirect('admin/courses');

        $pv = $this->_pivot();
        $checked = array_map(function ($r) use ($pv) { return $r->{$pv['prodi']}; },
            $this->db->get_where('course_study_program', [$pv['course'] => $id])->result());

        $data = [
            'title' => 'Edit Mata Kuliah', 'active_menu' => 'courses',
            'course' => $course, 'checked' => $checked,
            'programs' => $this->db->order_by('name', 'ASC')->get('study_programs')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/courses/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $course = $this->db->get_where('courses', ['id' => $id])->row();
        if (!$course) redirect('admin/courses');
        $this->_validate($id);
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $payload = $this->_payload($course);
        if ($payload === FALSE) return;

        $this->db->where('id', $id)->update('courses', $payload);
        $this->_sync_prodi($id, $this->input->post('prodi_ids') ?: []);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_course', 'Edit MK: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Mata kuliah berhasil diperbarui!');
        redirect('admin/courses');
    }

    public function delete($id) {
        $course = $this->db->get_where('courses', ['id' => $id])->row();
        if ($course) {
            $pv = $this->_pivot();
            if ($course->syllabus_file && file_exists(FCPATH . 'assets/uploads/syllabus/' . $course->syllabus_file)) {
                @unlink(FCPATH . 'assets/uploads/syllabus/' . $course->syllabus_file);
            }
            $this->db->where($pv['course'], $id)->delete('course_study_program');
            $this->db->where('id', $id)->delete('courses');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_course', 'Hapus MK: ' . $course->name);
            $this->session->set_flashdata('success', 'Mata kuliah berhasil dihapus.');
        }
        redirect('admin/courses');
    }

    // ===== 🔥 FITUR GILA: Duplicate (clone) course =====
    public function duplicate($id) {
        $course = $this->db->get_where('courses', ['id' => $id])->row();
        if (!$course) { show_404(); }

        $new = clone $course;
        unset($new->id);
        $new->course_code = $course->course_code . '-COPY';
        $new->name = $course->name . ' (Salinan)';
        $new->syllabus_file = null; // Tidak copy file silabus (biar tidak double)

        // Pastikan course_code baru unik
        $counter = 1;
        while ($this->db->get_where('courses', ['course_code' => $new->course_code])->row()) {
            $new->course_code = $course->course_code . '-COPY' . $counter++;
        }

        $this->db->insert('courses', (array)$new);
        $new_id = $this->db->insert_id();

        // Copy relasi prodi
        $pv = $this->_pivot();
        $prodi_ids = array_map(function ($r) use ($pv) { return $r->{$pv['prodi']}; },
            $this->db->get_where('course_study_program', [$pv['course'] => $id])->result());
        $this->_sync_prodi($new_id, $prodi_ids);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'duplicate_course', 'Duplikat MK: ' . $course->name);
        $this->session->set_flashdata('success', 'Mata kuliah berhasil diduplikasi. Silakan edit salinan.');
        redirect('admin/courses/edit/' . $new_id);
    }

    private function _validate($id = NULL) {
        $this->load->library('form_validation');

        // Unique check (exclude current id saat edit)
        $unique_rule = 'required|trim|max_length[20]|is_unique[courses.course_code]';
        if ($id) {
            $unique_rule = 'required|trim|max_length[20]|callback__check_code_unique[' . $id . ']';
        }
        $this->form_validation->set_rules('course_code', 'Kode MK', $unique_rule);
        $this->form_validation->set_rules('name', 'Nama MK', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('sks', 'SKS', 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]');
        $this->form_validation->set_rules('semester', 'Semester', 'required|integer|greater_than_equal_to[1]|less_than_equal_to[14]');
        $this->form_validation->set_rules('course_type', 'Jenis MK', 'required|in_list[wajib,pilihan]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[2000]');
    }

    public function _check_code_unique($code, $id) {
        $exists = $this->db->where('course_code', $code)->where('id !=', $id)->get('courses')->row();
        if ($exists) {
            $this->form_validation->set_message('_check_code_unique', 'Kode MK sudah dipakai mata kuliah lain.');
            return FALSE;
        }
        return TRUE;
    }

    private function _payload($old = NULL) {
        $syllabus = $old ? $old->syllabus_file : NULL;
        $redirect_url = $old ? 'admin/courses/edit/' . $old->id : 'admin/courses/create';

        if ($this->input->post('remove_syllabus')) {
            if ($syllabus && file_exists(FCPATH . 'assets/uploads/syllabus/' . $syllabus)) {
                @unlink(FCPATH . 'assets/uploads/syllabus/' . $syllabus);
            }
            $syllabus = NULL;
        }

        if (!empty($_FILES['syllabus_file']['name'])) {
            $path = FCPATH . 'assets/uploads/syllabus/';
            if (!is_dir($path)) @mkdir($path, 0755, TRUE);
            $this->upload->initialize([
                'upload_path'   => $path,
                'allowed_types' => 'pdf|doc|docx',
                'max_size'      => 8192,
                'encrypt_name'  => TRUE,
            ]);
            if ($this->upload->do_upload('syllabus_file')) {
                $updata = $this->upload->data();

                // MIME validation
                $allowed_mime = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];
                if (!in_array($updata['file_type'], $allowed_mime, true)) {
                    @unlink($updata['full_path']);
                    $this->session->set_flashdata('error', 'Tipe file silabus tidak valid. Gunakan PDF, DOC, atau DOCX.');
                    redirect($redirect_url);
                    return FALSE;
                }

                if ($syllabus && file_exists(FCPATH . 'assets/uploads/syllabus/' . $syllabus)) {
                    @unlink(FCPATH . 'assets/uploads/syllabus/' . $syllabus);
                }
                $syllabus = $updata['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Upload silabus gagal: ' . strip_tags($this->upload->display_errors('', '')));
                redirect($redirect_url);
                return FALSE;
            }
        }

        return [
            'course_code'   => strtoupper(trim($this->input->post('course_code', TRUE))),
            'name'          => $this->input->post('name', TRUE),
            'sks'           => (int)$this->input->post('sks'),
            'semester'      => (int)$this->input->post('semester'),
            'course_type'   => $this->input->post('course_type', TRUE) ?: 'wajib',
            'description'   => $this->input->post('description'),
            'syllabus_file' => $syllabus,
        ];
    }
}