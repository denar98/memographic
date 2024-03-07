<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

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
		if($this->session->userdata('role')!='Admin' && $this->session->userdata('role')!='Project Manager'){
			$this->session->set_flashdata("error", "You Don't Have Access To This Page");
      $cur_url = current_url();
			redirect('Dashboard/designer');
		}
    error_reporting(0);
    $this->load->model('datatable_model');
    $this->load->model('crud_model');
	}

	public function index()
	{
    $data['employees'] = $this->db->get("employees")->result();
    $data['page'] = 'account_data';
		$data['module'] = 'employee';

		$this->load->view('template/head.html',$data);
		$this->load->view('account/index.html',$data);
		$this->load->view('template/foot.html');
	}

  public function getAllAccount()
    {
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        $order = $this->input->post("order");
        $search= $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";
        if(!empty($order))
        {
            foreach($order as $o)
            {
                $col = $o['column'];
                $dir= $o['dir'];
            }
        }

        if($dir != "asc" && $dir != "desc")
        {
            $dir = "desc";
        }
        $valid_columns = array(
            0=>'employee_name',
            1=>'username',
            2=>'role',
            3=>'last_login',
        );
        if(!isset($valid_columns[$col]))
        {
            $order = null;
        }
        else
        {
            $order = $valid_columns[$col];
        }
        if($order !=null)
        {
            $this->db->order_by($order, $dir);
        }

        if(!empty($search))
        {
            $x=0;
            foreach($valid_columns as $sterm)
            {
                if($x==0)
                {
                    $this->db->like($sterm,$search);
                }
                else
                {
                    $this->db->or_like($sterm,$search);
                }
                $x++;
            }
        }
        $this->db->limit($length,$start);
        $users = $this->db->select('users.user_id,users.username,users.role,users.last_login,employees.employee_name')
                 ->from('users')
                 ->join("employees","users.employee_id = employees.employee_id")
                 ->get();
        $data = array();
        foreach($users->result() as $rows)
        {
          if($rows->role==1){
            $role="Admin";
          }
          else if($rows->role==2){
            $role="Project Manager";
          }
          else if($rows->role==3){
            $role="Project Master";
          }
          else if($rows->role==4){
            $role="Graphic Designer";
          }
          $user_id = "'$rows->user_id'";
            $data[]= array(
                $rows->employee_name,
                $rows->username,
                $rows->role,
                $rows->last_login,
                '<a href="#" class="btn btn-warning mr-1  btn-action" onclick="getAccount('.$user_id.')" data-toggle="modal" data-target="#updateModal"><i class="fa fa-pencil"></i></a>
                 <a href="'.base_url().'Account/deleteAction/'.$rows->user_id.'" class="btn btn-danger mr-1  btn-action"><i class="fa fa-trash"></i></a>'
            );
        }
        $total_users = $this->totalUsers();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_users,
            "recordsFiltered" => $total_users,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function totalUsers()
    {
        $query = $this->db->select('COUNT(*) as num')
                 ->from('users')
                 ->join("employees","users.employee_id = employees.employee_id")
                 ->get();
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }

    public function addAction()
    {
      $employee_id = $this->input->post('employee_id');
      $username = $this->input->post('username');
      $password = md5($this->input->post('password'));
      $role = $this->input->post('role');
      $user_id = $this->uuid->v4();

      $data = array(
        'user_id' => $user_id,
        'employee_id' => $employee_id,
        'username' => $username,
        'password' => $password,
        'role' => $role,
      );
      $add = $this->crud_model->createData('users',$data);
      $this->session->set_flashdata("success", "Your Data Has Been Added !");
      redirect('Account');

    }

    public function updateAction()
    {
      $user_id = $this->input->post('user_id');
      $employee_id = $this->input->post('employee_id');
      $username = $this->input->post('username');
      $password = md5($this->input->post('password'));
      $role = $this->input->post('role');

      $data = array(
        'employee_id' => $employee_id,
        'username' => $username,
        'password' => $password,
        'role' => $role,
      );
      $where="user_id='".$user_id."'";
      $update = $this->crud_model->updateData('users',$data,$where);
      if($update){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
        redirect('Account');
      }
    }

    public function deleteAction($user_id)
    {
      $where="user_id='".$user_id."'";
      $delete = $this->crud_model->deleteData('users',$where);
      if($delete){
        $this->session->set_flashdata("success", "Your Data Has Been Deleted !");
        redirect('Account');

      }
    }

    public function getAccount()
    {
      $user_id = $this->input->post('user_id');
      $where = "user_id=".$user_id;
      $user = $this->crud_model->readData('*','users',$where)->row();
      echo json_encode($user);

    }
}
