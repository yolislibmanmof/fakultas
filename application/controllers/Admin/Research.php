<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Research extends CI_Controller {

    private $valid_types   = ['research', 'community_service'];
    private $valid_statuses = ['proposed', 'ongoing', 'completed'];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model(['Research_model', 'Lecturer_model', 'Auth_model']);
        $this->load->library('upload');
        $this->load->helper(['url', 'text', 'download']);
    }

    public function index() {
        $q      = trim((string)$this->input->get('q'));
        $type   = $this->input->get('type');
        $status = $this->input->get('status');
        $year   = $this->input->get('year');
        $lecturer = $this->input->get('lecturer');

        // 🔥 FIX: Filter di DB (bukan PHP) — jauh lebih cepat
        $this->db->select('research.*, lecturers.name as lecturer_name')
            ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left');

        if ($type && in_array($type, $this->valid_types)) $this->db->where('research.type', $type);
        if ($status && in_array($status, $this->valid_statuses)) $this->db->where('research.status', $status);
        if ($year && preg_match('/^\d{4}$/', $year)) $this->db->where('research.year', (int)$year);
        if ($lecturer) $this->db->where('research.lecturer_id', (int)$lecturer);
        if ($q !== '') {
            $this->db->group_start()
                ->like('research.title', $q)
                ->or_like('lecturers.name', $q)
                ->or_like('research.funding_source', $q)
                ->group_end();
        }
        $this->db->order_by('research.year', 'DESC')->order_by('research.id', 'DESC')->limit(500);
        $items = $this->db->get('research')->result();

        // Counts dari DB (1 query gabungan per grup)
        $type_counts = ['research' => 0, 'community_service' => 0];
        $type_raw = $this->db->select('type, COUNT(*) as c', FALSE)->group_by('type')->get('research')->result();
        foreach ($type_raw as $r) if (isset($type_counts[$r->type])) $type_counts[$r->type] = (int)$r->c;

        $status_counts = ['proposed' => 0, 'ongoing' => 0, 'completed' => 0];
        $status_raw = $this->db->select('status, COUNT(*) as c', FALSE)->group_by('status')->get('research')->result();
        foreach ($status_raw as $r) if (isset($status_counts[$r->status])) $status_counts[$r->status] = (int)$r->c;

        $counts = [
            'all' => array_sum($type_counts),
            'research' => $type_counts['research'],
            'community' => $type_counts['community_service'],
            'ongoing' => $status_counts['ongoing'],
            'completed' => $status_counts['completed'],
            'proposed' => $status_counts['proposed'],
        ];

        // Years available dari DB (bukan map array)
        $years = [];
        try {
            $year_rows = $this->db->select('year', FALSE)->distinct()
                ->where('year IS NOT NULL')->order_by('year', 'DESC')->get('research')->result();
            foreach ($year_rows as $yr) if ($yr->year) $years[] = (int)$yr->year;
        } catch (Exception $e) { /* silent */ }

        $data = [
            'title' => 'Manajemen Riset', 'active_menu' => 'research',
            'items' => $items, 'counts' => $counts, 'years' => $years,
            'q' => $q, 'type' => $type, 'status' => $status, 'year' => $year, 'lecturer' => $lecturer,
            'valid_types' => $this->valid_types, 'valid_statuses' => $this->valid_statuses,
            'lecturers' => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('lecturers')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/research/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = ['title' => 'Tambah Riset', 'active_menu' => 'research', 'item' => NULL,
                 'lecturers' => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('lecturers')->result(),
                 'valid_types' => $this->valid_types, 'valid_statuses' => $this->valid_statuses];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/research/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }
        $file = $this->_handle_upload();
        if ($file === 'error') { $this->create(); return; }

        $this->Research_model->insert($this->_payload($file));
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_research', 'Tambah riset: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Riset berhasil ditambahkan!');
        redirect('admin/research');
    }

    public function edit($id) {
        $item = $this->Research_model->get_by_id($id);
        if (!$item) redirect('admin/research');
        $data = ['title' => 'Edit Riset', 'active_menu' => 'research', 'item' => $item,
                 'lecturers' => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('lecturers')->result(),
                 'valid_types' => $this->valid_types, 'valid_statuses' => $this->valid_statuses];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/research/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $item = $this->Research_model->get_by_id($id);
        if (!$item) redirect('admin/research');
        $this->_validate($id);
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $file = $item->document_file;
        if ($this->input->post('remove_file')) {
            $this->_safe_unlink('research/' . $file);
            $file = NULL;
        }
        if (!empty($_FILES['document_file']['name'])) {
            $new = $this->_handle_upload();
            if ($new === 'error') { $this->edit($id); return; }
            $this->_safe_unlink('research/' . $file);
            $file = $new;
        }

        $this->Research_model->update($id, $this->_payload($file));
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_research', 'Edit riset: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Riset berhasil diperbarui!');
        redirect('admin/research');
    }

    public function delete($id) {
        $item = $this->Research_model->get_by_id($id);
        if ($item) {
            $this->_safe_unlink('research/' . $item->document_file);
            $this->Research_model->delete($id);
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_research', 'Hapus riset: ' . $item->title);
            $this->session->set_flashdata('success', 'Riset berhasil dihapus.');
        }
        redirect('admin/research');
    }

    // ===== 🔥 FITUR GILA: Duplicate riset =====
    public function duplicate($id) {
        $item = $this->Research_model->get_by_id($id);
        if (!$item) show_404();
        $new = clone $item;
        unset($new->id);
        $new->title = $item->title . ' (Salinan)';
        $new->status = 'proposed';
        $new->document_file = null;
        $this->Research_model->insert((array)$new);
        $new_id = $this->db->insert_id();
        $this->session->set_flashdata('success', 'Riset berhasil diduplikasi sebagai proposal baru.');
        redirect('admin/research/edit/' . $new_id);
    }

    // ===== 🔥 FITUR GILA: Bulk status update =====
    public function bulk_status() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');
        if (!is_array($ids) || !in_array($action, $this->valid_statuses)) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/research'); return;
        }
        $this->db->where_in('id', $ids)->update('research', ['status' => $action]);
        $count = $this->db->affected_rows();
        $this->session->set_flashdata('success', "{$count} riset berhasil di-update ke {$action}.");
        redirect('admin/research');
    }

    // ===== 🔥 FITUR GILA: Stats per dosen =====
    public function lecturer_stats() {
        if (!$this->input->is_ajax_request()) show_404();
        $stats = $this->db->select('lecturers.id, lecturers.name, 
            SUM(CASE WHEN research.type = "research" THEN 1 ELSE 0 END) as research_count,
            SUM(CASE WHEN research.type = "community_service" THEN 1 ELSE 0 END) as service_count,
            SUM(CASE WHEN research.status = "ongoing" THEN 1 ELSE 0 END) as ongoing_count', FALSE)
            ->join('research', 'research.lecturer_id = lecturers.id', 'left')
            ->where('lecturers.is_active', 1)
            ->group_by('lecturers.id')
            ->order_by('research_count', 'DESC')
            ->limit(20)
            ->get('lecturers')->result();
        $this->output->set_content_type('application/json')->set_output(json_encode($stats));
    }

    // ===== 🔥 FITUR GILA: Export riset dosen ke CSV =====
    public function export_csv($lecturer_id = NULL) {
        if (!$lecturer_id) redirect('admin/research');
        $lecturer = $this->db->get_where('lecturers', ['id' => $lecturer_id])->row();
        if (!$lecturer) show_404();

        $items = $this->db->where('lecturer_id', $lecturer_id)
            ->order_by('year', 'DESC')->get('research')->result();

        $filename = 'riset-' . url_title($lecturer->name, '-', true) . '.csv';
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Tahun', 'Judul', 'Tipe', 'Status', 'Sumber Dana', 'Dosen']);
        foreach ($items as $r) {
            fputcsv($output, [
                $r->year,
                $r->title,
                $r->type === 'research' ? 'Penelitian' : 'Pengabdian',
                $r->status,
                $r->funding_source ?? '-',
                $lecturer->name,
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        force_download($filename, $csv);
    }

    private function _validate($exclude_id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Judul', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('lecturer_id', 'Dosen', 'required|integer');
        $this->form_validation->set_rules('year', 'Tahun', 'required|integer|greater_than_equal_to[1990]|less_than_equal_to[' . (date('Y') + 1) . ']');
        $this->form_validation->set_rules('funding_source', 'Sumber Dana', 'trim|max_length[150]');
        $this->form_validation->set_rules('amount', 'Jumlah Dana', 'trim|numeric');
        $this->form_validation->set_rules('abstract', 'Abstrak', 'trim|max_length[5000]');
        $this->form_validation->set_rules('type', 'Tipe', 'required|in_list[research,community_service]');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[proposed,ongoing,completed]');
    }

    private function _payload($file) {
        $type = $this->input->post('type', TRUE);
        $status = $this->input->post('status', TRUE);
        return [
            'lecturer_id' => (int)$this->input->post('lecturer_id'),
            'title' => $this->input->post('title', TRUE),
            'type' => in_array($type, $this->valid_types) ? $type : 'research',
            'year' => (int)$this->input->post('year'),
            'funding_source' => $this->input->post('funding_source', TRUE),
            'amount' => $this->input->post('amount') ?: NULL,
            'abstract' => $this->input->post('abstract'),
            'status' => in_array($status, $this->valid_statuses) ? $status : 'ongoing',
            'document_file' => $file,
        ];
    }

    // ===== Upload dengan MIME check + chmod 0755 =====
    private function _handle_upload() {
        if (empty($_FILES['document_file']['name'])) return NULL;
        $path = FCPATH . 'assets/uploads/research/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'pdf|doc|docx',
            'max_size' => 10240, // 10MB
            'encrypt_name' => TRUE,
        ]);
        if ($this->upload->do_upload('document_file')) {
            $updata = $this->upload->data();
            $allowed_mime = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            if (!in_array($updata['file_type'], $allowed_mime, true)) {
                @unlink($updata['full_path']);
                $this->session->set_flashdata('error', 'Tipe file tidak valid. Gunakan PDF, DOC, atau DOCX.');
                return 'error';
            }
            return $updata['file_name'];
        }
        $this->session->set_flashdata('error', 'Upload gagal: ' . strip_tags($this->upload->display_errors('', '')));
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