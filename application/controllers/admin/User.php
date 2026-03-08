<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->require_role('SUPERADMIN');

        $this->load->model('User_model');
        $this->load->model('Activity_log_model');
        $this->load->model('Team_model');
    }

    public function index()
    {
        $search     = $this->input->get('search');
        $role       = $this->input->get('role');
        $status     = $this->input->get('status');
        $team       = $this->input->get('team');
        $date_from  = $this->input->get('date_from');
        $date_to    = $this->input->get('date_to');

        $data['users'] = $this->User_model->get_filtered_users(
            $search, $role, $status, $team, $date_from, $date_to
        );

        $data['teams'] = $this->Team_model->get_all_teams();

        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/users/index', $data);
        $this->load->view('admin/layout/footer');
    }

    public function store()
    {
        $full_name = trim($this->input->post('full_name'));
        $username  = trim($this->input->post('username'));
        $password  = trim($this->input->post('password'));
        $role      = trim($this->input->post('role'));
        $team_id   = $this->input->post('team_id');

        if (empty($full_name) || empty($username) || empty($password) || empty($role)) {
            $this->session->set_flashdata('error', 'All fields are required.');
            redirect('admin/user');
            return;
        }

        $existing = $this->User_model->get_by_username($username);
        if ($existing) {
            $this->session->set_flashdata('error', 'Username already taken.');
            redirect('admin/user');
            return;
        }

        $data = [
            'full_name'  => $full_name,
            'username'   => $username,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => $role,
            'team_id'    => !empty($team_id) ? $team_id : null,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->User_model->insert_user($data);

        $team_name = 'Unassigned';
        if (!empty($team_id)) {
            $team = $this->Team_model->get_team($team_id);
            if ($team) {
                $team_name = $team->team_name;
            }
        }

        $this->Activity_log_model->log(
            $this->session->userdata('user_id'),
            "Created user: {$full_name} ({$role}) - Team: {$team_name}"
        );

        $this->session->set_flashdata('success', 'User created successfully.');
        redirect('admin/user');
    }

    public function update($id)
    {
        $user = $this->User_model->get_user($id);
        if (!$user) show_404();

        // Capture OLD values BEFORE updating
        $old_role = $user->role;
        $old_team = $user->team_id;
        $old_name = $user->full_name;

        $full_name    = trim($this->input->post('full_name'));
        $role         = trim($this->input->post('role'));
        $new_password = trim($this->input->post('password'));
        $team_id      = $this->input->post('team_id');

        if (empty($full_name) || empty($role)) {
            $this->session->set_flashdata('error', 'Full name and role are required.');
            redirect('admin/user');
            return;
        }

        if ($user->role === 'SUPERADMIN' && $role !== 'SUPERADMIN') {
            show_error('Cannot downgrade SUPERADMIN.', 403);
        }

        if ($user->id == $this->session->userdata('user_id') 
            && $this->session->userdata('role') !== $role) {
            show_error('You cannot change your own role.', 403);
        }

        $update_data = [
            'full_name' => $full_name,
            'role'      => $role,
            'team_id'   => !empty($team_id) ? $team_id : null
        ];

        if (!empty($new_password)) {
            $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $this->User_model->update_user($id, $update_data);

        // Prepare readable team names
        $new_team_name = 'Unassigned';
        $old_team_name = 'Unassigned';

        if (!empty($team_id)) {
            $new_team = $this->Team_model->get_team($team_id);
            if ($new_team) $new_team_name = $new_team->team_name;
        }

        if (!empty($old_team)) {
            $old_team_obj = $this->Team_model->get_team($old_team);
            if ($old_team_obj) $old_team_name = $old_team_obj->team_name;
        }

        $changes = [];

        if ($old_name !== $full_name) {
            $changes[] = "Name: {$old_name} → {$full_name}";
        }
        

        if ($old_role !== $role) {
            $changes[] = "Role: {$old_role} → {$role}";
        }

        if ($old_team != $team_id) {
            $changes[] = "Team: {$old_team_name} → {$new_team_name}";
        }

        if (!empty($new_password)) {
            $changes[] = "Password updated by " . $this->session->userdata('role');
        }

        $change_text = !empty($changes) ? implode(', ', $changes) : "No major changes";

        $this->Activity_log_model->log(
            $this->session->userdata('user_id'),
            "Updated user: {$full_name} ({$change_text})"
        );

        $this->session->set_flashdata('success', 'User updated successfully.');
        redirect('admin/user');
    }

    public function toggle($id)
    {
        $user = $this->User_model->get_user($id);
        if (!$user) show_404();

        if ($user->role === 'SUPERADMIN') {
            show_error('Cannot disable SUPERADMIN', 403);
        }

        if ($user->id == $this->session->userdata('user_id')) {
            show_error('You cannot disable your own account.', 403);
        }

        $new_status = $user->is_active ? 0 : 1;

        $this->User_model->update_user($id, ['is_active' => $new_status]);

        $action = $new_status ? "Enabled" : "Disabled";

        $this->Activity_log_model->log(
            $this->session->userdata('user_id'),
            "{$action} user: {$user->full_name} ({$user->role})"
        );

        redirect('admin/user');
    }

    public function check_username()
    {
        $username = $this->input->post('username');

        if (!$username) {
            echo json_encode(['status' => 'empty']);
            return;
        }

        $existing = $this->User_model->get_by_username($username);

        if ($existing) {
            echo json_encode(['status' => 'taken']);
        } else {
            echo json_encode(['status' => 'available']);
        }
    }
}