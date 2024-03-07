<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServicePackage extends CI_Controller {

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
    $data['page'] = 'service_package';
		$data['module'] = 'service';

    $data['services'] = $this->db->get("services")->result();
		$this->load->view('template/head.html',$data);
		$this->load->view('service/package.html',$data);
		$this->load->view('template/foot.html');
	}

  public function getAllServicePackage()
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
            0=>'service_name',
            1=>'service_package_name',
            2=>'service_package_price',
            3=>'service_package_detail',
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
        $service_packages = $this->db->select('services.service_name,service_packages.service_package_id,service_packages.service_package_name,service_packages.service_package_price,service_packages.service_package_detail')
                 ->from('service_packages')
                 ->join("services","services.service_id = service_packages.service_id")
                 ->get();
        $data = array();
        
        foreach($service_packages->result() as $rows)
        {
          $service_package_id = "'$rows->service_package_id'";

            $data[]= array(
                $rows->service_name,
                $rows->service_package_name,
                $rows->service_package_price,
                $rows->service_package_detail,
                '<a href="#" class="btn btn-warning mr-1 btn-action" onclick="getServicePackage('.$service_package_id.')" data-toggle="modal" data-target="#updateModal"><i class="fa fa-pencil"></i></a>
                 <a href="'.base_url().'ServicePackage/deleteAction/'.$rows->service_package_id.'" class="btn btn-danger mr-1  btn-action"><i class="fa fa-trash"></i></a>'
            );
        }
        $total_services_package = $this->totalServicesPackage();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_services_package,
            "recordsFiltered" => $total_services_package,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function totalServicesPackage()
    {
        $query = $this->db->select('COUNT(*) as num')
               ->from('service_packages')
               ->join("services","services.service_id = service_packages.service_id")
               ->get();
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }

    public function addAction()
    {
      $service_id = $this->input->post('service_id');
      $service_package_name = $this->input->post('service_package_name');
      $service_package_price = $this->input->post('service_package_price');
      $service_package_detail = $this->input->post('service_package_detail');
      $service_package_id = $this->uuid->v4();

      $data = array(
        'service_package_id' => $service_package_id,
        'service_id' => $service_id,
        'service_package_name' => $service_package_name,
        'service_package_price' => $service_package_price,
        'service_package_detail' => $service_package_detail,
      );
      $add = $this->crud_model->createData('service_packages',$data);
      $this->session->set_flashdata("success", "Your Data Has Been Added !");
      redirect('ServicePackage');

    }

    public function updateAction()
    {
      $service_id = $this->input->post('service_id');
      $service_package_id = $this->input->post('service_package_id');
      $service_package_name = $this->input->post('service_package_name');
      $service_package_price = $this->input->post('service_package_price');
      $service_package_detail = $this->input->post('service_package_detail');
      $service_package_detail = $this->input->post('service_package_detail');
      $data = array(
        'service_id' => $service_id,
        'service_package_name' => $service_package_name,
        'service_package_price' => $service_package_price,
        'service_package_detail' => $service_package_detail,
      );
      $where="service_package_id='".$service_package_id."'";
      $update = $this->crud_model->updateData('service_packages',$data,$where);
      if($update){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
        redirect('ServicePackage');
      }
    }

    public function deleteAction($service_package_id)
    {
      $where="service_package_id='".$service_package_id."'";
      $delete = $this->crud_model->deleteData('service_packages',$where);
      if($delete){
        $this->session->set_flashdata("success", "Your Data Has Been Deleted !");
        redirect('ServicePackage');

      }
    }

    public function getServicePackage()
    {
      $service_package_id = $this->input->post('service_package_id');
      $where="service_package_id='".$service_package_id."'";
      $service = $this->crud_model->readData('*','service_packages',$where)->row();
      echo json_encode($service);

    }
    public function getServicePackageByServiceID()
    {
      $service_id = $this->input->post('service_id');
      $where = "service_id='".$service_id."'";
      $service = $this->crud_model->readData('*','service_packages',$where)->result();
      echo json_encode($service);

    }
}
