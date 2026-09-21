<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riset extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Research_model');
    }

    public function index() {
        $data['title'] = 'Riset & Pengabdian - Fakultas';
        $data['type'] = $this->input->get('type', TRUE);
        $data['year'] = $this->input->get('year', TRUE);
        $data['items'] = $this->Research_model->get_filtered($data['type'] ?: NULL, $data['year'] ?: NULL);
        $data['years'] = $this->Research_model->get_years();
        $data['stats'] = [
            'research'  => $this->Research_model->count_by_type('research'),
            'community' => $this->Research_model->count_by_type('community_service'),
        ];

        // ===== 🔥 UPGRADE: chart aktivitas per tahun (stacked) =====
        $yr_rows = $this->db->select('year, type, COUNT(*) AS c')
            ->group_by(['year', 'type'])
            ->order_by('year', 'ASC')
            ->get('research')->result();
        $year_chart = [];
        foreach ($yr_rows as $row) {
            $y = (int)$row->year;
            if (!isset($year_chart[$y])) $year_chart[$y] = ['year' => $y, 'research' => 0, 'community' => 0];
            if ($row->type === 'research') $year_chart[$y]['research'] += (int)$row->c;
            else $year_chart[$y]['community'] += (int)$row->c;
        }
        $data['year_chart'] = array_values($year_chart);

        $this->load->view('templates/header', $data);
        $this->load->view('frontend/riset_list', $data);
        $this->load->view('templates/footer');
    }
}