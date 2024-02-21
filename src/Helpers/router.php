<?php

/**
 * Get route path from route name and parameters
 *
 * @param string $name
 * @param array $args
 *
 * @return string
 * @throws Exception
 */
function route(string $name, array $args = []): string
{
    $router = \App\Core\Router\Router::getInstance();

    return $router->getUriByName($name, $args);
}

/**
 * Get route url from route name and parameters
 *
 * @param string $name
 * @param array $args
 *
 * @return string
 * @throws Exception
 */
function url(string $name, array $args = []): string
{
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $router = \App\Core\Router\Router::getInstance();
    $path = $router->getUriByName($name, $args);

    return $request->getUriForPath($path);
}
