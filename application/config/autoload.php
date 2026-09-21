<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();
$autoload['config']   = array();

// ✅ SATU baris, semua helper lengkap (text = character_limiter, cookie = get_cookie)
$autoload['helper'] = array('url', 'form', 'text', 'cookie', 'site_lang', 'site');

$autoload['language'] = array();
$autoload['libraries']= array('database', 'session', 'form_validation', 'upload');
$autoload['drivers']  = array();
$autoload['model']    = array('Setting_model');