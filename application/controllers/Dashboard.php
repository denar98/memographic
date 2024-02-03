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
		error_reporting(0);

		$this->load->model('task_model');
		$this->load->model('dashboard_model');

	}

	public function index()
	{
		// if($this->session->userdata('role')!='Admin' && $this->session->userdata('role')!='Project Manager'){
		// 	$this->session->set_flashdata("error", "You Don't Have Access To This Page");
		// 	redirect('Dashboard/designer');
		// }
		$data['page'] = 'overview';
		$data['module'] = 'dashboard';
		$employee_id = $this->session->userdata('employee_id');
		$data['employee_ratings'] = $this->dashboard_model->getTaskRatingByEmployee()->result();
		$data['my_rating'] = $this->dashboard_model->getMyRating($employee_id)->row();
		$data['my_total_task'] = $this->dashboard_model->getMyTotalTask($employee_id)->num_rows();
		$data['my_overtime_hours'] = $this->dashboard_model->getMyOvertimeHours($employee_id)->row();
		
		$my_overtime_hours_weekday = $this->dashboard_model->getMyOvertimeHoursType($employee_id,'Weekday')->row();
		$my_overtime_hours_weekend = $this->dashboard_model->getMyOvertimeHoursType($employee_id,'Weekend')->row();
		if($my_overtime_hours_weekday != ''){
			$my_overtime_fee_weekday = ($my_overtime_hours_weekday->overtime_hours / 60) * 10000;
		}else{
			$my_overtime_fee_weekday = 0;
		}

		if($my_overtime_hours_weekend != ''){
			$my_overtime_fee_weekend = ($my_overtime_hours_weekend->overtime_hours / 60) * 10000;
		}else{
			$my_overtime_fee_weekend = 0;
		}
		$data['my_overtime_fee'] = $my_overtime_fee_weekday + $my_overtime_fee_weekend;

		$estimation_times = $this->dashboard_model->getMyWorkHours($employee_id)->result();

		$i=0;
		foreach($estimation_times as $estimation_time){
			$i++;
			$data['total_hour_'.$i] = substr($estimation_time->total_hour + ($estimation_time->total_minute/60),0,3);
			$data['day_name_'.$i] = substr($estimation_time->Day,0,3);
		}

		$data['my_overtime_fee'] = $my_overtime_fee_weekday + $my_overtime_fee_weekend;
		$this->load->view('template/head.html',$data);
		$this->load->view('dashboard/designer.html',$data);
		$this->load->view('template/foot.html');
	}
	public function designer()
	{
		$data['page'] = 'overview';
		$data['module'] = 'dashboard';
		$employee_id = $this->session->userdata('employee_id');
		$data['employee_ratings'] = $this->dashboard_model->getTaskRatingByEmployee()->result();
		$data['my_rating'] = $this->dashboard_model->getMyRating($employee_id)->row();
		$data['my_total_task'] = $this->dashboard_model->getMyTotalTask($employee_id)->num_rows();
		$data['my_overtime_hours'] = $this->dashboard_model->getMyOvertimeHours($employee_id)->row();
		
		$my_overtime_hours_weekday = $this->dashboard_model->getMyOvertimeHoursType($employee_id,'Weekday')->row();
		$my_overtime_hours_weekend = $this->dashboard_model->getMyOvertimeHoursType($employee_id,'Weekend')->row();
		if($my_overtime_hours_weekday != ''){
			$my_overtime_fee_weekday = ($my_overtime_hours_weekday->overtime_hours / 60) * 10000;
		}else{
			$my_overtime_fee_weekday = 0;
		}

		if($my_overtime_hours_weekend != ''){
			$my_overtime_fee_weekend = ($my_overtime_hours_weekend->overtime_hours / 60) * 10000;
		}else{
			$my_overtime_fee_weekend = 0;
		}
		$data['my_overtime_fee'] = $my_overtime_fee_weekday + $my_overtime_fee_weekend;

		$estimation_times = $this->dashboard_model->getMyWorkHours($employee_id)->result();

		$i=0;
		foreach($estimation_times as $estimation_time){
			$i++;
			$data['total_hour_'.$i] = substr($estimation_time->total_hour + ($estimation_time->total_minute/60),0,3);
			$data['day_name_'.$i] = substr($estimation_time->Day,0,3);
		}



		$this->load->view('template/head.html',$data);
		$this->load->view('dashboard/designer.html',$data);
		$this->load->view('template/foot.html');
	}

	public function getProgressToday()
    {
      $employee_id = $this->session->userdata('employee_id');
      
      $where="employee_id='".$employee_id."'";
      $progress_today_done = $this->dashboard_model->getMyProgressToday($employee_id,'Done')->row();
      $progress_today_progress = $this->dashboard_model->getMyProgressToday($employee_id,'Progress')->row();
	  $progress_today = array(
		'progress_today_done'=>$progress_today_done->total_progress, 
		'progress_today_progress'=>$progress_today_progress->total_progress, 
	);
      echo json_encode($progress_today);

    }
	public function getWorkHours()
    {
      $employee_id = $this->session->userdata('employee_id');
      
	  $estimation_times = $this->dashboard_model->getMyWorkHours($employee_id)->result();

	  $total_hours = array();
	  $day_names = array();

		foreach($estimation_times as $estimation_time){
			$total_hour = $estimation_time->total_hour + ($estimation_time->total_minute/60);
			$day_name = $estimation_time->Day;
			array_push($total_hours,$total_hour);
			array_push($day_names,$day_name);
		}

		// $country_name = ["italia", "japan", "congo", "uk"];
		// $country_capital = ["roma", "tokyo", "kinshasa", "london"];

		$work_hours = array_map(function ($totalHours, $dayName) {
			return [
				'total_hours' => $totalHours,
				'day_names' => $dayName,
			];
		}, $total_hours, $day_names);

		// $workdd(collect($work_hours));

		$progress_today = array(
		'progress_today_done'=>$progress_today_done->total_progress, 
		'progress_today_progress'=>$progress_today_progress->total_progress, 
		);
      echo json_encode($work_hours);

    }
}
