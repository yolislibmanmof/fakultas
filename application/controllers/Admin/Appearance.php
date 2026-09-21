<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appearance extends CI_Controller {

    private $keys = [
    'site_name', 'site_name_en', 'site_logo', 'site_favicon',
        'font_family','font_scale',
        'hero_alignment', 'section_alignment', 'content_alignment',
        'hero_animation','hero_particles','hero_label','hero_title','hero_subtitle','welcome_text','hero_photo',
        'video_tour_enabled','video_tour_type','video_tour_youtube','video_tour_title','video_tour_subtitle',
        'facebook_url','instagram_url','linkedin_url','youtube_url','twitter_url','tiktok_url',
        'contact_phone','contact_email','contact_address','contact_map_embed',
        'footer_tagline','footer_copyright'
    ];

    // PRESET THEMES (fitur gila)
    private $presets = [
        'editorial' => ['font_family' => 'editorial', 'font_scale' => 'md'],
        'classic'   => ['font_family' => 'classic',   'font_scale' => 'md'],
        'modern'    => ['font_family' => 'modern',    'font_scale' => 'lg'],
        'tech'      => ['font_family' => 'tech',      'font_scale' => 'md'],
    ];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Setting_model');
    }

    public function index() {
        $data = ['title' => 'Tampilan Website', 'active_menu' => 'appearance', 'settings' => [], 'presets' => $this->presets];
        foreach ($this->keys as $k) $data['settings'][$k] = $this->Setting_model->get($k, '');
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/appearance/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update() {
        $this->load->library('form_validation');

        // Validasi URL & email
        $this->form_validation->set_rules('contact_email', 'Email Kontak', 'trim|valid_email');
        $this->form_validation->set_rules('facebook_url',  'Facebook URL',  'trim|valid_url');
        $this->form_validation->set_rules('instagram_url', 'Instagram URL', 'trim|valid_url');
        $this->form_validation->set_rules('linkedin_url',  'LinkedIn URL',  'trim|valid_url');
        $this->form_validation->set_rules('youtube_url',   'YouTube URL',   'trim|valid_url');
        $this->form_validation->set_rules('twitter_url',   'Twitter URL',   'trim|valid_url');
        $this->form_validation->set_rules('tiktok_url',    'TikTok URL',    'trim|valid_url');
        $this->form_validation->set_rules('contact_map_embed', 'Embed Peta', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors('<p>• ', '</p>'));
            redirect('admin/appearance'); return;
        }

        $skip_upload = ['hero_photo', 'site_logo', 'site_favicon'];
        $checkboxes  = ['video_tour_enabled', 'hero_particles'];

        foreach ($this->keys as $k) {
            if (in_array($k, $skip_upload)) continue;
            if ($this->input->post($k) !== NULL) {
                $this->Setting_model->set($k, $this->input->post($k, TRUE));
            } elseif (in_array($k, $checkboxes)) {
                $this->Setting_model->set($k, '0');
            }
        }

        // Upload dengan validasi MIME + error reporting
        $upload_errors = [];
        if (!$this->_handle_upload('hero_photo', 'hero/', null, 'jpg|jpeg|png|webp|svg', $upload_errors)) return;
        if ($this->input->post('remove_photo')) $this->_safe_remove('hero_photo');

        if (!$this->_handle_upload('site_logo', 'brand/', 'site_logo', 'jpg|jpeg|png|webp|svg', $upload_errors)) return;
        if ($this->input->post('remove_logo')) $this->_safe_remove('site_logo');

        if (!$this->_handle_upload('site_favicon', 'brand/', 'site_favicon', 'ico|png|svg', $upload_errors)) return;
        if ($this->input->post('remove_favicon')) $this->_safe_remove('site_favicon');

        if (!empty($upload_errors)) {
            $this->session->set_flashdata('error', implode('<br>', $upload_errors));
            redirect('admin/appearance'); return;
        }

        $this->session->set_flashdata('success', 'Pengaturan tampilan berhasil dipublikasikan ke website!');
        redirect('admin/appearance');
    }

    // ===== FITUR GILA: Apply preset theme =====
    public function apply_preset($preset = null) {
        if (!isset($this->presets[$preset])) {
            $this->session->set_flashdata('error', 'Preset tidak valid.');
            redirect('admin/appearance'); return;
        }
        foreach ($this->presets[$preset] as $k => $v) {
            $this->Setting_model->set($k, $v);
        }
        $this->session->set_flashdata('success', 'Preset "' . ucfirst($preset) . '" berhasil diterapkan!');
        redirect('admin/appearance');
    }

    // ===== FITUR GILA: Export settings ke JSON (backup) =====
    public function export() {
        $data = [];
        foreach ($this->keys as $k) $data[$k] = $this->Setting_model->get($k, '');
        $this->load->helper('download');
        $filename = 'appearance-backup-' . date('Y-m-d-His') . '.json';
        force_download($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // ===== FITUR GILA: Import settings dari JSON =====
    public function import() {
        if (empty($_FILES['import_file']['name'])) {
            $this->session->set_flashdata('error', 'Pilih file JSON backup terlebih dahulu.');
            redirect('admin/appearance'); return;
        }
        $content = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = json_decode($content, true);
        if (!is_array($data)) {
            $this->session->set_flashdata('error', 'File JSON tidak valid.');
            redirect('admin/appearance'); return;
        }
        $count = 0;
        foreach ($data as $k => $v) {
            if (in_array($k, $this->keys) && !is_array($v)) {
                $this->Setting_model->set($k, $v);
                $count++;
            }
        }
        $this->session->set_flashdata('success', "Berhasil import {$count} pengaturan dari backup.");
        redirect('admin/appearance');
    }

    // ===== Helper: safe remove (cek path traversal) =====
    private function _safe_remove($key) {
        $old = $this->Setting_model->get($key, '');
        if ($old) {
            $real = realpath(FCPATH . 'assets/uploads/' . $old);
            $base = realpath(FCPATH . 'assets/uploads');
            if ($real && $base && strpos($real, $base) === 0 && file_exists($real)) {
                @unlink($real);
            }
            $this->Setting_model->set($key, '');
        }
    }

    // ===== Upload handler dengan MIME check + error reporting =====
    private function _handle_upload($input_name, $subdir, $setting_key = NULL, $allowed = 'jpg|jpeg|png|webp|svg', &$errors = []) {
        if (empty($_FILES[$input_name]['name'])) return true;
        $path = FCPATH . 'assets/uploads/' . $subdir;
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path'   => $path,
            'allowed_types' => $allowed,
            'max_size'      => 8192, // 8MB (naik dari 4MB)
            'encrypt_name'  => TRUE,
        ]);
        if ($this->upload->do_upload($input_name)) {
            $updata = $this->upload->data();

            // MIME validation (anti-extension spoofing)
            $allowed_mime = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/webp',
                'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon',
            ];
            if (!in_array($updata['file_type'], $allowed_mime, true)) {
                @unlink($updata['full_path']);
                $errors[] = "File {$input_name}: tipe MIME tidak valid ({$updata['file_type']}).";
                return false;
            }

            $key = $setting_key ?: $input_name;
            $old = $this->Setting_model->get($key, '');

            // Safe unlink (anti-path traversal)
            if ($old) {
                $real = realpath(FCPATH . 'assets/uploads/' . $old);
                $base = realpath(FCPATH . 'assets/uploads');
                if ($real && $base && strpos($real, $base) === 0 && file_exists($real)) {
                    @unlink($real);
                }
            }
            $this->Setting_model->set($key, $subdir . $updata['file_name']);
            return true;
        } else {
            $errors[] = "Upload {$input_name} gagal: " . strip_tags($this->upload->display_errors('', ''));
            return false;
        }
    }
}