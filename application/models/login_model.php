<?php

class Login_model extends CI_Model
{
    private $_table = "users";

    public function loginCheck($username,$password){

        $where = array('username' => $username, 'password' => $password);
        $user = $this->db->select('users.user_id,users.username,users.role,employees.employee_name,employees.employee_id')
                 ->from('users')
                 ->join("employees","users.employee_id = employees.employee_id")
                 ->where($where)
                 ->get()
                 ->row();

        if($user){
            $this->session->set_userdata(['user_logged' => $user]);
            $this->_updateLastLogin("'$user->user_id'");
            return $user;
        }

        // login gagal
		return false;
    }

    public function isNotLogin(){
        return $this->session->userdata('user_logged') === null;
    }

    private function _updateLastLogin($user_id){
        $sql = "UPDATE users SET last_login=now() WHERE user_id=$user_id";
        $this->db->query($sql);
    }

}
