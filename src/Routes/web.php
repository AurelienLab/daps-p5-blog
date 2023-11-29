<?php

use App\Core\Router\Route;

Route::get('/', [\App\Controller\HomeController::class, 'index']);
Route::get('/articles', [\App\Controller\PostController::class, 'index']);
Route::get('/articles/test', [\App\Controller\PostController::class, 'show']);
