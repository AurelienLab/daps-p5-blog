<?php

//Project root path
define('ROOT', realpath(__DIR__ . '/..'));

require_once(ROOT . '/vendor/autoload.php');


//Initialize Error Handler
Spatie\Ignition\Ignition::make()->register();

//Initialize dotenv
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

//Load config
App\Core\Config\Config::load(ROOT . '/config');

//Initialize Helpers (custom functions)
App\Core\Utils\Utils::loadHelpers(ROOT . '/src/Helpers');

//Initialize Router
App\Core\Router\Router::getInstance(ROOT . '/src/Routes')->handle();



