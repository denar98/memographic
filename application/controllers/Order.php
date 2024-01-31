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
		if($this->session->userdata('login_status')!='logged'){
			$this->session->set_flashdata("error", 'Please Login Before You Access This Page');
			redirect('Login');
		}
    error_reporting(0);
    $this->load->helper(array('url','html','form'));
    $this->load->model('datatable_model');
    $this->load->model('crud_model');
    $this->load->model('order_model');
    $this->load->model('task_model');
    date_default_timezone_set('Asia/Jakarta');

	}

	public function index()
	{
    if($this->session->userdata('role')!='Admin' && $this->session->userdata('role')!='Project Manager' && $this->session->userdata('role')!='Head Designer'){
			$this->session->set_flashdata("error", "You Don't Have Access To This Page");
			redirect('Dashboard/designer');
		}
    $data['page'] = 'order';
		$data['module'] = 'project';

    $data['orders'] = $this->db->get("orders")->result();
    $data['employees'] = $this->db->get("employees")->result();
    $data['tags'] = $this->db->get("tags")->result();
		$this->load->view('template/head.html',$data);
		$this->load->view('order/order.html',$data);
		$this->load->view('template/foot.html');
	}
	public function detail($order_id)
	{
    $data['order'] = $this->order_model->getDetailDataOrder($order_id)->row();
    $data['task_deliveries'] = $this->task_model->getTaskDeliveryByOrderId($order_id)->result();
    $data['task'] = $this->task_model->getTaskByOrderId($order_id)->result();
    $data['attachments'] = $this->order_model->getAttachmentOrder($order_id)->result();
    $data['employees'] = $this->db->get("employees")->result();
    $data['tags'] = $this->db->get("tags")->result();

		$this->load->view('template/head.html');
		$this->load->view('order/detail.html',$data);
		$this->load->view('template/foot.html');
	}
	public function edit($order_id)
	{
    $data['clients'] = $this->db->get("clients")->result();
    $data['services'] = $this->db->get("services")->result();
    $data['service_packages'] = $this->db->get("service_packages")->result();
    $data['order'] = $this->order_model->getDetailDataOrder($order_id)->row();
    $data['attachments'] = $this->order_model->getAttachmentOrder($order_id)->result();
		$this->load->view('template/head.html');
		$this->load->view('order/edit.html',$data);
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
        $service_image = 'Infographic-image.png';
      }
      else if($row->service_name=="Illustration"){
        $progress_color = 'bg-illustration';
        $service_image = 'Illustration-image.png';
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

      if($row->service_package_name=="Basic"){
        $service_color = 'primary';
        $service_package_name = 'BSC';
      }
      else if($row->service_package_name=="Standard"){
        $service_color = 'warning';
        $service_package_name = 'STD';
      }
      else{
        $service_color = 'info';
        $service_package_name = 'PRM';
      }

      if($row->assign_to==null){
        $assign = "Not Assign";
        $btn_status = '';
      }else{
        $assign = $row->assign_to;
        $btn_status = 'disabled="disabled"';
      }

      $rem = strtotime($row->order_deadline) - time();
      $days_remaining = floor($rem / 86400);
      $hours_remaining  = floor(($rem % 86400) / 3600);
      $due_in = $days_remaining.'d, '.$hours_remaining.'h'; 
      $due_color = "black"; 
      if($days_remaining<=0 && $hours_remaining<=0){
        $due_in = "Late";
        $due_color = "danger";
      }
      // date_format(date_create($row->order_deadline),"d M Y")
      // echo "There are $days_remaining days and $hours_remaining hours left";
      $order_id = "'$row->order_id'";
      $output .= '
      
      <div class="row">
        <div class="col-xl-12 col-lg-12 mt-1 mb-2">
          <!-- <div class="project-box" style="padding:5px 15px !important;"><span class="badge badge-'.$badge_color.'">'.$row->order_status.'</span> -->
            <div class="row">
              <div class="media col-md-4  mt-2 mb-1">
              <img class="img mr-4 rounded" src="'.base_url().'assets/images/'.$service_image.'" style="width:65px;" alt="" data-original-title="" title="">
              <div class="media-body pt-2">
                  <h7 class="mb-0 mt-2">Order Number</h7>
                  <a href="'.base_url().'Order/detail/'.$row->order_id.'"><h6>'.$row->order_number.'</h6></a>
                </div>
              </div>
              <div class="media col-md-2 mt-2 mb-1">
                <div class="media-body pt-2">
                  <h7 class="mb-0 mt-2">Client Name</h7>
                  <h6>'.$row->client_name.'</h6>
                </div>
              </div>';
              if($this->session->userdata('role')=='Admin' || $this->session->userdata('role')=='Project Manager'){
                $output .= '<div class="media col-md-2 mt-2 mb-1">
                <div class="media-body pt-2">
                  <h7 class="mb-0 mt-2">Nominal</h7>
                  <h6>$'.$row->order_nominal.'</h6>
                </div>
                </div>';
              }else{
                $output .= '<div class="media col-md-2 mt-2 mb-1">
                <div class="media-body pt-2">
                  <h7 class="mb-0 mt-2">Service</h7>
                  <h6>'.$row->service_name.'</h6>
                </div>
                </div>';
              }
      

              $output .= '
              <div class="media col-md-2 mt-2 mb-1">
                <div class="media-body pt-2">
                  <h7 class="mb-0 mt-2">Deadline</h7>
                  <h6 class="text-'.$due_color.'">'.$due_in.'</h6>
                </div>
              </div>
              <div class="media col-md-2 mt-2 mb-1">
                <div class="media-body pt-2">
                  <button class="btn btn-'.$service_color.'" style="padding: 1px 30px; display:block; width: 95px; cursor:context-menu;">'.$service_package_name.'</button>
                  <button class="btn btn-secondary mt-1 btn-not-assign" data-assign="'.$assign.'" '.$btn_status.'   onclick="assignOrder('.$order_id.')" style="padding: 1px 10px; display:block;">'.$assign.'</button>
                </div>
              </div>
            </div>
            <div class="project-status mt-0">
            <!--<div class="progress" style="height: 5px">
                <div class="progress-bar-animated '.$progress_color.' progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
              </div> -->
            </div>
          </div>
        </div>
        <hr>
      </div>';
     }
    }
    echo $output;
  }
  
    public function addAction()
    {
      $order_id = $this->uuid->v4();
      $client_id = $this->input->post('client_id');
      $client_name = $this->input->post('client_name');
      $order_nominal = $this->input->post('order_nominal');
      $order_date = $this->input->post('order_date');
      $order_deadline = $this->input->post('order_deadline');
      $order_number = $this->input->post('order_number');
      $service_id = $this->input->post('service_id');
      $service_package_id = $this->input->post('service_package_id');
      $order_status = "On Progress";
      $brief= $this->input->post('brief');
      $user_id = $this->session->userdata('user_id');
      $order_row = $this->db->limit(1)->order_by('order_id','desc')->get('orders')->row();
      $service_row = $this->db->where('service_id',$service_id)->get('services')->row();
  

      if($client_name == ''){
        $client_id = $client_id;
        $client_exist_row = $this->db->where('client_id',$client_id)->get('clients')->row();
      }
      else{
        $client_row = $this->db->limit(1)->order_by('client_id','desc')->get('clients')->row();
        $client_id = $this->uuid->v4();
        $client_data = array(
          'client_id' => $client_id,
          'client_name' => $client_name,
        );
        $add_clients = $this->crud_model->createData('clients',$client_data);
      }

      $data = array(
        'order_id' => $order_id,
        'client_id' => $client_id,
        'user_id' => $user_id,
        'order_nominal' =>  str_replace( ',', '', $order_nominal),
        'order_number' => $order_number,
        'service_id' => $service_id,
        'service_package_id' => $service_package_id,
        'order_date' => $order_date,
        'order_deadline' => $order_deadline,
        'brief' => $brief,
        'order_status' => $order_status
      );

      $attachments = [];
   
      $count = count($_FILES['files']['name']);
    
      for($i=0;$i<$count;$i++){
    
        if(!empty($_FILES['files']['name'][$i])){
    

          // $date = str_replace( ':', '', $date);
          if (!is_dir('assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_number.'/attachments')) {
            mkdir('./assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_number.'/attachments', 0777, TRUE);
          }
          $path = './assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_number.'/attachments';
          $_FILES['file']['name'] = $_FILES['files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['files']['size'][$i];
  
          $config['upload_path'] = $path; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
          $config['overwrite'] = FALSE;
          $config['file_name'] = $_FILES['files']['name'][$i];
          
          $this->upload->initialize($config);
  
          if($this->upload->do_upload('file')){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
   
            $attachments['totalFiles'][] = $filename;
            $order_attachment_id  = $this->uuid->v4();

            $data_attachments = array(
              'order_attachment_id ' => $order_attachment_id ,
              'order_id' => $order_id,
              'order_attachment_name' => $filename,
            );
            $add_attachments = $this->crud_model->createData('order_attachments',$data_attachments);
        
          }
        }
  
      }
      $add = $this->crud_model->createData('orders',$data);
     
    $this->session->set_flashdata("success", "Your Data Has Been Added !");
    redirect('Order/');
  

    }
    public function updateAction()
    {
      $order_id = $this->input->post('order_id');
      $client_id = $this->input->post('client_id');
      $client_name = $this->input->post('client_name');
      $order_nominal = $this->input->post('order_nominal');
      $order_date = $this->input->post('order_date');
      $order_deadline = $this->input->post('order_deadline');
      $order_number = $this->input->post('order_number');
      $service_id = $this->input->post('service_id');
      $service_package_id = $this->input->post('service_package_id');
      $order_status = "On Progress";
      $brief= $this->input->post('brief');
      $user_id = $this->session->userdata('user_id');
  
      if($client_name == ''){
        $client_id = $client_id;
      }
      else{
        $client_row = $this->db->limit(1)->order_by('client_id','desc')->get('clients')->row();
        $client_id = $this->uuid->v4();
        $client_data = array(
          'client_id' => $client_id,
          'client_name' => $client_name,
        );
        $add_clients = $this->crud_model->createData('clients',$client_data);
      }

      $data = array(
        'client_id' => $client_id,
        'user_id' => $user_id,
        'order_nominal' =>  str_replace( ',', '', $order_nominal),
        'order_number' => $order_number,
        'service_id' => $service_id,
        'service_package_id' => $service_package_id,
        'order_date' => $order_date,
        'order_deadline' => $order_deadline,
        'brief' => $brief,
        'order_status' => $order_status
      );

      if(!empty($_FILES['files'])) {
        $attachments = [];
   
        $count = count($_FILES['files']['name']);
      
        for($i=0;$i<$count;$i++){
      
          if(!empty($_FILES['files']['name'][$i])){
      
            $_FILES['file']['name'] = $_FILES['files']['name'][$i];
            $_FILES['file']['type'] = $_FILES['files']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['file']['error'] = $_FILES['files']['error'][$i];
            $_FILES['file']['size'] = $_FILES['files']['size'][$i];
            if (!is_dir('assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_number.'/attachments')) {
              mkdir('./assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_number.'/attachments', 0777, TRUE);
            }
            $path = './assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_number.'/attachments';
  
            $config['upload_path'] = $path; 
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
            $config['overwrite'] = FALSE;
            $config['file_name'] = $_FILES['files']['name'][$i];
            
            $this->upload->initialize($config);
    
            if($this->upload->do_upload('file')){
              $uploadData = $this->upload->data();
              $filename = $uploadData['file_name'];
     
              $attachments['totalFiles'][] = $filename;
    
              $order_attachment_id  = $this->uuid->v4();

              $data_attachments = array(
                'order_attachment_id ' => $order_attachment_id ,
                'order_id' => $order_id,
                'order_attachment_name' => $filename,
              );
                $add_attachments = $this->crud_model->createData('order_attachments',$data_attachments);
          
            }
          }
    
        }
    }
      $where="order_id='".$order_id."'";
      $update = $this->crud_model->updateData('orders',$data,$where);
      $this->session->set_flashdata("success", "Your Data Has Been Updated !");
      redirect('Order/');

    }
    public function assignAction()
    {
      
      $order_id = $this->input->post('order_id');
      $assign_type = $this->input->post('assign_type');
      $order_row = $this->db->where('order_id',$order_id)->get('orders')->row();
      $task_revision_count_row = $this->db->where('order_id',$order_id)->order_by('task_revision_count','desc')->get('tasks')->row();
      $task_new_count_row = $this->db->where('order_id',$order_id)->order_by('task_new_count','desc')->get('tasks')->row();

      if($task_revision_count_row){
        $task_revision_count = $task_revision_count_row->task_revision_count + 1;
      }
      else{
        $task_revision_count = 1;
      }

      if($task_new_count_row ){
        $task_new_count = $task_new_count_row->task_new_count + 1;
      }
      else{
        $task_new_count = 1;
      }

      if($order_row->order_status=='On Progress'){
        $task_type = "New";
      }
      else{
        $task_type = "Revision";
      }

      if($task_type == 'New'){
        $task_revision_count = "";
      }
      else{
        $task_new_count = "";

      }
      if($assign_type=='update'){
        $task_id = $this->input->post('task_id');
      }else{
        $task_id = $this->uuid->v4();
      }     
      $employee_id = $this->input->post('employee_id');
      $task_date = Date('Y-m-d');
      $task_estimation_hour = $this->input->post('task_estimation_hour');
      $task_estimation_minute = $this->input->post('task_estimation_minute');
      $task_start = $this->input->post('task_start');
      $task_brief = $order_row->brief;
      $tag_id = $this->input->post('tag_id');
      $employee_row = $this->db->where('employee_id',$employee_id)->get('employees')->row();
      $data = array(
        'task_id' => $task_id,
        'order_id' => $order_id,
        'task_type' => $task_type,
        'employee_id' => $employee_id,
        'task_name' => $task_date,
        'task_date' => $task_date,
        'task_estimation_hour' => $task_estimation_hour,
        'task_estimation_minute' => $task_estimation_minute,
        'task_start' => $task_start,
        'task_brief' => 'As described on the initial requirement',
        'tag_id' => $tag_id,
        'task_status' => 'Open',
        'task_new_count' => $task_new_count,
        'task_revision_count' => $task_revision_count,
      );
      
      // if($order_row->assign_to != '' || $order_row->assign_to != null){
      //   $assign_to = $order_row->assign_to.', '.$employee_row->employee_name;
      // }
      // else{
      //   $assign_to = $employee_row->employee_name;
      // }
      $data_order = array(
        'assign_to' =>  $employee_row->employee_name
      );

      if($assign_type=='update'){
        
        $task_todays = $this->task_model->getTaskByEmployee($employee_id)->result();
        $estimate_hour = 0;
        $estimate_minute = 0;
        $overtime_id = $this->uuid->v4();

        foreach ($task_todays as $task_today) {
          $estimate_hour += $task_today->task_estimation_hour;
          $estimate_minute += $task_today->task_estimation_minute;
        } 
  
        $current_estimate = ($estimate_hour*60) + $estimate_minute;
        $new_estimate = ($task_estimation_hour*60) + $task_estimation_minute;
        $total_estimate = $new_estimate + $current_estimate;
        $overtime_time = ($new_estimate + $current_estimate) - 420;

        if(Date('D') =='Sun'){
          $overtime_type = 'Weekend';
        }else{
          $overtime_type = 'Weekday';
        }
        
        if($total_estimate >420){
          $data_overtime = array(
            'overtime_id' => $overtime_id,
            'employee_id' => $employee_id,
            'date' => Date('Y-m-d'),
            'overtime_type' => $overtime_type,
            'overtime_time' => $overtime_time,
          );
          $overtimes = $this->db->get('overtimes');
          if($overtimes->num_rows() > 0){
            $where_overtime = "employee_id='".$employee_id."' AND date='".Date('Y-m-d')."'";
            $this->crud_model->updateData('overtimes',$data_overtime,$where_overtime);          
          }else{
            $this->crud_model->createData('overtimes',$data_overtime);
          }

        $where = "task_id='".$task_id."'";
        $add = $this->crud_model->updateData('tasks',$data,$where);
        }      
      }
      else{

        $task_todays = $this->task_model->getTaskByEmployee($employee_id)->result();
        $estimate_hour = 0;
        $estimate_minute = 0;
        $overtime_id = $this->uuid->v4();

        foreach ($task_todays as $task_today) {
          $estimate_hour += $task_today->task_estimation_hour;
          $estimate_minute += $task_today->task_estimation_minute;
        } 
  
        $current_estimate = ($estimate_hour*60) + $estimate_minute;
        $new_estimate = ($task_estimation_hour*60) + $task_estimation_minute;
        $total_estimate = $new_estimate + $current_estimate;
        $overtime_time = ($new_estimate + $current_estimate) - 420;

        if(Date('D') =='Sun'){
          $overtime_type = 'Weekend';
        }else{
          $overtime_type = 'Weekday';
        }

        if($total_estimate >420){
          $data_overtime = array(
            'overtime_id' => $overtime_id,
            'employee_id' => $employee_id,
            'date' => Date('Y-m-d'),
            'overtime_type' => $overtime_type,
            'overtime_time' => $overtime_time,
          );
          $overtimes = $this->db->get('overtimes');
          if($overtimes->num_rows() > 0){
            $where_overtime = "employee_id='".$employee_id."' AND date='".Date('Y-m-d')."'";
            $this->crud_model->updateData('overtimes',$data_overtime,$where_overtime);          
          }else{
            $this->crud_model->createData('overtimes',$data_overtime);
          }

        }
        $add = $this->crud_model->createData('tasks',$data);
      }
      
      $where = "order_id='".$order_id."'";
      $update = $this->crud_model->updateData('orders',$data_order,$where);
      if($assign_type=='update'){
        $this->session->set_flashdata("success", "Your Data Has Been Updated !");
      }      
      else{
        $this->session->set_flashdata("success", "Your Data Has Been Added !");
      }
      redirect('Order/detail/'.$order_id);
 
      

    }
    public function assignRevisionAction()
    {
      
      $order_id = $this->input->post('order_id');
      $order_row = $this->db->where('order_id',$order_id)->get('orders')->row();

      $assign_type = $this->input->post('assign_type');
      $task_id = $this->input->post('task_id');
      $task_type = $this->input->post('task_type');
      $employee_id = $this->input->post('employee_id');
      $employee_id_before = $this->input->post('employee_id_before');
      $task_date = Date('Y-m-d');
      $task_estimation_hour = $this->input->post('task_estimation_hour');
      $task_estimation_minute = $this->input->post('task_estimation_minute');
      $task_start = $this->input->post('task_start');
      $task_brief = $this->input->post('brief');
      $tag_id = $this->input->post('tag_id');

      $service_row = $this->db->where('service_id',$order_row->service_id)->get('services')->row();
      $client_exist_row = $this->db->where('client_id',$order_row->client_id)->get('clients')->row();
      $employee_row = $this->db->where('employee_id',$employee_id)->get('employees')->row();

      if($task_type == 'New'){
        $order_status = "On Progress";
      }
      else{
        $order_status = "In Revision";
      }     
     

      $data = array(
        'task_id' => $task_id,
        'order_id' => $order_id,
        'task_type' => $task_type,
        'employee_id' => $employee_id,
        'task_date' => $task_date,
        'task_estimation_hour' => $task_estimation_hour,
        'task_estimation_minute' => $task_estimation_minute,
        'task_start' => $task_start,
        'task_brief' => $task_brief,
        'tag_id' => $tag_id,
        'task_status' => 'Open',
      );
      $data_order = array(
        'assign_to' => $employee_row->employee_name,
        'order_status' => $order_status
      );

      $attachments = [];
   
      $count = count($_FILES['files']['name']);
    
      for($i=0;$i<$count;$i++){
    
        if(!empty($_FILES['files']['name'][$i])){
    

          // $date = str_replace( ':', '', $date);
          if (!is_dir('assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_row->order_number.'/attachments')) {
            mkdir('./assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_row->order_number.'/attachments', 0777, TRUE);
          }
          $path = './assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_row->order_number.'/attachments';
          $_FILES['file']['name'] = $_FILES['files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['files']['size'][$i];
          $_FILES['file']['file_ext'] = $_FILES['files']['file_ext'][$i];
    
          $config['upload_path'] = $path; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
          $config['overwrite'] = FALSE;
          $config['file_name'] = $_FILES['files']['name'][$i];
          
          $this->upload->initialize($config);
  
          if($this->upload->do_upload('file')){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
   
            $attachments['totalFiles'][] = $filename;
            $task_attachment_id =  $this->uuid->v4();
            $data_attachments = array(
              'task_attachment_id' => $task_attachment_id,
              'task_id' => $task_id,
              'task_attachment_name' => $filename,
              'task_attachment_ext' =>$uploadData['file_ext'],
              'task_attachment_width' => $uploadData['image_width'],
              'task_attachment_height' => $uploadData['image_height'],
            );
            $add_attachments = $this->crud_model->createData('task_attachments',$data_attachments);
        
          }
        }
      }
      // Step 1 Overtime
      if($employee_id == $employee_id_before){
        $task_todays = $this->task_model->getTaskByEmployee($employee_id)->result();
        $this_task = $this->task_model->getDetailTask($task_id)->row();

        $estimate_hour = 0;
        $estimate_minute = 0;
        foreach ($task_todays as $task_today) {
          $estimate_hour += $task_today->task_estimation_hour;
          $estimate_minute += $task_today->task_estimation_minute;
        } 
  
        $current_estimate = ((int)$estimate_hour*60) + (int)$estimate_minute;
        $this_estimate = (((int)$this_task->task_estimation_hour*60) + (int)$this_task->task_estimation_minute);
        $new_estimate = (((int)$task_estimation_hour*60) + (int)$task_estimation_minute);

        $total_estimate = (int)$new_estimate + (int)$current_estimate - (int)$this_estimate;
        $overtime_time = (int)$total_estimate - 420;
        if(Date('D') =='Sun'){
          $overtime_type = 'Weekend';
        }else{
          $overtime_type = 'Weekday';
        }
        if($total_estimate > 420){
          $overtime_id =  $this->uuid->v4();

          $data_overtime = array(
            'overtime_id' => $overtime_id,
            'employee_id' => $employee_id,
            'date' => Date('Y-m-d'),
            'overtime_type' => $overtime_type,
            'overtime_time' => $overtime_time,
          );
          $overtimes = $this->db->get('overtimes');
          if($overtimes->num_rows() > 0){
            $where_overtime = "employee_id='".$employee_id."' AND date=CURDATE()";
            $this->crud_model->deleteData('overtimes',$where_overtime);          
            $this->crud_model->createData('overtimes',$data_overtime);
          }else{
            $this->crud_model->createData('overtimes',$data_overtime);
          }
        }
        else{
          $where_overtime = "employee_id='".$employee_id."' AND date=CURDATE()";
          $this->crud_model->deleteData('overtimes',$where_overtime);          
        }
      }else{
        // Employee 1
        $task_todays = $this->task_model->getTaskByEmployee($employee_id)->result();
        $this_task = $this->task_model->getDetailTask($task_id)->row();

        $estimate_hour = 0;
        $estimate_minute = 0;
        foreach ($task_todays as $task_today) {
          $estimate_hour += $task_today->task_estimation_hour;
          $estimate_minute += $task_today->task_estimation_minute;
        } 
  
        $current_estimate = ((int)$estimate_hour*60) + (int)$estimate_minute;
        $this_estimate = (((int)$this_task->task_estimation_hour*60) + (int)$this_task->task_estimation_minute);
        $new_estimate = (((int)$task_estimation_hour*60) + (int)$task_estimation_minute);

        $total_estimate = (int)$new_estimate + (int)$current_estimate;
        $overtime_time = (int)$total_estimate - 420;
        if(Date('D') =='Sun'){
          $overtime_type = 'Weekend';
        }else{
          $overtime_type = 'Weekday';
        }
        if($total_estimate > 420){
          $overtime_id =  $this->uuid->v4();

          $data_overtime = array(
            'overtime_id' => $overtime_id,
            'employee_id' => $employee_id,
            'date' => Date('Y-m-d'),
            'overtime_type' => $overtime_type,
            'overtime_time' => $overtime_time,
          );
          $overtimes = $this->db->get('overtimes');
          if($overtimes->num_rows() > 0){
            $where_overtime = "employee_id='".$employee_id."' AND date=CURDATE()";
            $this->crud_model->deleteData('overtimes',$where_overtime);          
            $this->crud_model->createData('overtimes',$data_overtime);
          }else{
            $this->crud_model->createData('overtimes',$data_overtime);
          }
        }
        else{
          $where_overtime = "employee_id='".$employee_id."' AND date=CURDATE()";
          $this->crud_model->deleteData('overtimes',$where_overtime);          
        }

        // Employee 2
        $task_todays_before = $this->task_model->getTaskByEmployee($employee_id_before)->result();
        $this_task_before = $this->task_model->getDetailTask($task_id)->row();

        $estimate_hour_before = 0;
        $estimate_minute_before = 0;
        foreach ($task_todays_before as $task_today_before) {
          $estimate_hour_before += $task_today_before->task_estimation_hour;
          $estimate_minute_before += $task_today_before->task_estimation_minute;
        } 
        $current_estimate_before = ((int)$estimate_hour_before*60) + (int)$estimate_minute_before;
        $this_estimate_before = (((int)$this_task_before->task_estimation_hour*60) + (int)$this_task_before->task_estimation_minute);

        $total_estimate_before = (int)$current_estimate_before - (int)$this_estimate_before;
        $overtime_time_before = (int)$total_estimate_before - 420;;
        $where_overtime = "employee_id='".$employee_id_before."' AND date='".Date('Y-m-d')."'";
        if($total_estimate_before > 420){
          $data_overtime_before = array(
            'date' => Date('Y-m-d'),
            'overtime_time' => $overtime_time_before,
          );
          $this->crud_model->updateData('overtimes',$data_overtime_before,$where_overtime);       
        }else{
          $this->crud_model->deleteData('overtimes',$where_overtime);          
        }
      }
      

      $where_task = "task_id='".$task_id."'";
      $this->crud_model->updateData('tasks',$data,$where_task);
      $where = "order_id='".$order_id."'";
      $this->crud_model->updateData('orders',$data_order,$where);
      $this->session->set_flashdata("success", "Your Data Has Been Added !");
      if($this->input->post('source_assign') == "fromDetailPage"){
        redirect('Order/detail/'.$order_id);
      }else if($this->input->post('source_assign') == "fromTaskPage"){
        redirect('Task');
      }else{
        redirect('Order');
      }
    

    }
    public function assignNewTaskAction()
    {
      
      $order_id = $this->input->post('order_id');
      $task_type = $this->input->post('task_type');
      $order_row = $this->db->where('order_id',$order_id)->get('orders')->row();
      $assign_type = $this->input->post('assign_type');
      $task_id = $this->uuid->v4();
      $employee_id = $this->input->post('employee_id');
      $task_date = Date('Y-m-d');
      $task_estimation_hour = $this->input->post('task_estimation_hour');
      $task_estimation_minute = $this->input->post('task_estimation_minute');
      $task_start = $this->input->post('task_start');
      $task_brief = $this->input->post('brief');
      $tag_id = $this->input->post('tag_id');

      $task_row = $this->db->limit(1)->order_by('task_id','desc')->get('tasks')->row();
      $service_row = $this->db->where('service_id',$order_row->service_id)->get('services')->row();
      $client_exist_row = $this->db->where('client_id',$order_row->client_id)->get('clients')->row();

      $employee_row = $this->db->where('employee_id',$employee_id)->get('employees')->row();
      $task_revision_count_row = $this->db->where('order_id',$order_id)->order_by('task_revision_count','desc')->get('tasks')->row();
      $task_new_count_row = $this->db->where('order_id',$order_id)->order_by('task_new_count','desc')->get('tasks')->row();

      if($task_revision_count_row){
        $task_revision_count = $task_revision_count_row->task_revision_count + 1;
      }
      else{
        $task_revision_count = 1;
      }

      if($task_new_count_row ){
        $task_new_count = $task_new_count_row->task_new_count + 1;
      }
      else{
        $task_new_count = 1;
      }


      if($task_type == 'New'){
        $task_revision_count = "";
        $order_status = "On Progress";
      }
      else{
        $task_new_count = "";
        $order_status = "In Revision";
      }

      $data = array(
        'task_id' => $task_id,
        'order_id' => $order_id,
        'task_type' => $task_type,
        'employee_id' => $employee_id,
        'task_date' => $task_date,
        'task_estimation_hour' => $task_estimation_hour,
        'task_estimation_minute' => $task_estimation_minute,
        'task_start' => $task_start,
        'task_brief' => $task_brief,
        'tag_id' => $tag_id,
        'task_status' => 'Open',
        'task_new_count' => $task_new_count,
        'task_revision_count' => $task_revision_count,
      );

      $data_order = array(
        'assign_to' => $employee_row->employee_name,
        'order_status' => $order_status
      );

      $attachments = [];
   
      $count = count($_FILES['files']['name']);
    
      for($i=0;$i<$count;$i++){
    
        if(!empty($_FILES['files']['name'][$i])){
    

          // $date = str_replace( ':', '', $date);
          if (!is_dir('assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_row->order_number.'/attachments')) {
            mkdir('./assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_row->order_number.'/attachments', 0777, TRUE);
          }
          $path = './assets/attachments/'.$service_row->service_name.'/'.$client_exist_row->client_name.'/'.$order_row->order_number.'/attachments';
          $_FILES['file']['name'] = $_FILES['files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['files']['size'][$i];
          $_FILES['file']['file_ext'] = $_FILES['files']['file_ext'][$i];
    
          $config['upload_path'] = $path; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
          $config['overwrite'] = FALSE;
          $config['file_name'] = $_FILES['files']['name'][$i];
          
          $this->upload->initialize($config);
  
          if($this->upload->do_upload('file')){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
   
            $attachments['totalFiles'][] = $filename;
            $task_attachment_id =  $this->uuid->v4();

            $data_attachments = array(
              'task_attachment_id' => $task_attachment_id,
              'task_id' => $task_id,
              'task_attachment_name' => $filename,
              'task_attachment_ext' =>$uploadData['file_ext'],
              'task_attachment_width' => $uploadData['image_width'],
              'task_attachment_height' => $uploadData['image_height'],
            );
            $add_attachments = $this->crud_model->createData('task_attachments',$data_attachments);
        
          }
        }
      }

      $task_todays = $this->task_model->getTaskByEmployee($employee_id)->result();
      $estimate_hour = 0;
      $estimate_minute = 0;
      foreach ($task_todays as $task_today) {
        $estimate_hour += $task_today->task_estimation_hour;
        $estimate_minute += $task_today->task_estimation_minute;
      } 

      $current_estimate = ($estimate_hour*60) + $estimate_minute;
      $new_estimate = ($task_estimation_hour*60) + $task_estimation_minute;
      $total_estimate = $new_estimate + $current_estimate;
      $overtime_time = ($new_estimate + $current_estimate) - 420;
      if(Date('D') =='Sun'){
        $overtime_type = 'Weekend';
      }else{
        $overtime_type = 'Weekday';
      }

      if($total_estimate > 420){
        $overtime_id =  $this->uuid->v4();

        $data_overtime = array(
          'overtime_id' => $overtime_id,
          'employee_id' => $employee_id,
          'date' => Date('Y-m-d'),
          'overtime_type' => $overtime_type,
          'overtime_time' => $overtime_time,
        );
        $overtimes = $this->db->where("employee_id = '".$employee_id."' and date = '".Date('Y-m-d')."'")->get('overtimes');
     
        if($overtimes->num_rows()>0){
            $where_overtime = "employee_id='".$employee_id."' AND date='".Date('Y-m-d')."'";
          $this->crud_model->updateData('overtimes',$data_overtime,$where_overtime);          
        }else{
          $this->crud_model->createData('overtimes',$data_overtime);
        }
      }
      $add = $this->crud_model->createData('tasks',$data);
    

    
        $where = "order_id='".$order_id."'";
        $update = $this->crud_model->updateData('orders',$data_order,$where);
        $this->session->set_flashdata("success", "Your Data Has Been Added !");
        if($this->input->post('source_assign') == "fromDetailPage"){
          redirect('Order/detail/'.$order_id);
        }else if($this->input->post('source_assign') == "fromTaskPage"){
          redirect('Task');
        }else{
          redirect('Order/');
        }
      

    }
  // function uploadImageSummernote(){
  //   if(isset($_FILES["image"]["name"])){
  //     $config['upload_path'] = 'assets/requirement_attachments/';
  //     $config['allowed_types'] = 'jpg|jpeg|png|gif';
  //     $this->upload->initialize($config);
  //     if(!$this->upload->do_upload('image')){
  //       $this->upload->display_errors();
  //       return FALSE;
  //     }else{
  //       $data = $this->upload->data();

  //       $config['image_library']='gd2';
  //       $config['source_image']='assets/requirement_attachments/'.$data['file_name'];
  //       $config['create_thumb']= FALSE;
  //       $config['maintain_ratio']= TRUE;
  //       $config['quality']= '60%';
  //       $config['width']= 800;
  //       $config['height']= 800;
  //       $config['new_image']= 'assets/requirement_attachments/'.$data['file_name'];
  //       $this->load->library('image_lib', $config);
  //       $this->image_lib->resize();
  //       echo base_url().'assets/requirement_attachments/'.$data['file_name'];
  //       }
  //   }
  // }
    
  // //Delete image summernote
  // function deleteImageSummernote(){
  //   $src = $this->input->post('src');
  //   $file_name = str_replace(base_url(), '', $src);
  //   if(unlink($file_name))
  //   {
  //     echo 'File Delete Successfully';
  //   }
  // }
   
    public function sentTaskAction($task_id,$order_id)
    {

      $data_task = array(
        'task_status' => 'Wait Rating'
      );
      $data_order = array(
        'order_status' => 'Delivered',
        'assign_to' => NULL
      );
      $where_task = "task_id='".$task_id."'";
      $where_order = "order_id='".$order_id."'";
      $update_task = $this->crud_model->updateData('tasks',$data_task,$where_task);
      if($update_task){
        $update_order = $this->crud_model->updateData('orders',$data_order,$where_order);
        if($update_order){
          $this->session->set_flashdata("success", "Your Data Has Been Delivered !");
          redirect('Order/detail/'.$order_id);  
        }
  
      }

    }
    public function rejectTaskAction($task_id,$order_id)
    {

      $data_task = array(
        'task_status' => 'Open'
      );
      $data_order = array(
        'order_status' => 'Revision',
      );
      $where_task = "task_id='".$task_id."'";
      $where_order = "order_id='".$order_id."'";
      $update_task = $this->crud_model->updateData('tasks',$data_task,$where_task);
      if($update_task){
        $update_order = $this->crud_model->updateData('orders',$data_order,$where_order);

        $task_delivery_attachments = $this->task_model->getTaskDeliveryByTaskId($task_id)->result();

        foreach ($task_delivery_attachments as $task_delivery_attachment) {
          if($task_delivery_attachment->task_delivery_attachment_type == "Preview"){
            $task_delivery_attachment_type = 'preview';
          }
          else if($task_delivery_attachment->task_delivery_attachment_type == "Proven"){
            $task_delivery_attachment_type = 'proven';
          }
          else if($task_delivery_attachment->task_delivery_attachment_type == "Source"){
            $task_delivery_attachment_type = 'source';
          }
          $path = base_url().'assets/attachments/'.$task_delivery_attachment->service_name.'/'.$task_delivery_attachment->client_name.'/'.$task_delivery_attachment->order_number.'/'.$task_delivery_attachment_type.'/'.$task_delivery_attachment->task_delivery_attachment_name;
          delete_files($path);
        }
        $delete_delivery_attachment = $this->crud_model->deleteData('task_delivery_attachments',$where_task);
        $delete_delivery = $this->crud_model->deleteData('task_deliveries',$where_task);

        if($update_order){
          $this->session->set_flashdata("success", "Your Data Has Been Rejected !");
          redirect('Order/detail/'.$order_id);  
        }
  
      }

    }
    public function completedAction($order_id)
    {
      $data_order = array(
        'order_status' => 'Completed',
        'assign_to' => NULL
      );
      $where_order = "order_id='".$order_id."'";
      $update_order = $this->crud_model->updateData('orders',$data_order,$where_order);
      if($update_order){
        $this->session->set_flashdata("success", "Your Order Has Been Completed !");
        redirect('Order/detail/'.$order_id);  
      }

    }
    public function ratingAction()
    {
      $order_id = $this->input->post('order_id');
      $task_id = $this->input->post('task_id');
      $employee_id = $this->input->post('employee_id');
      $brief_reading = $this->input->post('brief_reading');
      $quality = $this->input->post('quality');
      $speed = $this->input->post('speed');
      $user_id = $this->session->userdata('user_id');
      $rating_id = $this->uuid->v4();

      $data_task = array(
        'task_status' => 'Delivered'
      );
      $data_rating = array(
        'rating_id' => $rating_id,
        'task_id' => $task_id,
        'employee_id' => $employee_id,
        'brief_reading' => $brief_reading,
        'quality' => $quality,
        'speed' => $speed,
        'user_id' => $user_id
      );
      $add_rating = $this->crud_model->createData('ratings',$data_rating);
      $where_task = "task_id='".$task_id."'";
      $update_task = $this->crud_model->updateData('tasks',$data_task,$where_task);

      $this->session->set_flashdata("success", "Your Rating Has Been Added !");
      redirect('Order/detail/'.$order_id);  

    }


  public function getAssignOrder()
  {
    $order_id = $this->input->post('order_id');
    $where = "order_id='".$order_id."' AND task_status='Open'";
    $task = $this->crud_model->readData('*','tasks',$where)->row();
    echo json_encode($task);

  }

  public function getTask()
  {
    $task_id = $this->input->post('task_id');
    $where = "task_id='".$task_id."' AND task_status='Open'";
    $task = $this->crud_model->readData('*','tasks',$where)->row();
    echo json_encode($task);

  }
}
