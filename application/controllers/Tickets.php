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
        $data['title'] = 'My Tickets';

        $user_id = $this->session->userdata('user_id');

        $data['tickets'] = $this->Ticket_model->get_tickets_by_user($user_id);

        $this->load->model('Client_model');
        $data['clients'] = $this->Client_model->get_all_clients();

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
        $this->load->model('Ticket_model');

        // Update ticket status
        $this->Ticket_model->cancel_ticket($id);

        // Log activity
        $this->db->insert('activity_logs', [
            'ticket_id' => $id,
            'activity' => 'Ticket cancelled',
            'performed_by' => $this->session->userdata('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        redirect('tickets');
    }
}
