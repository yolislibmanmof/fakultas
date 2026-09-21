<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kalender extends CI_Controller {

    private $categories = ['pmb' => 'PMB', 'akademik' => 'Akademik', 'ujian' => 'Ujian', 'libur' => 'Libur', 'wisuda' => 'Wisuda', 'seminar' => 'Seminar', 'lainnya' => 'Lainnya'];

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model(['Auth_model', 'Setting_model']);
        $this->load->helper(['url', 'text', 'download']);
    }

    public function index() {
        $cat = $this->input->get('cat');
        $year = $this->input->get('year') ?: date('Y');

        $this->db->order_by('start_date', 'DESC');
        if ($cat && array_key_exists($cat, $this->categories)) $this->db->where('category', $cat);
        // Filter by year (cek kolom tahun dari start_date)
        if ($year && preg_match('/^\d{4}$/', $year)) {
            $this->db->where('YEAR(start_date)', (int)$year);
        }
        $this->db->limit(500);
        $events = $this->db->get('academic_calendar')->result();

        // Get distinct years for filter
        $years = [];
        try {
            $year_rows = $this->db->select('YEAR(start_date) as y', FALSE)
                ->where('start_date IS NOT NULL')
                ->group_by('YEAR(start_date)')
                ->order_by('y', 'DESC')
                ->get('academic_calendar')->result();
            foreach ($year_rows as $yr) if ($yr->y) $years[] = $yr->y;
        } catch (Exception $e) { $years = [date('Y')]; }
        if (!in_array(date('Y'), $years)) $years[] = date('Y');
        rsort($years);

        $data = [
            'title' => 'Kalender Akademik', 'active_menu' => 'kalender',
            'events' => $events, 'categories' => $this->categories, 'cat' => $cat,
            'years' => $years, 'current_year' => $year,
        ];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/kalender/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data = ['title' => 'Tambah Agenda', 'active_menu' => 'kalender', 'event' => NULL, 'categories' => $this->categories];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/kalender/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->create(); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->insert('academic_calendar', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'create_event', 'Tambah agenda: ' . $this->input->post('event_name', TRUE));
        $this->session->set_flashdata('success', 'Agenda berhasil ditambahkan!');
        redirect('admin/kalender');
    }

    public function edit($id) {
        $event = $this->db->get_where('academic_calendar', ['id' => $id])->row();
        if (!$event) redirect('admin/kalender');
        $data = ['title' => 'Edit Agenda', 'active_menu' => 'kalender', 'event' => $event, 'categories' => $this->categories];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/kalender/form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update($id) {
        $event = $this->db->get_where('academic_calendar', ['id' => $id])->row();
        if (!$event) redirect('admin/kalender');
        $this->_validate();
        if ($this->form_validation->run() == FALSE) { $this->edit($id); return; }

        $payload = $this->_payload();
        if ($payload === FALSE) return;

        $this->db->where('id', $id)->update('academic_calendar', $payload);
        $this->Auth_model->log_activity($this->session->userdata('user_id'), 'update_event', 'Edit agenda: ' . $this->input->post('event_name', TRUE));
        $this->session->set_flashdata('success', 'Agenda berhasil diperbarui!');
        redirect('admin/kalender');
    }

    public function delete($id) {
        $event = $this->db->get_where('academic_calendar', ['id' => $id])->row();
        if ($event) {
            $this->db->where('id', $id)->delete('academic_calendar');
            $this->Auth_model->log_activity($this->session->userdata('user_id'), 'delete_event', 'Hapus agenda: ' . $event->event_name);
            $this->session->set_flashdata('success', 'Agenda berhasil dihapus.');
        }
        redirect('admin/kalender');
    }

    // ===== 🔥 FITUR GILA: Duplicate event =====
    public function duplicate($id) {
        $event = $this->db->get_where('academic_calendar', ['id' => $id])->row();
        if (!$event) { show_404(); }
        $new = clone $event;
        unset($new->id);
        $new->event_name = $event->event_name . ' (Salinan)';
        $this->db->insert('academic_calendar', (array)$new);
        $this->session->set_flashdata('success', 'Agenda berhasil diduplikasi.');
        redirect('admin/kalender');
    }

    // ===== 🔥 FITUR GILA: iCal export (subscribe di Google Calendar) =====
    public function ical() {
        $events = $this->db
            ->where('is_active', 1)
            ->where('start_date IS NOT NULL')
            ->order_by('start_date', 'ASC')
            ->limit(500)
            ->get('academic_calendar')->result();

        $site_name = $this->Setting_model->get('site_name', 'Fakultas');
        $prodid = '-//' . $site_name . '//Academic Calendar//ID';

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:" . $prodid . "\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:Kalender Akademik\r\n";

        foreach ($events as $e) {
            $start = date('Ymd', strtotime($e->start_date));
            // Kalau end_date tidak sama dengan start_date, buat all-day event (VALUE=DATE)
            $end = $e->end_date ? date('Ymd', strtotime($e->end_date . ' +1 day')) : date('Ymd', strtotime($e->start_date . ' +1 day'));
            $uid = 'event-' . $e->id . '@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $uid . "\r\n";
            $ical .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $start . "\r\n";
            $ical .= "DTEND;VALUE=DATE:" . $end . "\r\n";
            $ical .= "SUMMARY:" . $this->_ical_escape($e->event_name) . "\r\n";
            if (!empty($e->description)) {
                $ical .= "DESCRIPTION:" . $this->_ical_escape($e->description) . "\r\n";
            }
            $ical .= "END:VEVENT\r\n";
        }
        $ical .= "END:VCALENDAR\r\n";

        $this->output
            ->set_content_type('text/calendar; charset=utf-8')
            ->set_header('Content-Disposition: attachment; filename="academic-calendar.ics"')
            ->set_output($ical);
    }

    private function _ical_escape($str) {
        $str = str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $str);
        return $str;
    }

    // ===== 🔥 FITUR GILA: AJAX calendar grid (month view) =====
    public function month_grid() {
        if (!$this->input->is_ajax_request()) show_404();
        $year = (int)($this->input->get('year') ?: date('Y'));
        $month = (int)($this->input->get('month') ?: date('m'));

        if ($month < 1 || $month > 12) $month = (int)date('m');
        if ($year < 2000 || $year > 2100) $year = (int)date('Y');

        $first_day = sprintf('%04d-%02d-01', $year, $month);
        $last_day  = date('Y-m-t', strtotime($first_day));

        $events = $this->db
            ->where('is_active', 1)
            ->group_start()
                ->where('start_date <=', $last_day)
                ->where('end_date >=', $first_day)
            ->group_end()
            ->order_by('start_date', 'ASC')
            ->get('academic_calendar')->result();

        // Group by date
        $by_date = [];
        foreach ($events as $e) {
            $start = strtotime($e->start_date);
            $end = strtotime($e->end_date ?: $e->start_date);
            for ($t = $start; $t <= $end; $t += 86400) {
                $d = date('Y-m-d', $t);
                if ($d >= $first_day && $d <= $last_day) {
                    if (!isset($by_date[$d])) $by_date[$d] = [];
                    $by_date[$d][] = ['id' => $e->id, 'name' => $e->event_name, 'category' => $e->category];
                }
            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'year' => $year, 'month' => $month,
            'events' => $by_date,
        ]));
    }

    // ===== 🔥 FITUR GILA: AJAX overlap check (prevent konflik event) =====
    public function check_overlap() {
        if (!$this->input->is_ajax_request()) show_404();
        $start = $this->input->post('start');
        $end = $this->input->post('end') ?: $start;
        $exclude_id = (int)$this->input->post('exclude_id');

        if (!$start || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['overlap' => false]));
            return;
        }

        $this->db->where('is_active', 1)
                 ->where('start_date <=', $end)
                 ->where('end_date >=', $start);
        if ($exclude_id > 0) $this->db->where('id !=', $exclude_id);
        $overlaps = $this->db->limit(5)->get('academic_calendar')->result();

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'overlap' => count($overlaps) > 0,
            'count' => count($overlaps),
            'events' => array_map(function ($e) {
                return ['name' => $e->event_name, 'start' => $e->start_date, 'end' => $e->end_date];
            }, $overlaps),
        ]));
    }

    private function _validate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('event_name', 'Nama Agenda', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'required|callback__validate_date');
        $this->form_validation->set_rules('end_date', 'Tanggal Selesai', 'callback__validate_date');
        $this->form_validation->set_rules('category', 'Kategori', 'callback__validate_category');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[1000]');
    }

    public function _validate_date($date) {
        if ($date === '' || $date === NULL) return TRUE;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->form_validation->set_message('_validate_date', 'Format tanggal tidak valid (YYYY-MM-DD).');
            return FALSE;
        }
        $parts = explode('-', $date);
        if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
            $this->form_validation->set_message('_validate_date', 'Tanggal tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    public function _validate_category($cat) {
        if (!array_key_exists($cat, $this->categories)) {
            $this->form_validation->set_message('_validate_category', 'Kategori tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    private function _payload() {
        $start = $this->input->post('start_date');
        $end   = $this->input->post('end_date') ?: $start;

        // Validasi: end_date tidak boleh sebelum start_date
        if ($end && $start && strtotime($end) < strtotime($start)) {
            $this->session->set_flashdata('error', 'Tanggal selesai tidak boleh sebelum tanggal mulai.');
            redirect('admin/kalender/create');
            return FALSE;
        }

        return [
            'event_name'  => $this->input->post('event_name', TRUE),
            'category'    => array_key_exists($this->input->post('category'), $this->categories) ? $this->input->post('category') : 'akademik',
            'start_date'  => $start,
            'end_date'    => $end,
            'description' => $this->input->post('description', TRUE),
            'is_active'   => $this->input->post('is_active') ? 1 : 0,
        ];
    }
}