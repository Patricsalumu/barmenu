<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OwnerController;

Route::get('/', [MenuController::class, 'index'])->name('menu');

Route::prefix('super-admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\SuperAdminController::class, 'login']);
    Route::get('/connexion', [\App\Http\Controllers\SuperAdminController::class, 'login'])->name('super-admin.login');
    Route::post('/connexion', [\App\Http\Controllers\SuperAdminController::class, 'authenticate'])
        ->middleware('throttle:5,1')
        ->name('super-admin.authenticate');

    Route::middleware('super-admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
        Route::post('/deconnexion', [\App\Http\Controllers\SuperAdminController::class, 'logout'])->name('super-admin.logout');
        Route::post('/entreprises', [\App\Http\Controllers\SuperAdminController::class, 'storeCompany'])->name('super-admin.companies.store');
    });
});

Route::middleware('resolve.company')->group(function () {
    Route::get('/proprietaire/connexion', [OwnerController::class, 'login'])->name('owner.login');
    Route::post('/proprietaire/connexion', [OwnerController::class, 'authenticate'])->middleware('throttle:5,1')->name('owner.authenticate');
    Route::post('/proprietaire/deconnexion', [OwnerController::class, 'logout'])->middleware('owner')->name('owner.logout');

    Route::middleware('owner')->prefix('proprietaire')->name('owner.')->group(function () {
        Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::put('/entreprise', [OwnerController::class, 'updateCompany'])->name('company.update');
        Route::post('/categories', [OwnerController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [OwnerController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [OwnerController::class, 'deleteCategory'])->name('categories.destroy');
        Route::post('/produits', [OwnerController::class, 'storeProduct'])->name('products.store');
        Route::put('/produits/{product}', [OwnerController::class, 'updateProduct'])->name('products.update');
        Route::delete('/produits/{product}', [OwnerController::class, 'deleteProduct'])->name('products.destroy');
    });
});
