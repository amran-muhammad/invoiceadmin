<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('userlist', function () {
    return Inertia::render('UserList');
})->middleware(['auth', 'verified'])->name('userlist');
Route::get('productlist', function () {
    return Inertia::render('ProductList');
})->middleware(['auth', 'verified'])->name('productlist');
Route::get('customerlist', function () {
    return Inertia::render('CustomerList');
})->middleware(['auth', 'verified'])->name('customerlist');

Route::get('createinvoice', function () {
    return Inertia::render('CreateInvoice');
})->middleware(['auth', 'verified'])->name('createinvoice');

Route::apiResource('invoices', InvoiceController::class)->middleware(['auth', 'verified']);
Route::apiResource('userlists', AdminUserController::class)->middleware(['auth', 'verified']);
Route::apiResource('productlists', ProductController::class)->middleware(['auth', 'verified']);
Route::apiResource('customerlists', CustomerController::class)->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
