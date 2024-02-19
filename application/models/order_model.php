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


    $where = "orders.order_number LIKE '%".$keyword."%' OR clients.client_name LIKE '%".$keyword."%' OR services.service_name LIKE '%".$keyword."%'";
    $query = $this->db->select('orders.*,services.*,clients.*,service_packages.*')
             ->from('orders')
             ->join("clients","orders.client_id = clients.client_id")
             ->join("services","orders.service_id = services.service_id")
             ->join("service_packages","orders.service_package_id = service_packages.service_package_id");
            $this->db->where($where);
            $this->db->where('orders.order_status',$order_status);
            $this->db->order_by('order_deadline', 'asc');
            $this->db->limit($limit, $start);
            $query = $this->db->get();

            return $query;

  }
  
  public function getDataOrdersAll()
  {


    $query = $this->db->select('orders.*,services.*,clients.*,service_packages.*')
             ->from('orders')
             ->join("clients","orders.client_id = clients.client_id")
             ->join("services","orders.service_id = services.service_id")
             ->join("service_packages","orders.service_package_id = service_packages.service_package_id");
            $this->db->order_by('order_deadline', 'asc');
            $query = $this->db->get();

            return $query;

  }
  
  public function getDetailDataOrder($order_id)
  {

    // $array = array('projects.project_name' => $keyword, 'clients.client_name' => $keyword, 'project_details.start_date' => $keyword, 'project_details.deadline' => $keyword);


    $query = $this->db->select('orders.*,services.*,clients.*,service_packages.*')
             ->from('orders')
             ->join("clients","orders.client_id = clients.client_id")
             ->join("services","orders.service_id = services.service_id")
             ->join("service_packages","orders.service_package_id = service_packages.service_package_id");
             $this->db->where('orders.order_id',$order_id);
              $query = $this->db->get();

            return $query;

  }
  public function getAttachmentOrder($order_id)
  {

    // $array = array('projects.project_name' => $keyword, 'clients.client_name' => $keyword, 'project_details.start_date' => $keyword, 'project_details.deadline' => $keyword);


    $query = $this->db->select('orders.*,order_attachments.*')
             ->from('orders')
             ->join("order_attachments","orders.order_id = order_attachments.order_id");
             $this->db->where('orders.order_id',$order_id);
            $query = $this->db->get();

            return $query;

  }

}
