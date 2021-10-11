<?php 
namespace VendMachine\models;
use VendMachine\library\Models;
use VendMachine\library\Encrypt;

class UserModel extends Models{
    
    /**
     * Save Product
     */
    public function save($params)
    {
        $apitoken = bin2hex(random_bytes(5)).rand(10000, 99999);
        $apikey = $apitoken.bin2hex(random_bytes(5));
        $params['deposit'] = (float) isset($params['deposit'])?$params['deposit']:0; 
        if($this->db->query('insert into '
            .' users(name, email, password, deposit, role_id, api_token, api_key)'
            .' values("'.$params['name'].'", "'.$params['email'].'", "'
                .Encrypt::hashString($params['password']).'",'.$params['deposit'].","
                .$params['role_id'].', "'.$apitoken.'", "'.$apikey.'")')){
                $query = $this->db->query("select LAST_INSERT_ID() id");
                $row = $query->fetch();
                return $row['id'];
            }
            return false;
    }
    public function getRoleByName($role){
        $query = $this->db->query("select * from roles where role_name='".$role."'");
        if($row = $query->fetch()){
            return $row;
        }
        return [];
    }
    public function getUser($user_id)
    {
        $query = $this->db->query("select * from users where id=".$user_id);
        $row = $query->fetch();
        return $row;
    }
    public function update($save_array, $user_id)
    {
        $this->db->query('update users set '.implode(",", $save_array)." where id=".$user_id);
    }
    /**
     * Delete User
     */
    public function delete($user_id)
    {
        $this->db->query('delete from users where id='.$user_id);
    }
    /**
     * Check Auth
     */
    public function checkAuth($email, $password)
    {
        $query = $this->db->query('select * from users where '
            .'email="'.$email.'" and password="'.Encrypt::hashString($password).'"');
        if($row=$query->fetch()){
            return $row;
        }
        return [];
    }
    /**
     * Get user by ApiKey
     */
    public function getUserByApiKey($apikey)
    {
        $query = $this->db->query('select * from users where '
            .'api_key="'.$apikey.'"');
        if($row=$query->fetch()){
            return $row;
        }
        return [];
    }
}
