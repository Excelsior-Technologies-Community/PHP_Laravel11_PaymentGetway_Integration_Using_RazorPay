<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider.
|
*/

// Default route to welcome page
Route::get('/', function () {
    return view('welcome'); // Show welcome page
});

// ------------------- Payment Routes -------------------

// Show payment form
Route::get('payment', [PaymentController::class, 'paymentForm'])
      ->name('payment.form');

// Process payment form submission
Route::post('payment', [PaymentController::class, 'processPayment'])
      ->name('payment.process');

// List all payments
Route::get('payments', [PaymentController::class, 'listPayments'])
      ->name('payments.list');

// Soft delete a payment by ID
Route::get('payments/delete/{id}', [PaymentController::class, 'deletePayment'])
      ->name('payments.delete');

// Restore a soft-deleted payment by ID
Route::get('payments/restore/{id}', [PaymentController::class, 'restorePayment'])
      ->name('payments.restore');

// Handle payment success callback
Route::post('payment/success', [PaymentController::class, 'paymentSuccess'])
      ->name('payment.success');

// Download invoice for a payment
Route::get('/payments/invoice/{id}', [PaymentController::class, 'downloadInvoice'])->name('payments.invoice');
