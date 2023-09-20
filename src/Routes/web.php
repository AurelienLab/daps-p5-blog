<?php

use App\Core\Router\Route;

Route::get('/', [\App\Controller\HomeController::class, 'index']);
