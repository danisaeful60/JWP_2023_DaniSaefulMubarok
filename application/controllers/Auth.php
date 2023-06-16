<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function  __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('login_model');
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
        $data['title'] = 'Login Page';
        $this->load->view('templates/auth_header', $data);
        $this->load->view('auth/login');
        $this->load->view('templates/auth_footer');
        } else {
            //success validasi
            $this->_login();
        }
    }

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('tb_user', ['email' => $email])->row_array();
        
       //jika ada user
       if ($user) {
        //jika user aktif
        if ($user['is_active'] == 1) {
           //cek pass
           if(password_verify($password, $user['password'])){
            $data = [
                'email' => $user['email'],
            ];
            $this->session->set_userdata($data);
            redirect('user');
           }else{
            $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Wrong password!</div>');
            redirect('auth');
           }

        }else{
            $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">This email has not been activated!</div>');
            redirect('auth');
        }

    }else{
        $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Email this not Registered!</div>');
        redirect('auth');
    }
}

    public function registrasi()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[tb_user.email]',['is_unique' => 'This email already registered']);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[6]|matches[password2]', 
            ['matches' => 'password dont match!',
            'min_length' => 'Password too short!'
            ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Registrasion Account Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registrasi');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email', true);
            $this->load->library('upload');
        $nmfile = "file_".time(); //nama file saya beri nama langsung dan diikuti fungsi time
        $config['upload_path'] = './assets/img/profile/'; //path folder
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|docx|txt'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '3072'; //maksimum besar file 3M
        $config['max_width']  = '5000'; //lebar maksimum 5000 px
        $config['max_height']  = '5000'; //tinggi maksimu 5000 px
        $config['file_name'] = $nmfile; //nama yang terupload nantinya

        $this->upload->initialize($config);

        if($_FILES['img']['name'])
        {
            if ($this->upload->do_upload('img'))
            {
                $gbr = $this->upload->data();
                $data = array(       

                  
                  'name' => htmlspecialchars($this->input->post('name', true)),
                  'email' => htmlspecialchars($email),
                  'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                  'is_active' => 0,
                  'img' =>$gbr['file_name']
                  
                  
                );

                //token
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token
                ];
              
                $this->login_model->simpan_regis($data); //akses model untuk menyimpan ke database
                $this->db->insert('user_token', $user_token);

                $this->_sendEmail($token, 'verify');

                //pesan yang muncul jika berhasil diupload pada session flashdata
                $this->session->set_flashdata('msg','<div class="alert alert-success" role="alert">Registered Success. Please Aactivation Your Accountc!</div>');
                redirect('auth'); //jika berhasil maka akan ditampilkan view upload
                }
              else{
                //pesan yang muncul jika terdapat error dimasukkan pada session flashdata
                $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Registered Failed. Please Check Again!</div>');
                redirect('auth/registrasi'); //jika gagal maka akan ditampilkan form upload
            }
        }
        }
    }

    private function _sendEmail($token, $type)
    {
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'jwp2023danisaefulmubarok@gmail.com',
            'smtp_pass' => 'yyhahekkqjemguit',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];

        $this->load->library('email', $config);
        $this->email->initialize($config);

        $this->email->from('jwp2023danisaefulmubarok@gmail.com', 'JWP 2023 Dani Saeful Mubarok');
        $this->email->to($this->input->post('email'));

        if ($type =='verify') {
            $this->email->subject('Account Activation');
            $this->email->message('Click this link to Aktivation Account : <a href="' . 
            base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activate</a>');
        } else if ($type =='forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Click this link to Reset Password : <a href="' . 
            base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a>');
        }

        if ($this->email->send()) {
            return true;
        }else{
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('tb_user', ['email' => $email])->row_array();

        if ($user) {
           $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
                $this->db->set('is_active', 1);
                $this->db->where('email', $email);
                $this->db->update('tb_user');

                $this->db->delete('user_token', ['email' => $email]);

                $this->session->set_flashdata('msg','<div class="alert alert-success" role="alert">'. $email .' Has been activated. Please Login!</div>');
                redirect('auth');
            } else {
                $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Wrong token activation!</div>');
                redirect('auth');
            }

        } else{
            $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Account activation failed!</div>');
            redirect('auth');
        }
    }

       public function logout()
	{
      $this->session->unset_userdata('email');

      $this->session->set_flashdata('msg','<div class="alert alert-success" role="alert">You have been logged out!</div>');
      redirect('auth');
	}

    public function forgot_password()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            
            $data['title'] = 'Forgot Password Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot_password_page');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email', true);
            $user = $this->db->get_where('tb_user', ['email' => $email, 'is_active' => 1])->row_array();

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token
                ];
              
                $this->db->insert('user_token', $user_token);

                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('msg','<div class="alert alert-success" 
                role="alert">Please check your Email!</div>');
                redirect('auth/forgot_password');

            } else {
                $this->session->set_flashdata('msg','<div class="alert alert-danger" 
                role="alert">Email his not Registered or Activated!</div>');
                redirect('auth/forgot_password');
            }
        }

    }

    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('tb_user', ['email' => $email])->row_array();

        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
                $this->session->set_userdata('reset_email', $email);
                $this->changePassword();
                
            }else {
                $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Reset your password failed! Wrong Token.</div>');
                redirect('auth');
            }

        } else{
            $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Reset your password failed! Wrong Email.</div>');
            redirect('auth');
        }
    }

    public function changePassword()
    {
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[6]|matches[password2]', 
            ['matches' => 'password dont match!',
            'min_length' => 'Password too short!'
            ]);
        $this->form_validation->set_rules('password2', 'Repeat Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Change Password Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change_password_page');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'),PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->db->set('password', $password);
            $this->db->where('email', $email);
            $this->db->update('tb_user');

            $this->session->unset_userdata('reset_email');

            $this->session->set_flashdata('msg','<div class="alert alert-success" role="alert">Your password success changed! Please Login.</div>');
            redirect('auth');
        }
    }

}
