<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tl extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('TL');
        $this->load->model('Ticket_model');
        $this->load->model('Team_model');
    }

    public function dashboard()
    {
        // All tickets regardless of team
        $data['total']       = $this->_count_by_status(null);
        $data['new']         = $this->_count_by_status('New');
        $data['endorsed']    = $this->_count_by_status('Endorsed');
        $data['inprogress']  = $this->_count_by_status('In Progress');
        $data['resolved']    = $this->_count_by_status('Resolved');
        $data['forclosure']  = $this->_count_by_status('For Closure');
        $data['closed']      = $this->_count_by_status('Closed');
        $data['cancelled']   = $this->_count_by_status('Cancelled');

        $data['low']      = $this->_count_by_priority('Low');
        $data['medium']   = $this->_count_by_priority('Medium');
        $data['high']     = $this->_count_by_priority('High');
        $data['critical'] = $this->_count_by_priority('Critical');

        $data['recent_tickets'] = $this->db
            ->select('tickets.*, clients.client_name, teams.team_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->join('teams', 'teams.id = tickets.assigned_team', 'left')
            ->order_by('tickets.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result();

        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('tl/layout/header', $data);
        $this->load->view('tl/layout/sidebar', $data);
        $this->load->view('tl/dashboard', $data);
        $this->load->view('tl/layout/footer');
    }

    public function tickets()
    {
        $this->load->library('pagination');

        $page     = ($this->input->get('page')) ? (int) $this->input->get('page') : 0;
        $search   = $this->input->get('search');
        $status   = $this->input->get('status');
        $priority = $this->input->get('priority');
        $team     = $this->input->get('team');

        // Count query
        $this->db->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->join('teams', 'teams.id = tickets.assigned_team', 'left');

        if ($search) {
            $this->db->group_start()
                ->like('tickets.ticket_code', $search)
                ->or_like('tickets.requester_name', $search)
                ->group_end();
        }
        if ($status)   $this->db->where('tickets.ticket_status', $status);
        if ($priority) $this->db->where('tickets.priority', $priority);
        if ($team)     $this->db->where('tickets.assigned_team', $team);

        $total_rows = $this->db->count_all_results();

        $config['base_url']             = site_url('tl/tickets');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = 10;
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

        // Data query
        $this->db->select('tickets.*, clients.client_name, teams.team_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->join('teams', 'teams.id = tickets.assigned_team', 'left');

        if ($search) {
            $this->db->group_start()
                ->like('tickets.ticket_code', $search)
                ->or_like('tickets.requester_name', $search)
                ->group_end();
        }
        if ($status)   $this->db->where('tickets.ticket_status', $status);
        if ($priority) $this->db->where('tickets.priority', $priority);
        if ($team)     $this->db->where('tickets.assigned_team', $team);

        $data['tickets']    = $this->db
            ->order_by('tickets.created_at', 'DESC')
            ->limit($config['per_page'], $page)
            ->get()
            ->result();

        $data['pagination'] = $this->pagination->create_links();
        $data['teams']      = $this->db->get('teams')->result();
        $data['full_name']  = $this->session->userdata('full_name');
        $data['role']       = $this->session->userdata('role');

        $this->load->view('tl/layout/header', $data);
        $this->load->view('tl/layout/sidebar', $data);
        $this->load->view('tl/tickets/index', $data);
        $this->load->view('tl/layout/footer');
    }

    public function close_ticket($id)
    {
        $ticket = $this->db->where('id', $id)
            ->where('ticket_status', 'For Closure')
            ->get('tickets')
            ->row();

        if (!$ticket) {
            show_error('Ticket not found or not eligible for closure.', 403);
        }

        $this->db->where('id', $id)->update('tickets', [
            'ticket_status' => 'Closed',
            'closed_by'     => $this->session->userdata('user_id'),
            'closed_at'     => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('ticket_activities', [
            'ticket_id'    => $id,
            'activity'     => 'Ticket closed by TL',
            'performed_by' => $this->session->userdata('user_id'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Closed ticket #' . $id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Ticket successfully closed.');
        redirect('tl/tickets');
    }

    public function reassign($id)
    {
        $team_id = $this->input->post('assigned_team');

        if (!$team_id) {
            show_error('No team selected.', 400);
        }

        $team = $this->db->where('id', $team_id)->get('teams')->row();
        if (!$team) {
            show_error('Invalid team.', 400);
        }

        $this->db->where('id', $id)->update('tickets', [
            'assigned_team' => $team_id,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('ticket_activities', [
            'ticket_id'    => $id,
            'activity'     => 'Ticket reassigned to ' . $team->team_name,
            'performed_by' => $this->session->userdata('user_id'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Reassigned ticket #' . $id . ' to ' . $team->team_name,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Ticket successfully reassigned.');
        redirect('tl/tickets');
    }

    public function get_ticket($id)
    {
        $this->db->select('tickets.*, clients.client_name, teams.team_name,
                           resolver.full_name as resolved_by_name,
                           closer.full_name as closed_by_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->join('teams', 'teams.id = tickets.assigned_team', 'left')
            ->join('users as resolver', 'resolver.id = tickets.resolved_by', 'left')
            ->join('users as closer', 'closer.id = tickets.closed_by', 'left');

        $ticket = $this->db->where('tickets.id', $id)->get()->row();

        // Fallback: look up resolver from ticket_activities
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

        // Fetch ticket activities
        $ticket->activities = $this->db
            ->select('ticket_activities.activity, ticket_activities.created_at, users.full_name')
            ->from('ticket_activities')
            ->join('users', 'users.id = ticket_activities.performed_by', 'left')
            ->where('ticket_activities.ticket_id', $id)
            ->order_by('ticket_activities.created_at', 'ASC')
            ->get()
            ->result();

        echo json_encode($ticket);
    }

    private function _count_by_status($status)
    {
        $this->db->from('tickets');
        if ($status) $this->db->where('ticket_status', $status);
        return $this->db->count_all_results();
    }

    private function _count_by_priority($priority)
    {
        return $this->db
            ->from('tickets')
            ->where('priority', $priority)
            ->count_all_results();
    }
}