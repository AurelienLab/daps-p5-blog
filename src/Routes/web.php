<?php

use App\Core\Router\Route;

Route::get('/', [\App\Controller\HomeController::class, 'index'])->name('homepage.index');
Route::get('/articles', [\App\Controller\PostController::class, 'index'])->name('articles.index');
Route::get('/articles/test', [\App\Controller\PostController::class, 'show'])->name('articles.show');
