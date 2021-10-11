<?php
namespace VendMachine\contollers;
use \VendMachine\library\Request;
use \VendMachine\library\Response;
use \VendMachine\models\TransactionModel;
use \VendMachine\models\UserModel;
use \VendMachine\models\ProductModel;

class TransactionController{
    
    public function checkUserBuyerRole()
    {   
        $return = true;
        if(!Request::getParam('user_role') == 3){
            Response::json([
                'status' => 'failure',
                'reason' => 'User is not having buyer role'
            ]);  
            $return = false;
        }
        if($return && is_null(Request::getParam('user_id'))){             
            Response::json([
                'status' => 'failure',
                'reason' => 'Invalid User!!'
            ]);  
            $return = false;
        } else if($return){
            $userModel = new UserModel();
            $user = $userModel->getUser(Request::getParam('user_id'));
            if(!(is_array($user) && count($user))){
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Invalid User!!'
                ]);  
                $return = false;
            }            
        }
        
        return $return;
    }
    public function deposit()
    {              
        if($this->checkUserBuyerRole()){
            $params = array_merge(['amount' => 0], Request::getParams());
            $transactionModel = new TransactionModel();            
            if(isset($params['amount']) 
                && (float) $params['amount'] > 0
                && in_array((float) $params['amount'], [5,10,20,50,100])){
                $transactionModel->deposit($params['amount']);    
                //Get User Details
                $userModel = new UserModel();
                $user = $userModel->getUser($params['user_id']);
                
                Response::json([
                    'status' => 'success',
                    'data' => ['deposit'=>$user['deposit']]
                ]);
            } else {
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Invalid Amount choose from 5,10,20,50 and 100 for deposit!!'
                ]);
            }   
        }     
    }

    public function buy()
    {     
        if($this->checkUserBuyerRole()){   
            $params = array_merge(['product_id'=> 0, 'amount' =>0], Request::getParams()); 
            $transactionModel = new TransactionModel();  
            $userModel = new UserModel();
            $user = $userModel->getUser(Request::getParam('user_id'));
            $productModel = new ProductModel();
            $product = $productModel->getProduct($params['product_id']);
            if(is_array($product) && count($product)){
                if(!($user['deposit'] > 0 && $params['amount'] <= $user['deposit'])){
                    Response::json([
                        'status' => 'failure',
                        'reason' => 'Insufficient Deposit!!'
                    ]);
                } else if(!($product['amount_available'] > 0)){
                    Response::json([
                        'status' => 'failure',
                        'reason' => 'Insufficient Product!!'
                    ]);
                } else if(!((int) ($params['amount'] / $product['cost']) > 0)){
                    Response::json([
                        'status' => 'failure',
                        'reason' => 'Insufficient Amount!!'
                    ]); 
                } else {
                    $buy_amount = $params['amount'];
                    $buy_change = $user['deposit'] - $params['amount'];
                    if($params['amount'] > $product['amount_available']){                        
                        $buy_change += $params['amount'] - $product['amount_available'];
                    }                    
                    $qty = (int) ($params['amount'] / $product['cost']);
                    $buy_amount = $qty * $product['cost'];
                    $buy_change += $params['amount'] - $buy_amount;
                    
                    $transactionModel->buy($params['product_id'], $buy_amount, $buy_change);
                    Response::json([
                        'status' => 'success',
                        'data' => [
                                'buy'=>['product_id' => $product['id'], 'amount'=>$buy_amount],
                                'change'=>['amount'=>$buy_change]
                        ]
                    ]);
                }
            } else {
                Response::json([
                    'status' => 'failure',
                    'reason' => 'Invalid Product!!'
                ]);
            }
        }
    }

    public function reset()
    {
        if($this->checkUserBuyerRole()){               
            $transactionModel = new TransactionModel();  
            $userModel = new UserModel();
            $user = $userModel->getUser(Request::getParam('user_id'));
            $change = $user['deposit'];
            //Reset Deposit and give change
            $transactionModel->reset($user['deposit']);
            $user = $userModel->getUser(Request::getParam('user_id'));
            Response::json([
                'status' => 'success',
                'data' => ['deposit' => $user['deposit'], 'change'=>$change]
            ]);       
        }
    }
}
