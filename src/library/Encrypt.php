<?php 
namespace VendMachine\library;
class Encrypt {
    
    public static function hashString($string)
    {
        return base64_encode($string);
    }

    public static function unhashString($string)
    {
        return base64_decode($string);
    }
}
