<?php

namespace App\Core\Utils;

class Str
{


    /**
     * Transforms camelCase to snake_case
     *
     * @param string $string string to transform
     *
     * @return string
     */
    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }


    /**
     * Transforms snake_case to PascalCase
     *
     * @param string $string string to transform
     *
     * @return string
     */
    public static function toPascalCase(string $string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }


    /**
     * Transforms snake_case to camelCase
     *
     * @param string $string string to transform
     *
     * @return string
     */
    public static function toCamelCase(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }
}
