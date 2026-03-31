<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tech extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('TECH');
        $this->load->model('Ticket_model');
        $this->load->model('Team_model');
        $this->load->model('Client_model');
    }

    public function dashboard()
    {
        $team_id = $this->session->userdata('team_id');

        $data['total']      = $this->_count_by_status($team_id, null);
        $data['endorsed']   = $this->_count_by_status($team_id, 'Endorsed');
        $data['inprogress'] = $this->_count_by_status($team_id, 'In Progress');
        $data['resolved']   = $this->_count_by_status($team_id, 'Resolved');
        $data['cancelled']  = $this->_count_by_status($team_id, 'Cancelled');

        $data['low']      = $this->_count_by_priority($team_id, 'Low');
        $data['medium']   = $this->_count_by_priority($team_id, 'Medium');
        $data['high']     = $this->_count_by_priority($team_id, 'High');
        $data['critical'] = $this->_count_by_priority($team_id, 'Critical');

        $data['recent_tickets'] = $this->db
            ->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->where('tickets.assigned_team', $team_id)
            ->order_by('tickets.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result();

        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('tech/layout/header', $data);
        $this->load->view('tech/layout/sidebar', $data);
        $this->load->view('tech/dashboard', $data);
        $this->load->view('tech/layout/footer');
    }

    public function tickets()
    {
        $this->load->library('pagination');

        $team_id  = $this->session->userdata('team_id');
        $page     = ($this->input->get('page')) ? (int) $this->input->get('page') : 0;
        $search   = $this->input->get('search');
        $status   = $this->input->get('status');
        $priority = $this->input->get('priority');

        // Count query with filters
        $this->db->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->where('tickets.assigned_team', $team_id);

        if ($search) {
            $this->db->group_start()
                ->like('tickets.ticket_code', $search)
                ->or_like('tickets.requester_name', $search)
                ->group_end();
        }
        if ($status)   $this->db->where('tickets.ticket_status', $status);
        if ($priority) $this->db->where('tickets.priority', $priority);

        $total_rows = $this->db->count_all_results();

        $config['base_url']             = site_url('tech/tickets');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = 5;
        $config['use_page_numbers']     = FALSE;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['uri_segment']          = 0;

        $config['full_tag_open']   = '<ul class="pagination">';
        $config['full_tag_close']  = '</ul>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><span>';
        $config['cur_tag_close']   = '</span></li>';
        $config['next_link']       = '&raquo;';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_link']       = '&laquo;';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['last_link']       = 'Last';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['first_link']      = 'First';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        // Data query with filters
        $this->db->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->where('tickets.assigned_team', $team_id);

        if ($search) {
            $this->db->group_start()
                ->like('tickets.ticket_code', $search)
                ->or_like('tickets.requester_name', $search)
                ->group_end();
        }
        if ($status)   $this->db->where('tickets.ticket_status', $status);
        if ($priority) $this->db->where('tickets.priority', $priority);

        $data['tickets']    = $this->db
            ->order_by('tickets.created_at', 'DESC')
            ->limit($config['per_page'], $page)
            ->get()
            ->result();

        $data['pagination'] = $this->pagination->create_links();
        $data['clients']    = $this->Client_model->get_all_clients();
        $data['full_name']  = $this->session->userdata('full_name');
        $data['role']       = $this->session->userdata('role');

        $this->load->view('tech/layout/header', $data);
        $this->load->view('tech/layout/sidebar', $data);
        $this->load->view('tech/tickets/index', $data);
        $this->load->view('tech/layout/footer');
    }

    public function store()
    {
        $team_id  = $this->session->userdata('team_id');
        $category = $this->input->post('category');

        $ticket_data = [
            'ticket_code'    => 'TKT-' . date('Ymd-His'),
            'requester_name' => $this->input->post('requester_name'),
            'contact_info'   => $this->input->post('contact_info'),
            'client_id'      => $this->input->post('client_id'),
            'category'       => $category,
            'priority'       => $this->input->post('priority'),
            'ticket_type'    => $this->input->post('ticket_type'),
            'channel'        => $this->input->post('channel'),
            'subject'        => $this->input->post('subject'),
            'description'    => $this->input->post('description'),
            'ticket_status'  => 'In Progress',
            'assigned_team'  => $team_id,
            'created_by'     => $this->session->userdata('user_id'),
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $this->Ticket_model->create_ticket($ticket_data);
        $ticket_id = $this->db->insert_id();

        $this->Ticket_model->log_activity($ticket_id, 'Ticket created by TECH');

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Created ticket ' . $ticket_data['ticket_code'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        redirect('tech/tickets');
    }

    public function update_status($id)
    {
        $team_id = $this->session->userdata('team_id');

        $ticket = $this->db->where('id', $id)
            ->where('assigned_team', $team_id)
            ->get('tickets')
            ->row();

        if (!$ticket) {
            $this->_redirect_to_dashboard();
            exit;
        }

        $status = $this->input->post('ticket_status');

        $allowed = ['In Progress', 'Resolved'];
        if (!in_array($status, $allowed)) {
            redirect('tech/tickets');
            exit;
        }

        $update_data = [
            'ticket_status' => $status,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        if ($status === 'Resolved') {
            $resolution_notes = $this->input->post('resolution_notes');
            if (empty($resolution_notes)) {
                redirect('tech/tickets');
                exit;
            }
            $update_data['resolution_details'] = $resolution_notes;
            $update_data['resolved_at']        = date('Y-m-d H:i:s');
            $update_data['resolved_by']        = $this->session->userdata('user_id');
        }

        $this->db->where('id', $id)->update('tickets', $update_data);

        $this->db->insert('ticket_activities', [
            'ticket_id'    => $id,
            'activity'     => 'Status changed to ' . $status,
            'performed_by' => $this->session->userdata('user_id'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        $status_comment = $this->input->post('status_comment');
        if (!empty($status_comment)) {
            $this->db->insert('ticket_activities', [
                'ticket_id'    => $id,
                'activity'     => 'Progress update: ' . $status_comment,
                'performed_by' => $this->session->userdata('user_id'),
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Updated ticket #' . $id . ' status to ' . $status,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        redirect('tech/tickets');
    }

    public function return_ticket($id)
    {
        $team_id = $this->session->userdata('team_id');

        $ticket = $this->db->where('id', $id)
            ->where('assigned_team', $team_id)
            ->get('tickets')
            ->row();

        if (!$ticket) {
            $this->_redirect_to_dashboard();
            exit;
        }

        $this->db->where('id', $id)->update('tickets', [
            'ticket_status' => 'New',
            'assigned_team' => null,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('ticket_activities', [
            'ticket_id'    => $id,
            'activity'     => 'Ticket returned to CSR by TECH',
            'performed_by' => $this->session->userdata('user_id'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Returned ticket #' . $id . ' to CSR',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Ticket returned to CSR successfully.');
        redirect('tech/tickets');
    }

    public function get_ticket($id)
    {
        $this->db->select('tickets.*, clients.client_name, resolver.full_name as resolved_by_name, closer.full_name as closed_by_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->join('users as resolver', 'resolver.id = tickets.resolved_by', 'left')
            ->join('users as closer', 'closer.id = tickets.closed_by', 'left');

        $ticket = $this->db->where('tickets.id', $id)->get()->row();

        if ($ticket && empty($ticket->resolved_by_name) && in_array($ticket->ticket_status, ['Resolved', 'For Closure', 'Closed'])) {
            $activity = $this->db
                ->select('users.full_name')
                ->from('ticket_activities')
                ->join('users', 'users.id = ticket_activities.performed_by', 'left')
                ->where('ticket_activities.ticket_id', $ticket->id)
                ->like('ticket_activities.activity', 'Status changed to Resolved')
                ->order_by('ticket_activities.created_at', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($activity) {
                $ticket->resolved_by_name = $activity->full_name;
            }
        }

        echo json_encode($ticket);
    }

    private function _count_by_status($team_id, $status)
    {
        $this->db->from('tickets')->where('assigned_team', $team_id);
        if ($status) $this->db->where('ticket_status', $status);
        return $this->db->count_all_results();
    }

    private function _count_by_priority($team_id, $priority)
    {
        return $this->db
            ->from('tickets')
            ->where('assigned_team', $team_id)
            ->where('priority', $priority)
            ->count_all_results();
    }
}
