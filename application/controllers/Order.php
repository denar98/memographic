<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

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
    unset($_SESSION['success']);
		if($this->session->userdata('login_status')!='logged'){
			$this->session->set_flashdata("error", 'Please Login Before You Access This Page');
			redirect('Login');
		}
    error_reporting(0);
    $this->load->helper(array('url','html','form'));
    $this->load->model('datatable_model');
    $this->load->model('crud_model');
    $this->load->model('order_model');
	}

	public function index()
	{
    $data['page'] = 'order';
		$data['module'] = 'project';

    $data['orders'] = $this->db->get("orders")->result();
		$this->load->view('template/head.html',$data);
		$this->load->view('order/order.html',$data);
		$this->load->view('template/foot.html');
	}

	public function createOrder()
	{
    $data['page'] = 'order';
		$data['module'] = 'project';
    $data['clients'] = $this->db->get("clients")->result();
    $data['services'] = $this->db->get("services")->result();
    $data['service_packages'] = $this->db->get("service_packages")->result();
		$this->load->view('template/head.html',$data);
		$this->load->view('order/create_order.html',$data);
		$this->load->view('template/foot.html');
	}
	public function createOrderProject($order_id)
	{
    $data['order_id'] = $order_id;
    $select_package = "services.service_name,service_packages.service_package_id,service_packages.service_package_name";
    $where_package = "services.service_id = service_packages.service_id";
    $join_package = "'service_packages', 'services.service_id=service_packages.service_id'";
    $data['packages'] = $this->order_model->getAllService()->result();
		$this->load->view('template/head.html');
		$this->load->view('order/create_order_project.html',$data);
		$this->load->view('template/foot.html');
	}
  public function fetchOrders()
  {
    $output = '';

    
    $keyword = $this->input->post('keyword');
    $order_status = $this->input->post('order_status');
    $service_id = $this->input->post('service_id');
    $client_id = $this->input->post('client_id');

    // if($keyword != 'null'){
      $data = $this->order_model->getDataOrders($this->input->post('limit'), $this->input->post('start'),$keyword,$order_status,$service_id,$client_id);
    // }
    // else if($keyword == 'null'){
    //   $data = $this->custom_model->getDataProjects($this->input->post('limit'), $this->input->post('start'),'null');

    // }

    if($data->num_rows() > 0)
    {
     foreach($data->result() as $row)
     {

     
      // $base_url = base_url()."Project/detail/";
      // $url="'$base_url$row->project_id'";
      if($row->service_name=="Infographic"){
        $progress_color = 'bg-infographic';
      }
      else if($row->service_name=="Illustration"){
        $progress_color = 'bg-illustration';
      }


      if($row->order_status=="On Progress"){
        $badge_color = 'info';
      }
      else if($row->order_status=="In Revision"){
        $badge_color = 'danger';
      }
      else if($row->order_status=="Delivered"){
        $badge_color = 'primary';
      }
      else{
        $badge_color = 'secondary';
      }
      $output .= '
      <div class="row">
        <div class="col-xl-12 col-lg-12 mt-4">
          <div class="project-box"><span class="badge badge-'.$badge_color.'">'.$row->order_status.'</span>
            <div class="row">
              <div class="media col-md-3 mb-1">
                <div class="media-body">
                  <h7 class="mb-0 mt-2">Order Number</h7>
                  <h6>'.$row->order_number.'</h6>
                </div>
              </div>
              <div class="media col-md-3 mb-1">
                <!-- <img class="img-50 mr-3 rounded-circle" src="<?=base_url()?>assets/images/user/3.jpg" alt="" data-original-title="" title=""> -->
                <div class="media-body">
                  <h7 class="mb-0 mt-2">Client Name</h7>
                  <h6>'.$row->client_name.'</h6>
                </div>
              </div>
              <div class="media col-md-3 mb-1">
                <div class="media-body">
                  <h7 class="mb-0 mt-2">Service</h7>
                  <h6>'.$row->service_name.'</h6>
                </div>
              </div>
              <div class="media col-md-3 mb-1">
                <div class="media-body">
                  <h7 class="mb-0 mt-2">Package</h7>
                  <h6>'.$row->service_package_name.'</h6>
                </div>
              </div>
            </div>
            <div class="project-status mt-0">
              <div class="progress" style="height: 5px">
                <div class="progress-bar-animated '.$progress_color.' progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>';
     }
    }
    echo $output;
  }
  
    public function addAction()
    {
      $client_id = $this->input->post('client_id');
      $order_nominal = $this->input->post('order_nominal');
      $order_date = $this->input->post('order_date');
      $order_number = $this->input->post('order_number');
      $service_id = $this->input->post('service_id');
      $service_package_id = $this->input->post('service_package_id');
      $order_status = "On Progress";
      $brief= $this->input->post('brief');
      $user_id = $this->session->userdata('user_id');
      $order_id="1";
      $order_row = $this->db->limit(1)->order_by('order_id','desc')->get('orders')->row();
  
      if($order_row->order_id !=0 || $order_row->order_id != ''){
        $order_id = $order_row->order_id + 1;
      }
  
  
      $data = array(
        'client_id' => $client_id,
        'user_id' => $user_id,
        'order_nominal' => $order_nominal,
        'order_number' => $order_number,
        'service_id' => $service_id,
        'service_package_id' => $service_package_id,
        'order_date' => $order_date,
        'brief' => $brief,
        'order_status' => $order_status
      );

      $attachments = [];
   
      $count = count($_FILES['files']['name']);
    
      for($i=0;$i<$count;$i++){
    
        if(!empty($_FILES['files']['name'][$i])){
    
          $_FILES['file']['name'] = $_FILES['files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['files']['size'][$i];
  
          $config['upload_path'] = 'assets/attachments'; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
          $config['file_name'] = $_FILES['files']['name'][$i];
          
          $this->upload->initialize($config);
  
          if($this->upload->do_upload('file')){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
   
            $attachments['totalFiles'][] = $filename;
  
            $data_attachments = array(
              'order_id' => $order_id,
              'order_attachment_name' => $filename,
            );
            $add_attachments = $this->crud_model->createData('order_attachments',$data_attachments);
        
          }
        }
  
      }
      $add = $this->crud_model->createData('orders',$data);
      if($add){
        $this->session->set_flashdata("success", "Your Data Has Been Added !");
        redirect('Order/');
      }

    }
    
  function uploadImageSummernote(){
    if(isset($_FILES["image"]["name"])){
      $config['upload_path'] = 'assets/requirement_attachments/';
      $config['allowed_types'] = 'jpg|jpeg|png|gif';
      $this->upload->initialize($config);
      if(!$this->upload->do_upload('image')){
        $this->upload->display_errors();
        return FALSE;
      }else{
        $data = $this->upload->data();

        $config['image_library']='gd2';
        $config['source_image']='assets/requirement_attachments/'.$data['file_name'];
        $config['create_thumb']= FALSE;
        $config['maintain_ratio']= TRUE;
        $config['quality']= '60%';
        $config['width']= 800;
        $config['height']= 800;
        $config['new_image']= 'assets/requirement_attachments/'.$data['file_name'];
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
        echo base_url().'assets/requirement_attachments/'.$data['file_name'];
        }
    }
  }
    
  //Delete image summernote
  function deleteImageSummernote(){
    $src = $this->input->post('src');
    $file_name = str_replace(base_url(), '', $src);
    if(unlink($file_name))
    {
      echo 'File Delete Successfully';
    }
  }
    public function addOrderProjectAction()
    {
      $order_id = $this->input->post('order_id');
      $service_package_id = $this->input->post('service_package_id');
      $project_brief = $this->input->post('project_brief');
      $project_notes = $this->input->post('project_notes');
      $project_status = 0;

      $data = array(
        'order_id' => $order_id,
        'service_package_id' => $service_package_id,
        'project_brief' => $project_brief,
        'project_notes' => $project_notes,
        'project_status' => $project_status
      );
      $add = $this->crud_model->createData('projects',$data);
      $this->session->set_flashdata("success", "Your Data Has Been Added !");
      redirect('Order/createOrderProject/'.$order_id);

    }

   

    public function getOrder()
    {
      $orders_id = $this->input->post('orders_id');
      $where = "orders_id=".$orders_id;
      $orders = $this->crud_model->readData('*','orderss',$where)->row();
      echo json_encode($orders);

    }

    function projectAttachment(){
        if(!empty($_FILES)){
            // File upload configuration
            echo "test";
            $uploadPath = './uploads/project_attachment/';
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = '*';

            // Load and initialize upload library
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            // Upload file to the server
            if($this->upload->do_upload('userfile')){
                $fileData = $this->upload->data();
                $this->db->order_by('project_id', 'DESC');
                $this->db->limit('1');
                $project_last_id_row = $this->db->get('projects')->row();
                if($project_last_id_row!=null){
                  $project_last_id = $project_last_id_row;
                }
                else{
                  $project_last_id = 1;
                }
                $token=$this->input->post('token_foto');
                $uploadData['project_id'] = $project_last_id;
                $uploadData['token'] = $token;
                $uploadData['file_name'] = $fileData['file_name'];
                $uploadData['uploaded_date'] = date("Y-m-d H:i:s");

                // Insert files info into the database
                $insert = $this->crud_model->createData('project_attachments',$uploadData);
                echo"uploaded";
            }
            else {
              $error = array('error' => $this->upload->display_errors());
              print_r($error);
              // $this->load->view('display', $error);
            }
        }
    }

    //Untuk menghapus foto
	function removeProjectAttachment(){

		//Ambil token foto
		$token=$this->input->post('token');

		$foto=$this->db->get_where('project_attachments',array('token'=>$token));


		if($foto->num_rows()>0){
			$hasil=$foto->row();
			$nama_foto=$hasil->file_name;
			if(file_exists($file='./uploads/project_attachment/'.$nama_foto)){
				unlink($file);
        echo "deleted";
			}else{
        echo "nope";
      }
			$this->db->delete('project_attachments',array('token'=>$token));

		}


		echo "{}";
	}
}
