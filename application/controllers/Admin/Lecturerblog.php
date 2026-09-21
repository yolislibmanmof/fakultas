<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lecturerblog extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Auth_model');
        $this->load->helper(['url', 'text']);
    }

    public function index() {
        $q      = trim((string)$this->input->get('q'));
        $status = $this->input->get('status');

        // 🔥 FIX: Filter di DB (bukan PHP) — jauh lebih cepat
        $this->db->select('lecturer_posts.*, lecturers.name as lecturer_name, lecturers.title_front, lecturers.photo as lecturer_photo')
            ->join('lecturers', 'lecturers.id = lecturer_posts.lecturer_id', 'left');

        if ($status && in_array($status, ['published', 'draft'])) {
            $this->db->where('lecturer_posts.status', $status);
        }
        if ($q !== '') {
            $this->db->group_start()
                ->like('lecturer_posts.title', $q)
                ->or_like('lecturers.name', $q)
                ->group_end();
        }
        $this->db->order_by('lecturer_posts.created_at', 'DESC')->limit(300);
        $items = $this->db->get('lecturer_posts')->result();

        // Counts dari DB (1 query gabungan)
        $counts_raw = $this->db->select('status, COUNT(*) as c', FALSE)
            ->group_by('status')
            ->get('lecturer_posts')->result();
        $counts = ['all' => 0, 'published' => 0, 'draft' => 0];
        foreach ($counts_raw as $r) {
            if (isset($counts[$r->status])) $counts[$r->status] = (int)$r->c;
        }
        $counts['all'] = array_sum($counts);

        $data = [
            'title' => 'Blog Dosen', 'active_menu' => 'lecturerblog',
            'posts' => $items, 'counts' => $counts, 'q' => $q, 'status' => $status,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/lecturerblog/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title' => 'Tulis Artikel Blog', 'active_menu' => 'lecturerblog',
            'post' => NULL,
            'lecturers' => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('lecturers')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/lecturerblog/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $image = $this->_upload_image();
        if ($image === 'error') redirect('admin/lecturerblog/create');

        $status = $this->input->post('status');
        $this->db->insert('lecturer_posts', [
            'lecturer_id'    => (int)$this->input->post('lecturer_id'),
            'title'          => $this->input->post('title', TRUE),
            'slug'           => $this->_unique_slug($this->input->post('title', TRUE)),
            'excerpt'        => $this->input->post('excerpt'),
            'content'        => $this->input->post('content'),
            'featured_image' => $image,
            'status'         => $status,
            'published_at'   => $status == 'published' ? date('Y-m-d H:i:s') : NULL,
        ]);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_blog', 'Tulis blog: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Artikel blog berhasil disimpan!');
        redirect('admin/lecturerblog');
    }

    public function edit($id = NULL) {
        $post = $this->db->get_where('lecturer_posts', ['id' => $id])->row();
        if (!$post) redirect('admin/lecturerblog');

        $data = [
            'title' => 'Edit Artikel Blog', 'active_menu' => 'lecturerblog',
            'post' => $post,
            'lecturers' => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('lecturers')->result(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/lecturerblog/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id = NULL) {
        $post = $this->db->get_where('lecturer_posts', ['id' => $id])->row();
        if (!$post) redirect('admin/lecturerblog');

        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $status = $this->input->post('status');
        $image = $post->featured_image;

        if ($this->input->post('remove_image')) {
            $this->_safe_unlink($image);
            $image = NULL;
        }
        $new = $this->_upload_image();
        if ($new === 'error') redirect('admin/lecturerblog/edit/' . $id);
        if ($new) {
            $this->_safe_unlink($image);
            $image = $new;
        }

        $this->db->where('id', $id)->update('lecturer_posts', [
            'lecturer_id'    => (int)$this->input->post('lecturer_id'),
            'title'          => $this->input->post('title', TRUE),
            'excerpt'        => $this->input->post('excerpt'),
            'content'        => $this->input->post('content'),
            'featured_image' => $image,
            'status'         => $status,
            'published_at'   => ($status == 'published' && !$post->published_at) ? date('Y-m-d H:i:s') : $post->published_at,
        ]);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_blog', 'Edit blog: ' . $this->input->post('title', TRUE));
        $this->session->set_flashdata('success', 'Artikel blog berhasil diperbarui!');
        redirect('admin/lecturerblog');
    }

    public function delete($id = NULL) {
        $post = $this->db->get_where('lecturer_posts', ['id' => $id])->row();
        if ($post) {
            $this->_safe_unlink($post->featured_image);
            $this->db->where('id', $id)->delete('lecturer_posts');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_blog', 'Hapus blog: ' . $post->title);
            $this->session->set_flashdata('success', 'Artikel blog berhasil dihapus.');
        }
        redirect('admin/lecturerblog');
    }

    // ===== 🔥 FITUR GILA: Preview artikel =====
    public function preview($id = NULL) {
        $post = $this->db
            ->select('lecturer_posts.*, lecturers.name as lecturer_name, lecturers.title_front, lecturers.title_back, lecturers.photo as lecturer_photo')
            ->join('lecturers', 'lecturers.id = lecturer_posts.lecturer_id', 'left')
            ->where('lecturer_posts.id', $id)
            ->get('lecturer_posts')->row();
        if (!$post) show_404();
        $this->load->view('admin/lecturerblog/preview', ['post' => $post]);
    }

    // ===== 🔥 FITUR GILA: Duplicate post =====
    public function duplicate($id) {
        $post = $this->db->get_where('lecturer_posts', ['id' => $id])->row();
        if (!$post) { show_404(); }
        $new = clone $post;
        unset($new->id);
        $new->title = $post->title . ' (Salinan)';
        $new->slug = $this->_unique_slug($new->title);
        $new->status = 'draft';
        $new->published_at = NULL;
        $new->views = 0;
        $new->featured_image = null; // Tidak duplikat foto
        $this->db->insert('lecturer_posts', (array)$new);
        $new_id = $this->db->insert_id();
        $this->session->set_flashdata('success', 'Artikel berhasil diduplikasi sebagai draft.');
        redirect('admin/lecturerblog/edit/' . $new_id);
    }

    // ===== 🔥 FITUR GILA: Bulk publish/draft =====
    public function bulk_status() {
        $ids = $this->input->post('ids');
        $action = $this->input->post('action');
        if (!is_array($ids) || !in_array($action, ['publish', 'draft'])) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/lecturerblog'); return;
        }
        $status = ($action === 'publish') ? 'published' : 'draft';
        $data = ['status' => $status];
        if ($status === 'published') {
            $this->db->where_in('id', $ids)->where('published_at IS NULL')->update('lecturer_posts', ['published_at' => date('Y-m-d H:i:s')]);
        }
        $this->db->where_in('id', $ids)->update('lecturer_posts', $data);
        $count = $this->db->affected_rows();
        $this->session->set_flashdata('success', "{$count} artikel berhasil di-{$action}.");
        redirect('admin/lecturerblog');
    }

    // ===== 🔥 FITUR GILA: AI generate excerpt dari content =====
    public function ai_excerpt() {
        if (!$this->input->is_ajax_request()) show_404();
        $content = strip_tags($this->input->post('content'));
        $excerpt = trim(substr($content, 0, 200));
        if (strlen($content) > 200) {
            $last_space = strrpos($excerpt, ' ');
            if ($last_space !== false) $excerpt = substr($excerpt, 0, $last_space);
            $excerpt .= '...';
        }
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['excerpt' => $excerpt]));
    }

    private function _validate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('lecturer_id', 'Dosen', 'required|integer');
        $this->form_validation->set_rules('title', 'Judul', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('excerpt', 'Ringkasan', 'trim|max_length[500]');
        $this->form_validation->set_rules('content', 'Konten', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,published]');
    }

    private function _unique_slug($title, $exclude_id = NULL) {
        $base = url_title(strtolower(trim($title)), '-', TRUE);
        if ($base === '') $base = 'post';
        $slug = $base; $i = 2;
        while (true) {
            $this->db->where('slug', $slug);
            if ($exclude_id) $this->db->where('id !=', $exclude_id);
            if ($this->db->count_all_results('lecturer_posts') == 0) break;
            $slug = $base . '-' . $i; $i++;
            if ($i > 9999) break; // anti infinite loop
        }
        return $slug;
    }

    // ===== Upload dengan MIME check + chmod 0755 + error reporting =====
    private function _upload_image() {
        if (empty($_FILES['featured_image']['name'])) return NULL;
        $path = FCPATH . 'assets/uploads/blog/';
        if (!is_dir($path)) @mkdir($path, 0755, TRUE);
        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 3072, // 3MB
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
            return 'blog/' . $updata['file_name'];
        }
        $this->session->set_flashdata('error', 'Upload foto gagal: ' . strip_tags($this->upload->display_errors('', '')));
        return 'error';
    }

    // ===== Helper: safe unlink dengan path traversal protection =====
    private function _safe_unlink($rel_path) {
        if (!$rel_path) return;
        $upload_base = realpath(FCPATH . 'assets/uploads');
        $full = realpath(FCPATH . 'assets/uploads/' . $rel_path);
        if ($full && $upload_base && strpos($full, $upload_base) === 0 && file_exists($full)) {
            @unlink($full);
        }
    }
}