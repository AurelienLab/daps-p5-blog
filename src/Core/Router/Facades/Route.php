<?php

namespace App\Core\Router\Facades;

/**
 * Transforms any static call to object instance
 * and perform call
 */
class Route
{

    public static function __callStatic($name, $arguments)
    {
        $route = new \App\Core\Router\Route();
        return call_user_func_array([$route, $name], $arguments);
    }
}
