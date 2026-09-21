<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends CI_Controller {

    private $valid_statuses = ['draft', 'published', 'archived'];
    private $valid_types    = ['news', 'announcement', 'event'];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->library('upload');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        $status = $this->input->get('status');
        $type   = $this->input->get('type');
        $q      = trim((string)$this->input->get('q'));
        $cat    = $this->input->get('cat');

        $this->db->select('posts.*, categories.name as category_name, users.full_name as author')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left');

        if ($status && in_array($status, $this->valid_statuses)) $this->db->where('posts.status', $status);
        if ($type && in_array($type, $this->valid_types)) $this->db->where('posts.type', $type);
        if ($cat) $this->db->where('posts.category_id', (int)$cat);
        if ($q !== '') {
            $this->db->group_start()
                ->like('posts.title', $q)
                ->or_like('posts.excerpt', $q)
                ->or_like('users.full_name', $q)
                ->group_end();
        }
        $this->db->order_by('posts.created_at', 'DESC')->limit(500);

        $posts = $this->db->get()->result();

        // Counts (1 query gabungan)
        $counts_raw = $this->db->select('status, COUNT(*) as c', FALSE)
            ->group_by('status')->get('posts')->result();
        $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
        foreach ($counts_raw as $r) if (isset($counts[$r->status])) $counts[$r->status] = (int)$r->c;
        $counts['all'] = array_sum($counts);

        // Category counts
        $cat_counts_raw = $this->db->select('category_id, COUNT(*) as c', FALSE)
            ->group_by('category_id')->get('posts')->result();
        $cat_counts = [];
        foreach ($cat_counts_raw as $r) $cat_counts[(int)$r->category_id] = (int)$r->c;

        $data = [
            'title'       => 'Manajemen Berita & Pengumuman',
            'active_menu' => 'posts',
            'posts'       => $posts,
            'status'      => $status, 'type' => $type, 'q' => $q, 'cat' => $cat,
            'categories'  => $this->db->order_by('name', 'ASC')->get('categories')->result(),
            'counts'      => $counts, 'cat_counts' => $cat_counts,
        ];

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/posts/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title' => 'Tulis Berita Baru', 'active_menu' => 'posts',
            'post' => NULL,
            'categories' => $this->db->order_by('name', 'ASC')->get('categories')->result(),
            'statuses' => $this->valid_statuses, 'types' => $this->valid_types,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/posts/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $image = $this->_upload();
        if ($image === 'error') { $this->create(); return; }

        $status = $this->input->post('status');
        if (!in_array($status, $this->valid_statuses)) $status = 'draft';

        $this->db->insert('posts', [
            'title'          => $this->input->post('title', TRUE),
            'slug'           => $this->_unique_slug($this->input->post('title', TRUE)),
            'category_id'    => $this->input->post('category_id') ?: NULL,
            'user_id'        => $this->session->userdata('user_id'),
            'type'           => in_array($this->input->post('type'), $this->valid_types) ? $this->input->post('type') : 'news',
            'status'         => $status,
            'excerpt'        => $this->input->post('excerpt'),
            'content'        => $this->input->post('content'),
            'featured_image' => $image,
            'published_at'   => $status == 'published' ? date('Y-m-d H:i:s') : NULL,
        ]);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_post', 'Tulis berita: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Berita berhasil disimpan!');
        redirect('admin/posts');
    }

    public function edit($id) {
        $post = $this->db->get_where('posts', ['id' => $id])->row();
        if (!$post) redirect('admin/posts');

        $data = [
            'title' => 'Edit Berita', 'active_menu' => 'posts',
            'post' => $post,
            'categories' => $this->db->order_by('name', 'ASC')->get('categories')->result(),
            'statuses' => $this->valid_statuses, 'types' => $this->valid_types,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/posts/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $post = $this->db->get_where('posts', ['id' => $id])->row();
        if (!$post) redirect('admin/posts');

        $this->_validate($id);
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $image = $post->featured_image;
        if ($this->input->post('remove_image')) {
            $this->_safe_unlink($image);
            $image = NULL;
        }
        if (!empty($_FILES['featured_image']['name'])) {
            $new = $this->_upload();
            if ($new === 'error') { $this->edit($id); return; }
            $this->_safe_unlink($image);
            $image = $new;
        }

        $status = $this->input->post('status');
        if (!in_array($status, $this->valid_statuses)) $status = $post->status;

        $this->db->where('id', $id)->update('posts', [
            'title'          => $this->input->post('title', TRUE),
            'slug'           => $this->_unique_slug($this->input->post('title', TRUE), $id),
            'category_id'    => $this->input->post('category_id') ?: NULL,
            'type'           => in_array($this->input->post('type'), $this->valid_types) ? $this->input->post('type') : 'news',
            'status'         => $status,
            'excerpt'        => $this->input->post('excerpt'),
            'content'        => $this->input->post('content'),
            'featured_image' => $image,
            'published_at'   => ($status == 'published' && !$post->published_at) ? date('Y-m-d H:i:s') : $post->published_at,
        ]);

        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_post', 'Edit berita: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Berita berhasil diperbarui!');
        redirect('admin/posts');
    }

    public function delete($id) {
        $post = $this->db->get_where('posts', ['id' => $id])->row();
        if ($post) {
            $this->_safe_unlink($post->featured_image);
            $this->db->where('id', $id)->delete('posts');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_post', 'Hapus berita: ' . $post->title);
            $this->session->set_flashdata('success', 'Berita berhasil dihapus.');
        }
        redirect('admin/posts');
    }

    // ===== FITUR GILA: Preview =====
    public function preview($id) {
        $post = $this->db
            ->select('posts.*, categories.name as category_name, users.full_name as author')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.id', $id)
            ->get('posts')->row();
        if (!$post) show_404();
        $this->load->view('admin/posts/preview', ['post' => $post]);
    }

    // ===== FITUR GILA: Duplicate =====
    public function duplicate($id) {
        $post = $this->db->get_where('posts', ['id' => $id])->row();
        if (!$post) show_404();
        $new = clone $post;
        unset($new->id);
        $new->title = $post->title . ' (Salinan)';
        $new->slug = $this->_unique_slug($new->title);
        $new->status = 'draft';
        $new->published_at = NULL;
        $new->views = 0;
        $new->featured_image = null;
        $this->db->insert('posts', (array)$new);
        $new_id = $this->db->insert_id();
        $this->session->set_flashdata('success', 'Berita berhasil diduplikasi sebagai draft.');
        redirect('admin/posts/edit/' . $new_id);
    }

    // ===== FITUR GILA: Bulk publish/archive/delete =====
    public function bulk_action() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');
        if (!is_array($ids) || empty($ids)) {
            $this->session->set_flashdata('error', 'Tidak ada berita yang dipilih.');
            redirect('admin/posts'); return;
        }

        $count = 0;
        if ($action === 'publish') {
            $this->db->where_in('id', $ids)->where('published_at IS NULL')->update('posts', ['published_at' => date('Y-m-d H:i:s')]);
            $this->db->where_in('id', $ids)->update('posts', ['status' => 'published']);
            $count = $this->db->affected_rows();
        } elseif ($action === 'draft') {
            $this->db->where_in('id', $ids)->update('posts', ['status' => 'draft']);
            $count = $this->db->affected_rows();
        } elseif ($action === 'archive') {
            $this->db->where_in('id', $ids)->update('posts', ['status' => 'archived']);
            $count = $this->db->affected_rows();
        } elseif ($action === 'delete') {
            foreach ($ids as $id) {
                $post = $this->db->get_where('posts', ['id' => (int)$id])->row();
                if ($post) {
                    $this->_safe_unlink($post->featured_image);
                    $this->db->where('id', (int)$id)->delete('posts');
                    $count++;
                }
            }
        }

        $this->Auth_model->log_activity($this->session->userdata('user_id'), "bulk_{$action}_posts", "Bulk {$action}: {$count} berita");
        $this->session->set_flashdata('success', "{$count} berita berhasil di-{$action}.");
        redirect('admin/posts');
    }

    // ===== FITUR GILA: AI generate excerpt =====
    public function ai_excerpt() {
        if (!$this->input->is_ajax_request()) show_404();
        $content = trim(strip_tags((string)$this->input->post('content')));
        $title   = trim((string)$this->input->post('title'));

        // Kalau judul DAN konten kosong → tolak
        if ($content === '' && $title === '') {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['ok' => FALSE, 'message' => 'Isi judul atau konten terlebih dahulu.']));
            return;
        }

        if ($content !== '') {
            // Ada konten → potong rapi 200 karakter
            $excerpt = trim(substr($content, 0, 200));
            if (strlen($content) > 200) {
                $last_space = strrpos($excerpt, ' ');
                if ($last_space !== FALSE) $excerpt = substr($excerpt, 0, $last_space);
                $excerpt .= '...';
            }
        } else {
            // Konten kosong → generate dari JUDUL
            $excerpt = $title . ' — Informasi resmi dari ' . site_name() . '. Simak detail lengkap, tanggal penting, dan panduan terkait pada pengumuman ini.';
        }

        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => TRUE, 'excerpt' => $excerpt]));
    }

    // ===== FITUR GILA: Pin/unpin (featured) =====
    public function toggle_pin($id) {
        if (!$this->input->is_ajax_request()) show_404();
        if (!$this->db->field_exists('is_pinned', 'posts')) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false, 'message' => 'Kolom is_pinned belum ada']));
            return;
        }
        $post = $this->db->get_where('posts', ['id' => $id])->row();
        if (!$post) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['ok' => false]));
            return;
        }
        $new_val = $post->is_pinned ? 0 : 1;
        $this->db->where('id', $id)->update('posts', ['is_pinned' => $new_val]);
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['ok' => true, 'pinned' => $new_val]));
    }

    private function _validate($exclude_id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Judul', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('excerpt', 'Ringkasan', 'trim|max_length[500]');
        $this->form_validation->set_rules('content', 'Konten', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,published,archived]');
        $this->form_validation->set_rules('type', 'Tipe', 'required|in_list[news,announcement,event]');
        $this->form_validation->set_rules('category_id', 'Kategori', 'integer');
    }

    private function _unique_slug($title, $exclude_id = NULL) {
        $base = url_title(strtolower(trim($title)), '-', TRUE);
        if ($base === '') $base = 'post';
        $slug = $base; $i = 2;
        $max_iterations = 1000;
        while ($max_iterations-- > 0) {
            $this->db->where('slug', $slug);
            if ($exclude_id) $this->db->where('id !=', $exclude_id);
            if ($this->db->count_all_results('posts') == 0) return $slug;
            $slug = $base . '-' . $i; $i++;
        }
        // Fallback dengan timestamp jika 1000 iterasi gagal
        return $base . '-' . time();
    }

    private function _upload() {
        if (empty($_FILES['featured_image']['name'])) return NULL;
        $path = FCPATH . 'assets/uploads/posts/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 3072,
            'encrypt_name' => TRUE,
        ]);
        if ($this->upload->do_upload('featured_image')) {
            $updata = $this->upload->data();
            $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($updata['file_type'], $allowed_mime, true)) {
                @unlink($updata['full_path']);
                $this->session->set_flashdata('error', 'Tipe file foto tidak valid.');
                return 'error';
            }
            return 'posts/' . $updata['file_name'];
        }
        $this->session->set_flashdata('error', 'Upload gambar gagal: ' . strip_tags($this->upload->display_errors('', '')));
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