<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Berita extends CI_Controller {

    public function index() {
        $cat_slug = $this->input->get('cat');

        // Ambil semua kategori untuk filter chips
        $categories = $this->db->order_by('name', 'ASC')->get('categories')->result();

        // Query posts
        $this->db->select('posts.*, categories.name AS category_name, categories.slug AS category_slug')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->where('posts.status', 'published');

        if ($cat_slug) {
            $this->db->where('categories.slug', $cat_slug);
        }

        $this->db->order_by('posts.published_at', 'DESC')->limit(15);
        $posts = $this->db->get()->result();

        // Hitung total views untuk stats
        $total_views = $this->db->select_sum('views')->where('status', 'published')->get('posts')->row()->views ?? 0;

        $data = [
            'title'      => 'Berita & Informasi - ' . site_name(),
            'posts'      => $posts,
            'categories' => $categories,
            'cat'        => $cat_slug,
            'total_views'=> $total_views,
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/news_list', $data);
        $this->load->view('templates/footer');
    }

    public function detail($slug = NULL) {
        if (!$slug) show_404();

        $data['post'] = $this->db
            ->select('posts.*, categories.name AS category_name, categories.slug AS category_slug, users.full_name AS author')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.slug', $slug)
            ->where('posts.status', 'published')
            ->get()->row();

        if (!$data['post']) show_404();

        // tambah counter views
        $this->db->where('id', $data['post']->id)->update('posts', ['views' => $data['post']->views + 1]);

        $data['title'] = html_escape($data['post']->title) . ' - ' . site_name();
        $data['related'] = $this->db
            ->where('status', 'published')
            ->where('category_id', $data['post']->category_id)
            ->where('id !=', $data['post']->id)
            ->order_by('published_at', 'DESC')
            ->limit(3)->get('posts')->result();

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/news_detail', $data);
        $this->load->view('templates/footer');
    }
}