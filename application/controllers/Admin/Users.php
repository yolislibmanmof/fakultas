<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    private $has_role = false;
    private $has_email = false;
    private $pw_col = 'password';

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $fields = $this->db->list_fields('users');
        $this->has_role  = in_array('role', $fields);
        $this->has_email = in_array('email', $fields);
        $this->pw_col    = in_array('password', $fields) ? 'password'
                         : (in_array('password_hash', $fields) ? 'password_hash' : 'password');
    }

    private function _uid() {
        return $this->session->userdata('user_id') ?: $this->session->userdata('id');
    }

    private function _roles() {
        if (!$this->has_role) return [];
        $existing = array_column(
            $this->db->select('role')->distinct()->where('role IS NOT NULL')->get('users')->result_array(),
            'role'
        );
        $defaults = ['super_admin', 'admin', 'editor', 'viewer'];
        $merged = array_unique(array_merge($defaults, $existing));
        sort($merged);
        return $merged;
    }

    private function _is_admin_role($role) {
        return stripos((string)$role, 'admin') !== FALSE;
    }

    public function index() {
        $q    = trim((string)$this->input->get('q'));
        $role = $this->input->get('role');

        $this->db->select('*')->from('users');
        if ($q !== '') {
            $this->db->group_start()->like('username', $q)->or_like('full_name', $q);
            if ($this->has_email) $this->db->or_like('email', $q);
            $this->db->group_end();
        }
        if ($role && $this->has_role) $this->db->where('role', $role);
        $this->db->order_by('id', 'ASC');

        $data = [
            'title' => 'Manajemen Users - Mission Control',
            'active_menu' => 'users',
            'users' => $this->db->get()->result(),
            'q' => $q, 'role' => $role,
            'roles' => $this->_roles(),
            'has_role' => $this->has_role, 'has_email' => $this->has_email,
            'uid' => $this->_uid(),
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users_list', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = [
            'title' => 'Tambah User - Mission Control',
            'active_menu' => 'users',
            'user' => NULL,
            'roles' => $this->_roles(),
            'has_role' => $this->has_role, 'has_email' => $this->has_email,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users_form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_]+$/]|is_unique[users.username]');
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        if ($this->has_email) {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|is_unique[users.email]');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('form_errors', validation_errors('<p>• ', '</p>'));
            $this->session->set_flashdata('old', $this->input->post());
            redirect('admin/users/create'); return;
        }
        $row = [
            'username'  => $this->input->post('username', TRUE),
            'full_name' => $this->input->post('full_name', TRUE),
            $this->pw_col => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
        ];
        if ($this->has_email) $row['email'] = $this->input->post('email', TRUE);
        if ($this->has_role)  $row['role']  = $this->input->post('role', TRUE) ?: 'viewer';
        $this->db->insert('users', $row);
        $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
        redirect('admin/users');
    }

    public function edit($id) {
        $user = $this->db->get_where('users', ['id' => $id])->row();
        if (!$user) show_404();
        $data = [
            'title' => 'Edit User - Mission Control',
            'active_menu' => 'users',
            'user' => $user,
            'roles' => $this->_roles(),
            'has_role' => $this->has_role, 'has_email' => $this->has_email,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users_form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_]+$/]|callback__check_username[' . $id . ']');
        if ($this->input->post('password') !== '') {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }
        if ($this->has_email) {
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback__check_email[' . $id . ']');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('form_errors', validation_errors('<p>• ', '</p>'));
            redirect('admin/users/edit/' . $id); return;
        }
        $row = [
            'username'  => $this->input->post('username', TRUE),
            'full_name' => $this->input->post('full_name', TRUE),
        ];
        if ($this->has_email) $row['email'] = $this->input->post('email', TRUE);
        if ($this->has_role)  $row['role']  = $this->input->post('role', TRUE) ?: 'viewer';
        if ($this->input->post('password') !== '') {
            $row[$this->pw_col] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }
        $this->db->where('id', $id)->update('users', $row);
        $this->session->set_flashdata('success', 'User berhasil diperbarui.');
        redirect('admin/users');
    }

    public function _check_username($username, $id) {
        $exists = $this->db->where('username', $username)->where('id !=', $id)->get('users')->row();
        if ($exists) { $this->form_validation->set_message('_check_username', 'Username sudah dipakai user lain.'); return FALSE; }
        return TRUE;
    }

    public function _check_email($email, $id) {
        if (!$this->has_email || !$email) return TRUE;
        $exists = $this->db->where('email', $email)->where('id !=', $id)->get('users')->row();
        if ($exists) { $this->form_validation->set_message('_check_email', 'Email sudah dipakai user lain.'); return FALSE; }
        return TRUE;
    }

    public function delete($id) {
        $uid = $this->_uid();
        if ((int)$id === (int)$uid) {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus akun sendiri.');
            redirect('admin/users'); return;
        }
        if ($this->has_role) {
            $target = $this->db->get_where('users', ['id' => $id])->row();
            if ($target && $this->_is_admin_role($target->role)) {
                $admin_count = $this->db->like('role', 'admin')->count_all_results('users');
                if ($admin_count <= 1) {
                    $this->session->set_flashdata('error', 'Tidak dapat menghapus admin terakhir.');
                    redirect('admin/users'); return;
                }
            }
        }
        $this->db->where('id', $id)->delete('users');
        $this->session->set_flashdata('success', 'User berhasil dihapus.');
        redirect('admin/users');
    }

    // ===== 🔥 FITUR GILA: Activity log viewer per user =====
    public function activity($id) {
        if (!$this->db->table_exists('audit_logs')) {
            $this->session->set_flashdata('error', 'Tabel audit log tidak tersedia.');
            redirect('admin/users'); return;
        }
        $user = $this->db->get_where('users', ['id' => $id])->row();
        if (!$user) show_404();
        $logs = $this->db->where('user_id', $id)
            ->order_by('created_at', 'DESC')
            ->limit(100)
            ->get('audit_logs')->result();
        $data = [
            'title' => 'Activity Log - ' . $user->full_name,
            'active_menu' => 'users',
            'user' => $user,
            'logs' => $logs,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users_activity', $data);
        $this->load->view('templates/admin_footer');
    }

    // ===== 🔥 FITUR GILA: Reset password user (admin-only) =====
    public function reset_password($id) {
        $user = $this->db->get_where('users', ['id' => $id])->row();
        if (!$user) show_404();
        $new_pw = 'Reset@' . rand(1000, 9999);
        $this->db->where('id', $id)->update('users', [
            $this->pw_col => password_hash($new_pw, PASSWORD_DEFAULT),
        ]);
        $this->session->set_flashdata('success', "Password user {$user->username} berhasil direset ke: <strong>{$new_pw}</strong> (simpan segera!)");
        redirect('admin/users');
    }
}