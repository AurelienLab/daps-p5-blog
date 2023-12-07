<?php


use App\Core\Router\Facades\Route;

Route::prefix('/admin')->name('admin.')->group([
    Route::get('/', [\App\Controller\Admin\DashboardController::class, 'index'])->name('dashboard'),

    Route::prefix('/categories')->name('category.')->group([
        Route::get('/', [\App\Controller\Admin\CategoryController::class, 'index'])->name('index'),
        
        Route::get('/ajouter', [\App\Controller\Admin\CategoryController::class, 'add'])->name('add'),
        Route::post('/ajouter', [\App\Controller\Admin\CategoryController::class, 'create'])->name('add.post'),

        Route::get('/{id}/editer', [\App\Controller\Admin\CategoryController::class, 'edit'])->name('edit'),
        Route::post('/{id}/editer', [\App\Controller\Admin\CategoryController::class, 'update'])->name('edit.post'),
    ])
]);
