<?php

namespace App\Core\Utils;

class Str
{

    /**
     * Transforms camelCase to snake_case
     *
     * @param $string
     *
     * @return string
     */
    public static function toSnakeCase($string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * Transforms snake_case to PascalCase
     *
     * @param $string
     *
     * @return string
     */
    public static function toPascalCase($string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }

    public static function toCamelCase($string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }
}
