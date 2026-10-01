<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    private $keys = [
        'faculty_history', 'faculty_vision', 'faculty_mission', 'dean_message',
        'dean_name', 'wadek_1_name', 'wadek_2_name', 'wadek_3_name', 'tu_head_name',
        'wadek_label', 'org_layout',
        'faculty_milestones', 'core_values', 'dean_priorities',
    ];

    // 🔥 FIX: semua 4 foto terdaftar (dean + 3 wadek)
    private $photo_fields = [
        'dean_photo'    => 'profile/dean',
        'wadek_1_photo' => 'profile/wadek',
        'wadek_2_photo' => 'profile/wadek',
        'wadek_3_photo' => 'profile/wadek',
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Setting_model');
    }

    public function index() {
        $data = ['title' => 'Edit Profil Fakultas', 'active_menu' => 'profile', 'settings' => []];
        foreach ($this->keys as $k) $data['settings'][$k] = $this->Setting_model->get($k, '');
        foreach (array_keys($this->photo_fields) as $k) $data['settings'][$k] = $this->Setting_model->get($k, '');

        $data['programs'] = $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('study_programs')->result();

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/profile/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('dean_name', 'Nama Dekan', 'trim|max_length[150]');
        $this->form_validation->set_rules('wadek_1_name', 'Wadek 1', 'trim|max_length[150]');
        $this->form_validation->set_rules('wadek_2_name', 'Wadek 2', 'trim|max_length[150]');
        $this->form_validation->set_rules('wadek_3_name', 'Wadek 3', 'trim|max_length[150]');
        $this->form_validation->set_rules('tu_head_name', 'Kepala TU', 'trim|max_length[150]');
        $this->form_validation->set_rules('faculty_vision', 'Visi', 'trim|max_length[1000]');
        $this->form_validation->set_rules('faculty_history', 'Sejarah', 'trim|max_length[5000]');
        $this->form_validation->set_rules('faculty_mission', 'Misi', 'trim|max_length[2000]');
        $this->form_validation->set_rules('dean_message', 'Sambutan Dekan', 'trim|max_length[3000]');
        $this->form_validation->set_rules('org_layout', 'Layout Organisasi', 'in_list[tiered,flat]');

        foreach (['faculty_milestones', 'core_values', 'dean_priorities'] as $json_key) {
            $this->form_validation->set_rules($json_key, ucfirst(str_replace('_', ' ', $json_key)), 'callback__validate_json');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors('<p>• ', '</p>'));
            redirect('admin/profile'); return;
        }

        // 1. Text settings
        foreach ($this->keys as $k) {
            if ($this->input->post($k) !== NULL) {
                $this->Setting_model->set($k, $this->input->post($k, FALSE));
            }
        }

        // 2. 🔥 Foto Dekan + 3 Wadek (semua tersimpan!)
        foreach ($this->photo_fields as $setting_key => $subdir) {
            if (!empty($_FILES[$setting_key]['name'])) {
                $new_path = $this->_handle_upload($setting_key, $subdir);
                if ($new_path === 'error') continue;
                if ($new_path) {
                    $this->_safe_unlink($this->Setting_model->get($setting_key, ''));
                    $this->Setting_model->set($setting_key, $new_path);
                }
            }
            if ($this->input->post('remove_' . $setting_key)) {
                $this->_safe_unlink($this->Setting_model->get($setting_key, ''));
                $this->Setting_model->set($setting_key, '');
            }
        }

        // 3. Nama Kaprodi (ke tabel study_programs)
        $kaprodi = $this->input->post('kaprodi');
        if (is_array($kaprodi)) {
            foreach ($kaprodi as $pid => $arr) {
                $name = trim($arr['name'] ?? '');
                if (strlen($name) > 150) $name = substr($name, 0, 150);
                $this->db->where('id', (int)$pid)->update('study_programs', [
                    'head_of_study_program' => $name,
                ]);
            }
        }

        // 4. Foto Kaprodi
        if (!empty($_FILES['kaprodi_photo']['name'])) {
            foreach ($_FILES['kaprodi_photo']['name'] as $pid => $fname) {
                if (empty($fname)) continue;
                $_FILES['kphoto'] = [
                    'name'     => $fname,
                    'type'     => $_FILES['kaprodi_photo']['type'][$pid],
                    'tmp_name' => $_FILES['kaprodi_photo']['tmp_name'][$pid],
                    'error'    => $_FILES['kaprodi_photo']['error'][$pid],
                    'size'     => $_FILES['kaprodi_photo']['size'][$pid],
                ];
                $path = FCPATH . 'assets/uploads/programs/';
                if (!is_dir($path)) @mkdir($path, 0755, TRUE);
                $this->load->library('upload');
                $this->upload->initialize([
                    'upload_path' => $path, 'allowed_types' => 'jpg|jpeg|png|webp',
                    'max_size' => 2048, 'encrypt_name' => TRUE,
                ]);
                if ($this->upload->do_upload('kphoto')) {
                    $updata = $this->upload->data();
                    $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    if (!in_array($updata['file_type'], $allowed_mime, true)) {
                        @unlink($updata['full_path']);
                        continue;
                    }
                    $new = 'programs/' . $updata['file_name'];
                    $old = $this->db->get_where('study_programs', ['id' => (int)$pid])->row();
                    if ($old && !empty($old->head_photo)) {
                        $this->_safe_unlink($old->head_photo);
                    }
                    $this->db->where('id', (int)$pid)->update('study_programs', ['head_photo' => $new]);
                }
            }
        }

        // 5. Hapus foto Kaprodi
        $removes = $this->input->post('remove_kaprodi_photo');
        if (is_array($removes)) {
            foreach ($removes as $pid => $v) {
                if (!$v) continue;
                $old = $this->db->get_where('study_programs', ['id' => (int)$pid])->row();
                if ($old && !empty($old->head_photo)) {
                    $this->_safe_unlink($old->head_photo);
                }
                $this->db->where('id', (int)$pid)->update('study_programs', ['head_photo' => NULL]);
            }
        }

        // Log aktivitas (aman — tidak crash walau Auth_model belum dimuat)
        if (file_exists(APPPATH . 'models/Auth_model.php')) {
            $this->load->model('Auth_model');
            $this->Auth_model->log_activity(
                $this->session->userdata('user_id'),
                'update_profile',
                'Update profil fakultas'
            );
        }

        $this->session->set_flashdata('success', 'Profil fakultas berhasil diperbarui!');
        redirect('admin/profile');
    }

    // ===== 🔥 Preview struktur organisasi (dipanggil dari tombol preview) =====
    public function preview() {
        $data = ['settings' => []];
        foreach ($this->keys as $k) $data['settings'][$k] = $this->Setting_model->get($k, '');
        foreach (array_keys($this->photo_fields) as $k) $data['settings'][$k] = $this->Setting_model->get($k, '');
        $data['programs'] = $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('study_programs')->result();

        $this->load->view('templates/admin_header', ['title' => 'Preview Struktur']);
        $this->load->view('admin/profile/preview', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== 🔥 Test JSON (AJAX) =====
    public function test_json() {
        if (!$this->input->is_ajax_request()) show_404();
        $key = $this->input->post('key');
        $value = $this->input->post('value');
        
        if ($value === '' || $value === '[]') {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => true, 'count' => 0, 'errors' => []]));
            return;
        }
        
        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false, 'count' => 0, 'errors' => ['JSON tidak valid']]));
            return;
        }
        
        $errors = [];
        if ($key === 'faculty_milestones') {
            foreach ($decoded as $i => $ms) {
                if (empty($ms['year']))  $errors[] = "Item #" . ($i+1) . ": year kosong";
                if (empty($ms['title'])) $errors[] = "Item #" . ($i+1) . ": title kosong";
            }
        } elseif ($key === 'core_values') {
            foreach ($decoded as $i => $cv) {
                if (empty($cv['title'])) $errors[] = "Item #" . ($i+1) . ": title kosong";
                if (empty($cv['icon']))  $errors[] = "Item #" . ($i+1) . ": icon kosong";
            }
        } elseif ($key === 'dean_priorities') {
            foreach ($decoded as $i => $dp) {
                if (empty($dp['title'])) $errors[] = "Item #" . ($i+1) . ": title kosong";
            }
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'ok' => empty($errors),
            'count' => count($decoded),
            'errors' => array_slice($errors, 0, 5),
        ]));
    }

    public function _validate_json($value) {
        if ($value === '' || $value === '[]') return TRUE;
        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            $this->form_validation->set_message('_validate_json', 'Format JSON tidak valid pada field %s.');
            return FALSE;
        }
        return TRUE;
    }

    private function _handle_upload($input_name, $subdir) {
        $path = FCPATH . 'assets/uploads/' . $subdir;
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 3072,
            'encrypt_name'  => TRUE,
        ]);
        if ($this->upload->do_upload($input_name)) {
            $updata = $this->upload->data();
            $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($updata['file_type'], $allowed_mime, true)) {
                @unlink($updata['full_path']);
                $this->session->set_flashdata('error', "Tipe file {$input_name} tidak valid (MIME: {$updata['file_type']}).");
                return 'error';
            }
            return $subdir . '/' . $updata['file_name'];
        }
        $this->session->set_flashdata('error', "Upload {$input_name} gagal: " . strip_tags($this->upload->display_errors('', '')));
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