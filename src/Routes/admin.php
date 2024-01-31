<?php

use App\Controller\Admin;
use App\Core\Router\Facades\Route;

Route::prefix('/admin')->name('admin.')->group([
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard'),

    Route::prefix('/categories')->name('category.')->group([
        Route::get('/', [Admin\CategoryController::class, 'index'])->name('index'),

        Route::get('/ajouter', [Admin\CategoryController::class, 'add'])->name('add'),
        Route::post('/ajouter', [Admin\CategoryController::class, 'create'])->name('add.post'),

        Route::get('/{id}/editer', [Admin\CategoryController::class, 'edit'])->name('edit'),
        Route::post('/{id}/editer', [Admin\CategoryController::class, 'update'])->name('edit.post'),

        Route::get('/{id}/remove', [Admin\CategoryController::class, 'remove'])->name('remove')
    ]),

    Route::prefix('/articles')->name('post.')->group([
        Route::get('/', [Admin\PostController::class, 'index'])->name('index'),

        Route::get('/ajouter', [Admin\PostController::class, 'add'])->name('add'),
        Route::post('/ajouter', [Admin\PostController::class, 'create'])->name('add.post'),

        Route::get('/{id}/editer', [Admin\PostController::class, 'edit'])->name('edit'),
        Route::post('/{id}/editer', [Admin\PostController::class, 'update'])->name('edit.post'),

        Route::get('/{id}/remove', [Admin\PostController::class, 'remove'])->name('remove')
    ]),

    Route::prefix('/utilisateurs')->name('user.')->group([
        Route::get('/', [Admin\UserController::class, 'index'])->name('index'),

        Route::get('/ajouter', [Admin\UserController::class, 'add'])->name('add'),
        Route::post('/ajouter', [Admin\UserController::class, 'create'])->name('add.post'),

        Route::get('/{id}/editer', [Admin\UserController::class, 'edit'])->name('edit'),
        Route::post('/{id}/editer', [Admin\UserController::class, 'update'])->name('edit.post'),

        Route::get('/{id}/remove', [Admin\UserController::class, 'remove'])->name('remove')
    ]),
    Route::get('/tags/search/{query?}', [Admin\TagController::class, 'search'])->name('tag.search'),
    Route::post('/editorjs/upload-file', [Admin\EditorController::class, 'uploadFile']),
    Route::post('/editorjs/fetch-url', [Admin\EditorController::class, 'fetchUrl']),
]);
