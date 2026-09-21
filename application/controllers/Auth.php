<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    public function login() {
        if ($this->session->userdata('is_logged_in')) {
            redirect('admin/dashboard');
        }

        if ($this->input->method() === 'post') {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);

            $user = $this->Auth_model->get_by_username($username);

            if ($user && $user->locked_until && strtotime($user->locked_until) > time()) {
                $this->session->set_flashdata('error', 'Akun terkunci. Coba lagi nanti.');
                redirect('auth/login');
            }

            if ($user && password_verify($password, $user->password)) {
                if (!$user->is_active) {
                    $this->session->set_flashdata('error', 'Akun tidak aktif.');
                    redirect('auth/login');
                }

                $this->Auth_model->reset_login_attempts($user->id);

                $this->session->set_userdata([
                    'user_id'      => $user->id,
                    'username'     => $user->username,
                    'full_name'    => $user->full_name,
                    'role'         => $user->role,
                    'is_logged_in' => TRUE
                ]);

                $this->Auth_model->log_activity($user->id, 'login', 'Berhasil login');
                redirect('admin/dashboard');
            } else {
                if ($user) {
                    $this->Auth_model->increment_login_attempts($user->id);
                }
                $this->session->set_flashdata('error', 'Username atau password salah.');
                redirect('auth/login');
            }
        }

        $this->load->view('admin/login');
    }

    public function logout() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $this->Auth_model->log_activity($user_id, 'logout', 'Logout');
        }
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}