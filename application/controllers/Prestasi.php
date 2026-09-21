<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestasi extends CI_Controller {

    public function index() {
        $data['title'] = 'Prestasi Mahasiswa - Fakultas';
        $data['achievements'] = $this->db
            ->select('achievements.*, study_programs.name AS prodi_name')
            ->from('achievements')
            ->join('study_programs', 'study_programs.id = achievements.study_program_id', 'left')
            ->order_by('achievements.year', 'DESC')
            ->order_by("CASE achievements.level
                WHEN 'international' THEN 1
                WHEN 'national' THEN 2
                WHEN 'regional' THEN 3
                WHEN 'university' THEN 4
                WHEN 'faculty' THEN 5
                ELSE 6 END", 'ASC', FALSE)
            ->get()
            ->result();

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/prestasi_list', $data);
        $this->load->view('templates/footer');
    }
}