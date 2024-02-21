<?php

namespace App\Core\Router;

use App\Core\Exception\DisplayableException;
use App\Core\Utils\Str;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class Router
{

    /**
     * @var array
     */
    private array $routingFiles;

    /**
     * @var Route[]
     */
    private array $routeCollection;

    private ?string $currentRoute = null;

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

        foreach ($this->routeCollection as $method) {
            foreach ($method as $route) {
                $route->parseParameters();
            }
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
        $requestedUri = Str::removeTrailingSlash($request->getPathInfo());
        $collection = $this->routeCollection[$request->getMethod()];
        foreach ($collection as $route) {
            /* @var Route $route */


            if ($route->matchUri($requestedUri) === true) {
                // Initialize sessions
                $sessionStorage = new NativeSessionStorage();
                $session = new Session($sessionStorage);
                if (!$session->isStarted()) {
                    $session->start();
                }
                $request->setSession($session);

                $this->currentRoute = $route->getName();
                $this->runMiddleware($route, $request);
                $this->runController($route, $requestedUri, $request);
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
    private function runController(Route $route, string $requestedUri, Request $request): void
    {
        $class = $route->getFunction()['class'];
        $controller = new $class($request);
        if (class_exists($class) === false) {
            throw new Exception(sprintf('Unable to find class %s.', $class), 500);
        }

        $method = $route->getFunction()['method'];
        if (method_exists($class, $method) === false) {
            throw new Exception(sprintf('Unable to find method %s in %s.', $method, $class), 500);
        }

        $args = $route->retrieveParametersFromUri($requestedUri);

        $reflectionMethod = new \ReflectionMethod($class, $method);

        foreach ($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->getType() == Request::class) {
                $args[$parameter->getName()] = $request;
            }
        }

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
        } catch (DisplayableException $e) {
            throw $e;
        } catch (\PDOException $e) {
            throw new Exception(
                sprintf(
                    'Error while trying to call %s::%s. %s in %s::%s',
                    $class,
                    $method,
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ),
                500
            );
        } catch (Exception $e) {
            throw new Exception(
                sprintf(
                    'Error while trying to call %s::%s. %s in %s::%s',
                    $class,
                    $method,
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                ),
                $e->getCode() ?: 500,
            );
        }

        $response->send();
    }

    /**
     * Get the first route that matches given name
     *
     * @param string $name
     *
     * @return Route
     * @throws Exception
     */
    private function findRouteByName(string $name): Route
    {
        foreach ($this->routeCollection as $method) {
            foreach ($method as $route) {
                if ($route->getName() == $name) {
                    return $route;
                }
            }
        }

        throw new Exception(sprintf('Unable to find route named "%s"', $name));
    }

    /**
     * Return matching route name as URI constructed with passed arguments
     *
     * @param string $name
     * @param array $args
     *
     * @return string
     * @throws Exception
     */
    public function getUriByName(string $name, array $args): string
    {
        $route = $this->findRouteByName($name);

        // Check if required parameters are present
        foreach ($route->getParameters() as $parameter) {
            if (!$parameter->isNullable() && !isset($args[$parameter->getName()])) {
                throw new Exception(
                    sprintf(
                        'Missing parameter "%s" for route named "%s"',
                        $parameter->getName(),
                        $route->getName()
                    )
                );
            }

            if (isset($args[$parameter->getName()])) {
                $parameter->setValue($args[$parameter->getName()]);
                unset($args[$parameter->getName()]);
            }
        }

        //Generate route from parameters values
        $result = $route->constructUriWithParameters();

        //If we still have args, append them as query params
        if (!empty($args)) {
            $result .= '?'.http_build_query($args);
        }

        return $result;
    }

    /**
     * @return string|null
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
}
