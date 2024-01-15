<?php

class Order_model extends CI_Model
{
  public function getAllService()
  {
    $this->db->select('services.service_name,service_packages.service_package_id,service_packages.service_package_name');
    $this->db->from('services');
    $this->db->join('service_packages', 'services.service_id = service_packages.service_id');
    return $this->db->get();
  }
  public function getDataOrders($limit, $start, $keyword, $order_status, $service_id, $client_id)
  {

    // $array = array('projects.project_name' => $keyword, 'clients.client_name' => $keyword, 'project_details.start_date' => $keyword, 'project_details.deadline' => $keyword);

    $where = "orders.order_number LIKE '%".$keyword."%' OR clients.client_name LIKE '%".$keyword."%' OR services.service_name LIKE '%".$keyword."%' OR orders.order_status LIKE '%".$keyword."%'";

    $query = $this->db->select('orders.*,services.*,clients.*,service_packages.*')
             ->from('orders')
             ->join("clients","orders.client_id = clients.client_id")
             ->join("services","orders.service_id = services.service_id")
             ->join("service_packages","orders.service_package_id = service_packages.service_package_id");
            //  if($order_status != 'In Revision' && $order_status != 'Delivered' && $order_status != 'Completed' ){
            //   $this->db->where('orders.order_status','In Progress');
            //  }
             $this->db->where('orders.order_status',$order_status);
             // ->order_by('order_id', 'DESC')
            //  if( $order_status != 'All' ){
            //   $this->db->where('project_details.project_detail_type',$order_status);
            //  }
            //  if($project_status != 'All' ){
              // $this->db->where('project_details.project_detail_status',$project_status);
            //  }
            //  if($client_id != 'All' ){
            //   $this->db->where('projects.client_id',$client_id);
            //  }
            //  if($keyword != ''){
            //   $this->db->where($where);
              // $this->db->like('projects.project_name',$keyword);
              // $this->db->or_like('clients.client_name',$keyword);
              // $this->db->or_like('project_details.start_date',$keyword);
              // $this->db->or_like('project_details.deadline',$keyword);
            // }
            $this->db->order_by('order_date', 'desc');
            $this->db->limit($limit, $start);
            $query = $this->db->get();

            return $query;

  }

}
