<?php

// Project root path
const ROOT = __DIR__.'/..';

$autoloadPath = ROOT.'/vendor/autoload.php';
require_once $autoloadPath;

// Initialize dotenv
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

// Initialize Error Handler
Spatie\Ignition\Ignition::make()
    ->shouldDisplayException(env('APP_ENV') !== 'dev')
    ->register();

// Load config
$configPath = ROOT.'/config';
App\Core\Config\Config::load($configPath);

// Initialize Helpers (custom functions)
$helpersPath = ROOT.'/src/Helpers';
App\Core\Utils\Utils::loadHelpers($helpersPath);

try {
    // Initialize Router
    $routerPath = ROOT.'/src/Routes';
    App\Core\Router\Router::getInstance($routerPath)->handle();
} catch (Exception $e) {
    if (config('app.env') != 'dev') {
        (new \App\Controller\ErrorController())->error($e->getCode(), $e->getMessage());
    } else {
        throw $e;
    }
}
