<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function index() {
        $q = trim((string) $this->input->get('q', TRUE));
        $data['q'] = $q;
        $data['title'] = 'Pencarian - Fakultas';

        $r = ['posts' => [], 'lecturers' => [], 'programs' => [], 'courses' => [], 'documents' => []];

        if ($q !== '') {
            // Berita & Pengumuman
            $r['posts'] = $this->db
                ->select('posts.*, categories.name AS category_name')
                ->from('posts')
                ->join('categories', 'categories.id = posts.category_id', 'left')
                ->where('posts.status', 'published')
                ->group_start()->like('posts.title', $q)->or_like('posts.content', $q)->group_end()
                ->order_by('posts.published_at', 'DESC')
                ->limit(10)
                ->get()->result();

            // Dosen
            $r['lecturers'] = $this->db
                ->where('is_active', 1)
                ->group_start()->like('name', $q)->or_like('expertise', $q)->group_end()
                ->limit(8)
                ->get('lecturers')->result();

            // Program Studi
            $r['programs'] = $this->db
                ->where('is_active', 1)
                ->group_start()->like('name', $q)->or_like('description', $q)->group_end()
                ->limit(8)
                ->get('study_programs')->result();

            // Mata Kuliah
            $r['courses'] = $this->db
                ->group_start()->like('name', $q)->or_like('course_code', $q)->group_end()
                ->limit(8)
                ->get('courses')->result();

            // Dokumen
            $r['documents'] = $this->db
                ->where('is_active', 1)
                ->group_start()->like('title', $q)->or_like('description', $q)->group_end()
                ->limit(8)
                ->get('documents')->result();
        }

        $data['results'] = $r;
        $data['total'] = count($r['posts']) + count($r['lecturers']) + count($r['programs']) + count($r['courses']) + count($r['documents']);

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/search_results', $data);
        $this->load->view('templates/footer');
    }
}