<?php


use App\Core\Router\Facades\Route;

Route::prefix('/admin')->name('admin.')->group([
    Route::get('/', [\App\Controller\Admin\DashboardController::class, 'index'])->name('dashboard')
]);
