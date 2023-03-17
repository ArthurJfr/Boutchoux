<?php

namespace Core\Kernel;

/**
 * @author Legiemble Quentin
 */
class Router
{
    private $routes = [];
    private $pages = [];

    public function __construct($routes) {
        $this->routes = $routes;
        $this->getAllPages();
        $urls = $this->getUrls();
        $getpage = $this->getPage($urls);
        if(in_array($getpage,$this->pages)) {
            $goodroutes  = $this->getGoodRoutes($getpage);
            $goodroute = $this->getGoodRoute($goodroutes, $getpage);
            $controller = $this->getController($goodroute);
            $method = $this->getMethod($goodroute);
            $this->callGoodController($controller,$method,$goodroute,$urls);
        } else {
            $this->redirectTo404();
        }
    }

    private function getAllPages() : void
    {
        foreach($this->routes as $route) {
            $page = str_replace(':', '', $route[1]);
            if (str_contains($page, '/')) {
                $segments = explode('/', $page);
                $page = $segments[0];
            }
            $this->pages[] = $page;
        }
    }

    private function getUrls() : array
    {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $parse = parse_url($actual_link);
        return explode('/',trim($parse['path'],'/'));
    }

    private function getPage($urls)
    {
        return empty($urls[0]) ? 'home' : $urls[0];
    }

    private function getGoodRoutes($getpage)
    {
        $keys = array_keys($this->pages, $getpage, true);
        $goodRoutes = array();
        foreach ($keys as $key) {
            $goodRoutes[] = $this->routes[$key];
        }
        return $goodRoutes;
    }

    private function getGoodRoute($routes, $getpage)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $parse = parse_url($actual_link);
        $currentUrlParams = explode('/', $parse['path']);
        if (end($currentUrlParams) === '' && $getpage !== 'home') {
            array_pop($currentUrlParams);
        }
        foreach ($routes as $route) {
            $urlParams = explode('/', $route[1]);
            if (in_array($requestMethod, $route[0]) && count($urlParams) === count($currentUrlParams) - 1) {
                return $route;
            }
        }
        $this->redirectTo404();
    }

    private function getController($goodroute)
    {
        return '\\App\\Controller\\' . ucfirst($goodroute[2]) . 'Controller';
    }

    private function getMethod($goodroute)
    {
        return $goodroute[3];
    }

    private function callGoodController($controller, $method, $goodroute, $urls)
    {
        if (class_exists($controller)) {
            $instance = new $controller();
            if (method_exists($controller, $method)) {
                if (!empty($goodroute[4])) {
                    $arguments = $this->getArguments($goodroute, $urls);
                    $instance->$method(...$arguments);
                } else {
                    $instance->$method();
                }
            }
        }
    }

    private function getArguments($goodroute, $urls)
    {
        $arguments = array();
        $urlParams = explode('/', $goodroute[1]);
        $urlValues = array_slice($urls, 1);
        foreach ($urlParams as $index => $param) {
            if (str_contains($param, ':')) {
                if (isset($urlValues[$index - 1])) {
                    $arguments[] = $urlValues[$index - 1];
                } else {
                    $arguments[] = null;
                }
            }
        }
        return $arguments;
    }


    private function redirectTo404(): void
    {
        $controller = new \App\Controller\DefaultController();
        $controller->Page404();
    }
}
