<?php

namespace App\Core\Router;

use Spatie\FlareClient\Http\Exceptions\NotFound;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    private array $routingFiles;
    private array $routeCollection;
    private static $_instance;

    public function __construct(private readonly string $routesFolder)
    {
        $directory = scandir($routesFolder);
        $this->routingFiles = array_diff($directory, array('..', '.'));
    }

    public static function getInstance(string $routeFolder = ''): Router
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

    private function collectRoutes(): void
    {
        foreach ($this->routingFiles as $file) {
            require_once($this->routesFolder . '/' . $file);
        }
    }

    public function add(Route $route): void
    {
        $this->routeCollection[$route->getMethod()][] = $route;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $request = Request::createFromGlobals();
        $requestedUri = $this->removeLastSlash($request->getPathInfo());
        $collection = $this->routeCollection[$request->getMethod()];
        foreach ($collection as $route) {
            /** @var Route $route */

            if ($route->matchUri($requestedUri)) {
                $class = $route->getFunction()['class'];
                $controller = new $class();

                $method = $route->getFunction()['method'];

                $args = $route->retrieveParametersFromUri($requestedUri);

                try {
                    $response = call_user_func_array(array($controller, $method), $args);
                } catch (\Exception $e) {
                    //TODO: Renvoyer vers un controller d'exception
                }

                print($response);

                return;
            }
        }
        //redirection vers un controller error(404)

        throw new \Exception(sprintf('Undefined route for %s : %s', $request->getMethod(), $requestedUri));
    }

    private function removeLastSlash($uri): string
    {
        $newUri = $uri;
        if ($newUri != '/' && str_ends_with($newUri, '/')) {
            $newUri = substr_replace($newUri, '', -1);
        }

        return $newUri;
    }
}