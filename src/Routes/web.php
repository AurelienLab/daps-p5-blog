<?php

use App\Controller\CommentController;
use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\PostController;
use App\Controller\ResetPasswordController;
use App\Controller\SubscriptionController;
use App\Controller\UserController;
use App\Core\Router\Facades\Route;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\AutoLoginMiddleware;

Route::middleware([AutoLoginMiddleware::class])->group([
    // HOMEPAGE
    Route::get('/', [HomeController::class, 'index'])->name('homepage.index'),
    Route::post('/', [HomeController::class, 'contact'])->name('homepage.contact'),

    // POSTS
    Route::get('/articles', [PostController::class, 'index'])->name('articles.index'),
    Route::get('/articles/tag/{slug}', [PostController::class, 'tag'])->name('articles.tag'),
    Route::get('/articles/{slug}', [PostController::class, 'show'])->name('articles.show'),

    // COMMENTS
    Route::post('/articles/post-comment/{postId}', [CommentController::class, 'postComment'])->middleware(AuthMiddleware::class)->name('comment.post'),
    Route::post('/articles/edit-comment/{commentId}', [CommentController::class, 'editComment'])->middleware(AdminAuthMiddleware::class)->name('comment.edit'),
    Route::post('/articles/remove-comment/{commentId}', [CommentController::class, 'removeComment'])->middleware(AdminAuthMiddleware::class)->name('comment.remove'),

    // SUBSCRIPTION
    Route::get('/inscription', [SubscriptionController::class, 'subscribe'])->name('user.subscribe'),
    Route::post('/inscription', [SubscriptionController::class, 'register'])->name('user.subscribe.post'),
    Route::get('/verification-email', [SubscriptionController::class, 'verifyEmail'])->name('user.verify'),
    Route::get('/bienvenue', [SubscriptionController::class, 'success'])->name('user.subscribe.success'),

    // LOGIN
    Route::get('/connexion', [LoginController::class, 'login'])->name('user.login'),
    Route::post('/connexion', [LoginController::class, 'loginPost'])->name('user.login.post'),
    Route::get('/deconnexion', [LoginController::class, 'logout'])->name('user.logout')->middleware(AuthMiddleware::class),

    // PASSWORD REQUEST
    Route::get('/mot-de-passe', [ResetPasswordController::class, 'index'])->name('password-request.form'),
    Route::post('/mot-de-passe', [ResetPasswordController::class, 'post'])->name('password-request.post'),
    Route::get('/recuperer-mot-de-passe', [ResetPasswordController::class, 'resetPassword'])->name('password-request.reset'),
    Route::post('/recuperer-mot-de-passe', [ResetPasswordController::class, 'resetPasswordPost'])->name('password-request.reset.post'),

    Route::get('/mon-profil', [UserController::class, 'editProfile'])->name('user.profile.edit')->middleware(AuthMiddleware::class),
    Route::post('/mon-profil', [UserController::class, 'updateProfile'])->name('user.profile.post')->middleware(AuthMiddleware::class),
]);
