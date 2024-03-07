<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tag extends CI_Controller {

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
    $data['page'] = 'main_tag';
		$data['module'] = 'tag';

		$this->load->view('template/head.html',$data);
		$this->load->view('tag/index.html');
		$this->load->view('template/foot.html');
	}

  public function getAllTag()
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
            0=>'tag_id',
            1=>'tag_name',
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
        $tags = $this->db->get("tags");
        $data = array();
        $i=0;
        foreach($tags->result() as $rows)
        {
          $tag_id = "'$rows->tag_id'";
          $i++;
            $data[]= array(
                $i,
                $rows->tag_name,
                '<a href="#" class="btn btn-warning mr-1 btn-action" onclick="getTag('.$tag_id.')" data-toggle="modal" data-target="#updateModal"><i class="fa fa-pencil"></i></a>
                 <a href="'.base_url().'Tag/deleteAction/'.$rows->tag_id.'" class="btn btn-danger mr-1 btn-action"><i class="fa fa-trash"></i></a>'
            );
        }
        $total_tags = $this->totalTags();
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_tags,
            "recordsFiltered" => $total_tags,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }
    public function totalTags()
    {
        $query = $this->db->select("COUNT(*) as num")->get("tags");
        $result = $query->row();
        if(isset($result)) return $result->num;
        return 0;
    }

    public function addAction()
    {
      $tag_name = $this->input->post('tag_name');
      $tag_id = $this->uuid->v4();

      $data = array(
        'tag_id' => $tag_id,
        'tag_name' => $tag_name,
      );
      $add = $this->crud_model->createData('tags',$data);
      $this->session->set_flashdata("success", "Your Data Has Been Added !");
      redirect('Tag');

    }

    public function updateAction()
    {
      $tag_id = $this->input->post('tag_id');
      $tag_name = $this->input->post('tag_name');

      $data = array(
        'tag_name' => $tag_name,
      );
      $where="tag_id='".$tag_id."'";
      $update = $this->crud_model->updateData('tags',$data,$where);
      if($update){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
        redirect('Tag');
      }
    }

    public function deleteAction($tag_id)
    {
      $where="tag_id='".$tag_id."'";
      $delete = $this->crud_model->deleteData('tags',$where);
      if($delete){
        $this->session->set_flashdata("success", "Your Data Has Been Deleted !");
        redirect('Tag');

      }
    }

    public function getTag()
    {
      $tag_id = $this->input->post('tag_id');
      $where="tag_id='".$tag_id."'";
      $tag = $this->crud_model->readData('*','tags',$where)->row();
      echo json_encode($tag);

    }
}
