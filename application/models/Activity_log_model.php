<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model {

    public function log($user_id, $action)
    {
        return $this->db->insert('activity_logs', [
            'user_id' => $user_id,
            'action'  => $action
        ]);
    }

    public function get_logs()
    {
        $this->db->select('activity_logs.*, users.full_name');
        $this->db->from('activity_logs');
        $this->db->join('users', 'users.id = activity_logs.user_id');
        $this->db->order_by('activity_logs.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_recent($limit = 5)
    {
        return $this->db
            ->select('activity_logs.*, users.full_name')
            ->join('users', 'users.id = activity_logs.user_id')
            ->order_by('activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->get('activity_logs')
            ->result();
    }

    public function get_logs_paginated($limit, $offset, $search = null, $date_from = null, $date_to = null)
    {
        $this->db->select('activity_logs.*, users.full_name');
        $this->db->from('activity_logs');
        $this->db->join('users', 'users.id = activity_logs.user_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('activity_logs.action', $search);
            $this->db->or_like('users.full_name', $search);
            $this->db->group_end();
        }
        if (!empty($date_from)) $this->db->where('DATE(activity_logs.created_at) >=', $date_from);
        if (!empty($date_to))   $this->db->where('DATE(activity_logs.created_at) <=', $date_to);

        return $this->db
            ->order_by('activity_logs.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_filtered_logs($search = null, $date_from = null, $date_to = null)
    {
        $this->db->from('activity_logs');
        $this->db->join('users', 'users.id = activity_logs.user_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('activity_logs.action', $search);
            $this->db->or_like('users.full_name', $search);
            $this->db->group_end();
        }
        if (!empty($date_from)) $this->db->where('DATE(activity_logs.created_at) >=', $date_from);
        if (!empty($date_to))   $this->db->where('DATE(activity_logs.created_at) <=', $date_to);

        return $this->db->count_all_results();
    }

    // ===== USER LOGS (ticket_activities) =====

    public function get_user_logs_paginated($limit, $offset, $search = null, $date_from = null, $date_to = null)
    {
        $this->db->select('ticket_activities.*, tickets.ticket_code, users.full_name');
        $this->db->from('ticket_activities');
        $this->db->join('tickets', 'tickets.id = ticket_activities.ticket_id', 'left');
        $this->db->join('users', 'users.id = ticket_activities.performed_by', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ticket_activities.activity', $search);
            $this->db->or_like('tickets.ticket_code', $search);
            $this->db->or_like('users.full_name', $search);
            $this->db->group_end();
        }
        if (!empty($date_from)) $this->db->where('DATE(ticket_activities.created_at) >=', $date_from);
        if (!empty($date_to))   $this->db->where('DATE(ticket_activities.created_at) <=', $date_to);

        return $this->db
            ->order_by('ticket_activities.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_filtered_user_logs($search = null, $date_from = null, $date_to = null)
    {
        $this->db->from('ticket_activities');
        $this->db->join('tickets', 'tickets.id = ticket_activities.ticket_id', 'left');
        $this->db->join('users', 'users.id = ticket_activities.performed_by', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ticket_activities.activity', $search);
            $this->db->or_like('tickets.ticket_code', $search);
            $this->db->or_like('users.full_name', $search);
            $this->db->group_end();
        }
        if (!empty($date_from)) $this->db->where('DATE(ticket_activities.created_at) >=', $date_from);
        if (!empty($date_to))   $this->db->where('DATE(ticket_activities.created_at) <=', $date_to);

        return $this->db->count_all_results();
    }
}