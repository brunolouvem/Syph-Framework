<?php

/**
 * Created by PhpStorm.
 * User: Bruno
 * Date: 12/08/2015
 * Time: 12:02
 */
include_once(realpath(dirname(__FILE__)).'/services/Interfaces/HttpInterface.php');
class AppKernel
{

    private $http;
    /**
     * Construtor do Kernel.
     */
    public function __construct()
    {

    }

    public function handleRequest(HttpInterface $http)
    {
        $this->http = $http;
    }

    public function getResponse()
    {
        $route = Router::execute($this->http->getRequest());

        if(!is_null($route['args'])){
            return $this->handleController($route['controller'],$route['action'],$route['args']);
        }else{
            return $this->handleController($route['controller'],$route['action']);
        }



    }

    public function handleController($controllerName,$actionName,$args = array())
    {
        if(file_exists(realpath(dirname(__FILE__)) . '/controller/' .$controllerName.'.php')){
            include_once(realpath(dirname(__FILE__)) . '/controller/' . $controllerName . '.php');
            $controller = $this->getController($controllerName);
            if(method_exists($controllerName,$actionName)) {
                if(count($args)>0){
                    return $this->runController($controller, $actionName, $args);
                }
                return $this->runController($controller, $actionName);
            }else{
                throw new Exception('N�o encontrado',404);
            }
        }else{
            throw new Exception('N�o encontrado',404);
        }

    }

    public function getController($controllerName)
    {
        return new $controllerName;
    }

    public function runController($controller,$action,$args = array())
    {
        if(count($args)>0){
            return $controller->$action($args);
        }
        return $controller->$action();
    }

}