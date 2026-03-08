<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team_model extends CI_Model {

    public function get_all_teams()
    {
        return $this->db
                    ->order_by('team_name', 'ASC')
                    ->get('teams')
                    ->result();
    }

    public function insert_team($data)
    {
        return $this->db->insert('teams', $data);
    }

    public function get_team($id)
    {
        return $this->db
            ->where('id', $id)
            ->get('teams')
            ->row();
    }

}