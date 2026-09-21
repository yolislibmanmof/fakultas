<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kemahasiswaan extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title'] = 'Kemahasiswaan - Fakultas';
        
        // Data Ormawa & Beasiswa (Struktur tetap, bisa di-hardcode)
        $data['orgs'] = [
            ['name' => 'BEM Fakultas', 'desc' => 'Badan Eksekutif Mahasiswa tingkat fakultas', 'icon' => 'fa-landmark', 'color' => 'blue'],
            ['name' => 'DPM Fakultas', 'desc' => 'Dewan Perwakilan Mahasiswa fakultas', 'icon' => 'fa-gavel', 'color' => 'red'],
            ['name' => 'HIMA Teknik Informatika', 'desc' => 'Himpunan Mahasiswa Teknik Informatika', 'icon' => 'fa-laptop-code', 'color' => 'green'],
            ['name' => 'HIMA Sistem Informasi', 'desc' => 'Himpunan Mahasiswa Sistem Informasi', 'icon' => 'fa-database', 'color' => 'purple'],
            ['name' => 'UKM Robotika', 'desc' => 'Unit Kegiatan Mahasiswa bidang robotika', 'icon' => 'fa-robot', 'color' => 'orange'],
            ['name' => 'UKM Olahraga', 'desc' => 'Berbagai cabang olahraga fakultas', 'icon' => 'fa-futbol', 'color' => 'teal'],
        ];
        
        $data['beasiswa'] = [
            ['name' => 'Beasiswa KIP-Kuliah', 'source' => 'Kemdikbud', 'amount' => 'Rp 2.400.000/semester'],
            ['name' => 'Beasiswa Unggulan', 'source' => 'Kemdikbud', 'amount' => 'Full tuition + living cost'],
            ['name' => 'Beasiswa Prestasi Akademik', 'source' => 'Fakultas', 'amount' => 'Potongan UKT 50%'],
            ['name' => 'Beasiswa Tahfidz', 'source' => 'Yayasan', 'amount' => 'Rp 5.000.000/tahun'],
        ];

        // --- DATA DINAMIS DARI DATABASE (Bisa diatur di Admin) ---
        $data['tracer'] = $this->db->table_exists('tracer_settings')
            ? $this->db->where('is_active', 1)->get('tracer_settings')->row()
            : null;
        $data['portal_items'] = $this->db->table_exists('portal_content')
            ? $this->db->where('is_active', 1)->order_by('sort_order', 'ASC')->get('portal_content')->result()
            : [];
        $data['elearning_links'] = $this->db->table_exists('elearning_links')
            ? $this->db->where('is_active', 1)->get('elearning_links')->result()
            : [];

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/kemahasiswaan', $data);
        $this->load->view('templates/footer');
    }
}