<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeasonController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/orders');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('customers', CustomerController::class);
    Route::resource('seasons', SeasonController::class)->except(['show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.update-status');
    Route::resource('orders', OrderController::class)->except(['show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
