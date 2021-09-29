<?php
namespace VendMachine\contollers;
use \VendMachine\library\Request;
use \VendMachine\library\Response;
use \VendMachine\models\TransactionModel;
use \VendMachine\models\UserModel;
use \VendMachine\models\ProductModel;

class TransactionController{

    public function __construct()
    {
        if(!Request::getParam('user_role') == 3){
            Response::json([
                'status' => 'failure',
                'reason' => 'User is not a buyer'
            ]); exit;
        }
    }
    public function deposit()
    {        
        $params = Request::getParams();
        $transactionModel = new TransactionModel();
        if(isset($params['amount']) 
            && (float) $params['amount'] > 0
            && in_array((float) $params['amount'], [5,10,20,50,100])){
            $transactionModel->deposit($params['amount']);    
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid Amount choose from 5,10,20,50 and 100 for deposit!!'
            ]);
        }        
    }

    public function buy()
    {        
        $params = Request::getParams(); 
        $transactionModel = new TransactionModel();  
        $userModel = new UserModel();
        $user = $userModel->getUser(Request::getParam('user_id'));
        $productModel = new ProductModel();
        $product = $productModel->getProduct(Request::getParam('product_id'));
        if(is_array($user) && count($user) && is_array($product) && count($product)){
            if(!($user['deposit'] > 0 && $params['amount'] <= $user['deposit'])){
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Insufficient Deposit!!'
                ]); exit;
            }

            if(!($product['amount_available'] > 0)){
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Insufficient Product!!'
                ]); exit;
            }

            if(!((int) ($params['amount'] / $product['cost']) > 0)){
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Insufficient Amount!!'
                ]); exit;
            }
            $buy_amount = $params['amount'];
            $buy_change = 0;
            if($params['amount'] > $product['amount_available']){
                $buy_amount = $params['amount'];
                $buy_change = $params['amount'] - $product['amount_available'];
            }

            if($params['amount'] < $product['amount_available']){
                $qty = (int) ($params['amount'] / $product['cost']);
                $buy_amount = $qty * $product['cost'];
                $buy_change = $params['amount'] - $buy_amount;
            }
            $transactionModel->buy($params['product_id'], $buy_amount, $buy_change);
            Response::json([
                'status' => 'success',
                'data' => [
                        'buy'=>['product_id' => $product['id'], 'amount'=>$buy_amount],
                        'change'=>['amount'=>$buy_change]
                ]
            ]);
        } else {
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User/Product!!'
            ]);
        }
    }

    public function reset()
    {
        $params = Request::getParams(); 
        $transactionModel = new TransactionModel();  
        $userModel = new UserModel();
        $user = $userModel->getUser(Request::getParam('user_id'));
        if(is_array($user) && count($user)){
            $transactionModel->reset();
            $user = $userModel->getUser(Request::getParam('user_id'));
            Response::json([
                'status' => 'success',
                'data' => ['deposit'=>$user['deposit']]
            ]);
        } else{
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User!!'
            ]);
        }
    }
}
