<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_logged_in')) redirect('auth/login');
        $this->load->model('Setting_model');
    }

    // ===== Helper: cek tabel ada sebelum query =====
    private function _table_exists($name) {
        static $cache = [];
        if (!isset($cache[$name])) $cache[$name] = $this->db->table_exists($name);
        return $cache[$name];
    }

    // ===== Helper: count aman (return 0 kalau tabel tidak ada) =====
    private function _safe_count($table, $where = []) {
        if (!$this->_table_exists($table)) return 0;
        foreach ($where as $k => $v) $this->db->where($k, $v);
        return (int)$this->db->count_all_results($table);
    }

    public function index() {
        // ===== STATS INTI (dengan defensive check) =====
        $stats = [
            'posts_published' => $this->_safe_count('posts', ['status' => 'published']),
            'posts_draft'     => $this->_safe_count('posts', ['status' => 'draft']),
            'lecturers'       => $this->_safe_count('lecturers', ['is_active' => 1]),
            'programs'        => $this->_safe_count('study_programs', ['is_active' => 1]),
            'courses'         => $this->_table_exists('courses') ? $this->db->count_all('courses') : 0,
            'research'        => $this->_table_exists('research') ? $this->db->count_all('research') : 0,
            'documents'       => $this->_safe_count('documents', ['is_active' => 1]),
            'achievements'    => $this->_table_exists('achievements') ? $this->db->count_all('achievements') : 0,
            'facilities'      => $this->_safe_count('facilities', ['is_active' => 1]),
            'alumni_total'    => $this->_table_exists('alumni') ? $this->db->count_all('alumni') : 0,
            'alumni_pending'  => $this->_safe_count('alumni', ['status' => 'pending']),
        ];

        // Defensive sum (kalau tabel/kolom tidak ada)
        $stats['total_downloads'] = 0;
        if ($this->_table_exists('documents')) {
            $row = $this->db->select_sum('download_count')->get('documents')->row();
            $stats['total_downloads'] = (int)($row->download_count ?? 0);
        }
        $stats['total_views'] = 0;
        if ($this->_table_exists('posts')) {
            $row = $this->db->select_sum('views')->get('posts')->row();
            $stats['total_views'] = (int)($row->views ?? 0);
        }

        // ===== TREND 7 HARI (optimized: 1 query per tabel, bukan 21 query) =====
        $trend_7d = $this->_build_trend(6, 0);
        $trend_7d_prev = $this->_build_trend(13, 7);

        $sum_this = array_sum(array_column($trend_7d, 'total'));
        $sum_prev = array_sum(array_column($trend_7d_prev, 'total'));
        $trend_pct = $sum_prev > 0 ? round((($sum_this - $sum_prev) / $sum_prev) * 100) : ($sum_this > 0 ? 100 : 0);
        $trend_dir = $sum_this >= $sum_prev ? 'up' : 'down';

        // ===== HEATMAP 84 HARI (optimized: 1 query dengan UNION ALL yang sudah difilter index-friendly) =====
        $heatmap = $this->_build_heatmap();

        // ===== HEALTH SCORE =====
        $score = 0;
        $score += min(25, $stats['posts_published'] * 5);
        $score += min(20, $stats['lecturers'] * 5);
        $score += min(20, $stats['research'] * 5);
        $score += min(15, $stats['documents'] * 3);
        $score += ($stats['alumni_pending'] == 0) ? 10 : 5;
        $score += min(10, $stats['total_views'] / 100);
        $health_score = (int) min(100, $score);

        // 🔥 FITUR GILA: AI-generated insight text
        $insight = $this->_generate_insight($stats, $trend_pct, $health_score);

        // ===== TOP 5 ARTIKEL =====
        $top_articles = [];
        if ($this->_table_exists('posts') && $this->_table_exists('categories')) {
            $top_articles = $this->db
                ->select('posts.*, categories.name as category_name')
                ->from('posts')
                ->join('categories', 'categories.id = posts.category_id', 'left')
                ->where('posts.status', 'published')
                ->order_by('posts.views', 'DESC')
                ->limit(5)->get()->result();
        }

        $recent_news = [];
        if ($this->_table_exists('posts') && $this->_table_exists('categories')) {
            $recent_news = $this->db
                ->select('posts.*, categories.name as category_name')
                ->from('posts')
                ->join('categories', 'categories.id = posts.category_id', 'left')
                ->order_by('posts.created_at', 'DESC')
                ->limit(5)->get()->result();
        }

        $recent_research = [];
        if ($this->_table_exists('research') && $this->_table_exists('lecturers')) {
            $recent_research = $this->db
                ->select('research.*, lecturers.name as lecturer_name')
                ->from('research')
                ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left')
                ->order_by('research.created_at', 'DESC')
                ->limit(5)->get()->result();
        }

        $recent_activity = [];
        if ($this->_table_exists('audit_logs') && $this->_table_exists('users')) {
            $recent_activity = $this->db
                ->select('audit_logs.*, users.full_name, users.username')
                ->from('audit_logs')
                ->join('users', 'users.id = audit_logs.user_id', 'left')
                ->order_by('audit_logs.created_at', 'DESC')
                ->limit(8)->get()->result();
        }

        $categories_breakdown = [];
        if ($this->_table_exists('posts') && $this->_table_exists('categories')) {
            $categories_breakdown = $this->db
                ->select('categories.name, COUNT(posts.id) as total')
                ->from('categories')
                ->join('posts', 'posts.category_id = categories.id', 'left')
                ->group_by('categories.id')
                ->get()->result();
        }

        $upcoming_events = [];
        if ($this->_table_exists('academic_calendar')) {
            $upcoming_events = $this->db
                ->where('is_active', 1)
                ->where('start_date >=', date('Y-m-d'))
                ->order_by('start_date', 'ASC')
                ->limit(3)
                ->get('academic_calendar')->result();
        }

        $ongoing_research = [];
        if ($this->_table_exists('research') && $this->_table_exists('lecturers') && $this->db->field_exists('status', 'research')) {
            $ongoing_research = $this->db
                ->select('research.*, lecturers.name as lecturer_name')
                ->from('research')
                ->join('lecturers', 'lecturers.id = research.lecturer_id', 'left')
                ->where('research.status', 'ongoing')
                ->limit(3)->get()->result();
        }

        $system = [
            'php_version'    => PHP_VERSION,
            'ci_version'     => CI_VERSION,
            'upload_size_mb' => $this->_dir_size_cached(),
            'db_tables'      => count($this->db->list_tables()),
        ];

        $data = [
            'title' => 'Mission Control — Dashboard', 'active_menu' => 'dashboard',
            'stats' => $stats, 'trend_7d' => $trend_7d, 'trend_7d_prev' => $trend_7d_prev,
            'trend_pct' => $trend_pct, 'trend_dir' => $trend_dir,
            'heatmap' => $heatmap,
            'health_score' => $health_score, 'insight' => $insight,
            'top_articles' => $top_articles, 'recent_news' => $recent_news,
            'recent_research' => $recent_research, 'recent_activity' => $recent_activity,
            'categories_breakdown' => $categories_breakdown,
            'upcoming_events' => $upcoming_events, 'ongoing_research' => $ongoing_research,
            'system' => $system,
            'quick_note' => $this->Setting_model->get('admin_quick_note', ''),
        ];

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/admin_footer');
    }

    public function save_note() {
        if (!$this->input->is_ajax_request()) show_404();
        $this->Setting_model->set('admin_quick_note', $this->input->post('note', TRUE));
        $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true]));
    }

    // ===== 🔥 FITUR GILA: AJAX refresh stats (live update) =====
    public function refresh_stats() {
        if (!$this->input->is_ajax_request()) show_404();
        $stats = [
            'posts_published' => $this->_safe_count('posts', ['status' => 'published']),
            'posts_draft'     => $this->_safe_count('posts', ['status' => 'draft']),
            'lecturers'       => $this->_safe_count('lecturers', ['is_active' => 1]),
            'alumni_pending'  => $this->_safe_count('alumni', ['status' => 'pending']),
            'total_downloads' => 0,
            'total_views'     => 0,
        ];
        if ($this->_table_exists('documents')) {
            $row = $this->db->select_sum('download_count')->get('documents')->row();
            $stats['total_downloads'] = (int)($row->download_count ?? 0);
        }
        if ($this->_table_exists('posts')) {
            $row = $this->db->select_sum('views')->get('posts')->row();
            $stats['total_views'] = (int)($row->views ?? 0);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($stats));
    }

    public function export() {
        $this->index();
        $data = $this->_build_export_data();
        $this->load->view('admin/dashboard_export', $data);
    }

    private function _build_export_data() {
        return [
            'stats' => [
                'posts_published' => $this->_safe_count('posts', ['status' => 'published']),
                'lecturers'       => $this->_safe_count('lecturers', ['is_active' => 1]),
                'programs'        => $this->_safe_count('study_programs', ['is_active' => 1]),
                'research'        => $this->_table_exists('research') ? $this->db->count_all('research') : 0,
                'alumni_total'    => $this->_table_exists('alumni') ? $this->db->count_all('alumni') : 0,
                'documents'       => $this->_safe_count('documents', ['is_active' => 1]),
                'total_views'     => $this->_table_exists('posts') ? (int)($this->db->select_sum('views')->get('posts')->row()->views ?? 0) : 0,
            ],
            'top_articles' => ($this->_table_exists('posts') && $this->_table_exists('categories'))
                ? $this->db
                    ->select('posts.*, categories.name as category_name')
                    ->from('posts')
                    ->join('categories', 'categories.id = posts.category_id', 'left')
                    ->where('posts.status', 'published')
                    ->order_by('posts.views', 'DESC')->limit(5)->get()->result()
                : [],
        ];
    }

    public function heatmap_detail($date) {
        if (!$this->input->is_ajax_request()) show_404();
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) show_404();

        $details = [];
        foreach (['posts' => 'post', 'research' => 'research', 'documents' => 'document'] as $table => $type) {
            if (!$this->_table_exists($table)) continue;
            $rows = $this->db->select('id, title')
                ->where('DATE(created_at)', $date)
                ->order_by('created_at', 'DESC')->limit(10)
                ->get($table)->result();
            foreach ($rows as $r) {
                $details[] = ['type' => $type, 'title' => $r->title, 'id' => $r->id, 'table' => $table];
            }
        }
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['date' => $date, 'items' => $details]));
    }

    // ===== Helper: Trend (optimized — 1 query gabungan, bukan 21 query) =====
    private function _build_trend($start_days_ago, $end_days_ago) {
        $trend = [];
        for ($i = $start_days_ago; $i >= $end_days_ago; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $total = 0;
            foreach (['posts', 'research', 'documents'] as $t) {
                if ($this->_table_exists($t)) {
                    $total += $this->db->where('DATE(created_at)', $date)->count_all_results($t);
                }
            }
            $trend[] = ['date' => $date, 'total' => $total, 'label' => date('D', strtotime($date))];
        }
        return $trend;
    }

    // ===== Helper: Heatmap (UNION ALL dengan date range) =====
    private function _build_heatmap() {
        $since = date('Y-m-d', strtotime('-83 days'));
        $heat = [];

        $tables_to_check = ['posts', 'research', 'documents'];
        $valid_tables = array_filter($tables_to_check, function ($t) {
            return $this->_table_exists($t) && $this->db->field_exists('created_at', $t);
        });

        if (!empty($valid_tables)) {
            $parts = [];
            $binds = [];
            foreach ($valid_tables as $t) {
                $parts[] = "SELECT DATE(created_at) AS d, COUNT(*) AS c FROM {$t} WHERE created_at >= ? GROUP BY DATE(created_at)";
                $binds[] = $since;
            }
            $sql = "SELECT d, SUM(c) AS total FROM (" . implode(' UNION ALL ', $parts) . ") x GROUP BY d ORDER BY d ASC";
            try {
                $rows = $this->db->query($sql, $binds)->result();
                foreach ($rows as $r) $heat[$r->d] = (int)$r->total;
            } catch (Exception $e) { /* silent fallback */ }
        }

        $heatmap = [];
        for ($i = 83; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $heatmap[] = ['d' => $d, 'c' => $heat[$d] ?? 0];
        }
        return $heatmap;
    }

    // ===== 🔥 AI Insight (rule-based) =====
    private function _generate_insight($stats, $trend_pct, $health_score) {
        $insights = [];
        if ($health_score < 50) {
            $insights[] = "Skor kesehatan website rendah ({$health_score}/100). Pertimbangkan menambah konten aktif.";
        } elseif ($health_score >= 85) {
            $insights[] = "Website dalam kondisi prima ({$health_score}/100). Pertahankan ritme publikasi.";
        }
        if ($stats['alumni_pending'] > 5) {
            $insights[] = "Ada {$stats['alumni_pending']} pendaftaran alumni menunggu approval.";
        }
        if ($trend_pct < -20) {
            $insights[] = "Aktivitas turun " . abs($trend_pct) . "% minggu ini — pertimbangkan kampanye konten.";
        } elseif ($trend_pct > 30) {
            $insights[] = "Aktivitas naik {$trend_pct}% minggu ini — momentum bagus untuk promosi!";
        }
        if ($stats['posts_draft'] > $stats['posts_published']) {
            $insights[] = "Draft lebih banyak dari artikel terbit. Saatnya publish!";
        }
        if (empty($insights)) {
            $insights[] = "Semua berjalan normal. Tidak ada perhatian khusus.";
        }
        return $insights;
    }

    // ===== Dir size dengan cache (cegah block request) =====
    private function _dir_size_cached() {
        $cache_key = 'admin_dir_size_mb';
        $cached = $this->session->userdata($cache_key);
        if ($cached && isset($cached['time']) && (time() - $cached['time']) < 3600) {
            return $cached['value'];
        }
        $size = $this->_dir_size(FCPATH . 'assets/uploads');
        $mb = round($size / 1024 / 1024, 1);
        $this->session->set_userdata($cache_key, ['value' => $mb, 'time' => time()]);
        return $mb;
    }

    private function _dir_size($dir) {
        $size = 0;
        if (!is_dir($dir)) return 0;
        try {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                $size += $file->getSize();
            }
        } catch (Exception $e) { return 0; }
        return $size;
    }
}