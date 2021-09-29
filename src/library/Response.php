<?php
namespace VendMachine\library;

/** All request attributes */
class Response{
        
    public static function json($data){
        echo json_encode($data);
    }
}