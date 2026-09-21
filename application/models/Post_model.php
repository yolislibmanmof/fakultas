<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model {

    public function get_all() {
        return $this->db->select('posts.*, categories.name AS category_name, users.full_name AS author')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->order_by('posts.created_at', 'DESC')
            ->limit(500)
            ->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('posts', ['id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert('posts', $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('posts', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('posts');
    }

    public function get_categories() {
        return $this->db->order_by('name', 'ASC')->get('categories')->result();
    }

    // 🔥 FIX: is_slug_exists sekarang dipakai (tidak dead code lagi)
    public function is_slug_exists($slug, $exclude_id = NULL) {
        $this->db->where('slug', $slug);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return $this->db->count_all_results('posts') > 0;
    }

    // ===== 🔥 FITUR GILA: Generate unique slug (reusable) =====
    public function generate_unique_slug($title, $exclude_id = NULL) {
        $base = url_title(strtolower(trim($title)), '-', TRUE);
        if ($base === '') $base = 'post';
        
        $slug = $base;
        $i = 2;
        $max_iterations = 1000;
        
        while ($max_iterations-- > 0) {
            if (!$this->is_slug_exists($slug, $exclude_id)) {
                return $slug;
            }
            $slug = $base . '-' . $i;
            $i++;
        }
        
        // Fallback dengan timestamp
        return $base . '-' . time();
    }

    // ===== 🔥 FITUR GILA: Get published posts (untuk frontend) =====
    public function get_published($limit = 10, $offset = 0) {
        return $this->db->select('posts.*, categories.name AS category_name, users.full_name AS author')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.status', 'published')
            ->order_by('posts.published_at', 'DESC')
            ->limit($limit, $offset)
            ->get()->result();
    }

    // ===== 🔥 FITUR GILA: Get by slug (untuk detail page) =====
    public function get_by_slug($slug) {
        return $this->db->select('posts.*, categories.name AS category_name, users.full_name AS author')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.slug', $slug)
            ->where('posts.status', 'published')
            ->get()->row();
    }

    // ===== 🔥 FITUR GILA: Increment views (atomic) =====
    public function increment_views($id) {
        $this->db->where('id', $id)->set('views', 'views + 1', FALSE)->update('posts');
    }

    // ===== 🔥 FITUR GILA: Get most viewed =====
    public function get_most_viewed($limit = 5) {
        return $this->db->select('posts.*, categories.name AS category_name')
            ->from('posts')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->where('posts.status', 'published')
            ->order_by('posts.views', 'DESC')
            ->limit($limit)
            ->get()->result();
    }
}