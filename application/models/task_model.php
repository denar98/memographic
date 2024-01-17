<?php

class Task_model extends CI_Model
{
  public function getTaskByEmployee($employee_id)
  {
    $this->db->select('tasks.*,orders.*,employees.*,services.*,service_packages.*,clients.*,tags.*');
    $this->db->from('tasks');
    $this->db->join('tags', 'tags.tag_id = tasks.tag_id');
    $this->db->join('orders', 'orders.order_id = tasks.order_id');
    $this->db->join('employees', 'employees.employee_id = tasks.employee_id');
    $this->db->join('clients', 'orders.client_id = clients.client_id');
    $this->db->join('services', 'orders.service_id = services.service_id');
    $this->db->join('service_packages', 'orders.service_package_id = service_packages.service_package_id');
    $this->db->where('tasks.employee_id',$employee_id);
    // $this->db->where('task_status','Open');
    return $this->db->get();
  }
  
  public function getDetailTask($task_id)
  {
    $this->db->select('tasks.*,orders.*,employees.*,services.*,service_packages.*,clients.*,tags.*');
    $this->db->from('tasks');
    $this->db->join('tags', 'tags.tag_id = tasks.tag_id');
    $this->db->join('orders', 'orders.order_id = tasks.order_id');
    $this->db->join('employees', 'employees.employee_id = tasks.employee_id');
    $this->db->join('clients', 'orders.client_id = clients.client_id');
    $this->db->join('services', 'orders.service_id = services.service_id');
    $this->db->join('service_packages', 'orders.service_package_id = service_packages.service_package_id');
    $this->db->where('tasks.task_id',$task_id);
    $this->db->where('task_status','Open');
    return $this->db->get();
  }
  public function getTaskDeliveryByOrderId($order_id)
  {
    $this->db->select('task_deliveries.*,tasks.*,orders.*,employees.*,services.*,service_packages.*,clients.*,tags.*');
    $this->db->from('task_deliveries');
    $this->db->join('tasks', 'task_deliveries.task_id = tasks.task_id');
    $this->db->join('tags', 'tags.tag_id = tasks.tag_id');
    $this->db->join('orders', 'orders.order_id = tasks.order_id');
    $this->db->join('employees', 'employees.employee_id = tasks.employee_id');
    $this->db->join('clients', 'orders.client_id = clients.client_id');
    $this->db->join('services', 'orders.service_id = services.service_id');
    $this->db->join('service_packages', 'orders.service_package_id = service_packages.service_package_id');
    $this->db->where('task_deliveries.order_id',$order_id);
    $this->db->order_by('task_delivery_id','desc');
    // $this->db->where('task_status','Open');
    return $this->db->get();
  }
  public function getTaskDeliveryAttachment($task_delivery_id)
  {
    $this->db->select('task_delivery_attachments.*');
    $this->db->from('task_delivery_attachments');
    $this->db->where('task_delivery_attachments.task_delivery_id',$task_delivery_id);
    return $this->db->get();
  }
  
}
