<?php

namespace App\Core\Router;

use App\Core\Utils\Str;
use Exception;

class Route
{

    /** @var string */
    private string $method;

    /**
     * @var string
     */
    private string $prefix = '';

    /** @var string */
    private string $uri = '';

    /**
     * @var string|null
     */
    private ?string $name = null;

    /** @var array */
    private array $function;

    /**
     * @var array|null
     */
    private array $middleware = [];

    /** @var array */
    private array $parameters;

    /** @var string */
    private string $matchRegex;

    /**
     * @var Route[]
     */
    private array $group;

    /**
     * @var Route|null
     */
    private ?Route $parent;

    /**
     * @return void
     * @throws Exception
     */
    private function addToRouter(): void
    {
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
    public function get(string $path, array $function): self
    {

        $class = $function[0];
        $classMethod = $function[1];

        $this->method = 'GET';
        $this->uri = $path;
        $this->function = [
            'class' => $class,
            'method' => $classMethod
        ];

        $this->addToRouter();

        return $this;
    }


    /**
     * Add a route with a POST method
     *
     * @param string $path Route path
     * @param array $function Controller class & method
     *
     * @throws Exception
     */
    public function post(string $path, array $function): self
    {
        $class = $function[0];
        $classMethod = $function[1];

        $this->method = 'POST';
        $this->uri = !empty($this->getPrefix()) ? $this->getPrefix().$path : $path;
        $this->function = [
            'class' => $class,
            'method' => $classMethod
        ];

        $this->addToRouter();

        return $this;
    }

    /**
     * Set a name to retrieve path in views
     *
     * @param string $name
     *
     * @return Route
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getPrefix(): string
    {
        $prefix = '';
        if (!empty($this->parent)) {
            $prefix = $this->parent->getPrefix();
        }

        return $prefix.$this->prefix;
    }


    /**
     * Get Route name
     *
     * @return string
     */
    public function getName(): ?string
    {
        $name = '';
        if (!empty($this->parent)) {
            $name = $this->parent->getName();
        }
        return $name.$this->name;
    }

    /**
     * Add middleware to execute before controller
     *
     * @param string|array $middleware
     *
     * @return $this
     */
    public function middleware(string|array $middleware): self
    {
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        $this->middleware = array_merge($this->middleware, $middleware);

        return $this;
    }

    /**
     * Get middleware(s) of the route
     *
     * @return string|array|null
     */
    public function getMiddleware(): array|null
    {
        return $this->middleware;
    }

    /**
     * Define multiple routes and apply the same config to them
     *
     * @param array $group
     *
     * @return void
     */
    public function group(array $group): self
    {
        $this->group = $group;
        foreach ($this->group as $route) {
            /** @var Route $route */
            $route->parent = $this;
        }

        return $this;
    }

    /**
     * Convert parameters given in route declaration to RouteParameter object
     *
     * @return void
     */
    public function parseParameters(): void
    {
        $regex = '#{([a-zA-Z0-9]+)(\?)?}#';

        $fullUri = $this->getPrefix().$this->uri;
        $fullUri = Str::removeTrailingSlash($fullUri);

        // Get list of parameters.
        preg_match_all($regex, $fullUri, $matches);

        // Initialize the match regex.
        $this->matchRegex = '#^'.$fullUri.'$#s';

        $this->parameters = [];
        foreach ($matches[1] as $key => $param) {
            // Add '?' to make a parameter optional ex: '/post/{user?}.
            $isNullable = $matches[2][$key] == '?';

            // Add parameter to list.
            $this->parameters[] = (new RouteParameter($param, $isNullable));

            // In the route match regex, replace the parameter by a regex catchable group.
            $this->matchRegex = preg_replace('#\{'.$param.'\??}#', '([^\s!?\/.*\#|]+)', $this->matchRegex);
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

    /**
     * @return RouteParameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns Route uri with parameters values inside
     *
     * @return string
     */
    public function constructUriWithParameters(): string
    {
        $uri = !empty($this->getPrefix()) ? $this->getPrefix().$this->uri : $this->uri;
        foreach ($this->parameters as $parameter) {
            $pattern = '%{'.$parameter->getName().'\??}%';
            $value = !empty($parameter->getValue()) ? $parameter->getValue() : '';
            $uri = preg_replace($pattern, $value, $uri);
        }

        if ($uri === '/') {
            return $uri;
        }
        return Str::removeTrailingSlash(str_replace('//', '/', $uri));
    }
}
