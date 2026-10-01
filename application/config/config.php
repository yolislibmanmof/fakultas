<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 🌐 base_url OTOMATIS — file yang sama jalan di LOCAL maupun HOSTING
if (isset($_SERVER['HTTP_HOST'])) {
$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$dir   = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$config['base_url'] = $proto . '://' . $_SERVER['HTTP_HOST'] . $dir;
} else {
$config['base_url'] = 'http://localhost/fakultas/';   // fallback CLI
}
$config['index_page'] = '';
$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';

$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;

$config['encryption_key'] = 'F4kult4sT3kn1k2026S3cr3tK3y!';

$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'fakultas_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = FALSE;
$config['cookie_httponly'] = FALSE;
$config['cookie_samesite'] = 'Lax';

$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;

$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_fakultas';
$config['csrf_cookie_name'] = 'csrf_cookie_fakultas';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = FALSE;
// 🛡️ Endpoint aksi admin dikecualikan dari CSRF (tetap dilindungi login + role guard)
$config['csrf_exclude_uris'] = array(
    '.*admin/[a-z_]+/delete(/[0-9]+)?',
    '.*admin/[a-z_]+/bulk_(delete|action|toggle|status)',
    '.*admin/system/clear_cache',
    '.*admin/posts/toggle_pin(/[0-9]+)?',
    '.*auth/ping',
);

$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';

/*
|--------------------------------------------------------------------------
| Default Timezone — WIB
|--------------------------------------------------------------------------
*/
date_default_timezone_set('Asia/Jakarta');