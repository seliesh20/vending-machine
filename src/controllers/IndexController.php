<?php
namespace VendMachine\contollers;
use \VendMachine\library\Request;

class IndexController{

    public function index()
    {
        echo '<pre>'; print_r(Request::getParams());
        echo "Index Controller";
    }
}