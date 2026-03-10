<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function get_all_users()
    {
        return $this->db->get('users')->result();
    }

    public function insert_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function get_user($id)
    {
        return $this->db->where('id', $id)->get('users')->row();
    }

    public function update_user($id, $data)
    {
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function get_user_by_username($username)
    {
        return $this->db->where('username', $username)->get('users')->row();
    }

    public function count_all()
    {
        return $this->db->count_all('users');
    }

    public function count_by_role($role)
    {
        return $this->db->where('role', $role)
            ->count_all_results('users');
    }

    public function count_active()
    {
        return $this->db->where('is_active', 1)
            ->count_all_results('users');
    }

    public function count_disabled()
    {
        return $this->db->where('is_active', 0)
            ->count_all_results('users');
    }

    public function get_filtered_users($search = null, $role = null, $status = null, $team = null, $date_from = null, $date_to = null)
    {
        $this->db->select('users.*, teams.team_name');
        $this->db->from('users');
        $this->db->join('teams', 'teams.id = users.team_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.full_name', $search);
            $this->db->or_like('users.username', $search);
            $this->db->group_end();
        }

        if (!empty($role)) {
            $this->db->where('users.role', $role);
        }

        if ($status !== null && $status !== '') {
            $this->db->where('users.is_active', $status);
        }

        if (!empty($team)) {
            $this->db->where('users.team_id', $team);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(users.created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(users.created_at) <=', $date_to);
        }

        return $this->db->order_by('users.id', 'DESC')
            ->get()
            ->result();
    }

    public function count_users_by_role()
    {
        $this->db->select('role, COUNT(*) as total');
        $this->db->group_by('role');
        $query = $this->db->get('users');

        $result = [];
        foreach ($query->result() as $row) {
            $result[$row->role] = $row->total;
        }

        return $result;
    }

    public function get_by_username($username)
    {
        return $this->db
            ->where('username', $username)
            ->get('users')
            ->row();
    }

    public function get_filtered_users_paginated($limit, $offset, $search = null, $role = null, $status = null, $team = null, $date_from = null, $date_to = null)
    {
        $this->db->select('users.*, teams.team_name');
        $this->db->from('users');
        $this->db->join('teams', 'teams.id = users.team_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.full_name', $search);
            $this->db->or_like('users.username', $search);
            $this->db->group_end();
        }

        if (!empty($role)) {
            $this->db->where('users.role', $role);
        }

        if ($status !== null && $status !== '') {
            $this->db->where('users.is_active', $status);
        }

        if (!empty($team)) {
            $this->db->where('users.team_id', $team);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(users.created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(users.created_at) <=', $date_to);
        }

        return $this->db->order_by('users.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_filtered_users($search = null, $role = null, $status = null, $team = null, $date_from = null, $date_to = null)
    {
        $this->db->from('users');
        $this->db->join('teams', 'teams.id = users.team_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.full_name', $search);
            $this->db->or_like('users.username', $search);
            $this->db->group_end();
        }

        if (!empty($role)) {
            $this->db->where('users.role', $role);
        }

        if ($status !== null && $status !== '') {
            $this->db->where('users.is_active', $status);
        }

        if (!empty($team)) {
            $this->db->where('users.team_id', $team);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(users.created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(users.created_at) <=', $date_to);
        }

        return $this->db->count_all_results();
    }
}
