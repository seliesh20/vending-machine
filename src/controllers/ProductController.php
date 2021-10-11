<?php
namespace VendMachine\contollers;
use \VendMachine\library\Request;
use VendMachine\library\Response;
use \VendMachine\models\ProductModel;

class ProductController{

    private $productModel;
    public function __construct()
    {
        $this->productModel = new ProductModel();

    }
    public function create()
    {
        if (Request::getParam('user_role') == 2) {
            $params = Request::getParams();
            $param_array = ['product_name', 'amount_available', 'cost'];
            if (count(array_diff($param_array, array_keys($params)))) {
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Required fields are missing!!'
                ]);
            } else {
                if (in_array((float) $params['cost'], [5, 10, 20, 50, 100])) {
                    //save product
                    $id = $this->productModel->save($params);
                    Response::json([
                        'status' => 'success',
                        'data' => ['id' => $id, 'product_name' => $params['product_name']]
                    ]);
                } else {
                    Response::json([
                        'status' => 'failure',
                        'reason' => 'Invalid Amount choose from 5,10,20,50 and 100 for product cost!!'
                    ]);
                }
            }
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'User is not a seller'
            ]);
        }
               
    }

    public function view()
    {
        $params = array_merge(['product_id'=> 0], Request::getParams());
        $product = $this->productModel->getProduct($params['product_id']);
        if(is_array($product) && count($product)){
            Response::json([
                'status' => 'success',
                'data' => ['product'=>$product]
            ]);
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid Product!!'
            ]);
        }        
    }
    /**
     * Update Product
     */
    public function update()
    {
        $params = array_merge(['user_id' => 0, 'role_id' => 0, 'product_id' => 0] , Request::getParams());
        $product = $this->productModel->getProduct($params['product_id']);
        if(is_array($product) && count($product)) {
            if(!($product['user_id'] == Request::getParam('user_id')
                && Request::getParam('role_id') == 2)){
                    Response::json([
                        'status' => 'failure',
                        'reason' => 'User is not a seller'
                    ]); 
            } else {
                $save_array = [];
                if(isset($params['product_name']) && ($params["product_name"] != $product["product_name"])){
                    $save_array[] = 'product_name="'.$params['product_name'].'"';
                }
                if(isset($params['amount_available']) && ($params["amount_available"] != $product["amount_available"])){
                    $save_array[] = 'amount_available='.$params['amount_available'];
                }
                if(isset($params['cost']) && ($params["cost"] != $product["cost"])){
                    $save_array[] = 'cost='.$params['cost'];
                }
                if(is_array($save_array) && count($save_array)){
                    $this->productModel->update($save_array, $params['product_id']);
                    $product = $this->productModel->getProduct($params['product_id']);
                    Response::json([
                        'status' => 'success',
                        'data' => ['product'=>$product]
                    ]);
                } else {
                    Response::json([
                        'status' => 'failure',
                        'reason' => 'Invalid Fields!!'
                    ]);
                }
            }
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid Product!!'
            ]);
        }
    }
    /**
     * Delete Product
     */
    public function delete()
    {
        if (!Request::getParam('user_role') == 2) {
            Response::json([
                'status' => 'failure',
                'reason' => 'User is not a seller'
            ]); 
        } else {
            $params = Request::getParams();
            $product = $this->productModel->getProduct($params['product_id']);
            if(is_array($product) && count($product) && $product['user_id'] == Request::getParam('user_id')){
                $this->productModel->delete($params['product_id']);
                Response::json([
                    'status' => 'success'                
                ]);
            } else {
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Product not created by this seller!!'
                ]);
            }
        }        
    }
}