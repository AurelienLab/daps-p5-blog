<?php

namespace App\Core\Router;

class Route
{
    private string $method;
    private string $uri;
    private array $function;

    /**
     * @throws \Exception
     */
    private function addToRouter(): void
    {
        Router::getInstance()->add($this);
    }

    /**
     * @throws \Exception
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
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getFunction(): array
    {
        return $this->function;
    }
}