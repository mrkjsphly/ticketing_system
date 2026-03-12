<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Csr extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('CSR');
        $this->load->model('Ticket_model');
    }

    public function dashboard()
    {
        $user_id = $this->session->userdata('user_id');

        // Status counts
        $data['total']       = $this->_count_by_status($user_id, null);
        $data['new']         = $this->_count_by_status($user_id, 'New');
        $data['endorsed']    = $this->_count_by_status($user_id, 'Endorsed');
        $data['inprogress']  = $this->_count_by_status($user_id, 'In Progress');
        $data['resolved']    = $this->_count_by_status($user_id, 'Resolved');
        $data['cancelled']   = $this->_count_by_status($user_id, 'Cancelled');

        // Priority counts
        $data['low']      = $this->_count_by_priority($user_id, 'Low');
        $data['medium']   = $this->_count_by_priority($user_id, 'Medium');
        $data['high']     = $this->_count_by_priority($user_id, 'High');
        $data['critical'] = $this->_count_by_priority($user_id, 'Critical');

        // Recent tickets
        $data['recent_tickets'] = $this->db
            ->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id', 'left')
            ->where('tickets.created_by', $user_id)
            ->order_by('tickets.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result();

        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        $this->load->view('csr/layout/header');
        $this->load->view('csr/layout/sidebar');
        $this->load->view('csr/dashboard', $data);
        $this->load->view('csr/layout/footer');
    }

    private function _count_by_status($user_id, $status)
    {
        $this->db->from('tickets')->where('created_by', $user_id);
        if ($status) $this->db->where('ticket_status', $status);
        return $this->db->count_all_results();
    }

    private function _count_by_priority($user_id, $priority)
    {
        return $this->db
            ->from('tickets')
            ->where('created_by', $user_id)
            ->where('priority', $priority)
            ->count_all_results();
    }
}
