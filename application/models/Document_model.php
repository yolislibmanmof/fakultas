<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document_model extends CI_Model {

    public function get_all() {
        return $this->db->order_by('created_at', 'DESC')->limit(500)->get('documents')->result();
    }

    public function get_active($category = NULL, $q = NULL) {
        $this->db->where('is_active', 1);
        if ($category) $this->db->where('category', $category);
        if ($q) $this->db->group_start()->like('title', $q)->or_like('description', $q)->group_end();
        return $this->db->order_by('category', 'ASC')->order_by('title', 'ASC')->limit(300)->get('documents')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('documents', ['id' => $id])->row();
    }

    public function insert($data) {
        $this->db->insert('documents', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('documents', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('documents');
    }

    // 🔥 FIX: Atomic increment (anti race condition)
    public function increment_download($id) {
        $this->db->where('id', $id)->set('download_count', 'download_count + 1', FALSE)->update('documents');
    }

    // ===== 🔥 FITUR GILA: Get most downloaded =====
    public function get_most_downloaded($limit = 10) {
        return $this->db->where('is_active', 1)
            ->order_by('download_count', 'DESC')
            ->limit($limit)
            ->get('documents')->result();
    }

    // ===== 🔥 FITUR GILA: Stats per category =====
    public function get_stats_by_category() {
        return $this->db->select('category, COUNT(*) as count, SUM(download_count) as total_downloads', FALSE)
            ->where('is_active', 1)
            ->group_by('category')
            ->get('documents')->result();
    }

    // ===== 🔥 FITUR GILA: Reset download counter =====
    public function reset_download_count($id) {
        return $this->db->where('id', $id)->update('documents', ['download_count' => 0]);
    }
}