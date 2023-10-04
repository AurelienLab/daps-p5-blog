<?php

use App\Core\Router\Route;

Route::get('/post/{id}', [\App\Controller\HomeController::class, 'test']);
Route::post('/post/{id}', [\App\Controller\HomeController::class, 'testPost']);
Route::get('/', [\App\Controller\HomeController::class, 'index']);
