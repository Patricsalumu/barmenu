<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OwnerController;

Route::get('/', [MenuController::class, 'index'])->name('menu');
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
