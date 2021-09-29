<?php 
namespace VendMachine\library;

class Route{
    private array $routes = [
        'get'=> [

        ],
        'post' => [

        ],
        'put' => [

        ],
        'delete' => [

        ]
    ];
    private $current_type;

    public function get($path, $callback){
        return $this->setpath('get', $path, $callback);
    }
    public function post($path, $callback){
        return $this->setpath('post', $path, $callback);
    }
    public function put($path, $callback){
        return $this->setpath('put', $path, $callback);
    }
    public function delete($path, $callback){
        return $this->setpath('delete', $path, $callback);
    }
    function setpath($type, $path, $callback){
        $this->current_type = $type;
        $this->routes[$type][] = [
            'path' => $path,
            'callback' => $callback
        ];
        if(!is_callable($callback)){
            $callback = explode('@', $callback);            
            $this->routes[$type][count($this->routes[$type])-1]['callback'] = [
                'controller' => $callback[0],
                'action' => $callback[1]
            ];                
        }
        return $this;        
    }
    public function requires($library)
    {    
        $this->routes[$this->current_type][count($this->routes[$this->current_type])-1]
            ['requires']=$library;         
    }
    public function getRoutes($type){    
        return $this->routes[$type];
    }
}