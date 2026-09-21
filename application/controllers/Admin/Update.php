<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends CI_Controller {

    private $backup_dir;

    public function __construct(){
        parent::__construct();

        $this->load->helper(['file', 'download', 'url']);
        $this->load->dbutil();

        if (file_exists(APPPATH . 'helpers/update_helper.php')) {
            $this->load->helper('update');
        }

        // ⭐ WAJIB: load Setting_model
        $this->load->model('Setting_model');

        $this->backup_dir = APPPATH . 'backups/';
        if (!is_dir($this->backup_dir)) {
            @mkdir($this->backup_dir, 0775, true);
            @file_put_contents($this->backup_dir . '.htaccess', "Deny from all\n");
        }

        $this->_guard();
    }

    private function _guard(){
        $logged = $this->session->userdata('logged_in')
               ?: $this->session->userdata('is_logged_in')
               ?: $this->session->userdata('user_id');
        if (!$logged) redirect('auth/login');

        $role = strtolower(trim((string)(
            $this->session->userdata('role')
            ?? $this->session->userdata('user_role')
            ?? $this->session->userdata('level')
            ?? ''
        )));
        $allowed = ['admin', 'superadmin', 'administrator', 'super_admin', 'root', 'editor'];
        if (!empty($role) && !in_array($role, $allowed)) {
            $this->session->set_flashdata('error', 'Akses ditolak. Role: ' . $role);
            redirect('admin/dashboard');
        }
    }

    public function index(){
        $logf = APPPATH . 'logs/update_history.json';
        $log = file_exists($logf) ? json_decode(file_get_contents($logf), true) : [];

        $data = ['title' => 'Update Sistem', 'active_menu' => 'update'];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/update', [
            'version' => function_exists('app_version') ? app_version() : '1.0.0',
            'manifest_url' => $this->Setting_model->get('update_manifest_url', ''),
            'log' => $log ?: [],
        ]);
        $this->load->view('templates/admin_footer');
    }

    public function save_manifest(){
        $this->Setting_model->set('update_manifest_url', trim($this->input->post('url')));
        $this->session->set_flashdata('success', 'URL manifest disimpan.');
        redirect('admin/update');
    }

    public function check(){
        $url = $this->Setting_model->get('update_manifest_url', '');
        $m = function_exists('fetch_update_manifest') ? fetch_update_manifest($url) : null;
        if (!$m) { echo json_encode(['ok' => false, 'msg' => 'Tidak dapat menghubungi server update.']); return; }
        $cur = function_exists('app_version') ? app_version() : '1.0.0';
        $has = version_compare($m->version, $cur, '>');
        echo json_encode(['ok' => true, 'has_update' => $has, 'latest' => $m->version, 'current' => $cur, 'notes' => $m->notes ?? [], 'released' => $m->released ?? '']);
    }

    public function run(){
        $url = $this->Setting_model->get('update_manifest_url', '');
        $m = function_exists('fetch_update_manifest') ? fetch_update_manifest($url) : null;
        if (!$m) { $this->session->set_flashdata('error', 'Manifest tidak ditemukan.'); redirect('admin/update'); }
        $cur = function_exists('app_version') ? app_version() : '1.0.0';
        if (!version_compare($m->version, $cur, '>')) { $this->session->set_flashdata('error', 'Sudah versi terbaru.'); redirect('admin/update'); }

        $zipTmp = $this->backup_dir . 'pkg_' . time() . '.zip';
        if (!function_exists('download_url_to') || !download_url_to($m->package_url, $zipTmp)) {
            $this->session->set_flashdata('error', 'Gagal mengunduh paket.'); redirect('admin/update');
        }
        if (!empty($m->sha256) && hash_file('sha256', $zipTmp) !== $m->sha256) {
            @unlink($zipTmp); $this->session->set_flashdata('error', 'Checksum tidak cocok.'); redirect('admin/update');
        }
        $this->_apply($zipTmp, $m->version, $m);
        @unlink($zipTmp);
        redirect('admin/update');
    }

    public function upload(){
        if (empty($_FILES['pkg']['tmp_name'])) redirect('admin/update');
        $zipTmp = $this->backup_dir . 'manual_' . time() . '.zip';
        move_uploaded_file($_FILES['pkg']['tmp_name'], $zipTmp);
        $ver = $this->input->post('version') ?: date('Ymd.His');
        $this->_apply($zipTmp, $ver, null);
        @unlink($zipTmp);
        redirect('admin/update');
    }

    private function _apply($zipPath, $version, $manifest){
        $preSql = null; $snap = null;
        try {
            $prefs = ['format'=>'txt','filename'=>'pre.sql','add_drop'=>TRUE,'add_insert'=>TRUE,'newline'=>"\n",'foreign_key_checks'=>FALSE];
            $preSql = $this->backup_dir . 'pre_update_' . date('Ymd_His') . '.sql';
            $b = $this->dbutil->backup($prefs);
            write_file($preSql, $b);

            if (function_exists('snapshot_code')) {
                $snap = $this->backup_dir . 'code_snapshot_' . date('Ymd_His') . '.zip';
                snapshot_code($snap);
            }

            $this->_extract_protected($zipPath);

            $za = new ZipArchive();
            if ($za->open($zipPath) === true) {
                $mig = $za->getFromName('migration.sql');
                if ($mig && function_exists('run_sql_dump')) run_sql_dump($mig);
                $za->close();
            }

            write_file(APPPATH . 'config/version.php', "<?php defined('BASEPATH') OR exit; define('APP_VERSION', '" . $version . "');\n");

            if (function_exists('update_log_write')) {
                update_log_write(['time' => date('Y-m-d H:i:s'), 'version' => $version, 'status' => 'SUCCESS']);
            }
            $this->session->set_flashdata('success', 'Update ke v' . $version . ' BERHASIL. Data aman.');
        } catch (Exception $e) {
            if ($snap && file_exists($snap)) $this->_restore_snapshot($snap);
            if ($preSql && file_exists($preSql)) {
                try { if (function_exists('run_sql_dump')) run_sql_dump(file_get_contents($preSql)); } catch (Exception $x) {}
            }
            if (function_exists('update_log_write')) {
                update_log_write(['time' => date('Y-m-d H:i:s'), 'version' => $version, 'status' => 'FAILED: ' . $e->getMessage()]);
            }
            $this->session->set_flashdata('error', 'Update GAGAL & di-rollback: ' . $e->getMessage());
        }
    }

    private function _extract_protected($zipPath){
        $protected = [
            'application/config/database.php',
            'application/config/version.php',
            'application/backups/',
            'assets/uploads/',
            'application/logs/',
        ];
        $za = new ZipArchive();
        if ($za->open($zipPath) !== true) throw new Exception('Zip tidak valid.');
        for ($i = 0; $i < $za->numFiles; $i++) {
            $name = $za->getNameIndex($i);
            if (substr($name, -1) === '/') continue;
            if (strpos($name, '..') !== false) continue;
            $skip = false;
            foreach ($protected as $p) if (strpos($name, $p) === 0) { $skip = true; break; }
            if ($skip) continue;
            $target = FCPATH . $name;
            if (!is_dir(dirname($target))) @mkdir(dirname($target), 0775, true);
            file_put_contents($target, $za->getFromIndex($i));
        }
        $za->close();
        return true;
    }

    private function _restore_snapshot($snap){
        $za = new ZipArchive();
        if ($za->open($snap) !== true) return;
        for ($i = 0; $i < $za->numFiles; $i++) {
            $name = $za->getNameIndex($i);
            if (substr($name, -1) === '/') continue;
            $target = FCPATH . $name;
            if (!is_dir(dirname($target))) @mkdir(dirname($target), 0775, true);
            file_put_contents($target, $za->getFromIndex($i));
        }
        $za->close();
    }

    // ===== HAPUS RIWAYAT UPDATE =====
    public function clear_log(){
        $logf = APPPATH . 'logs/update_history.json';
        if (file_exists($logf)) @unlink($logf);
        $this->session->set_flashdata('success', 'Riwayat update berhasil dihapus.');
        redirect('admin/update');
    }

    // ===== UNDUH FILE LOG =====
    public function download_log(){
        $logf = APPPATH . 'logs/update_history.json';
        if (!file_exists($logf)) {
            $this->session->set_flashdata('error', 'File log tidak ditemukan.');
            redirect('admin/update');
        }
        $this->load->helper('download');
        force_download('update_history_' . date('Ymd_His') . '.json', file_get_contents($logf));
    }
}