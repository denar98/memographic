<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends CI_Controller {

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
    $data['page'] = 'main_service';
		$data['module'] = 'service';

		$this->load->view('template/head.html',$data);
		$this->load->view('service/index.html');
		$this->load->view('template/foot.html');
	}

  public function getAllService()
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
            0=>'service_id',
            1=>'service_name',
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
        $services = $this->db->get("services");
        $data = array();
        foreach($services->result() as $rows)
        {

            $data[]= array(
                $rows->service_id,
                $rows->service_name,
                '<a href="#" class="btn btn-warning mr-1 btn-action" onclick="getService('.$rows->service_id.')" data-toggle="modal" data-target="#updateModal"><i class="fa fa-pencil"></i></a>
                 <a href="'.base_url().'Service/deleteAction/'.$rows->service_id.'" class="btn btn-danger mr-1 btn-action"><i class="fa fa-trash"></i></a>'
            );
        }
        $total_services = $this->totalServices();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_services,
            "recordsFiltered" => $total_services,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function totalServices()
    {
        $query = $this->db->select("COUNT(*) as num")->get("services");
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }

    public function addAction()
    {
      $service_name = $this->input->post('service_name');

      $data = array(
        'service_name' => $service_name,
      );
      $add = $this->crud_model->createData('services',$data);
      if($add){
        $this->session->set_flashdata("success", "Your Data Has Been Added !");
        redirect('Service');
      }

    }

    public function updateAction()
    {
      $service_id = $this->input->post('service_id');
      $service_name = $this->input->post('service_name');

      $data = array(
        'service_name' => $service_name,
      );
      $where="service_id=".$service_id;
      $update = $this->crud_model->updateData('services',$data,$where);
      if($update){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
        redirect('Service');
      }
    }

    public function deleteAction($service_id)
    {
      $where="service_id=".$service_id;
      $delete = $this->crud_model->deleteData('services',$where);
      if($delete){
        $this->session->set_flashdata("success", "Your Data Has Been Deleted !");
        redirect('Service');

      }
    }

    public function getService()
    {
      $service_id = $this->input->post('service_id');
      $where = "service_id=".$service_id;
      $service = $this->crud_model->readData('*','services',$where)->row();
      echo json_encode($service);

    }
}
