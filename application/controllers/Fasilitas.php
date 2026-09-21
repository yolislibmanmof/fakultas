<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fasilitas extends CI_Controller {

    // Sinkron dengan admin Facilities controller
    private $types = [
        'laboratory' => 'Laboratorium',
        'classroom'  => 'Ruang Kelas',
        'library'    => 'Perpustakaan',
        'mosque'     => 'Tempat Ibadah',
        'sport'      => 'Fasilitas Olahraga',
        'other'      => 'Lainnya',
    ];

    private $type_icons = [
        'laboratory' => 'fa-flask',
        'classroom'  => 'fa-chalkboard',
        'library'    => 'fa-book-open',
        'mosque'     => 'fa-mosque',
        'sport'      => 'fa-running',
        'other'      => 'fa-building',
    ];

    public function index() {
        $type_filter = $this->input->get('type');
        $q = trim((string) $this->input->get('q'));

        $this->db->where('is_active', 1);

        if ($type_filter && array_key_exists($type_filter, $this->types)) {
            $this->db->where('type', $type_filter);
        }

        if ($q !== '') {
            $this->db->group_start()
                     ->like('name', $q)
                     ->or_like('location', $q)
                     ->or_like('description', $q)
                     ->group_end();
        }

        $this->db->order_by('type', 'ASC')->order_by('name', 'ASC');
        $facilities = $this->db->get('facilities')->result();

        $grouped = [];
        $counts = [];
        foreach ($facilities as $f) {
            $grouped[$f->type][] = $f;
            $counts[$f->type] = ($counts[$f->type] ?? 0) + 1;
        }

        $data = [
            'title'        => 'Fasilitas Kampus - ' . $this->_site_name(),
            'description'  => 'Jelajahi fasilitas modern kampus kami.',
            'facilities'   => $facilities,
            'grouped'      => $grouped,
            'counts'       => $counts,
            'total'        => count($facilities),
            'types'        => $this->types,
            'type_icons'   => $this->type_icons,
            'type_filter'  => $type_filter,
            'q'            => $q,
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/fasilitas_list', $data);
        $this->load->view('templates/footer');
    }

    private function _site_name() {
        $this->load->model('Setting_model');
        return $this->Setting_model->get('site_name', 'Fakultas Teknik & Ilmu Komputer');
    }
}