<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lecturer_model extends CI_Model {

    public function get_all() {
        return $this->db->order_by('name', 'ASC')->limit(500)->get('lecturers')->result();
    }

    public function get_active() {
        return $this->db->where('is_active', 1)->order_by('name', 'ASC')->limit(300)->get('lecturers')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('lecturers', ['id' => $id])->row();
    }

    public function insert($data) {
        $this->db->insert('lecturers', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('lecturers', $data);
    }

    public function delete($id) {
        // Hapus relasi prodi dulu
        $this->db->where('lecturer_id', $id)->delete('lecturer_study_program');
        return $this->db->where('id', $id)->delete('lecturers');
    }

    public function get_all_prodi() {
        return $this->db->order_by('name', 'ASC')->get('study_programs')->result();
    }

    public function get_prodi_of($lecturer_id) {
        return $this->db->get_where('lecturer_study_program', ['lecturer_id' => $lecturer_id])->result();
    }

    public function get_prodi_names($lecturer_id) {
        $rows = $this->db->select('study_programs.name')
            ->from('lecturer_study_program')
            ->join('study_programs', 'study_programs.id = lecturer_study_program.study_program_id')
            ->where('lecturer_study_program.lecturer_id', $lecturer_id)
            ->get()->result();
        return array_map(function ($r) { return $r->name; }, $rows);
    }

    // 🔥 FIX: sync_prodi dengan unique check (anti duplicate)
    public function sync_prodi($lecturer_id, $prodi_ids) {
        $this->db->where('lecturer_id', $lecturer_id)->delete('lecturer_study_program');
        
        $inserted = [];
        foreach ((array) $prodi_ids as $pid) {
            if (!$pid || in_array($pid, $inserted)) continue; // skip duplicate
            $this->db->insert('lecturer_study_program', [
                'lecturer_id' => $lecturer_id,
                'study_program_id' => $pid,
            ]);
            $inserted[] = $pid;
        }
    }

    // ===== 🔥 FITUR GILA: Get lecturer with research count =====
    public function get_with_research_count($id) {
        $lecturer = $this->get_by_id($id);
        if (!$lecturer) return null;
        
        $lecturer->research_count = $this->db->where('lecturer_id', $id)->count_all_results('research');
        $lecturer->prodi_names = implode(', ', $this->get_prodi_names($id));
        return $lecturer;
    }

    // ===== 🔥 FITUR GILA: Search lecturer (untuk autocomplete) =====
    public function search($q, $limit = 10) {
        return $this->db->where('is_active', 1)
            ->group_start()
                ->like('name', $q)
                ->or_like('nidn', $q)
                ->or_like('expertise', $q)
            ->group_end()
            ->limit($limit)
            ->get('lecturers')->result();
    }
}