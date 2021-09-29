<?php 
namespace VendMachine\library;
use \VendMachine\library\Route;
use \VendMachine\library\Request;
use \VendMachine\library\Response;
use \VendMachine\models\UserModel;
class Bootstrap{
 
    public $main;
    public function __construct()
    {
        $this->loadEnv();
        $this->main = new \VendMachine\library\Main();
        $this->loadModels();
        $this->loadRoutes();
        $this->route();    
    }
    public function loadModels()    
    {
        //Models Library Class
        include_once 'library/Models.php';
        //Models Class
        $path = getcwd().'/models';
        $files = scandir($path);
        $files = array_diff(scandir($path), array('.', '..'));
        foreach($files as $file){
            if(strpos($file, '.php')){
                include_once 'models/'.$file;
            }
        }
    }
    private function loadEnv(){        
        if(file_exists(getcwd().'/config/.env')){
            $lines = file(getcwd().'/config/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {

                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
    
                list($name, $value) = explode('=', $line, 2);                
                $name = trim($name);
                $value = trim($value);                
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));                    
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
    private function loadRoutes()
    {
        $routes = $this->main->route;
        include_once 'config/routes.php';        
    }
    private function route(){        
        $routes_list = $this->main->route->getRoutes($this->main->request::type());
        $uri = $this->main->request::uri();

        foreach($routes_list as $route){
            $path = preg_replace('/(\$)\w+/i', '([A-Za-z0-9_-]+)', $route['path']);
            $path = str_replace('/', '\\/', $path);
            if(preg_match('/^'.$path.'?$/', $uri)){
                //set parameters
                $params = explode('/$', $route['path']);                
                $loc_path = $params[0];
                unset($params[0]);                
                if(is_array($params) && count($params)){
                    $uris = explode('/', str_replace($loc_path, '', $uri));
                    unset($uris[0]);
                    $this->main->request->setParams(array_combine($params, $uris));
                }    
                if(is_array($_POST) && count($_POST)){
                    $this->main->request->setParams($_POST);
                }                
                if(isset($route['requires']) && $route['requires'] == "APIKEY"){
                    //API KEY CHECK
                    $server_params = Request::getServerParams();
                    if(isset($server_params['HTTP_APIKEY'])){
                        $apikey = $server_params['HTTP_APIKEY'];
                        $usermodel = new UserModel();
                        $user = $usermodel->getUserByApiKey($apikey);
                        if(is_array($user) && count($user)){
                            Request::setParams(['user_id' => $user['id']]);
                            Request::setParams(['role_id' => $user['role_id']]);
                        } else {
                            Response::json([
                                'status'=>'failure',
                                'reason'=>'Invalid User!!'
                            ]); exit;    
                        }
                    } else {
                        Response::json([
                            'status'=>'failure',
                            'reason'=>'APIKEY is missing!!'
                        ]); exit;
                    }
                }
                if(is_callable($route['callback'])){
                   $route['callback']();
                } else {                    
                   //load controller                   
                   include_once('controllers/'.$route['callback']["controller"].'.php');
                   $controller_class = '\VendMachine\contollers\\'.$route['callback']["controller"];                                   
                
                   $controller = new $controller_class(); 
                   $controller->{$route['callback']['action']}();                   
                }
            } else {
                http_response_code(404);                
            }       
        }
    }
}