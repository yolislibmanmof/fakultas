<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CI_Controller {

    private $backup_dir;

    public function __construct(){
        parent::__construct();

        $this->load->helper(['file', 'download', 'url']);
        $this->load->dbutil();

        if (file_exists(APPPATH . 'helpers/update_helper.php')) {
            $this->load->helper('update');
        }

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
        try {
            if ($this->Setting_model->get('auto_backup_daily', '1') === '1') $this->_auto_backup();
        } catch (Exception $e) { /* skip */ }

        $files = [];
        foreach (glob($this->backup_dir . '*.sql') as $f) {
            $files[] = ['name' => basename($f), 'size' => $this->_human(filesize($f)), 'date' => date('d M Y H:i', filemtime($f))];
        }
        usort($files, function($a,$b){ return strcmp($b['name'], $a['name']); });

        $drop_enabled = $this->Setting_model->get('backup_drop_tables', '0') === '1';

        $data = ['title' => 'Backup & Restore', 'active_menu' => 'backup'];
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/backup', ['files' => $files, 'drop_enabled' => $drop_enabled]);
        $this->load->view('templates/admin_footer');
    }

    private function _auto_backup(){
        $last = $this->Setting_model->get('last_auto_backup', '');
        if ($last && (time() - strtotime($last)) < 86400) return;
        $this->_create_backup('auto');
        $this->Setting_model->set('last_auto_backup', date('Y-m-d H:i:s'));
    }

    public function create(){ $this->_create_backup('manual'); redirect('admin/backup'); }

    // Toggle mode DROP TABLE (untuk migrasi server)
    public function toggle_drop(){
        $current = $this->Setting_model->get('backup_drop_tables', '0');
        $new = $current === '1' ? '0' : '1';
        $this->Setting_model->set('backup_drop_tables', $new);
        $this->session->set_flashdata('success', $new === '1' ? 'Mode DROP TABLE DIAKTIFKAN — hanya untuk migrasi server!' : 'Mode aman aktif (tanpa DROP TABLE)');
        redirect('admin/backup');
    }

    private function _create_backup($tag){
        $tables = $this->db->list_tables();
        $do_drop = $this->Setting_model->get('backup_drop_tables', '0') === '1';

        // ===== HEADER =====
        $output  = "-- =====================================================\n";
        $output .= "-- BACKUP: " . $this->db->database . "\n";
        $output .= "-- WAKTU: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- VERSI: " . (defined('APP_VERSION') ? APP_VERSION : '1.0.0') . "\n";
        $output .= "-- TABEL: " . count($tables) . "\n";
        $output .= "-- TIPE: " . strtoupper($tag) . ($do_drop ? ' (DROP MODE)' : ' (SAFE MODE)') . "\n";
        $output .= "-- =====================================================\n\n";
        $output .= "SET NAMES utf8mb4;\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $output .= "SET time_zone = '+00:00';\n\n";
        $output .= "START TRANSACTION;\n\n";

        foreach ($tables as $table) {
            $output .= "# -----------------------------------------------\n";
            $output .= "# TABEL: $table\n";
            $output .= "# -----------------------------------------------\n\n";

            if ($do_drop) {
                // Mode migrasi: DROP + CREATE lengkap
                $output .= "DROP TABLE IF EXISTS `$table`;\n";
                $res = $this->db->query("SHOW CREATE TABLE `$table`");
                if ($res && $res->num_rows() > 0) {
                    $row = $res->row_array();
                    $create_stmt = isset($row['Create Table']) ? $row['Create Table'] : '';
                    $output .= $create_stmt . ";\n\n";
                }
            } else {
                // Mode aman: DELETE data saja, tabel tidak hilang
                $output .= "DELETE FROM `$table`;\n\n";
            }

            // INSERT data dengan chunking (hemat memory)
            $query = $this->db->get($table);
            $result = $query->result_array();

            if (!empty($result)) {
                $cols = array_keys($result[0]);
                $col_list = '`' . implode('`,`', $cols) . '`';

                $chunks = array_chunk($result, 500);
                foreach ($chunks as $chunk) {
                    $output .= "INSERT INTO `$table` ($col_list) VALUES\n";
                    $rows = [];
                    foreach ($chunk as $row) {
                        $vals = [];
                        foreach ($cols as $c) {
                            $v = $row[$c];
                            if ($v === null) $vals[] = 'NULL';
                            else $vals[] = "'" . $this->db->escape_str($v) . "'";
                        }
                        $rows[] = '(' . implode(',', $vals) . ')';
                    }
                    $output .= implode(",\n", $rows) . ";\n\n";
                }
            }
        }

        $output .= "COMMIT;\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $name = 'backup_' . $tag . '_' . date('Ymd_His') . '.sql';
        write_file($this->backup_dir . $name, $output);

        $mode = $do_drop ? 'DROP MODE (migrasi)' : 'SAFE MODE (aman)';
        $this->session->set_flashdata('success', 'Backup ' . $mode . ' dibuat: <b>' . $name . '</b> (' . $this->_human(strlen($output)) . ')');
        return $name;
    }

    public function download($file){
        $file = basename($file); $path = $this->backup_dir . $file;
        if (!file_exists($path)) redirect('admin/backup');
        force_download($file, file_get_contents($path));
    }

    public function restore($file){
        $file = basename($file); $path = $this->backup_dir . $file;
        if (!file_exists($path)) redirect('admin/backup');

        // ⚠️ KONFIRMASI TAMBAHAN jika file berisi DROP TABLE
        $content = file_get_contents($path);
        if (strpos($content, 'DROP TABLE') !== false) {
            // Sudah di-handle di view dengan confirm dialog
            // Cek token konfirmasi di session
            $confirm = $this->session->userdata('confirm_restore_' . md5($file));
            if (!$confirm) {
                $this->session->set_flashdata('error', 'File mengandung DROP TABLE. Konfirmasi melalui halaman utama.');
                redirect('admin/backup');
            }
            $this->session->unset_userdata('confirm_restore_' . md5($file));
        }

        try {
            if (function_exists('run_sql_dump')) {
                run_sql_dump($content);
            } else {
                // Fallback: eksekusi per statement
                $this->db->trans_start();
                $statements = preg_split('/;\s*\n/', $content);
                foreach ($statements as $stmt) {
                    $stmt = trim($stmt);
                    if ($stmt === '' || strpos($stmt, '--') === 0) continue;
                    $this->db->query($stmt);
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaksi gagal, semua perubahan di-rollback');
                }
            }
            $this->session->set_flashdata('success', '✅ Restore berhasil dari <b>' . $file . '</b>');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', '❌ Restore gagal: ' . $e->getMessage());
        }
        redirect('admin/backup');
    }

    public function confirm_restore($file){
        // Set token konfirmasi lalu redirect ke restore
        $this->session->set_userdata('confirm_restore_' . md5(basename($file)), 1);
        redirect('admin/backup/restore/' . basename($file));
    }

    public function delete($file){
        $file = basename($file); $path = $this->backup_dir . $file;
        if (file_exists($path)) @unlink($path);
        $this->session->set_flashdata('success', 'Backup dihapus.');
        redirect('admin/backup');
    }

    private function _human($b){
        if ($b >= 1048576) return round($b/1048576, 2) . ' MB';
        if ($b >= 1024) return round($b/1024, 1) . ' KB';
        return $b . ' B';
    }
}