<?php

class Dashboard_model extends CI_Model
{
  

  public function getTaskRatingByEmployee()
  {
    $this->db->select('ratings.*,employees.*,users.*,AVG(brief_reading) AS avg_brief_reading,AVG(quality) AS avg_quality,AVG(speed) AS avg_speed');
    $this->db->from('employees');
    $this->db->join('ratings', 'ratings.employee_id = employees.employee_id','left');
    $this->db->join('users', 'users.employee_id = employees.employee_id');
    $this->db->where("users.role = 'Graphic Designer' or users.role = 'Head Designer'");
    $this->db->where("MONTH(ratings.rating_date) = MONTH(CURRENT_DATE())");
    $this->db->order_by('AVG(brief_reading + quality + speed)/3','desc');
    $this->db->group_by('employees.employee_id');
    return $this->db->get();
  }
  public function getMyRating($employee_id)
  {
    $this->db->select('ratings.*,employees.*,users.*,AVG(brief_reading) AS avg_brief_reading,AVG(quality) AS avg_quality,AVG(speed) AS avg_speed');
    $this->db->from('employees');
    $this->db->join('ratings', 'ratings.employee_id = employees.employee_id','left');
    $this->db->join('users', 'users.employee_id = employees.employee_id');
    $this->db->where("employees.employee_id = '".$employee_id."'  and MONTH(ratings.rating_date) = MONTH(CURRENT_DATE())");
    $this->db->order_by('AVG(brief_reading + quality + speed)/3','desc');
    $this->db->group_by('employees.employee_id');
    return $this->db->get();
  }
  
  public function getMyTotalTask($employee_id)
  {
    $this->db->select('tasks.*,employees.*,users.*');
    $this->db->from('tasks');
    $this->db->join('employees', 'tasks.employee_id = employees.employee_id');
    $this->db->join('users', 'users.employee_id = employees.employee_id');
    $this->db->where("employees.employee_id = '".$employee_id."'  and tasks.task_status != 'Delivered'");
    return $this->db->get();
  }
  public function getMyOvertimeHours($employee_id)
  {
    $this->db->select('sum(overtime_time) as overtime_hours');
    $this->db->from('overtimes');
    $this->db->where("overtimes.employee_id = '".$employee_id."'  and MONTH(overtimes.date) = MONTH(CURRENT_DATE())");
    $this->db->group_by('overtimes.employee_id');
    return $this->db->get();
  }
  public function getMyOvertimeHoursType($employee_id,$overtime_type)
  {
    $this->db->select('sum(overtime_time) as overtime_hours');
    $this->db->from('overtimes');
    $this->db->where("overtimes.employee_id = '".$employee_id."' and overtimes.overtime_type='".$overtime_type."'  and MONTH(overtimes.date) = MONTH(CURRENT_DATE())");
    $this->db->group_by('overtimes.employee_id');
    return $this->db->get();
  }
  public function getMyProgressToday($employee_id,$progress_type)
  {
    if($progress_type=='Done'){
    }
    $this->db->select('count(task_id) as total_progress');
    $this->db->from('tasks');
    if($progress_type=='Done'){
      $this->db->where("tasks.employee_id = '".$employee_id."' and tasks.task_status != 'Open' and DATE(tasks.task_date) = DATE(CURRENT_DATE())");
    }
    else if($progress_type=='Progress'){
      $this->db->where("tasks.employee_id = '".$employee_id."' and tasks.task_status = 'Open' and DATE(tasks.task_date) = DATE(CURRENT_DATE())");
    }
    return $this->db->get();
  }
  public function getMyWorkHours($employee_id)
  {
    $this->db->select("tasks.*,SUM(task_estimation_hour) as total_hour, SUM(task_estimation_minute) as total_minute, DAYNAME(task_date) AS Day");
    $this->db->from('tasks');
    $this->db->where("tasks.employee_id ='".$employee_id."' AND DATE(tasks.task_date) >= CURDATE() - INTERVAL 7 DAY");
    $this->db->group_by('tasks.task_date');
    return $this->db->get();
  }
  
}
