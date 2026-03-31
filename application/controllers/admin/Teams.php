<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/admin/Admin_Controller.php');

class Teams extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Team_model');
        $this->load->model('Activity_log_model');

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

    public function index()
    {
        $data['teams'] = $this->db
            ->select('teams.*, COUNT(users.id) as member_count')
            ->from('teams')
            ->join('users', 'users.team_id = teams.id', 'left')
            ->group_by('teams.id')
            ->order_by('teams.created_at', 'ASC')
            ->get()
            ->result();

        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/teams/index', $data);
        $this->load->view('admin/layout/footer');
    }

    public function store()
    {
        $team_name = trim($this->input->post('team_name'));
        $role      = trim($this->input->post('role'));

        if (empty($team_name) || empty($role)) {
            $this->session->set_flashdata('error', 'Team name and role are required.');
            redirect('admin/teams');
            return;
        }

        $this->db->insert('teams', [
            'team_name'  => $team_name,
            'role'       => $role,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->Activity_log_model->log(
            $this->session->userdata('user_id'),
            "Created team: {$team_name} ({$role})"
        );

        $this->session->set_flashdata('success', 'Team created successfully.');
        redirect('admin/teams');
    }

    public function update($id)
    {
        $team = $this->db->get_where('teams', ['id' => $id])->row();
        if (!$team) show_404();

        $team_name = trim($this->input->post('team_name'));
        $role      = trim($this->input->post('role'));

        if (empty($team_name) || empty($role)) {
            $this->session->set_flashdata('error', 'Team name and role are required.');
            redirect('admin/teams');
            return;
        }

        $this->db->where('id', $id)->update('teams', [
            'team_name' => $team_name,
            'role'      => $role
        ]);

        $this->Activity_log_model->log(
            $this->session->userdata('user_id'),
            "Updated team: {$team->team_name} → {$team_name} (Role: {$role})"
        );

        $this->session->set_flashdata('success', 'Team updated successfully.');
        redirect('admin/teams');
    }

    public function delete($id)
    {
        $team = $this->db->get_where('teams', ['id' => $id])->row();
        if (!$team) show_404();

        $ticket_count = $this->db
            ->where('assigned_team', $id)
            ->count_all_results('tickets');

        if ($ticket_count > 0) {
            $this->session->set_flashdata('error', "Cannot delete team — {$ticket_count} ticket(s) are still assigned to it.");
            redirect('admin/teams');
            return;
        }

        $this->db->where('team_id', $id)->update('users', ['team_id' => null]);
        $this->db->where('id', $id)->delete('teams');

        $this->Activity_log_model->log(
            $this->session->userdata('user_id'),
            "Deleted team: {$team->team_name}"
        );

        $this->session->set_flashdata('success', 'Team deleted successfully.');
        redirect('admin/teams');
    }

    public function members($id)
    {
        $team = $this->db->get_where('teams', ['id' => $id])->row();
        if (!$team) show_404();

        $data['team']    = $team;
        $data['members'] = $this->db
            ->select('id, full_name, username, role, is_active, created_at')
            ->where('team_id', $id)
            ->get('users')
            ->result();

        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/teams/members', $data);
        $this->load->view('admin/layout/footer');
    }
}