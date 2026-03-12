<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->require_roles(['CSR', 'TECH', 'ACCOUNTING']);
        $this->load->model('Ticket_model');
        $this->load->model('Team_model');
    }

    public function index()
    {
        $this->load->library('pagination');
        $this->load->model('Client_model');

        $user_id  = $this->session->userdata('user_id');
        $page     = ($this->input->get('page')) ? (int) $this->input->get('page') : 0;
        $search   = $this->input->get('search');
        $status   = $this->input->get('status');
        $priority = $this->input->get('priority');

        // Build base query with filters
        $this->db->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->where('tickets.created_by', $user_id);

        if ($search) {
            $this->db->group_start()
                ->like('tickets.ticket_code', $search)
                ->or_like('tickets.requester_name', $search)
                ->group_end();
        }
        if ($status)   $this->db->where('tickets.ticket_status', $status);
        if ($priority) $this->db->where('tickets.priority', $priority);

        $total_rows = $this->db->count_all_results();

        $config['base_url']             = site_url('tickets/index');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = 5;
        $config['use_page_numbers']     = FALSE;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['uri_segment']          = 0;

        $config['full_tag_open']  = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';

        $config['num_tag_open']  = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open']  = '<li class="active"><span>';
        $config['cur_tag_close'] = '</span></li>';

        $config['next_link']      = '&raquo;';
        $config['next_tag_open']  = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link']      = '&laquo;';
        $config['prev_tag_open']  = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['last_link']      = 'Last';
        $config['last_tag_open']  = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['first_link']      = 'First';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        // Build the actual data query with filters
        $this->db->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->where('tickets.created_by', $user_id);

        if ($search) {
            $this->db->group_start()
                ->like('tickets.ticket_code', $search)
                ->or_like('tickets.requester_name', $search)
                ->group_end();
        }
        if ($status)   $this->db->where('tickets.ticket_status', $status);
        if ($priority) $this->db->where('tickets.priority', $priority);

        $data['tickets'] = $this->db
            ->order_by('tickets.created_at', 'DESC')
            ->limit($config['per_page'], $page)
            ->get()
            ->result();

        $data['pagination'] = $this->pagination->create_links();
        $data['clients']    = $this->Client_model->get_all_clients();
        $data['teams']      = $this->db->get('teams')->result();
        $data['title']      = 'My Tickets';

        $this->load->view('csr/layout/header');
        $this->load->view('csr/layout/sidebar');
        $this->load->view('csr/tickets/index', $data);
        $this->load->view('csr/layout/footer');
    }

    public function store()
    {
        // Auto-assign team based on category
        $category  = $this->input->post('category');
        $team_role = ($category === 'Billing') ? 'ACCOUNTING' : 'TECH';
        $assigned_team    = $this->Team_model->get_default_team_by_role($team_role);
        $assigned_team_id = $assigned_team ? $assigned_team->id : null;

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
            'ticket_status'  => 'New',
            'assigned_team'  => $assigned_team_id,
            'created_by'     => $this->session->userdata('user_id'),
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $this->Ticket_model->create_ticket($ticket_data);

        $ticket_id = $this->db->insert_id();

        $this->Ticket_model->log_activity($ticket_id, 'Ticket created');

        $user_id = $this->session->userdata('user_id');

        $this->db->insert('activity_logs', [
            'user_id'    => $user_id,
            'action'     => 'Created ticket ' . $ticket_data['ticket_code'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        redirect('tickets');
    }

    public function update_status($id)
    {
        $role = $this->session->userdata('role');

        if (!in_array($role, ['TECH', 'ACCOUNTING'])) {
            show_error('Unauthorized Access', 403);
        }

        $status = $this->input->post('ticket_status');

        $this->Ticket_model->update_status($id, $status);

        $this->Ticket_model->log_activity($id, 'Status changed to ' . $status);

        redirect('tickets');
    }

    public function cancel($id)
    {
        $this->Ticket_model->cancel_ticket($id);

        $role = $this->session->userdata('role');
        $this->Ticket_model->log_activity($id, 'Ticket cancelled by ' . $role);

        redirect('tickets');
    }

    public function get_ticket($id)
    {
        $this->db->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id');

        $role = $this->session->userdata('role');
        if ($role === 'CSR') {
            $this->db->where('tickets.created_by', $this->session->userdata('user_id'));
        }

        $ticket = $this->db->where('tickets.id', $id)->get()->row();
        echo json_encode($ticket);
    }

    public function timeline()
    {
        $user_id   = $this->session->userdata('user_id');
        $limit     = 10;
        $offset    = (int) $this->input->get('offset') ?: 0;
        $date_from = $this->input->get('date_from');
        $date_to   = $this->input->get('date_to');
        $action    = $this->input->get('action');
        $code      = $this->input->get('code');

        // Count total for load more
        $this->db->from('ticket_activities');
        $this->db->join('tickets', 'tickets.id = ticket_activities.ticket_id');
        $this->db->where('tickets.created_by', $user_id);
        if (!empty($date_from)) $this->db->where('DATE(ticket_activities.created_at) >=', $date_from);
        if (!empty($date_to))   $this->db->where('DATE(ticket_activities.created_at) <=', $date_to);
        if (!empty($action))    $this->db->like('ticket_activities.activity', $action);
        if (!empty($code))      $this->db->like('tickets.ticket_code', $code);
        $total = $this->db->count_all_results();

        // Get paginated results
        $this->db->select('ticket_activities.*, tickets.ticket_code, tickets.priority, tickets.subject, tickets.id as ticket_id, clients.client_name, users.full_name as performed_by_name');
        $this->db->from('ticket_activities');
        $this->db->join('tickets', 'tickets.id = ticket_activities.ticket_id');
        $this->db->join('clients', 'clients.id = tickets.client_id', 'left');
        $this->db->join('users', 'users.id = ticket_activities.performed_by', 'left');
        $this->db->where('tickets.created_by', $user_id);
        if (!empty($date_from)) $this->db->where('DATE(ticket_activities.created_at) >=', $date_from);
        if (!empty($date_to))   $this->db->where('DATE(ticket_activities.created_at) <=', $date_to);
        if (!empty($action))    $this->db->like('ticket_activities.activity', $action);
        if (!empty($code))      $this->db->like('tickets.ticket_code', $code);

        $activities = $this->db
            ->order_by('ticket_activities.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();

        $data['activities']    = $activities;
        $data['has_more']      = ($offset + $limit) < $total;
        $data['next_offset']   = $offset + $limit;
        $data['date_from']     = $date_from;
        $data['date_to']       = $date_to;
        $data['filter_action'] = $action;
        $data['filter_code']   = $code;

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'activities'  => $activities,
                'has_more'    => $data['has_more'],
                'next_offset' => $data['next_offset']
            ]);
            return;
        }

        $this->load->view('csr/layout/header');
        $this->load->view('csr/layout/sidebar');
        $this->load->view('csr/tickets/timeline', $data);
        $this->load->view('csr/layout/footer');
    }

    public function endorse()
    {
        $ticket_id = $this->input->post('ticket_id');
        $team_id   = $this->input->post('team_id');

        $this->db->where('id', $ticket_id)
            ->where('created_by', $this->session->userdata('user_id'))
            ->update('tickets', [
                'assigned_team' => $team_id,
                'ticket_status' => 'Endorsed',
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

        $team = $this->db->get_where('teams', ['id' => $team_id])->row();
        $this->db->insert('ticket_activities', [
            'ticket_id'    => $ticket_id,
            'activity'     => 'Ticket endorsed to ' . $team->team_name,
            'performed_by' => $this->session->userdata('user_id'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => true]);
    }
}
