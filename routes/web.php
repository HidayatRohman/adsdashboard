<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Removed old /spent route; use resource-style /spents routes instead
// Spent CRUD
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('spents', [\App\Http\Controllers\SpentController::class, 'index'])->name('spents.index');
    Route::post('spents', [\App\Http\Controllers\SpentController::class, 'store'])->name('spents.store');
    Route::patch('spents/{spent}', [\App\Http\Controllers\SpentController::class, 'update'])->name('spents.update');
    Route::delete('spents/{spent}', [\App\Http\Controllers\SpentController::class, 'destroy'])->name('spents.destroy');
    
    // Sales CRUD
    Route::get('sales', [\App\Http\Controllers\SaleController::class, 'index'])->name('sales.index');
    Route::post('sales', [\App\Http\Controllers\SaleController::class, 'store'])->name('sales.store');
    Route::patch('sales/{sale}', [\App\Http\Controllers\SaleController::class, 'update'])->name('sales.update');
    Route::delete('sales/{sale}', [\App\Http\Controllers\SaleController::class, 'destroy'])->name('sales.destroy');
    
    // Product CRUD
    Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::post('products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    Route::patch('products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
    
    // Brand CRUD
    Route::get('brands', [\App\Http\Controllers\BrandController::class, 'index'])->name('brands.index');
    Route::post('brands', [\App\Http\Controllers\BrandController::class, 'store'])->name('brands.store');
    Route::patch('brands/{brand}', [\App\Http\Controllers\BrandController::class, 'update'])->name('brands.update');
    Route::delete('brands/{brand}', [\App\Http\Controllers\BrandController::class, 'destroy'])->name('brands.destroy');

    // Leads CRUD
    Route::get('leads', [\App\Http\Controllers\LeadController::class, 'index'])->name('leads.index');
    Route::post('leads', [\App\Http\Controllers\LeadController::class, 'store'])->name('leads.store');
    Route::patch('leads/{lead}', [\App\Http\Controllers\LeadController::class, 'update'])->name('leads.update');
    Route::delete('leads/{lead}', [\App\Http\Controllers\LeadController::class, 'destroy'])->name('leads.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
