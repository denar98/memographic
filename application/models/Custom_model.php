<?php

class Custom_model extends CI_Model
{
    public function getDataProjects($limit, $start, $keyword, $project_status, $project_type,$client_id)
    {
  
      // $array = array('projects.project_name' => $keyword, 'clients.client_name' => $keyword, 'project_details.start_date' => $keyword, 'project_details.deadline' => $keyword);
  
      $where = "projects.project_name LIKE '%".$keyword."%' OR clients.client_name LIKE '%".$keyword."%' OR project_details.start_date LIKE '%".$keyword."%' OR project_details.deadline LIKE '%".$keyword."%'";
  
      $query = $this->db->select('projects.*,project_details.*,clients.*')
               ->from('projects')
               ->join("project_details","projects.project_id = project_details.project_id")
               ->join("clients","projects.client_id = clients.client_id");
               // ->order_by('order_id', 'DESC')
               if( $project_type != 'All' ){
                $this->db->where('project_details.project_detail_type',$project_type);
               }
               if($project_status != 'All' ){
                $this->db->where('project_details.project_detail_status',$project_status);
               }
               if($client_id != 'All' ){
                $this->db->where('projects.client_id',$client_id);
               }
               if($keyword != ''){
                $this->db->where($where);
                // $this->db->like('projects.project_name',$keyword);
                // $this->db->or_like('clients.client_name',$keyword);
                // $this->db->or_like('project_details.start_date',$keyword);
                // $this->db->or_like('project_details.deadline',$keyword);
              }
              $this->db->order_by('project_date', 'desc');
              $this->db->limit($limit, $start);
              $query = $this->db->get();
  
              return $query;
  
    }
    // public function getAssignOrder($order_id)
    // {
  
    //   // $array = array('projects.project_name' => $keyword, 'clients.client_name' => $keyword, 'project_details.start_date' => $keyword, 'project_details.deadline' => $keyword);
  
  
    //   $query = $this->db->select('tasks.*,orderd.*,clients.*')
    //            ->from('projects')
    //            ->join("project_details","projects.project_id = project_details.project_id")
    //            ->join("clients","projects.client_id = clients.client_id");
    //            // ->order_by('order_id', 'DESC')
    //            if( $project_type != 'All' ){
    //             $this->db->where('project_details.project_detail_type',$project_type);
    //            }
    //            if($project_status != 'All' ){
    //             $this->db->where('project_details.project_detail_status',$project_status);
    //            }
    //            if($client_id != 'All' ){
    //             $this->db->where('projects.client_id',$client_id);
    //            }
    //            if($keyword != ''){
    //             $this->db->where($where);
    //             // $this->db->like('projects.project_name',$keyword);
    //             // $this->db->or_like('clients.client_name',$keyword);
    //             // $this->db->or_like('project_details.start_date',$keyword);
    //             // $this->db->or_like('project_details.deadline',$keyword);
    //           }
    //           $this->db->order_by('project_date', 'desc');
    //           $this->db->limit($limit, $start);
    //           $query = $this->db->get();
  
    //           return $query;
  
    // }
}