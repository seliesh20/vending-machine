<?php
namespace VendMachine\library;

/** All request attributes */
class Request{
    
    protected static $param;
    public function __construct()
    {
        self::$param = $_SERVER;        
        self::$param['params'] = [];
    }

    public static function type()
    {
        return strtolower(self::$param["REQUEST_METHOD"]);
    }

    public static function uri()
    {
        return strtolower(self::$param["REQUEST_URI"]);
    }

    public static function setParams($params)
    {
        self::$param['params'] = array_merge(self::$param['params'], $params);
    }

    public static function getParam($param_name)
    {
        if(isset(self::$param['params'][$param_name])){
            return self::$param['params'][$param_name];
        } 
        return null;
    }
    public static function getParams()
    {
        
        return self::$param['params'];
        
    }
    public static function getServerParams()
    {
        
        return self::$param;
        
    }

}