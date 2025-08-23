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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
