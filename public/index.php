<?php

//Project root path
define('ROOT', realpath(__DIR__ . '/..'));

require_once(ROOT . '/vendor/autoload.php');

//Initialize dotenv
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

//Initialize Error Handler
Spatie\Ignition\Ignition::make()
    ->shouldDisplayException($_ENV['APP_ENV'] == 'dev')
    ->register();


//Load config
App\Core\Config\Config::load(ROOT . '/config');

//Initialize Helpers (custom functions)
App\Core\Utils\Utils::loadHelpers(ROOT . '/src/Helpers');

try {
    //Initialize Router
    App\Core\Router\Router::getInstance(ROOT . '/src/Routes')->handle();
} catch (\Exception $e) {
    if (config('app.env') != 'dev') {
        (new \App\Controller\ErrorController())->error($e->getCode(), $e->getMessage());

    } else {
        throw $e;
    }
}



