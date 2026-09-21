<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';

// --- TAMBAHAN UNTUK MEMPERBAIKI DROPDOWN PROFIL ---
$route['profil/(:any)'] = 'profil/index/$1';
// --------------------------------------------------

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ===== BACKUP & UPDATE (WAJIB untuk subfolder controller) =====
$route['admin/backup']           = 'admin/backup/index';
$route['admin/backup/create']    = 'admin/backup/create';
$route['admin/backup/download/(:any)'] = 'admin/backup/download/$1';
$route['admin/backup/restore/(:any)']  = 'admin/backup/restore/$1';
$route['admin/backup/delete/(:any)']   = 'admin/backup/delete/$1';

$route['admin/update']           = 'admin/update/index';
$route['admin/update/check']     = 'admin/update/check';
$route['admin/update/run']       = 'admin/update/run';
$route['admin/update/upload']    = 'admin/update/upload';
$route['admin/update/save_manifest'] = 'admin/update/save_manifest';