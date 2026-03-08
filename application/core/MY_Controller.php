<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Load session library
        $this->load->library('session');
        $this->load->helper('url');

        // Global login check
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    protected function require_role($role)
    {
        if ($this->session->userdata('role') !== $role) {
            show_error('Unauthorized Access', 403);
        }
    }

    protected function require_roles($roles = [])
    {
        $user_role = $this->session->userdata('role');

        if (!$user_role || !in_array($user_role, $roles)) {
            show_error('Unauthorized Access', 403);
        }
    }

}