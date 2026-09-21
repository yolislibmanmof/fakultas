<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function get_by_username($username) {
        return $this->db->get_where('users', ['username' => $username])->row();
    }

    // 🔥 FIX: Atomic increment (anti race condition)
    public function increment_login_attempts($user_id) {
        $this->db->where('id', $user_id)->set('login_attempts', 'login_attempts + 1', FALSE)->update('users');
        
        // Cek apakah sudah 5 attempts
        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        if ($user && $user->login_attempts >= 5) {
            $this->db->where('id', $user_id)->update('users', [
                'locked_until' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
            ]);
        }
    }

    public function reset_login_attempts($user_id) {
        $this->db->where('id', $user_id)->update('users', [
            'login_attempts' => 0,
            'locked_until'   => NULL,
            'last_login'     => date('Y-m-d H:i:s')
        ]);
    }

    // 🔥 FIX: Defensive audit log (cek tabel ada)
    public function log_activity($user_id, $action, $description) {
        if (!$this->db->table_exists('audit_logs')) return;
        
        try {
            $this->db->insert('audit_logs', [
                'user_id'     => $user_id,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => $this->input->ip_address(),
                'user_agent'  => substr($this->input->user_agent(), 0, 255) // truncate
            ]);
        } catch (Exception $e) {
            // Silent fail — jangan crash aplikasi kalau audit log error
        }
    }

    // ===== 🔥 FITUR GILA: Get recent activity (untuk dashboard) =====
    public function get_recent_activity($limit = 10) {
        if (!$this->db->table_exists('audit_logs')) return [];
        
        return $this->db->select('audit_logs.*, users.full_name, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->order_by('audit_logs.created_at', 'DESC')
            ->limit($limit)
            ->get('audit_logs')->result();
    }

    // ===== 🔥 FITUR GILA: Get activity by user =====
    public function get_activity_by_user($user_id, $limit = 50) {
        if (!$this->db->table_exists('audit_logs')) return [];
        
        return $this->db->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get('audit_logs')->result();
    }

    // ===== 🔥 FITUR GILA: Clear old logs (maintenance) =====
    public function clear_old_logs($days = 90) {
        if (!$this->db->table_exists('audit_logs')) return 0;
        
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('created_at <', $cutoff)->delete('audit_logs');
        return $this->db->affected_rows();
    }
}