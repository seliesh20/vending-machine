<?php
namespace VendMachine\contollers;
use \VendMachine\library\Request;
use \VendMachine\library\Encrypt;
use \VendMachine\library\Response;
use \VendMachine\models\UserModel;

class UserController{
    private $usermodel;
    public function __construct()
    {
        $this->usermodel = new UserModel();
    }
    public function create()
    {
        $params = Request::getParams();
        $param_array = ['name', 'email', 'password', 'role'];
        if(count(array_diff($param_array, array_keys($params)))){
            Response::json([
                'status' => 'failure',
                'reason' => 'Required fields are missing!!'
            ]);
        } else {
            //save user
            $role = $this->usermodel->getRoleByName($params['role']);
            if(is_array($role) && count($role)){
                $params['role_id'] = $role['id'];
                $id = $this->usermodel->save($params);
                Response::json([
                    'status' => 'success',
                    'data' => ['id'=>$id, 'name'=>$params['name']]
                ]);
            } else {
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Invalid Role!!'
                ]);
            }            
        }
    }

    public function view()
    {
        $params = array_merge(['user_id'=>0], Request::getParams());
        $user = $this->usermodel->getUser($params['user_id']);
        if(is_array($user) && count($user)){
            Response::json([
                'status' => 'success',
                'data' => ['user'=>$user]
            ]);
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User!!'
            ]);
        }
    }

    public function update()
    {
        $params = array_merge(['user_id'=>0], Request::getParams());
        $user = $this->usermodel->getUser($params['user_id']);
        if(is_array($user) && count($user)){
            $save_array = [];
            if(isset($params['name']) && ($params["name"] != $user["name"])){
                $save_array[] = 'name="'.$params['name'].'"';
            }
            if(isset($params['password']) && (Encrypt::hashString($params["password"]) != $user["password"])){
                $save_array[] = 'password='.$params['password'];
            }            
            if(is_array($save_array) && count($save_array)){
                $this->usermodel->update($save_array, $params['user_id']);
                $user = $this->usermodel->getUser($params['user_id']);
                Response::json([
                    'status' => 'success',
                    'data' => ['user'=>$user]
                ]);
            } else {
                Response::json([
                    'status' => 'failure',
                    'reason' => 'No change in user email and password'
                ]);
            }
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User!!'
            ]);
        }
    }

    /**
     * Delete Product
     */
    public function delete()
    {
        $params = array_merge(['user_id' => 0], Request::getParams());
        $users = $this->usermodel->getUser($params['user_id']);
        if(is_array($users) && count($users)){
            $this->usermodel->delete($params['user_id']);
            Response::json([
                'status' => 'success'                
            ]);
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User!!'
            ]);
        }
    }
}