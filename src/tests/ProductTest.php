<?php declare(strict_types=1);
include_once './vendor/autoload.php';
include_once './library/Request.php';
include_once './library/Response.php';
include_once './library/Models.php';
include_once './library/Route.php';
include_once './library/Main.php';
include_once './models/ProductModel.php';
include_once './controllers/ProductController.php';

use PHPUnit\Framework\TestCase;
use \VendMachine\contollers\ProductController;
use VendMachine\library\Main;
use VendMachine\library\Request;

final class ProductTest extends TestCase{

    public function testCreateWithoutUserRole()
    {
        $main = new Main();
        $productController = new ProductController();
        $this->expectOutputString('{"status":"failure","reason":"User is not a seller"}');
        $productController->create();
    }
    
    public function testCreateWithoutRequiredFields()
    {
        $main = new Main();
        $productController = new ProductController();
        $main = new Main();
        $main->request->setParams(['user_role'=>2]);
        $this->expectOutputString('{"status":"failure","reason":"Required fields are missing!!"}');
        $productController->create();
    }

    public function testCreateInvalidAmount()
    {
        $main = new Main();
        $productController = new ProductController();
        $main = new Main();
        $main->request->setParams(['user_role'=>2, 'product_name'=>'test', 'amount_available'=>500, 'cost'=>200]);
        $this->expectOutputString('{"status":"failure","reason":"Invalid Amount choose from 5,10,20,50 and 100 for product cost!!"}');
        $productController->create();
    }

    public function testViewWithoutProductId()
    {
        $main = new Main();
        $productController = new ProductController();        
        $this->expectOutputString('{"status":"failure","reason":"Invalid Product!!"}');
        $productController->view();
    }

    public function testUpdateWithoutProductId()
    {
        $main = new Main();
        $productController = new ProductController();        
        $this->expectOutputString('{"status":"failure","reason":"Invalid Product!!"}');
        $productController->update();
    }

    public function testUpdateWithoutUserRoleSeller()
    {
        $productController = new ProductController();
        $main = new Main();
        $main->request->setParams(['product_id'=>2, 'user_id' => 2, 'user_role' => 1 ]);
        $this->expectOutputString('{"status":"failure","reason":"User is not a seller"}');
        $productController->update();
    }
    public function testUpdateWithoutRequiredFields()
    {
        $productController = new ProductController();
        $main = new Main();
        $main->request->setParams(['product_id'=>1, 'user_id' => 1, 'user_role' => 2 ]);
        $this->expectOutputString('{"status":"failure","reason":"Required fields are missing!!"}');
        $productController->create();
    }
    public function testDeleteWithoutUserRole()
    {
        $productController = new ProductController();
        $main = new Main();
        $main->request->setParams(['product_id'=> 0]);
        $this->expectOutputString('{"status":"failure","reason":"User is not a seller"}');
        $productController->delete();
    }

    public function testDeleteWithoutCreatedUser()
    {
        $productController = new ProductController();
        $main = new Main();
        $main->request->setParams(['product_id'=> 2, 'user_id' => 2, 'user_role' => 2]);
        $this->expectOutputString('{"status":"failure","reason":"Product not created by this seller!!"}');
        $productController->delete();
    }
}