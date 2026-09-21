<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profil extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Setting_model', 'Study_program_model']);
    }

    public function index($section = 'sejarah') {
        if ($section == 'visi-misi') $section = 'visi_misi';
        if (!in_array($section, ['sejarah', 'visi_misi', 'struktur', 'sambutan'])) $section = 'sejarah';

        $s = $this->Setting_model;
        $data = [
            'title' => 'Profil Fakultas - ' . ['sejarah' => 'Sejarah', 'visi_misi' => 'Visi & Misi', 'struktur' => 'Struktur Organisasi', 'sambutan' => 'Sambutan Dekan'][$section],
            'section' => $section,
            's' => (object) [
                'history' => $s->get('faculty_history', 'Sejarah belum diinput.'),
                'vision' => $s->get('faculty_vision', '-'),
                'mission' => $s->get('faculty_mission', '-'),
                'dean_message' => $s->get('dean_message', '-'),
                'dean_name' => $s->get('dean_name', 'Dekan'),
                'wadek_1' => $s->get('wadek_1_name', '-'),
                'wadek_2' => $s->get('wadek_2_name', '-'),
                'wadek_3' => $s->get('wadek_3_name', '-'),
                'tu_head' => $s->get('tu_head_name', '-'),
            ],
            'programs' => $this->Study_program_model->get_active(),
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/profil', $data);
        $this->load->view('templates/footer');
    }
}