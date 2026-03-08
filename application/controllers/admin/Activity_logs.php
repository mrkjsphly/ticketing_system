<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_logs extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->require_role('SUPERADMIN');
        $this->load->model('Activity_log_model');
    }

    public function index()
    {
        $this->load->library('pagination');

        // Get filters
        $search     = $this->input->get('search');
        $date_from  = $this->input->get('date_from');
        $date_to    = $this->input->get('date_to');

        // Pagination config
        $config['base_url']    = site_url('admin/activity_logs/index');
        $config['per_page']    = 10;
        $config['uri_segment'] = 4;

        $config['total_rows'] = $this->Activity_log_model->count_filtered_logs(
            $search,
            $date_from,
            $date_to
        );

        // Pagination styling
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = 'First';
        $config['last_link']  = 'Last';

        $config['prev_link'] = '&laquo;';
        $config['next_link'] = '&raquo;';

        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><span>';
        $config['cur_tag_close'] = '</span></li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        // Get paginated logs
        $data['logs'] = $this->Activity_log_model->get_logs_paginated(
            $config['per_page'],
            $page,
            $search,
            $date_from,
            $date_to
        );

        $data['pagination'] = $this->pagination->create_links();

        // 🔥 IMPORTANT — Pass session info to layout
        $data['full_name'] = $this->session->userdata('full_name');
        $data['role']      = $this->session->userdata('role');

        // Load views with $data
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/logs/index', $data);
        $this->load->view('admin/layout/footer');
    }
}