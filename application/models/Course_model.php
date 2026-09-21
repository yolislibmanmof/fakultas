<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course_model extends CI_Model {

    // 🔥 FIX: Defensive pivot column detection
    private function _pivot_columns() {
        static $cols = null;
        if ($cols === null) {
            $fields = $this->db->list_fields('course_study_program');
            $cols = [
                'course' => in_array('course_id', $fields) ? 'course_id' : 'courses_id',
                'prodi'  => in_array('study_program_id', $fields) ? 'study_program_id' : 'program_id',
            ];
        }
        return $cols;
    }

    public function get_all() {
        $pv = $this->_pivot_columns();
        return $this->db->select('courses.*, study_programs.name AS prodi_name')
            ->from('courses')
            ->join('course_study_program', "course_study_program.{$pv['course']} = courses.id", 'left')
            ->join('study_programs', "study_programs.id = course_study_program.{$pv['prodi']}", 'left')
            ->order_by('courses.semester', 'ASC')
            ->order_by('courses.course_code', 'ASC')
            ->limit(500)
            ->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('courses', ['id' => $id])->row();
    }

    public function get_prodi_id($course_id) {
        $pv = $this->_pivot_columns();
        $row = $this->db->get_where('course_study_program', [$pv['course'] => $course_id])->row();
        return $row ? $row->{$pv['prodi']} : NULL;
    }

    public function get_by_prodi($prodi_id) {
        $pv = $this->_pivot_columns();
        return $this->db->select('courses.*')
            ->from('courses')
            ->join('course_study_program', "course_study_program.{$pv['course']} = courses.id")
            ->where("course_study_program.{$pv['prodi']}", $prodi_id)
            ->order_by('courses.semester', 'ASC')
            ->order_by('courses.course_code', 'ASC')
            ->get()->result();
    }

    public function insert($data) {
        $this->db->insert('courses', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('courses', $data);
    }

    public function delete($id) {
        $pv = $this->_pivot_columns();
        // Hapus relasi pivot dulu
        $this->db->where($pv['course'], $id)->delete('course_study_program');
        return $this->db->where('id', $id)->delete('courses');
    }

    // 🔥 FIX: sync_prodi dengan unique check (anti duplicate)
    public function sync_prodi($course_id, $prodi_ids) {
        $pv = $this->_pivot_columns();
        $this->db->where($pv['course'], $course_id)->delete('course_study_program');
        
        $inserted = [];
        foreach ((array) $prodi_ids as $pid) {
            if (!$pid || in_array($pid, $inserted)) continue; // skip duplicate
            $this->db->insert('course_study_program', [
                $pv['course'] => $course_id,
                $pv['prodi']  => $pid,
            ]);
            $inserted[] = $pid;
        }
    }

    // ===== 🔥 FITUR GILA: Get course with all prodi names =====
    public function get_with_prodi($id) {
        $course = $this->get_by_id($id);
        if (!$course) return null;
        
        $pv = $this->_pivot_columns();
        $prodi_rows = $this->db->select('study_programs.name')
            ->from('course_study_program')
            ->join('study_programs', "study_programs.id = course_study_program.{$pv['prodi']}")
            ->where("course_study_program.{$pv['course']}", $id)
            ->get()->result();
        
        $course->prodi_names = implode(', ', array_map(function($r) { return $r->name; }, $prodi_rows));
        return $course;
    }
}