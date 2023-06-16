<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function index()
    {
        $data['title'] = 'Profile Page';
        $data['user'] = $this->db->get_where('tb_user', ['email' => 
        $this->session->userdata('email')])->row_array();

        $this->load->view('user/dashboard_user', $data);

    }
}