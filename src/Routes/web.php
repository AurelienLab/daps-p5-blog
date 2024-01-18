<?php

use App\Core\Router\Facades\Route;

Route::get('/', [\App\Controller\HomeController::class, 'index'])->name('homepage.index');
Route::get('/articles', [\App\Controller\PostController::class, 'index'])->name('articles.index');
Route::get('/articles/tag/{slug}', [\App\Controller\PostController::class, 'tag'])->name('articles.tag');
Route::get('/articles/{slug}', [\App\Controller\PostController::class, 'show'])->name('articles.show');
Route::get('/inscription', [\App\Controller\UserController::class, 'subscribe'])->name('user.subscribe');
