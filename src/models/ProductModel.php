<?php 
namespace VendMachine\models;
use VendMachine\library\Models;
use VendMachine\library\Request;

class ProductModel extends Models
{           
    /**
     * Save Product
     */
    public function save($params)
    {
        if($this->db->query('insert into '
            .' products(product_name, amount_available, cost, user_id)'
            .' values("'.$params['product_name'].'", '.$params['amount_available']
                .', '.$params['cost'].', '.Request::getParam('user_id').')')){
                $query = $this->db->query("select LAST_INSERT_ID() id");
                $row = $query->fetch();
                return $row['id'];
            }
            return false;
    }
    /**
     * Update Product
     */
    public function update($save_array, $id)
    {
        $this->db->query('update products set '.implode(",", $save_array)." where id=".$id);
    }
    /**
     * Delete Product
     */
    public function delete($id)
    {
        $this->db->query('delete from products where id='.$id);
    }
    /**
     * Get product
     */
    public function getProduct($id){
        $query = $this->db->query("select * from products where id=".$id);
        $row = $query->fetch();
        return $row;
    }
}