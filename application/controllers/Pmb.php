<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pmb extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Study_program_model', 'Setting_model']);
        $this->load->helper(['url', 'text', 'site_lang']);
    }

    public function index() {
        $data['title'] = 'Penerimaan Mahasiswa Baru - ' . site_name();
        $data['programs'] = $this->Study_program_model->get_active();
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/pmb', $data);
        $this->load->view('templates/footer');
    }

    public function submit() {
        // Rate limiting: max 1 submit per 60 detik per IP
        $ip = $this->input->ip_address();
        $last = $this->session->userdata('pmb_last_submit');
        if ($last && (time() - $last) < 60) {
            $this->session->set_flashdata('pmb_errors', '<p class="mb-1">• Terlalu cepat. Silakan tunggu 1 menit sebelum mencoba lagi.</p>');
            redirect('pmb'); return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('phone', 'No. HP', 'trim|regex_match[/^[0-9+\-\s()]{6,20}$/]');
        $this->form_validation->set_rules('study_program_id', 'Program Studi', 'required|integer');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pmb_errors', validation_errors('<p class="mb-1">• ', '</p>'));
            $this->session->set_flashdata('pmb_old', $this->input->post());
            redirect('pmb');
            return;
        }

        $this->db->insert('pmb_applications', [
            'full_name'        => $this->input->post('full_name', TRUE),
            'email'            => $this->input->post('email', TRUE),
            'phone'            => $this->input->post('phone', TRUE),
            'study_program_id' => $this->input->post('study_program_id'),
            'message'          => $this->input->post('message'),
            'status'           => 'new',
        ]);

        $this->session->set_userdata('pmb_last_submit', time());
        $this->session->set_flashdata('pmb_success', '1');
        redirect('pmb');
    }
}