<?php
namespace VendMachine\library;
use \VendMachine\library\Route;
use \VendMachine\library\Request;

class Main
{
    public $route;
    public $request;
    public function __construct()
    {
        $this->route = new Route();
        $this->request = new Request();
    }
}