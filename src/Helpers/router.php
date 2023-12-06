<?php


function route(string $name, array $args = []): string
{
    $router = \App\Core\Router\Router::getInstance();

    return $router->getUriByName($name, $args);
}
