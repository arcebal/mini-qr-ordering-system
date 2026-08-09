<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CustomerSessionController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\ProfileController;

Route::get('/', [CustomerSessionController::class, 'create'])->name('customer.start');
Route::post('/start-order', [CustomerSessionController::class, 'store'])->name('customer.start.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('customer.name')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu');
    Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
    Route::post('/cart/items/{product}', [CartController::class, 'add'])->name('customer.cart.add');
    Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('customer.cart.update');
    Route::delete('/cart/items/{product}', [CartController::class, 'remove'])->name('customer.cart.remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('customer.checkout');
    Route::post('/checkout', [CartController::class, 'placeOrder'])->name('customer.checkout.place');
    Route::get('/orders/{order}', [CartController::class, 'success'])->name('customer.order-success');
    Route::get('/orders/{order}/status', [CartController::class, 'status'])->name('customer.order-status');
});

Route::middleware(['auth', EnsureAdmin::class])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('categories', CategoryController::class);

    Route::resource('products', ProductController::class);

    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('reports/export/excel', [ReportController::class, 'excel'])->name('reports.excel');
});

require __DIR__.'/auth.php';
