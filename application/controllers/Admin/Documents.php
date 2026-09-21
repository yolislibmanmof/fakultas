<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends CI_Controller {

    private $categories = ['akademik' => 'Akademik', 'kemahasiswaan' => 'Kemahasiswaan', 'keuangan' => 'Keuangan', 'umum' => 'Umum'];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model(['Document_model', 'Auth_model']);
        $this->load->library('upload');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        $q   = trim((string)$this->input->get('q'));
        $cat = $this->input->get('cat');

        // 🔥 FIX: Hitung per kategori di DB (bukan filter PHP setelah get_all)
        $cat_counts = [];
        foreach ($this->categories as $key => $label) {
            $cat_counts[$key] = $this->db->where('category', $key)->count_all_results('documents');
        }
        $total_all = $this->db->count_all('documents');

        // Filter di DB (lebih cepat)
        $this->db->select('*')->from('documents');
        if ($cat && array_key_exists($cat, $this->categories)) {
            $this->db->where('category', $cat);
        }
        if ($q !== '') {
            $this->db->group_start()
                ->like('title', $q)
                ->or_like('file_name', $q)
                ->or_like('description', $q)
                ->group_end();
        }
        $this->db->order_by('id', 'DESC')->limit(500);
        $docs = $this->db->get()->result();

        $data = [
            'title' => 'Download Center', 'active_menu' => 'documents',
            'documents' => $docs, 'categories' => $this->categories,
            'cat_counts' => $cat_counts, 'q' => $q, 'cat' => $cat,
            'total_all' => $total_all,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/documents/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = ['title' => 'Upload Dokumen', 'active_menu' => 'documents', 'document' => NULL, 'categories' => $this->categories];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/documents/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }
        if (empty($_FILES['userfile']['name'])) {
            $this->session->set_flashdata('error', 'Silakan pilih file untuk diupload.');
            redirect('admin/documents/create'); return;
        }
        $up = $this->_handle_upload();
        if ($up === 'error') redirect('admin/documents/create');

        $this->Document_model->insert([
            'category' => $this->_safe_category(),
            'title' => $this->input->post('title', TRUE),
            'description' => $this->input->post('description', TRUE),
            'file_path' => $up['file_name'],
            'file_name' => $up['orig_name'],
            'file_size' => $up['file_size'],
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ]);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_document', 'Upload dokumen: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Dokumen berhasil diupload!');
        redirect('admin/documents');
    }

    public function edit($id) {
        $document = $this->Document_model->get_by_id($id);
        if (!$document) redirect('admin/documents');
        $data = ['title' => 'Edit Dokumen', 'active_menu' => 'documents', 'document' => $document, 'categories' => $this->categories];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/documents/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $document = $this->Document_model->get_by_id($id);
        if (!$document) redirect('admin/documents');
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $file_path = $document->file_path;
        $file_name = $document->file_name;
        $file_size = $document->file_size;

        if (!empty($_FILES['userfile']['name'])) {
            $up = $this->_handle_upload();
            if ($up === 'error') redirect('admin/documents/edit/' . $id);
            $old = FCPATH . 'assets/uploads/documents/' . $file_path;
            if (file_exists($old)) @unlink($old);
            $file_path = $up['file_name']; $file_name = $up['orig_name']; $file_size = $up['file_size'];
        }

        $this->Document_model->update($id, [
            'category' => $this->_safe_category(),
            'title' => $this->input->post('title', TRUE),
            'description' => $this->input->post('description', TRUE),
            'file_path' => $file_path,
            'file_name' => $file_name,
            'file_size' => $file_size,
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ]);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_document', 'Edit dokumen: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Dokumen berhasil diperbarui!');
        redirect('admin/documents');
    }

    public function delete($id) {
        $document = $this->Document_model->get_by_id($id);
        if ($document) {
            $old = FCPATH . 'assets/uploads/documents/' . $document->file_path;
            if (file_exists($old)) @unlink($old);
            $this->Document_model->delete($id);
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_document', 'Hapus dokumen: ' . $document->title);
            $this->session->set_flashdata('success', 'Dokumen berhasil dihapus.');
        }
        redirect('admin/documents');
    }

    // ===== 🔥 FITUR GILA: Bulk delete =====
    public function bulk_delete() {
        $ids = $this->input->post('ids');
        if (!is_array($ids) || empty($ids)) {
            $this->session->set_flashdata('error', 'Tidak ada dokumen yang dipilih.');
            redirect('admin/documents'); return;
        }
        $count = 0;
        foreach ($ids as $id) {
            $document = $this->Document_model->get_by_id((int)$id);
            if ($document) {
                $old = FCPATH . 'assets/uploads/documents/' . $document->file_path;
                if (file_exists($old)) @unlink($old);
                $this->Document_model->delete((int)$id);
                $count++;
            }
        }
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'bulk_delete_documents', "Hapus {$count} dokumen");
        $this->session->set_flashdata('success', "{$count} dokumen berhasil dihapus.");
        redirect('admin/documents');
    }

    // ===== 🔥 FITUR GILA: Bulk toggle active status =====
    public function bulk_toggle() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action'); // 'activate' or 'deactivate'
        if (!is_array($ids) || empty($ids) || !in_array($action, ['activate', 'deactivate'])) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/documents'); return;
        }
        $status = ($action === 'activate') ? 1 : 0;
        $this->db->where_in('id', $ids)->update('documents', ['is_active' => $status]);
        $count = $this->db->affected_rows();
        $this->Auth_model->log_activity($this->session->userdata('user_id'), "bulk_{$action}_documents", "Toggle {$count} dokumen");
        $this->session->set_flashdata('success', "{$count} dokumen berhasil di-{$action}.");
        redirect('admin/documents');
    }

    // ===== 🔥 FITUR GILA: Reset download counter =====
    public function reset_counter($id) {
        $document = $this->Document_model->get_by_id($id);
        if (!$document) { show_404(); }
        $this->db->where('id', $id)->update('documents', ['download_count' => 0]);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'reset_document_counter', 'Reset counter: ' . $document->title);
        $this->session->set_flashdata('success', 'Counter download berhasil direset.');
        redirect('admin/documents');
    }

    private function _validate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Judul', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[1000]');
    }

    private function _safe_category() {
        $c = $this->input->post('category', TRUE);
        return array_key_exists($c, $this->categories) ? $c : 'umum';
    }

    // ===== 🔥 FIX: Upload dengan MIME check + chmod 0755 + error reporting =====
    private function _handle_upload() {
        $path = FCPATH . 'assets/uploads/documents/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar',
            'max_size' => 10240, // 10MB (naik dari 5MB)
            'encrypt_name' => TRUE,
        ]);
        if ($this->upload->do_upload('userfile')) {
            $d = $this->upload->data();

            // MIME validation
            $allowed_mime = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip',
                'application/x-rar-compressed',
                'application/octet-stream', // fallback untuk zip/rar
            ];
            if (!in_array($d['file_type'], $allowed_mime, true)) {
                @unlink($d['full_path']);
                $this->session->set_flashdata('error', 'Tipe file tidak valid (MIME: ' . $d['file_type'] . '). Gunakan PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, atau RAR.');
                return 'error';
            }

            return ['file_name' => $d['file_name'], 'orig_name' => $d['orig_name'], 'file_size' => $d['file_size']];
        }
        $this->session->set_flashdata('error', 'Upload gagal: ' . strip_tags($this->upload->display_errors('', '')));
        return 'error';
    }
}