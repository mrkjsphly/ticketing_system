<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Only SUPERADMIN can access
        $this->require_role('SUPERADMIN');
    }

    public function index()
    {
        $this->load->model('User_model');
        $this->load->model('Ticket_model');
        $this->load->model('Activity_log_model');

        // ===== USER STATS =====
        $data['total_users']    = $this->User_model->count_all();
        $data['total_active']   = $this->User_model->count_active();
        $data['total_disabled'] = $this->User_model->count_disabled();

        // 🔥 Dynamic role counts
        $data['role_counts'] = $this->User_model->count_users_by_role();

        // ===== TICKET STATUS =====
        $data['total_tickets'] = $this->Ticket_model->count_all();
        $data['open_tickets'] = $this->Ticket_model->count_open_tickets();;
        $data['inprogress']    = $this->Ticket_model->count_by_status('In Progress');
        $data['resolved']      = $this->Ticket_model->count_by_status('Resolved');
        $data['cancelled']     = $this->Ticket_model->count_by_status('Cancelled');

        // ===== PRIORITY =====
        $data['high_priority']   = $this->Ticket_model->count_by_priority('High');
        $data['medium_priority'] = $this->Ticket_model->count_by_priority('Medium');
        $data['low_priority']    = $this->Ticket_model->count_by_priority('Low');
        $data['critical_priority'] = $this->Ticket_model->count_by_priority('Critical');

        // ===== RECENT =====
        $data['recent_tickets'] = $this->Ticket_model->get_recent(5);
        $data['recent_logs']    = $this->Activity_log_model->get_recent(5);

        // Session
        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('admin/layout/footer');
    }
}
