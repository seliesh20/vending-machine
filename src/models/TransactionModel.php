<?php 
namespace VendMachine\models;
use VendMachine\library\Models;
use VendMachine\library\Request;

class TransactionModel extends Models{
    /**
     * Deposit
     */
    public function deposit($amount)
    {
        $this->db->query("insert into transactions(user_id, tran_type, amount) "
            ."values(".Request::getParam('user_id').", 'DEPOSIT', ".(float) $amount.")");
        $this->db->query("update users set deposit=deposit+".$amount
            ." where id=".Request::getParam('user_id'));
    }
    /**
     * Buy
     */
    public function buy($product_id, $buy, $change)
    {
        $this->db->query("insert into transactions(user_id, tran_type, amount, product_id) "
            ."values(".Request::getParam('user_id').", 'BUY', ".(float) $buy.", ".$product_id.")");
        if($change){
            $this->db->query("insert into transactions(user_id, tran_type, amount, product_id) "
                ."values(".Request::getParam('user_id').", 'CHANGE', ".(float) $change.", ".$product_id.")");            
        } 
        $this->db->query("update users set deposit=deposit-".($buy + $change)
            ." where id=".Request::getParam('user_id'));
        $this->db->query("update products set amount_available=amount_available-".$buy
            ." where id=".$product_id);
    }
    /**
     * Reset
     */
    public function reset($deposit)
    {
        $this->db->query("insert into transactions(user_id, tran_type, amount) "
            ."values(".Request::getParam('user_id').", 'CHANGE', ".$deposit." )");             
        $this->db->query("update users set deposit=0"
            ." where id=".Request::getParam('user_id'));
    }
}