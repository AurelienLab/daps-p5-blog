<?php

use App\Controller\HomeController;
use App\Controller\PostController;
use App\Controller\SubscriptionController;
use App\Core\Router\Facades\Route;

// HOMEPAGE
Route::get('/', [HomeController::class, 'index'])->name('homepage.index');

// POSTS
Route::get('/articles', [PostController::class, 'index'])->name('articles.index');
Route::get('/articles/tag/{slug}', [PostController::class, 'tag'])->name('articles.tag');
Route::get('/articles/{slug}', [PostController::class, 'show'])->name('articles.show');

// SUBSCRIPTION
Route::get('/inscription', [SubscriptionController::class, 'subscribe'])->name('user.subscribe');
Route::post('/inscription', [SubscriptionController::class, 'register'])->name('user.subscribe.post');
Route::get('/bienvenue', [SubscriptionController::class, 'success'])->name('user.subscribe.success');
