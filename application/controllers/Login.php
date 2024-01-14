<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('login/index.html');
	}

	public function loginAction()
	{
    $this->load->model('login_model');

    $username = $this->input->post('username');
    $password = md5($this->input->post('password'));

    $user = $this->login_model->loginCheck($username, $password);

    if(!empty($user)){

			if($user->role==1){
				$role = "Admin";
			}
			else if($user->role==2){
				$role = "Project Manager";
			}
			else if($user->role==3){
				$role = "Project Master";
			}
			else if($user->role==4){
				$role = "Graphic Designer";
			}

        //looping data user
        $session_data = array(
            'user_id'   => $user->user_id,
            'username'  => $user->username,
            'employee_name' => $user->employee_name,
            'role' => $role,
            'login_status' => 'logged',
        );
        //buat session berdasarkan user yang login
        $this->session->set_userdata($session_data);
				redirect('Dashboard');

    } else {

			$this->session->set_flashdata("error", "Sorry, Your Username and Password Don't Match !");
			redirect('Login');

    }
	}

	public function logoutAction()
	 {
			 // hancurkan semua sesi
			 $this->session->sess_destroy();
			 redirect(site_url('Login/'));
	 }
}
