<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lecturerblog extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'text']);
    }

    // ===== INDEX: Daftar semua dosen yang punya blog =====
    public function index() {
        $data['title'] = 'Blog Dosen — Fakultas Teknik & Ilmu Komputer';
        $data['lecturers'] = $this->db
            ->select('lecturers.*, COUNT(p.id) as total_posts', FALSE)
            ->from('lecturers')
            ->join('lecturer_posts p', "p.lecturer_id = lecturers.id AND p.status = 'published'", 'left')
            ->where('lecturers.is_active', 1)
            ->group_by('lecturers.id')
            ->order_by('total_posts', 'DESC')
            ->order_by('lecturers.name', 'ASC')
            ->get()->result();

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/lecturer_blog_index', $data);
        $this->load->view('templates/footer');
    }

    // ===== VIEW: Blog milik satu dosen =====
    public function view($id = NULL) {
        $lecturer = $this->db->get_where('lecturers', ['id' => $id, 'is_active' => 1])->row();
        if (!$lecturer) show_404();

        $data['lecturer'] = $lecturer;
        $data['title'] = 'Blog ' . trim($lecturer->title_front . ' ' . $lecturer->name) . ' — Fakultas';
        $data['posts'] = $this->db
            ->where('lecturer_id', $id)
            ->where('status', 'published')
            ->order_by('published_at', 'DESC')
            ->get('lecturer_posts')->result();

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/lecturer_blog_list', $data);
        $this->load->view('templates/footer');
    }

    // ===== POST: Detail artikel blog =====
    public function post($slug = NULL) {
        $post = $this->db
            ->select('lecturer_posts.*, lecturers.name as lecturer_name, lecturers.title_front, lecturers.title_back, lecturers.photo as lecturer_photo, lecturers.expertise, lecturers.google_scholar_url, lecturers.sinta_url')
            ->join('lecturers', 'lecturers.id = lecturer_posts.lecturer_id')
            ->where('lecturer_posts.slug', $slug)
            ->where('lecturer_posts.status', 'published')
            ->get('lecturer_posts')->row();

        if (!$post) show_404();

        // Tambah views (atomic, anti-NULL, anti race condition)
        $this->db->query(
            "UPDATE lecturer_posts SET views = COALESCE(views, 0) + 1 WHERE id = ?",
            [$post->id]
        );

        $data['post'] = $post;
        $data['title'] = $post->title . ' — Blog Dosen';
        $data['related'] = $this->db
            ->where('lecturer_id', $post->lecturer_id)
            ->where('status', 'published')
            ->where('id !=', $post->id)
            ->order_by('published_at', 'DESC')
            ->limit(3)
            ->get('lecturer_posts')->result();

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/lecturer_blog_detail', $data);
        $this->load->view('templates/footer');
    }
}