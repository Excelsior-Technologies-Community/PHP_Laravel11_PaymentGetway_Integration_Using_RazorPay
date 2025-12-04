<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
Route::get('/', function () {
    return view('welcome');
});






// Route::get('/payment-form', function() {
//     return redirect()->route('payment.form');
// });

Route::get('payment', [PaymentController::class, 'paymentForm'])->name('payment.form');
Route::post('payment', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::get('payments', [PaymentController::class, 'listPayments'])->name('payments.list');
Route::get('payments/delete/{id}', [PaymentController::class, 'deletePayment'])->name('payments.delete');
Route::get('payments/restore/{id}', [PaymentController::class, 'restorePayment'])->name('payments.restore');
Route::post('payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');

