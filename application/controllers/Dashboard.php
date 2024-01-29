<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

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

 	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('login_status')!='logged'){
			$this->session->set_flashdata("error", 'Please Login Before You Access This Page');
			redirect('Login');
		}
	}

	public function index()
	{
		$data['page'] = 'overview';
		$data['module'] = 'dashboard';
		if($this->session->userdata('role')!='Admin' && $this->session->userdata('role')!='Project Manager'){
			$this->session->set_flashdata("error", "You Don't Have Access To This Page");
			redirect('Dashboard/designer');
		}
		$this->load->view('template/head.html',$data);
		$this->load->view('dashboard/index.html');
		$this->load->view('template/foot.html');
	}
	public function designer()
	{
		$data['page'] = 'overview';
		$data['module'] = 'dashboard';

		$this->load->view('template/head.html',$data);
		$this->load->view('dashboard/designer.html');
		$this->load->view('template/foot.html');
	}
}
