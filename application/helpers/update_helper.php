<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('app_version')) {
    function app_version(){
        if (defined('APP_VERSION')) return APP_VERSION;
        $f = APPPATH . 'config/version.php';
        if (file_exists($f)) { include_once $f; if (defined('APP_VERSION')) return APP_VERSION; }
        return '1.0.0';
    }
}

if (!function_exists('fetch_update_manifest')) {
    function fetch_update_manifest($url){
        if (!$url) return null;
        $ctx = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: FakultasCMS\r\n"]]);
        $raw = @file_get_contents($url, false, $ctx);
        if (!$raw) return null;
        $j = json_decode($raw);
        return $j;
    }
}

if (!function_exists('download_url_to')) {
    function download_url_to($url, $dest){
        $raw = @file_get_contents($url);
        if ($raw === false) return false;
        return file_put_contents($dest, $raw) !== false;
    }
}

if (!function_exists('run_sql_dump')) {
    // Jalankan dump SQL per-statement (aman untuk restore/migrasi)
    function run_sql_dump($sql){
        $CI =& get_instance();
        $lines = explode("\n", $sql);
        $buffer = '';
        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '' || strpos($t, '--') === 0 || strpos($t, '#') === 0) continue;
            $buffer .= $line . "\n";
            if (preg_match('/;\s*$/', $t)) { $CI->db->query($buffer); $buffer = ''; }
        }
        if (trim($buffer) !== '') $CI->db->query($buffer);
        return true;
    }
}

if (!function_exists('snapshot_code')) {
    // Zip folder application (kecuali backups & logs) untuk rollback
    function snapshot_code($destZip){
        $zip = new ZipArchive();
        if ($zip->open($destZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return false;
        $base = rtrim(APPPATH, '/');
        $skip = ['/backups/', '/logs/', '/cache/'];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
        foreach ($it as $file) {
            if ($file->isDir()) continue;
            $real = $file->getPathname();
            $rel = 'application/' . str_replace('\\', '/', substr($real, strlen($base) + 1));
            $bad = false; foreach ($skip as $s) if (strpos('/' . $rel, $s) !== false) { $bad = true; break; }
            if ($bad) continue;
            $zip->addFile($real, $rel);
        }
        $zip->close();
        return true;
    }
}

if (!function_exists('update_log_write')) {
    function update_log_write($entry){
        $f = APPPATH . 'logs/update_history.json';
        $log = file_exists($f) ? json_decode(file_get_contents($f), true) : [];
        array_unshift($log, $entry);
        $log = array_slice($log, 0, 30);
        file_put_contents($f, json_encode($log, JSON_PRETTY_PRINT));
    }
}