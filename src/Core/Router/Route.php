<?php

namespace App\Core\Router;

use Exception;

class Route
{

    /** @var string */
    private string $method;

    /** @var string */
    private string $uri;

    /** @var array */
    private array $function;

    /** @var array */
    private array $parameters;

    /** @var string */
    private string $matchRegex;


    /**
     * @return void
     * @throws Exception
     */
    private function addToRouter(): void
    {
        $this->parseParameters();
        Router::getInstance()->add($this);
    }


    /**
     * Add a route with a GET method
     *
     * @param string $path Route path
     * @param array $function Controller class & method
     *
     * @return void
     * @throws Exception
     */
    public static function get(string $path, array $function): void
    {
        $route = new Route();

        $class = $function[0];
        $classMethod = $function[1];

        $route->method = 'GET';
        $route->uri = $path;
        $route->function = [
            'class' => $class,
            'method' => $classMethod
        ];

        $route->addToRouter();
    }


    /**
     * Add a route with a POST method
     *
     * @param string $path Route path
     * @param array $function Controller class & method
     *
     * @throws Exception
     */
    public static function post(string $path, array $function): void
    {
        $route = new Route();

        $class = $function[0];
        $classMethod = $function[1];

        $route->method = 'POST';
        $route->uri = $path;
        $route->function = [
            'class' => $class,
            'method' => $classMethod
        ];

        $route->addToRouter();
    }


    /**
     * Convert parameters given in route declaration to RouteParameter object
     *
     * @return void
     */
    private function parseParameters(): void
    {
        $regex = '#{([a-zA-Z0-9]+)(\?)?}#';

        // Get list of parameters.
        preg_match_all($regex, $this->uri, $matches);

        // Initialize the match regex.
        $this->matchRegex = '#^'.$this->uri.'$#s';

        $this->parameters = [];
        foreach ($matches[1] as $key => $param) {
            // Add '?' to make a parameter optional ex: '/post/{user?}.
            $isNullable = $matches[2][$key] == '?';

            // Add parameter to list.
            $this->parameters[] = (new RouteParameter($param, $isNullable));

            // In the route match regex, replace the parameter by a regex catchable group.
            $this->matchRegex = preg_replace('#\{'.$param.'\??}#', '([a-zA-Z0-9]+)', $this->matchRegex);
        }
    }


    /**
     * Retrieve parameters values from request URI
     *
     * @param string $uri URI from request
     *
     * @return array
     * @throws Exception
     */
    public function retrieveParametersFromUri(string $uri): array
    {
        preg_match_all($this->matchRegex, $uri, $matches);

        $parametersValue = [];
        if (isset($matches[1]) === true) {
            foreach ($matches[1] as $key => $value) {
                if ($this->parameters[$key]->isNullable() === false && empty($value) === true) {
                    throw new Exception(
                        sprintf(
                            'Missing parameter for %s parameter',
                            $this->parameters[$key]->getName()
                        )
                    );
                }

                $this->parameters[$key]->setValue($value);
                $parametersValue[$this->parameters[$key]->getName()] = $value;
            }
        }

        return $parametersValue;
    }


    /**
     * @param string $route URI value to test against route path
     *
     * @return bool
     */
    public function matchUri(string $route): bool
    {
        return (bool) preg_match($this->matchRegex, $route);
    }


    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }


    /**
     * @return array
     */
    public function getFunction(): array
    {
        return $this->function;
    }
}
