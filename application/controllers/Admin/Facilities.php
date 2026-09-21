<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Facilities extends CI_Controller {

    private $types = [
        'laboratory' => 'Laboratorium',
        'classroom'  => 'Ruang Kelas',
        'library'    => 'Perpustakaan',
        'mosque'     => 'Tempat Ibadah',
        'sport'      => 'Fasilitas Olahraga',
        'other'      => 'Lainnya',
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->helper(['url', 'text', 'form']);
        $this->load->library('form_validation');
    }

    public function index() {
        $type = $this->input->get('type');
        if ($type && array_key_exists($type, $this->types)) $this->db->where('type', $type);
        $this->db->order_by('name', 'ASC');

        $data = [
            'title'       => 'Manajemen Fasilitas',
            'active_menu' => 'facilities',
            'facilities'  => $this->db->get('facilities')->result(),
            'types'       => $this->types,
            'filter_type' => $type,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/facilities/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title'       => 'Tambah Fasilitas',
            'active_menu' => 'facilities',
            'facility'    => NULL,
            'types'       => $this->types,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/facilities/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        if (!$this->_validate()) {
            $this->session->set_flashdata('error', validation_errors('<p>', '</p>'));
            redirect('admin/facilities/create'); return;
        }
        $images = $this->_upload_images();
        $this->db->insert('facilities', $this->_payload($images));
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_facility', 'Tambah fasilitas: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Fasilitas berhasil ditambahkan!');
        redirect('admin/facilities');
    }

    public function edit($id) {
        $facility = $this->db->get_where('facilities', ['id' => $id])->row();
        if (!$facility) redirect('admin/facilities');
        $data = [
            'title'       => 'Edit Fasilitas',
            'active_menu' => 'facilities',
            'facility'    => $facility,
            'types'       => $this->types,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/facilities/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $facility = $this->db->get_where('facilities', ['id' => $id])->row();
        if (!$facility) redirect('admin/facilities');

        if (!$this->_validate()) {
            $this->session->set_flashdata('error', validation_errors('<p>', '</p>'));
            redirect('admin/facilities/edit/' . $id); return;
        }

        $existing = json_decode((string)$facility->images, true) ?: [];
        $remove = $this->input->post('remove_images') ?: [];
        $kept = [];
        foreach ($existing as $idx => $img) {
            if (in_array((string)$idx, $remove)) {
                $this->_safe_unlink($img);
            } else {
                $kept[] = $img;
            }
        }
        $new = $this->_upload_images();
        $this->db->where('id', $id)->update('facilities', $this->_payload(array_merge($kept, $new)));

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_facility', 'Edit fasilitas: ' . $this->input->post('name', TRUE));
        $this->session->set_flashdata('success', 'Fasilitas berhasil diperbarui!');
        redirect('admin/facilities');
    }

    public function delete($id) {
        $facility = $this->db->get_where('facilities', ['id' => $id])->row();
        if ($facility) {
            foreach (json_decode((string)$facility->images, true) ?: [] as $img) $this->_safe_unlink($img);
            $this->db->where('id', $id)->delete('facilities');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_facility', 'Hapus fasilitas: ' . $facility->name);
            $this->session->set_flashdata('success', 'Fasilitas berhasil dihapus.');
        }
        redirect('admin/facilities');
    }

    public function duplicate($id) {
        $facility = $this->db->get_where('facilities', ['id' => $id])->row();
        if (!$facility) redirect('admin/facilities');

        $data = [
            'name'        => $facility->name . ' (Copy)',
            'type'        => $facility->type,
            'location'    => $facility->location,
            'capacity'    => $facility->capacity,
            'description' => $facility->description,
            'is_active'   => 0,
            'images'      => $facility->images,
        ];
        $this->db->insert('facilities', $data);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'duplicate_facility', 'Duplikat: ' . $facility->name);
        $this->session->set_flashdata('success', 'Fasilitas berhasil diduplikat (status: nonaktif).');
        redirect('admin/facilities');
    }

    public function bulk_toggle() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');

        if (!is_array($ids) || empty($ids)) redirect('admin/facilities');
        if (!in_array($action, ['activate', 'deactivate'])) {
            $this->session->set_flashdata('error', 'Aksi tidak valid.');
            redirect('admin/facilities'); return;
        }

        $status = $action === 'activate' ? 1 : 0;
        $this->db->where_in('id', $ids)->update('facilities', ['is_active' => $status]);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'bulk_' . $action, 'Bulk ' . $action . ' ' . count($ids) . ' fasilitas');
        $this->session->set_flashdata('success', count($ids) . ' fasilitas berhasil di' . ($action === 'activate' ? 'aktifkan' : 'nonaktifkan') . '.');
        redirect('admin/facilities');
    }

    private function _validate() {
        $this->form_validation->set_rules('name', 'Nama Fasilitas', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('type', 'Tipe Fasilitas', 'required|in_list[' . implode(',', array_keys($this->types)) . ']');
        $this->form_validation->set_rules('location', 'Lokasi', 'trim|max_length[200]');
        $this->form_validation->set_rules('capacity', 'Kapasitas', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[99999]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[2000]');
        return $this->form_validation->run();
    }

    private function _payload($images) {
        return [
            'name'        => $this->input->post('name', TRUE),
            'type'        => $this->input->post('type'),
            'location'    => $this->input->post('location', TRUE),
            'capacity'    => $this->input->post('capacity') !== NULL && $this->input->post('capacity') !== '' ? (int)$this->input->post('capacity') : NULL,
            'description' => $this->input->post('description'),
            'is_active'   => $this->input->post('is_active') ? 1 : 0,
            'images'      => json_encode(array_values($images)),
        ];
    }

    private function _upload_images() {
        $saved = [];
        if (empty($_FILES['images']['name']) || !is_array($_FILES['images']['name'])) return $saved;

        $path = FCPATH . 'assets/uploads/facilities/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->load->library('upload');

        foreach ($_FILES['images']['name'] as $i => $fname) {
            if (empty($fname)) continue;
            $_FILES['img'] = [
                'name'     => $fname,
                'type'     => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error'    => $_FILES['images']['error'][$i],
                'size'     => $_FILES['images']['size'][$i],
            ];
            $this->upload->initialize([
                'upload_path'  => $path,
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size'     => 5120,
                'encrypt_name' => TRUE,
            ]);
            if ($this->upload->do_upload('img')) {
                $saved[] = 'facilities/' . $this->upload->data('file_name');
            }
        }
        return $saved;
    }

    private function _safe_unlink($rel) {
        if (!$rel) return;
        $base = realpath(FCPATH . 'assets/uploads');
        $full = realpath(FCPATH . 'assets/uploads/' . $rel);
        if ($full && $base && strpos($full, $base) === 0 && file_exists($full)) @unlink($full);
    }
}