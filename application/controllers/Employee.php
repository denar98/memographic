<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

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
			redirect('Dashboard/designer');
		}
    error_reporting(0);
    $this->load->model('datatable_model');
    $this->load->model('crud_model');
	}

	public function index()
	{
    $data['page'] = 'employee_data';
		$data['module'] = 'employee';
		$this->load->view('template/head.html',$data);
		$this->load->view('employee/index.html');
		$this->load->view('template/foot.html');
	}

  public function getAllEmployee()
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
            1=>'employee_phone',
            2=>'employee_address',
            3=>'employee_job',
            4=>'employee_sallary',
            5=>'employee_level',
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
        $employees = $this->db->get("employees");
        $data = array();
        foreach($employees->result() as $rows)
        {

            $data[]= array(
                $rows->employee_name,
                $rows->employee_phone,
                $rows->employee_address,
                $rows->employee_job,
                number_format($rows->employee_sallary),
                $rows->employee_level,
                '<a href="#" class="btn btn-warning mr-1 btn-action" onclick="getEmployee('.$rows->employee_id.')" data-toggle="modal" data-target="#updateModal"><i class="fa fa-pencil"></i></a>
                 <a href="'.base_url().'Employee/deleteAction/'.$rows->employee_id.'" class="btn btn-danger mr-1  btn-action"><i class="fa fa-trash"></i></a>'
            );
        }
        $total_employees = $this->totalEmployees();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_employees,
            "recordsFiltered" => $total_employees,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function totalEmployees()
    {
        $query = $this->db->select("COUNT(*) as num")->get("employees");
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }

    public function addAction()
    {
      $employee_name = $this->input->post('employee_name');
      $employee_phone = $this->input->post('employee_phone');
      $employee_address = $this->input->post('employee_address');
      $employee_job = $this->input->post('employee_job');
      $employee_sallary = $this->input->post('employee_sallary');
      $employee_level = $this->input->post('employee_level');

      $data = array(
        'employee_name' => $employee_name,
        'employee_phone' => $employee_phone,
        'employee_address' => $employee_address,
        'employee_job' => $employee_job,
        'employee_sallary' => $employee_sallary,
        'employee_level' => $employee_level
      );
      $add = $this->crud_model->createData('employees',$data);
      if($add){
        $this->session->set_flashdata("success", "Your Data Has Been Added !");
        redirect('Employee');
      }

    }

    public function updateAction()
    {
      $employee_id = $this->input->post('employee_id');
      $employee_name = $this->input->post('employee_name');
      $employee_phone = $this->input->post('employee_phone');
      $employee_address = $this->input->post('employee_address');
      $employee_job = $this->input->post('employee_job');
      $employee_sallary = $this->input->post('employee_sallary');
      $employee_level = $this->input->post('employee_level');

      $data = array(
        'employee_name' => $employee_name,
        'employee_phone' => $employee_phone,
        'employee_address' => $employee_address,
        'employee_job' => $employee_job,
        'employee_sallary' => $employee_sallary,
        'employee_level' => $employee_level
      );
      $where="employee_id=".$employee_id;
      $update = $this->crud_model->updateData('employees',$data,$where);
      if($update){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
        redirect('Employee');
      }
    }

    public function deleteAction($employee_id)
    {
      $where="employee_id=".$employee_id;
      $delete = $this->crud_model->deleteData('employees',$where);
      if($delete){
        $this->session->set_flashdata("success", "Your Data Has Been Deleted !");
        redirect('Employee');

      }
    }

    public function getEmployee()
    {
      $employee_id = $this->input->post('employee_id');
      
      $where = "employee_id=".$employee_id;
      $employee = $this->crud_model->readData('*','employees',$where)->row();
      echo json_encode($employee);

    }
}
