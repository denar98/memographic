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

}
