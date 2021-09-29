<?php
namespace VendMachine\library;

use PDO;

class Models{
    public $db;

    public function __construct()
    {        
        try{                        
            $this->db = new \PDO('mysql:host=mysql:3306;dbname='.getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
            //echo '<pre>'; print_r($this->db);
        } catch(\Exception $e){
            echo '<pre>'; print_r($e);
        } catch(\Error $e){
            //echo '<pre>'; print_r($e);
        }        
    }

}
 