<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\mpesaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaCallbackController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


    Route::get('/stkpush', [mpesaController::class, 'stkpush'])->name('stkpush');
    Route::post('/stkpush', [mpesaController::class, 'stkpush_init'])->name('stkpush.init');

    // M-Pesa transaction delete routes
    Route::delete('/mpesa/transaction/{id}', [mpesaController::class, 'deleteTransaction'])->name('mpesa.delete');
    Route::delete('/mpesa/transactions/bulk-delete', [mpesaController::class, 'bulkDeleteTransactions'])->name('mpesa.bulk-delete');


require __DIR__.'/auth.php';

Route::post('/gpay', [MpesaCallbackController::class, 'handleCallback']);
