<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kontak extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'text', 'site_lang', 'security']);
    }
    public function index() {
        $data['title'] = 'Kontak - ' . site_name();
        $this->load->view('templates/header', $data);
        $this->load->view('frontend/kontak', $data);
        $this->load->view('templates/footer');
    }

    public function submit() {
        // Rate limiting sederhana: max 1 submit per 30 detik per IP
        $ip = $this->input->ip_address();
        $last = $this->session->userdata('contact_last_submit');
        if ($last && (time() - $last) < 30) {
            $this->session->set_flashdata('contact_errors', '<p class="mb-1">• Terlalu cepat. Silakan tunggu 30 detik sebelum mengirim lagi.</p>');
            redirect('kontak'); return;
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('message', 'Pesan', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('contact_errors', validation_errors('<p class="mb-1">• ', '</p>'));
            $this->session->set_flashdata('contact_old', $this->input->post());
            redirect('kontak');
            return;
        }

        $this->db->insert('contact_messages', [
            'name'    => $this->input->post('name', TRUE),
            'email'   => $this->input->post('email', TRUE),
            'subject' => $this->input->post('subject', TRUE),
            'message' => $this->input->post('message'),
        ]);

        $this->session->set_userdata('contact_last_submit', time());
        $this->session->set_flashdata('contact_success', '1');
        redirect('kontak');
    }
}