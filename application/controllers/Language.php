<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends CI_Controller {

    public function switch($lang = 'id') {
        $lang = ($lang === 'en') ? 'en' : 'id';
        $this->session->set_userdata('site_lang', $lang);
        $this->load->helper('cookie');
        set_cookie('site_lang', $lang, 86400 * 30);
        $ref = $this->input->server('HTTP_REFERER');
        redirect($ref ? $ref : base_url());
    }
}