<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/admin/Admin_Controller.php');

class Team extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('role') !== 'SUPERADMIN') {
            $role = $this->session->userdata('role');
            $dashboards = [
                'CSR'        => 'csr/dashboard',
                'TECH'       => 'tech/dashboard',
                'ACCOUNTING' => 'accounting/dashboard',
                'TL'         => 'tl/dashboard',
            ];
            $url = isset($dashboards[$role]) ? $dashboards[$role] : 'auth/login';
            header('Location: ' . base_url($url));
            exit;
        }
    }

    public function store()
    {
        $team_name = trim($this->input->post('team_name'));

        if (empty($team_name)) {
            redirect('admin/user');
        }

        // Prevent duplicates
        $exists = $this->db
            ->where('team_name', $team_name)
            ->get('teams')
            ->row();

        if ($exists) {
            $this->session->set_flashdata('error', 'Team already exists.');
            redirect('admin/user');
        }

        $this->Team_model->insert_team([
            'team_name' => $team_name,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Team created successfully.');

        redirect('admin/user');
    }
}
