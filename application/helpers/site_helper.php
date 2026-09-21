<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ===== SATU SUMBER KEBENARAN: NAMA FAKULTAS =====
if (!function_exists('site_name')) {
    function site_name() {
        $CI =& get_instance();
        static $cached = NULL;
        if ($cached === NULL) {
            $CI->load->model('Setting_model');
            $cached = trim((string)$CI->Setting_model->get('site_name', 'Fakultas Teknik & Ilmu Komputer'));
        }
        return $cached;
    }
}

// ===== NAMA VERSI INGGRIS (opsional di Appearance, fallback ke site_name) =====
if (!function_exists('site_tagline')) {
    function site_tagline() {
        $CI =& get_instance();
        static $cached = NULL;
        if ($cached === NULL) {
            $CI->load->model('Setting_model');
            $en = trim((string)$CI->Setting_model->get('site_name_en', ''));
            $cached = ($en !== '') ? $en : site_name();
        }
        return $cached;
    }
}