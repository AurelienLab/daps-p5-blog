<?php

use App\Core\Config\Config;

/**
 * Get a config value by its dot notation key
 * Example: config('db.db_name')
 *
 * @param $key
 *
 * @return mixed
 * @throws Exception
 */
function config($key): mixed
{
    return Config::get($key);
}
