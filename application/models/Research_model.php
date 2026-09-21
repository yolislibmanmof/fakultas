<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Research_model extends CI_Model {

    public function get_all() {
        return $this->db->select('research.*, lecturers.name AS lecturer_name, lecturers.title_front, lecturers.title_back')
            ->from('research')
            ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left')
            ->order_by('research.year', 'DESC')
            ->limit(500)
            ->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('research', ['id' => $id])->row();
    }

    // 🔥 FIX: get_filtered dengan limit
    public function get_filtered($type = NULL, $year = NULL) {
        $this->db->select('research.*, lecturers.name AS lecturer_name, lecturers.title_front, lecturers.title_back')
            ->from('research')
            ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left');
        if ($type) $this->db->where('research.type', $type);
        if ($year) $this->db->where('research.year', $year);
        return $this->db->order_by('research.year', 'DESC')->limit(500)->get()->result();
    }

    public function get_years() {
        $rows = $this->db->select('year')->distinct()->where('year IS NOT NULL')->order_by('year', 'DESC')->get('research')->result();
        return array_map(function ($r) { return (int)$r->year; }, $rows);
    }

    public function count_by_type($type) {
        return (int)$this->db->where('type', $type)->count_all_results('research');
    }

    public function insert($data) {
        $this->db->insert('research', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('research', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('research');
    }

    // ===== 🔥 FITUR GILA: Get by lecturer =====
    public function get_by_lecturer($lecturer_id, $limit = 50) {
        return $this->db->where('lecturer_id', $lecturer_id)
            ->order_by('year', 'DESC')
            ->limit($limit)
            ->get('research')->result();
    }

    // ===== 🔥 FITUR GILA: Stats per year (untuk chart) =====
    public function get_stats_by_year() {
        return $this->db->select('year, type, COUNT(*) as count', FALSE)
            ->group_by(['year', 'type'])
            ->order_by('year', 'ASC')
            ->get('research')->result();
    }

    // ===== 🔥 FITUR GILA: Get ongoing research =====
    public function get_ongoing($limit = 10) {
        return $this->db->select('research.*, lecturers.name AS lecturer_name')
            ->from('research')
            ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left')
            ->where('research.status', 'ongoing')
            ->order_by('research.created_at', 'DESC')
            ->limit($limit)
            ->get()->result();
    }
}