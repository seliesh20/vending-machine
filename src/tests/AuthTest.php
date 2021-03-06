<?php declare(strict_types=1);
include_once './vendor/autoload.php';
include_once './library/Request.php';
include_once './library/Response.php';
include_once 'library/Encrypt.php';
include_once './library/Models.php';
include_once './library/Route.php';
include_once './library/Main.php';
include_once './models/UserModel.php';
include_once './controllers/AuthController.php';

use PHPUnit\Framework\TestCase;
use \VendMachine\contollers\AuthController;
use VendMachine\library\Main;
use VendMachine\library\Encrypt;
use VendMachine\models\UserModel;

final class AuthTest extends TestCase{
    public function testLoginSuccess()
    {
        $authController = new AuthController();
        //with setting parameters
        $main = new Main();
        $userModel = new UserModel();
        $user = $userModel->getUser(1);
        $main->request->setParams(['email'=>$user['email'], 'password'=>Encrypt::unhashString($user['password'])]);
        $this->expectOutputString('{"status":"success","data":{"api_key":"'.$user["api_key"].'"}}');
        $result = $authController->login();        
    }

    public function testLoginFailure()
    {
        $authController = new AuthController();
        //without setting parameters
        $main = new Main();
        $main->request->setParams([]);
        $this->expectOutputString('{"status":"failure","reason":"Invalid User!!"}');
        $result = $authController->login();

    }
}