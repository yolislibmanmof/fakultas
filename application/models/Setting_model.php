<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

    // 🔥 FIX: In-memory cache (anti query berulang dalam 1 request)
    private static $_cache = [];

    public function get($key, $default = '') {
        // Cek cache dulu
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        
        $row = $this->db->get_where('site_settings', ['setting_key' => $key])->row();
        $value = $row ? $row->setting_value : $default;
        
        // Simpan ke cache
        self::$_cache[$key] = $value;
        
        return $value;
    }

    public function set($key, $value) {
        if ($this->db->get_where('site_settings', ['setting_key' => $key])->row()) {
            $this->db->where('setting_key', $key)->update('site_settings', ['setting_value' => $value]);
        } else {
            $this->db->insert('site_settings', ['setting_key' => $key, 'setting_value' => $value, 'setting_type' => 'text']);
        }
        
        // Update cache
        self::$_cache[$key] = $value;
    }

    // ===== 🔥 FITUR GILA: Get multiple settings sekaligus (batch) =====
    public function get_many($keys) {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, '');
        }
        return $result;
    }

    // ===== 🔥 FITUR GILA: Clear cache (force reload dari DB) =====
    public function clear_cache() {
        self::$_cache = [];
    }

    // ===== 🔥 FITUR GILA: Get all settings (untuk export) =====
    public function get_all() {
        $rows = $this->db->get('site_settings')->result();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r->setting_key] = $r->setting_value;
            self::$_cache[$r->setting_key] = $r->setting_value; // populate cache
        }
        return $settings;
    }
}