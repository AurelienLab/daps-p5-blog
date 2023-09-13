<?php

namespace App\Core\Router;

use Symfony\Component\HttpFoundation\Request;

class Router
{
    private array $routingFiles;
    private array $routeCollection;
    private static $_instance;

    public function __construct(private string $routesFolder)
    {
        $directory = scandir($routesFolder);
        $this->routingFiles = array_diff($directory, array('..', '.'));
    }

    public static function getInstance(string $routeFolder = '')
    {
        if (is_null(self::$_instance)) {
            if (!empty($routeFolder)) {
                self::$_instance = new Router($routeFolder);
                self::$_instance->collectRoutes();
            } else {
                throw new \Exception('Unable to create a new Router instance without folder path');
            }
        }

        return self::$_instance;
    }

    private function collectRoutes()
    {
        foreach ($this->routingFiles as $file) {
            require_once($this->routesFolder . '/' . $file);
        }
    }

    public function add(Route $route)
    {
        $this->routeCollection[$route->getMethod()][] = $route;
    }

    public function handle()
    {
        $request = Request::createFromGlobals();
        $requestedUri = $this->removeLastSlash($request->getPathInfo());
        $collection = $this->routeCollection[$request->getMethod()];
        foreach ($collection as $route) {
            /** @var Route $route */
            if ($requestedUri === $route->getUri()) {
                $class = $route->getFunction()['class'];
                $controller = new $class();

                $method = $route->getFunction()['method'];
                return $controller->$method();
            }
        }
    }

    private function removeLastSlash($uri)
    {
        $newUri = $uri;
        if ($newUri != '/' && str_ends_with($newUri, '/')) {
            $newUri = substr_replace($newUri, '', -1);
        }

        return $newUri;
    }
}