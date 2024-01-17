<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Controller {

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
    $this->load->model('datatable_model');
    $this->load->model('crud_model');
    $this->load->model('task_model');
	}

	public function index()
	{
    	$data['page'] = 'task';
		$data['module'] = 'order';
		$data['employees'] = $this->db->get("employees")->result();

		$this->load->view('template/head.html',$data);
		$this->load->view('task/index.html',$data);
		$this->load->view('template/foot.html');
	}

	public function deliveryAction()
    {
      $task_id = $this->input->post('task_id');
      $date = Date('Y-m-d H:i:s');
  	  
	  $detail_task = $this->task_model->getDetailTask($task_id)->row();
      $order_id = $detail_task->order_id;
	  $task_delivery = $this->db->limit(1)->order_by('task_delivery_id','desc')->get('task_deliveries')->result();
	  $task_delivery_last_count = $this->db->where('order_id',$order_id)->limit(1)->order_by('task_delivery_count','desc')->get('task_deliveries')->result();
	  if($detail_task->task_type == 'Revision'){
		$task_delivery_final_count = $task_delivery_last_count + 1;
		$data_task_delivery = array(
			'task_id'=>$task_id,
			'order_id'=>$order_id,
			'task_delivery_date' => Date('Y-m-d H:i:s'),
			'task_delivery_type' => $detail_task->task_type,
			'task_delivery_count' => $task_delivery_final_count
		  );
	   }
	   else{
		$data_task_delivery = array(
			'task_id'=>$task_id,
			'order_id'=>$order_id,
			'task_delivery_date' => Date('Y-m-d H:i:s'),
			'task_delivery_type' => $detail_task->task_type,
		  );

	   }

      $data_task = array(
        'task_finish' => Date('Y-m-d H:i:s'),
        'task_status' => 'Done',
      );
      $data_order = array(
        'order_status' => 'Done',
      );
	  $where_task = 'task_id='.$task_id;
	  $where_order = 'order_id='.$detail_task->order_id;

      $preview_files_attachments = [];
      $preview_files_count = count($_FILES['preview_files']['name']);
	  
      for($i=0;$i<$preview_files_count;$i++){
    
        if(!empty($_FILES['preview_files']['name'][$i])){
    

          // $date = str_replace( ':', '', $date);
          if (!is_dir('assets/attachments/'.$detail_task->service_name.'/'.$detail_task->client_name.'/'.$detail_task->order_number.'/preview')) {
              mkdir('./assets/attachments/'.$detail_task->service_name.'/'.$detail_task->client_name.'/'.$detail_task->order_number.'/preview', 0777, TRUE);
          }
		  $path = './assets/attachments/'.$detail_task->service_name.'/'.$detail_task->client_name.'/'.$detail_task->order_number.'/preview';
          $_FILES['file']['name'] = $_FILES['preview_files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['preview_files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['preview_files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['preview_files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['preview_files']['size'][$i];
  
          $config['upload_path'] = $path; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
		  $config['overwrite'] = TRUE;
          $config['file_name'] = $_FILES['preview_files']['name'][$i];
          
          $this->upload->initialize($config);
  
          if($this->upload->do_upload('file')){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
   
            $preview_files_attachments['totalFiles'][] = $filename;
  
            $preview_files_attachments_data = array(
              'task_id' => $task_id,
              'task_delivery_id' => $task_delivery->task_delivery_id,
              'task_delivery_attachment_type' => 'Preview',
              'task_delivery_attachment_name' => $filename,
            );
            $add_preview_files_attachments = $this->crud_model->createData('task_delivery_attachments',$preview_files_attachments_data);
        
          }
        }
  
      }

      $source_files_attachments = [];
      $source_files_count = count($_FILES['source_files']['name']);
	  
      for($i=0;$i<$source_files_count;$i++){
    
        if(!empty($_FILES['source_files']['name'][$i])){
    

          // $date = str_replace( ':', '', $date);
          if (!is_dir('assets/attachments/'.$detail_task->service_name.'/'.$detail_task->client_name.'/'.$detail_task->order_number.'/source')) {
			mkdir('./assets/attachments/'.$detail_task->service_name.'/'.$detail_task->client_name.'/'.$detail_task->order_number.'/source', 0777, TRUE);
  		  }
		  $path = './assets/attachments/'.$detail_task->service_name.'/'.$detail_task->client_name.'/'.$detail_task->order_number.'/source';
		  $_FILES['file']['name'] = $_FILES['source_files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['source_files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['source_files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['source_files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['source_files']['size'][$i];
  
          $config['upload_path'] = $path; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx|doc|xlsx|ai|psd|zip|rar';
		  $config['overwrite'] = TRUE;
          $config['file_name'] = $_FILES['source_files']['name'][$i];
          
          $this->upload->initialize($config);
  
          if($this->upload->do_upload('file')){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
   
            $source_files_attachments['totalFiles'][] = $filename;
  
            $source_files_attachments_data = array(
              'task_id' => $task_id,
              'task_delivery_id' => $task_delivery->task_delivery_id,
              'task_delivery_attachment_type' => 'Source Files',
              'task_delivery_attachment_name' => $filename,
            );
            $add_source_files_attachments = $this->crud_model->createData('task_delivery_attachments',$source_files_attachments_data);
        
          }
        }
  
      }

	  
      $add_task_delivery = $this->crud_model->createData('task_deliveries',$data_task_delivery);
      if($add_task_delivery){
		$update_task = $this->crud_model->updateData('tasks',$data_task,$where_task);
		if($update_task){
		  $update_order = $this->crud_model->updateData('orders',$data_order,$where_order);
		  if($update_task){
			  $this->session->set_flashdata("success", "Your Task Has Been Delivered !");
			  redirect('Task/');	
		  }
		}		
	  }	


    }

}
