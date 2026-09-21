<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Study_program_model extends CI_Model {

    public function get_all() {
        return $this->db->order_by('degree', 'ASC')->order_by('name', 'ASC')->get('study_programs')->result();
    }

    public function get_active() {
        return $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('study_programs')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('study_programs', ['id' => $id])->row();
    }

    public function get_by_slug($slug) {
        return $this->db->get_where('study_programs', ['slug' => $slug])->row();
    }

    public function insert($data) {
        $this->db->insert('study_programs', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('study_programs', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('study_programs');
    }

    // ===== 🔥 FITUR GILA: Check slug exists =====
    public function is_slug_exists($slug, $exclude_id = NULL) {
        $this->db->where('slug', $slug);
        if ($exclude_id) $this->db->where('id !=', $exclude_id);
        return $this->db->count_all_results('study_programs') > 0;
    }

    // ===== 🔥 FITUR GILA: Generate unique slug =====
    public function generate_unique_slug($name, $exclude_id = NULL) {
        $base = url_title(strtolower(trim($name)), '-', TRUE);
        if ($base === '') $base = 'program';
        
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
        
        return $base . '-' . time();
    }

    // ===== 🔥 FITUR GILA: Get with student count (jika ada tabel students) =====
    public function get_with_student_count() {
        $programs = $this->get_all();
        
        if ($this->db->table_exists('students')) {
            $counts = [];
            $counts_raw = $this->db->select('study_program_id, COUNT(*) as c', FALSE)
                ->group_by('study_program_id')
                ->get('students')->result();
            foreach ($counts_raw as $r) $counts[(int)$r->study_program_id] = (int)$r->c;
            
            foreach ($programs as $p) {
                $p->student_count = $counts[$p->id] ?? 0;
            }
        }
        
        return $programs;
    }

    // ===== 🔥 FITUR GILA: Get accreditation expiring soon =====
    public function get_accreditation_expiring($days = 90) {
        $threshold = date('Y-m-d', strtotime("+{$days} days"));
        return $this->db->where('accreditation_until IS NOT NULL')
            ->where('accreditation_until <=', $threshold)
            ->where('accreditation_until >=', date('Y-m-d'))
            ->where('is_active', 1)
            ->order_by('accreditation_until', 'ASC')
            ->get('study_programs')->result();
    }
}