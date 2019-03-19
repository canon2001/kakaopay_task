<?php

/**
 * Main 화면 컨트롤러
 */
class Main extends CI_Controller
{
    public function index()
    {
        $this->load->view('view_main');
    }
}
