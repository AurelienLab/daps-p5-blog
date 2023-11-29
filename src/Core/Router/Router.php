<?php

namespace App\Core\Router;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{

    /**
     * @var array
     */
    private array $routingFiles;

    /**
     * @var array
     */
    private array $routeCollection;

    /**
     * @var null|Router
     */
    private static $_instance;


    /**
     * This class should only be instanced once in base file.
     * Keep all routes in $routeCollection and finds a match from Request
     *
     * @param string $routesFolder path to the folder containing routes
     */
    public function __construct(private readonly string $routesFolder)
    {
        $directory = scandir($routesFolder);
        $this->routingFiles = array_diff($directory, ['..', '.']);
    }


    /**
     * Returns instance of current object if initialized
     *
     * @param string $routeFolder path to the folder containing routes
     *
     * @return Router
     * @throws Exception
     */
    public static function getInstance(string $routeFolder = ''): Router
    {
        if (self::$_instance === null) {
            if (empty($routeFolder) === false) {
                self::$_instance = new Router($routeFolder);
                self::$_instance->collectRoutes();
                return self::$_instance;
            }

            throw new Exception('Unable to create a new Router instance without folder path');
        }

        return self::$_instance;
    }


    /**
     * Parse all route files to execute Routes addition
     *
     * @return void
     */
    private function collectRoutes(): void
    {
        foreach ($this->routingFiles as $file) {
            $filePath = $this->routesFolder.'/'.$file;
            include_once $filePath;
        }
    }


    /**
     * Add a route to the collection corresponding to its method
     *
     * @param Route $route Statically generated route
     *
     * @return void
     */
    public function add(Route $route): void
    {
        $this->routeCollection[$route->getMethod()][] = $route;
    }


    /**
     * Compare current query to find a matching route
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $request = Request::createFromGlobals();
        $requestedUri = $this->removeLastSlash($request->getPathInfo());
        $collection = $this->routeCollection[$request->getMethod()];
        foreach ($collection as $route) {
            /* @var Route $route */

            if ($route->matchUri($requestedUri) === true) {
                $this->runMiddleware($route, $request);
                $this->runController($route, $requestedUri);
                return;
            } // end if
        }

        throw new Exception(sprintf('Undefined route for %s : %s', $request->getMethod(), $requestedUri), 404);
    }


    /**
     * Execute Middleware
     *
     * @param Route $route
     * @param Request $request
     *
     * @return void
     * @throws Exception
     */
    private function runMiddleware(Route $route, Request $request): void
    {
        if (empty($route->getMiddleware())) {
            return;
        }

        foreach ($route->getMiddleware() as $middleware) {
            if (class_exists($middleware) === false) {
                throw new Exception(sprintf('Unable to find middleware %s.', $middleware), 500);
            }

            $method = 'handle';
            if (method_exists($middleware, $method) === false) {
                throw new Exception(sprintf('Unable to find handle() method in %s.', $middleware), 500);
            }

            try {
                $middlewareObject = new $middleware();
                $middlewareObject->handle($request);
            } catch (Exception $e) {
                throw new Exception(
                    sprintf(
                        'Error while trying to call handle() in %s: %s',
                        $middleware,
                        $e->getMessage()
                    ),
                    $e->getCode()
                );
            }
        }
    }


    /**
     * Execute controller
     *
     * @param Route $route
     * @param string $requestedUri
     *
     * @return void
     * @throws Exception
     */
    private function runController(Route $route, string $requestedUri): void
    {
        $class = $route->getFunction()['class'];
        $controller = new $class();
        if (class_exists($class) === false) {
            throw new Exception(sprintf('Unable to find class %s.', $class), 500);
        }

        $method = $route->getFunction()['method'];
        if (method_exists($class, $method) === false) {
            throw new Exception(sprintf('Unable to find method %s in %s.', $method, $class), 500);
        }

        $args = $route->retrieveParametersFromUri($requestedUri);

        try {
            /** @var Response $response */
            $response = call_user_func_array([$controller, $method], $args);

            if (($response instanceof Response) === false) {
                throw new Exception(sprintf(
                    'Object of type %s expected %s given.',
                    Response::class,
                    gettype($response)
                ));
            }
        } catch (Exception $e) {
            throw new Exception(
                sprintf(
                    'Error while trying to call %s::%s. %s',
                    $class,
                    $method,
                    $e->getMessage()
                ),
                $e->getCode()
            );
        }

        $response->send();
    }

    /**
     * Remove trailing slash of given URI
     *
     * @param string $uri URI to clear
     *
     * @return string
     */
    private function removeLastSlash(string $uri): string
    {
        $newUri = $uri;
        if ($newUri !== '/' && str_ends_with($newUri, '/') === true) {
            $newUri = substr_replace($newUri, '', -1);
        }

        return $newUri;
    }
}
