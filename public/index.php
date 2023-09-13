<?php

use App\Controller\HomeController;
use Symfony\Component\HttpFoundation\Request;

//Project root path
define('ROOT', realpath(__DIR__ . '/..'));

require_once(ROOT . '/vendor/autoload.php');

App\Core\Router\Router::getInstance(ROOT . '/src/Routes')->handle();
