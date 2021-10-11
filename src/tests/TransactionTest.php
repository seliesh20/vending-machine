<?php declare(strict_types=1);
include_once './vendor/autoload.php';
include_once './library/Request.php';
include_once './library/Response.php';
include_once 'library/Encrypt.php';
include_once './library/Models.php';
include_once './library/Route.php';
include_once './library/Main.php';
include_once './models/UserModel.php';
include_once './models/TransactionModel.php';
include_once './controllers/TransactionController.php';

use PHPUnit\Framework\TestCase;
use \VendMachine\contollers\TransactionController;
use VendMachine\library\Main;
use VendMachine\library\Request;
use VendMachine\models\TransactionModel;
use VendMachine\models\UserModel;

final class TransactionTest extends TestCase{

    public function testUserRole()
    {
        $main = new Main();
        $transactionController = new TransactionController();
        $this->expectOutputString('{"status":"failure","reason":"User is not having buyer role"}');
        $transactionController->checkUserBuyerRole();        
    }

    public function testUserId()
    {
        $main = new Main();
        $transactionController = new TransactionController();        
        $main->request->setParams(['user_role'=>3, 'user_id'=>0]);
        $this->expectOutputString('{"status":"failure","reason":"Invalid User!!"}');
        $transactionController->checkUserBuyerRole();        
    }

    public function testDeposit()
    {
        $main = new Main();
        $transactionController = new TransactionController();
        $userModel = new UserModel();
        $user = $userModel->getUser(2);        
        $main->request->setParams(['user_role'=>$user['role_id'], 'user_id'=>$user['id']]);
        $this->expectOutputString('{"status":"failure","reason":"Invalid Amount choose from 5,10,20,50 and 100 for deposit!!"}');
        $transactionController->deposit();        
    }

    public function testBuyInvalidProduct()
    {
        $main = new Main();
        $transactionController = new TransactionController();
        $userModel = new UserModel();
        $user = $userModel->getUser(2);        
        $main->request->setParams(['user_role'=>$user['role_id'], 'user_id'=>$user['id']]);
        $this->expectOutputString('{"status":"failure","reason":"Invalid Product!!"}');
        $transactionController->buy();        
    }
    public function testBuyInsufficentDeposit()
    {
        $main = new Main();
        $transactionController = new TransactionController();
        $userModel = new UserModel();
        $user = $userModel->getUser(2);        
        $main->request->setParams(['user_role'=>$user['role_id'], 'user_id'=>$user['id'], 'product_id'=>2]);
        $this->expectOutputString('{"status":"failure","reason":"Insufficient Deposit!!"}');
        $transactionController->buy();        
    }

    public function testBuyInsufficentProduct()
    {
        $main = new Main();
        $transactionController = new TransactionController();
        $userModel = new UserModel();
        $user = $userModel->getUser(3);        
        $main->request->setParams(['user_role'=>$user['role_id'], 'user_id'=>$user['id'], 'product_id'=>1, 'amount'=>50]);
        $this->expectOutputString('{"status":"failure","reason":"Insufficient Product!!"}');
        $transactionController->buy();        
    }

    public function testBuyInsufficentAmount()
    {
        $main = new Main();
        $transactionController = new TransactionController();
        $userModel = new UserModel();
        $user = $userModel->getUser(3);        
        $main->request->setParams(['user_role'=>$user['role_id'], 'user_id'=>$user['id'], 'product_id'=>2, 'amount'=>5]);
        $this->expectOutputString('{"status":"failure","reason":"Insufficient Amount!!"}');
        $transactionController->buy();        
    }

    


    
}