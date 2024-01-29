<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {

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
    $data['page'] = 'client';
		$data['module'] = 'master';

		$this->load->view('template/head.html',$data);
		$this->load->view('client/index.html');
		$this->load->view('template/foot.html');
	}

  public function getAllClient()
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
            1=>'client_id',
            1=>'client_name',
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
        $clients = $this->db->get("clients");
        $data = array();
        $i=0;
        foreach($clients->result() as $rows)
        {
          $i++;
            $data[]= array(
                $rows->client_id,
                $rows->client_name,
                '<a href="#" class="btn btn-warning mr-1 btn-action" onclick="getClient('.$rows->client_id.')" data-toggle="modal" data-target="#updateModal"><i class="fa fa-pencil"></i></a>
                 <a href="'.base_url().'Client/deleteAction/'.$rows->client_id.'" class="btn btn-danger mr-1 btn-action"><i class="fa fa-trash"></i></a>'
            );
        }
        $total_clients = $this->totalClients();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_clients,
            "recordsFiltered" => $total_clients,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function totalClients()
    {
        $query = $this->db->select("COUNT(*) as num")->get("clients");
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }

    public function addAction()
    {
      $client_name = $this->input->post('client_name');
      $client_phone = $this->input->post('client_phone');
      $client_address = $this->input->post('client_address');
      $client_email = $this->input->post('client_email');
      $client_company = $this->input->post('client_company');

      $data = array(
        'client_name' => $client_name,
        'client_phone' => $client_phone,
        'client_address' => $client_address,
        'client_email' => $client_email,
        'client_company' => $client_company,
      );
      $add = $this->crud_model->createData('clients',$data);
      if($add){
        $this->session->set_flashdata("success", "Your Data Has Been Added !");
        redirect('Client');
      }

    }

    public function updateAction()
    {
      $client_id = $this->input->post('client_id');
      $client_name = $this->input->post('client_name');
      $client_phone = $this->input->post('client_phone');
      $client_address = $this->input->post('client_address');
      $client_email = $this->input->post('client_email');
      $client_company = $this->input->post('client_company');

      $data = array(
        'client_name' => $client_name,
        'client_phone' => $client_phone,
        'client_address' => $client_address,
        'client_email' => $client_email,
        'client_company' => $client_company,
      );
      $where="client_id=".$client_id;
      $update = $this->crud_model->updateData('clients',$data,$where);
      if($update){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
        redirect('Client');
      }
    }

    public function deleteAction($client_id)
    {
      $where="client_id=".$client_id;
      $delete = $this->crud_model->deleteData('clients',$where);
      if($delete){
        $this->session->set_flashdata("success", "Your Data Has Been Deleted !");
        redirect('Client');

      }
    }

    public function getClient()
    {
      $client_id = $this->input->post('client_id');
      $where = "client_id=".$client_id;
      $client = $this->crud_model->readData('*','clients',$where)->row();
      echo json_encode($client);

    }
}
