<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'controllers/admin/Admin_Controller.php');

class Activity_logs extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Activity_log_model');

        if ($this->session->userdata('role') !== 'SUPERADMIN') {
            $role = $this->session->userdata('role');
            $dashboards = [
                'CSR'        => 'csr/dashboard',
                'TECH'       => 'tech/dashboard',
                'ACCOUNTING' => 'accounting/dashboard',
                'TL'         => 'tl/dashboard',
            ];
            $url = isset($dashboards[$role]) ? $dashboards[$role] : 'auth/login';
            header('Location: ' . base_url($url));
            exit;
        }
    }

    public function index()
    {
        $this->load->library('pagination');

        $search    = $this->input->get('search');
        $date_from = $this->input->get('date_from') ?: date('Y-m-d');
        $date_to   = $this->input->get('date_to')   ?: date('Y-m-d');

        $config['base_url']    = site_url('admin/activity_logs/index');
        $config['per_page']    = 10;
        $config['uri_segment'] = 4;

        $config['total_rows'] = $this->Activity_log_model->count_filtered_logs(
            $search, $date_from, $date_to
        );

        $config['full_tag_open']   = '<ul class="pagination">';
        $config['full_tag_close']  = '</ul>';
        $config['first_link']      = 'First';
        $config['last_link']       = 'Last';
        $config['prev_link']       = '&laquo;';
        $config['next_link']       = '&raquo;';
        $config['first_tag_open']  = '<li>'; $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>'; $config['last_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>'; $config['prev_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>'; $config['next_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>'; $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><span>';
        $config['cur_tag_close']   = '</span></li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data['logs'] = $this->Activity_log_model->get_logs_paginated(
            $config['per_page'], $page, $search, $date_from, $date_to
        );

        $data['pagination'] = $this->pagination->create_links();
        $data['date_from']  = $date_from;
        $data['date_to']    = $date_to;
        $data['full_name']  = $this->session->userdata('full_name');
        $data['role']       = $this->session->userdata('role');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/logs/index', $data);
        $this->load->view('admin/layout/footer');
    }

    public function user_logs()
    {
        $this->load->library('pagination');

        $search    = $this->input->get('search');
        $date_from = $this->input->get('date_from') ?: date('Y-m-d');
        $date_to   = $this->input->get('date_to')   ?: date('Y-m-d');

        $config['base_url']    = site_url('admin/activity_logs/user_logs');
        $config['per_page']    = 10;
        $config['uri_segment'] = 4;

        $config['total_rows'] = $this->Activity_log_model->count_filtered_user_logs(
            $search, $date_from, $date_to
        );

        $config['full_tag_open']   = '<ul class="pagination">';
        $config['full_tag_close']  = '</ul>';
        $config['first_link']      = 'First';
        $config['last_link']       = 'Last';
        $config['prev_link']       = '&laquo;';
        $config['next_link']       = '&raquo;';
        $config['first_tag_open']  = '<li>'; $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>'; $config['last_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>'; $config['prev_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>'; $config['next_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>'; $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><span>';
        $config['cur_tag_close']   = '</span></li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data['logs'] = $this->Activity_log_model->get_user_logs_paginated(
            $config['per_page'], $page, $search, $date_from, $date_to
        );

        $data['pagination'] = $this->pagination->create_links();
        $data['date_from']  = $date_from;
        $data['date_to']    = $date_to;
        $data['full_name']  = $this->session->userdata('full_name');
        $data['role']       = $this->session->userdata('role');

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/logs/user_logs', $data);
        $this->load->view('admin/layout/footer');
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete('activity_logs');

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Deleted activity log #' . $id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Log entry deleted.');
        redirect('admin/activity_logs');
    }

    public function delete_user_log($id)
    {
        $this->db->where('id', $id)->delete('ticket_activities');

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Deleted user activity log #' . $id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'User log entry deleted.');
        redirect('admin/activity_logs/user_logs');
    }

    public function clear_all()
    {
        $this->db->empty_table('activity_logs');

        $this->db->insert('activity_logs', [
            'user_id'    => $this->session->userdata('user_id'),
            'action'     => 'Cleared all activity logs',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'All logs cleared.');
        redirect('admin/activity_logs');
    }
}