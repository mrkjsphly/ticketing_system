<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->require_role('SUPERADMIN');
        $this->load->model('Team_model');
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