<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dosen extends CI_Controller {

    public function index() {
        $this->load->model('Lecturer_model');
        $data['title'] = 'Direktori Dosen - Fakultas';
        $data['lecturers'] = $this->Lecturer_model->get_active();
        foreach ($data['lecturers'] as $l) {
            $l->prodi_names = implode(', ', $this->Lecturer_model->get_prodi_names($l->id));
        }
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/dosen_list', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id = NULL) {
        if (!$id) show_404();
        $this->load->model('Lecturer_model');

        $l = $this->db->where('id', (int)$id)->where('is_active', 1)->get('lecturers')->row();
        if (!$l) show_404();

        $prodi    = $this->Lecturer_model->get_prodi_names($l->id);
        $research = $this->db->where('lecturer_id', $l->id)->order_by('year', 'DESC')->get('research')->result();
        $blogs    = $this->db->where('lecturer_id', $l->id)->where('status', 'published')
                             ->order_by('published_at', 'DESC')->get('lecturer_posts')->result();

        $l->prodi_names = implode(', ', $prodi);

        $data = [
            'title'    => trim(($l->title_front ?? '') . ' ' . $l->name) . ' — Dosen',
            'l'        => $l,
            'prodi'    => $prodi,
            'research' => $research,
            'blogs'    => $blogs,
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/dosen_detail', $data);
        $this->load->view('templates/footer');
    }
}