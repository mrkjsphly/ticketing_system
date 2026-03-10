<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_model extends CI_Model
{

    public function count_all()
    {
        return $this->db->count_all('tickets');
    }

    public function count_by_status($status)
    {
        return $this->db->where('ticket_status', $status)
            ->count_all_results('tickets');
    }

    public function count_by_priority($priority)
    {
        return $this->db->where('priority', $priority)
            ->count_all_results('tickets');
    }

    public function count_by_team($team)
    {
        return $this->db->where('assigned_team', $team)
            ->count_all_results('tickets');
    }

    public function get_recent($limit = 5)
    {
        return $this->db->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get('tickets')
            ->result();
    }

    // =============================
    // NEW FUNCTIONS FOR TICKET PAGE
    // =============================

    public function get_tickets_by_user($user_id)
    {
        $this->db->select('tickets.*, clients.client_name');
        $this->db->from('tickets');
        $this->db->join('clients', 'clients.id = tickets.client_id', 'left');
        $this->db->where('tickets.created_by', $user_id);
        $this->db->order_by('tickets.created_at', 'DESC');

        return $this->db->get()->result();
    }

    public function create_ticket($data)
    {
        return $this->db->insert('tickets', $data);
    }

    public function get_ticket($id)
    {
        $this->db->select('tickets.*, clients.client_name');
        $this->db->from('tickets');
        $this->db->join('clients', 'clients.id = tickets.client_id', 'left');
        $this->db->where('tickets.id', $id);

        return $this->db->get()->row();
    }

    public function log_activity($ticket_id, $activity)
    {
        $this->db->insert('ticket_activities', [
            'ticket_id' => $ticket_id,
            'activity' => $activity,
            'performed_by' => $this->session->userdata('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_ticket_activities($ticket_id)
    {
        return $this->db
            ->where('ticket_id', $ticket_id)
            ->order_by('created_at', 'DESC')
            ->get('activity_logs')
            ->result();
    }

    public function cancel_ticket($id)
    {
        $this->db->where('id', $id);
        return $this->db->update('tickets', [
            'ticket_status' => 'Cancelled',
            'cancelled_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function count_tickets_by_user($user_id)
    {
        return $this->db
            ->where('created_by', $user_id)
            ->count_all_results('tickets');
    }

    public function get_tickets_by_user_paginated($user_id, $limit, $offset)
    {
        return $this->db
            ->select('tickets.*, clients.client_name')
            ->from('tickets')
            ->join('clients', 'clients.id = tickets.client_id')
            ->where('tickets.created_by', $user_id)
            ->order_by('tickets.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_open_tickets()
    {
        $this->db->where_in('ticket_status', ['New', 'In Progress']);
        return $this->db->count_all_results('tickets');
    }
}
