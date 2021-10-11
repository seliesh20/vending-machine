<?php declare(strict_types=1);
include_once './vendor/autoload.php';
include_once './library/Request.php';
include_once './library/Response.php';
include_once 'library/Encrypt.php';
include_once './library/Models.php';
include_once './library/Route.php';
include_once './library/Main.php';
include_once './models/UserModel.php';
include_once './controllers/UserController.php';

use PHPUnit\Framework\TestCase;
use \VendMachine\contollers\UserController;
use VendMachine\library\Encrypt;
use VendMachine\library\Main;
use VendMachine\library\Request;
use VendMachine\models\UserModel;

final class UserTest extends TestCase{

    public function testCreateWithoutRequiredFields()
    {        
        $main = new Main();   
        $userController = new UserController();
        $this->expectOutputString('{"status":"failure","reason":"Required fields are missing!!"}');
        $userController->create();        
    }

    public function testCreateWithoutValidRole()
    {        
        $main = new Main();
        $main->request->setParams(['name'=>'test', 'email'=>'test@email.com', 'password'=>'test123', 'role'=>'SELLERS']);
        $userController = new UserController();
        $this->expectOutputString('{"status":"failure","reason":"Invalid Role!!"}');
        $userController->create();        
    }

    public function testViewWithoutUserId()
    {
        $main = new Main();
        $userController = new UserController();
        $this->expectOutputString('{"status":"failure","reason":"Invalid User!!"}');
        $userController->view();
    }

    public function testUpdateWithoutUserId()
    {
        $main = new Main();
        $userController = new UserController();        
        $this->expectOutputString('{"status":"failure","reason":"Invalid User!!"}');
        $userController->update();
    }

    public function testUpdateWithoutChange()
    {
        $main = new Main();
        $userModel = new UserModel();
        $user = $userModel->getUser(1);
        $userController = new UserController();
        $main->request->setParams(['user_id' => $user['id'], 'name'=> $user['name'], 'password' => Encrypt::unhashString($user['password'])]);
        $this->expectOutputString('{"status":"failure","reason":"No change in user email and password"}');
        $userController->update();
    }

    public function testDeleteWithoutUserId()
    {
        $main = new Main();
        $userController = new UserController();        
        $this->expectOutputString('{"status":"failure","reason":"Invalid User!!"}');
        $userController->delete();
    }
}