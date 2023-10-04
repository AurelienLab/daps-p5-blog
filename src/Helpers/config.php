<?php

use App\Core\Config\Config;

/**
 * Get a config value by its dot notation key
 * Example: config('db.db_name')
 *
 * @param string $key Dot notation of key to retrieve
 *
 * @return mixed
 * @throws Exception
 */

function config(string $key): mixed
{
    return Config::get($key);
}
