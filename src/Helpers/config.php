<?php

/**
 * Get a config value by its dot notation key
 * Example: config('db.db_name')
 *
 * @param $key
 *
 * @return mixed
 * @throws Exception
 */
function config($key)
{
    return \App\Core\Config\Config::get($key);
} // end config()
