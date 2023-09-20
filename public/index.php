<?php

//Project root path
use Spatie\Ignition\Ignition;

define('ROOT', realpath(__DIR__ . '/..'));

require_once(ROOT . '/vendor/autoload.php');


//Initialize Error Handler
Ignition::make()->register();

//Initialize dotenv
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

//Initialize Router
App\Core\Router\Router::getInstance(ROOT . '/src/Routes')->handle();
