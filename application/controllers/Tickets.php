<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->require_roles(['CSR', 'TECH', 'ACCOUNTING']);

        $this->load->model('Ticket_model');
    }

    public function index()
    {
        $this->load->library('pagination');
        $this->load->model('Client_model');

        $user_id = $this->session->userdata('user_id');

        $config['base_url'] = site_url('tickets/index');
        $config['total_rows'] = $this->Ticket_model->count_tickets_by_user($user_id);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><span>';
        $config['cur_tag_close'] = '</span></li>';

        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data['tickets'] = $this->Ticket_model->get_tickets_by_user_paginated(
            $user_id,
            $config['per_page'],
            $page
        );

        $data['pagination'] = $this->pagination->create_links();
        $data['clients'] = $this->Client_model->get_all_clients();
        $data['title'] = 'My Tickets';

        $this->load->view('csr/tickets/index', $data);
    }

    public function create()
    {
        $data['title'] = 'Create Ticket';

        $this->load->view('csr/tickets/create', $data);
    }

    public function store()
    {
        $ticket_data = [

            'ticket_code' => 'TKT-' . date('Ymd-His'),

            'requester_name' => $this->input->post('requester_name'),
            'contact_info' => $this->input->post('contact_info'),
            'client_id' => $this->input->post('client_id'),

            'category' => $this->input->post('category'),
            'priority' => $this->input->post('priority'),
            'ticket_type' => $this->input->post('ticket_type'),
            'channel' => $this->input->post('channel'),

            'subject' => $this->input->post('subject'),
            'description' => $this->input->post('description'),

            'ticket_status' => 'New',

            'created_by' => $this->session->userdata('user_id'),

            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->Ticket_model->create_ticket($ticket_data);

        $ticket_id = $this->db->insert_id();

        $this->Ticket_model->log_activity($ticket_id, 'Ticket created');

        $user_id = $this->session->userdata('user_id');

        $this->db->insert('activity_logs', [
            'user_id' => $user_id,
            'action' => 'Created ticket ' . $ticket_data['ticket_code'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        redirect('tickets');
    }

    public function view($id)
    {
        $data['ticket'] = $this->Ticket_model->get_ticket($id);

        $data['activities'] = $this->Ticket_model->get_ticket_activities($id);

        $this->load->view('csr/tickets/view', $data);
    }

    public function update_status($id)
    {
        $role = $this->session->userdata('role');

        if (!in_array($role, ['TECH', 'ACCOUNTING'])) {
            show_error('Unauthorized Access', 403);
        }

        $status = $this->input->post('ticket_status');

        $this->Ticket_model->update_status($id, $status);

        $this->Ticket_model->log_activity(
            $id,
            'Status changed to ' . $status
        );

        redirect('tickets/view/' . $id);
    }

    public function cancel($id)
    {
        $this->Ticket_model->cancel_ticket($id);

        // Log activity to timeline
        $role = $this->session->userdata('role');

        $this->Ticket_model->log_activity($id, 'Ticket cancelled by ' . $role);

        redirect('tickets');
    }

    public function get_ticket($id)
    {
        $ticket = $this->db
            ->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id')
            ->where('tickets.id', $id)
            ->get()
            ->row();

        echo json_encode($ticket);
    }

    public function timeline()
    {
        $user_id = $this->session->userdata('user_id');

        $data['activities'] = $this->db
            ->select('ticket_activities.*, tickets.ticket_code')
            ->from('ticket_activities')
            ->join('tickets', 'tickets.id = ticket_activities.ticket_id')
            ->where('tickets.created_by', $user_id)
            ->order_by('ticket_activities.created_at', 'DESC')
            ->get()
            ->result();

        $this->load->view('csr/tickets/timeline', $data);
    }
}
