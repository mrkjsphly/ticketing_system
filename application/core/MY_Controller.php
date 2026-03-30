<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

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
            $this->_redirect_to_dashboard();
        }
    }

    protected function require_roles($roles = [])
    {
        $user_role = $this->session->userdata('role');

        if (!$user_role || !in_array($user_role, $roles)) {
            $this->_redirect_to_dashboard();
        }
    }

    protected function _redirect_to_dashboard()
    {
        $role = $this->session->userdata('role');

        $dashboards = [
            'SUPERADMIN' => 'admin/dashboard',
            'CSR'        => 'csr/dashboard',
            'TECH'       => 'tech/dashboard',
            'ACCOUNTING' => 'accounting/dashboard',
            'TL'         => 'tl/dashboard',
        ];

        $url = isset($dashboards[$role]) ? $dashboards[$role] : 'auth/login';
        redirect($url);
    }
}
