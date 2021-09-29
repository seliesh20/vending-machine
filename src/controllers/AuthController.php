<?php 
namespace VendMachine\contollers;
use \VendMachine\library\Request;
use \VendMachine\library\Response;
use \VendMachine\models\UserModel;

class AuthController {
 
    public function login()
    {
        $params = Request::getParams();
        $usermodel = new UserModel();
        $user = $usermodel->checkAuth($params['email'], $params['password']);        
        if(is_array($user) && count($user)){
            Response::json([
                'status' => 'success',
                'data' => ['api_key'=>$user['api_key']]
            ]);
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User!!'
            ]);
        }
        
    }

    public function logout()
    {
        
    }
}