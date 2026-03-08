<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function login()
    {
        // If already logged in, redirect based on role
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role($this->session->userdata('role'));
        }

        $this->load->view('auth/login');
    }

    public function do_login()
    {
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Please fill in all fields.');
            redirect('auth/login');
        }

        $user = $this->User_model->get_user_by_username($username);

        if ($user && $user->is_active == 1 && password_verify($password, $user->password)) {

            $this->session->set_userdata([
                'user_id'   => $user->id,
                'full_name' => $user->full_name,
                'role'      => $user->role,
                'logged_in' => TRUE
            ]);

            // 🔥 Redirect based on role
            $this->_redirect_by_role($user->role);

        } else {
            $this->session->set_flashdata('error', 'Invalid Username or Password');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    // 🔥 Private role redirect function
    private function _redirect_by_role($role)
    {
        switch ($role) {

            case 'SUPERADMIN':
                redirect('admin/dashboard');
                break;

            case 'CSR':
                redirect('tickets');
                break;

            case 'TECH':
                redirect('tech/dashboard');
                break;

            case 'ACCOUNTING':
                redirect('accounting/dashboard');
                break;

            case 'TL':
                redirect('tl/dashboard');
                break;

            default:
                redirect('auth/login');
                break;
        }
    }
}