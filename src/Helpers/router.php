<?php


function route(string $name, array $args = []): string
{
    $router = \App\Core\Router\Router::getInstance();

    return $router->getUriByName($name, $args);
}

function url(string $name, array $args = []): string
{
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $router = \App\Core\Router\Router::getInstance();
    $path = $router->getUriByName($name, $args);

    return $request->getUriForPath($path);
}
