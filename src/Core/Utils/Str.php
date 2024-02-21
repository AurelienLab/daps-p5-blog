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

    /**
     * Remove slash of string if it's at the last pos
     *
     * @param string $string
     *
     * @return string
     */
    public static function removeTrailingSlash(string $string): string
    {
        $newString = $string;
        if (str_ends_with($newString, '/') === true) {
            $newString = substr_replace($newString, '', -1);
        }

        return $newString;
    }

    /**
     * Generate a random string
     *
     * @param int $length
     *
     * @return string
     * @throws \Random\RandomException
     */
    public static function rand(int $length = 64)
    {
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }
}
