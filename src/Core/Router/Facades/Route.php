<?php

namespace App\Core\Router\Facades;

class Route
{

    public static function __callStatic($name, $arguments)
    {
        $route = new \App\Core\Router\Route();
        return call_user_func_array([$route, $name], $arguments);
    }
}
