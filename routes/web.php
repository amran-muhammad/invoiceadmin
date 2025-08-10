<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('createinvoice', function () {
    return Inertia::render('CreateInvoice');
})->middleware(['auth', 'verified'])->name('createinvoice');

Route::post('/invoices', [InvoiceController::class, 'store']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
