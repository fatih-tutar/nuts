<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeasonController;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Season;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', function () {
    return view('dashboard', [
        'stats' => [
            'products' => Product::query()->count(),
            'customers' => Customer::query()->count(),
            'seasons' => Season::query()->count(),
            'orders' => Order::query()->count(),
            'open_orders' => Order::query()->where('status', 'ordered')->count(),
        ],
    ]);
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('customers', CustomerController::class);
    Route::resource('seasons', SeasonController::class)->except(['show']);
    Route::resource('orders', OrderController::class)->except(['show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
