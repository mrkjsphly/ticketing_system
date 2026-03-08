<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_model extends CI_Model {

    public function get_all_clients()
    {
        return $this->db
            ->select('id, client_name')
            ->from('clients')
            ->order_by('client_name','ASC')
            ->get()
            ->result();
    }

}