<?php

use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\PostController;
use App\Controller\SubscriptionController;
use App\Controller\UserController;
use App\Core\Router\Facades\Route;
use App\Middleware\AuthMiddleware;
use App\Middleware\AutoLoginMiddleware;

Route::middleware([AutoLoginMiddleware::class])->group([
    // HOMEPAGE
    Route::get('/', [HomeController::class, 'index'])->name('homepage.index'),

    // POSTS
    Route::get('/articles', [PostController::class, 'index'])->name('articles.index'),
    Route::get('/articles/tag/{slug}', [PostController::class, 'tag'])->name('articles.tag'),
    Route::get('/articles/{slug}', [PostController::class, 'show'])->name('articles.show'),

    // COMMENTS
    Route::post('/articles/post-comment/{postId}', [\App\Controller\CommentController::class, 'postComment'])->name('comment.post'),

    // SUBSCRIPTION
    Route::get('/inscription', [SubscriptionController::class, 'subscribe'])->name('user.subscribe'),
    Route::post('/inscription', [SubscriptionController::class, 'register'])->name('user.subscribe.post'),
    Route::get('/bienvenue', [SubscriptionController::class, 'success'])->name('user.subscribe.success'),

    // LOGIN
    Route::get('/connexion', [LoginController::class, 'login'])->name('user.login'),
    Route::post('/connexion', [LoginController::class, 'loginPost'])->name('user.login.post'),
    Route::get('/deconnexion', [LoginController::class, 'logout'])->name('user.logout')->middleware(AuthMiddleware::class),

    Route::get('/mon-profil', [UserController::class, 'editProfile'])->name('user.profile.edit')->middleware(AuthMiddleware::class),
    Route::post('/mon-profil', [UserController::class, 'updateProfile'])->name('user.profile.post')->middleware(AuthMiddleware::class),
]);
