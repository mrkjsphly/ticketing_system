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

    public function get_filtered_logs($search = null, $date_from = null, $date_to = null)
    {
        $this->db->select('activity_logs.*, users.full_name');
        $this->db->from('activity_logs');
        $this->db->join('users', 'users.id = activity_logs.user_id');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('activity_logs.action', $search);
            $this->db->or_like('users.full_name', $search);
            $this->db->group_end();
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(activity_logs.created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(activity_logs.created_at) <=', $date_to);
        }

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
            $this->db->like('activity_logs.action', $search);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(activity_logs.created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(activity_logs.created_at) <=', $date_to);
        }

        return $this->db
            ->order_by('activity_logs.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_filtered_logs($search = null, $date_from = null, $date_to = null)
    {
        $this->db->from('activity_logs');

        if (!empty($search)) {
            $this->db->like('action', $search);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }

        return $this->db->count_all_results();
    }
}