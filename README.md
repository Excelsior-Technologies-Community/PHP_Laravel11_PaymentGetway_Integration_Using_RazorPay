# Laravel 11 Payment Gateway Integration (Razorpay)

**By:** Manasi Patel  
**Date:** 2025  
**Laravel Version:** 11  

A Payment Management System built with Laravel 11. Users can make payments using Razorpay, view active payments, soft delete them, and restore deleted payments.

---

## ⭐ Overview

This project demonstrates:

- Payment form using Razorpay
- Saving payments in the database (`pending → success`)
- Displaying active payments
- Soft delete with popup confirmation
- Restore deleted payments
- Centered Bootstrap design
- Beginner-friendly step-by-step tutorial

---

## 📁 1. Project Setup

```bash
# Install Laravel 11
composer create-project laravel/laravel laravel11-payment "^11.0"
cd laravel11-payment
⚙ 2. Configure Database
Update .env:

makefile

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_app
DB_USERNAME=root
DB_PASSWORD=
Create database:

sql

CREATE DATABASE payment_app;
📦 3. Install Razorpay SDK
bash

composer require razorpay/razorpay
🗄 4. Create Payments Table Migration
bash

php artisan make:migration create_payments_table --create=payments
Edit migration database/migrations/xxxx_create_payments_table.php:

php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
Run migration:

bash

php artisan migrate
🧑‍💻 5. Payment Model
bash
php artisan make:model Payment
app/Models/Payment.php:

php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'amount',
        'payment_method',
        'status',
        'created_by',
        'updated_by',
    ];
}
🧑‍💻 6. Payment Controller
bash

php artisan make:controller PaymentController
app/Http/Controllers/PaymentController.php:

php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function paymentForm()
    {
        return view('payment.form');
    }

    public function processPayment(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'receipt' => 'order_rcptid_'.rand(1000,9999),
            'amount' => $request->amount * 100,
            'currency' => 'INR',
        ]);

        $payment = Payment::create([
            'amount' => $request->amount,
            'payment_method' => 'razorpay',
            'status' => 'pending',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return view('payment.checkout', [
            'order' => $order,
            'payment' => $payment,
            'key' => env('RAZORPAY_KEY')
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);
        $payment->update(['status' => 'success', 'updated_by' => 1]);

        return redirect()->route('payments.list')->with('success','Payment successful!');
    }

    public function listPayments()
    {
        $payments = Payment::orderBy('id','desc')->get();
        return view('payment.list', compact('payments'));
    }

    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success','Payment soft deleted!');
    }

    public function restorePayment($id)
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $payment->restore();
        return redirect()->back()->with('success','Payment restored!');
    }
}
🌐 7. Routes
routes/web.php:

php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', fn() => view('welcome'));

// Payment Routes
Route::get('payment', [PaymentController::class, 'paymentForm'])->name('payment.form');
Route::post('payment', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('payments', [PaymentController::class, 'listPayments'])->name('payments.list');
Route::get('payments/delete/{id}', [PaymentController::class, 'deletePayment'])->name('payments.delete');
Route::get('payments/restore/{id}', [PaymentController::class, 'restorePayment'])->name('payments.restore');
Route::post('payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
🖥 8. Blade Views
Create folder: resources/views/payment/

8.1 list.blade.php
html
Copy code
<!DOCTYPE html>
<html>
<head>
    <title>Payments List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h3>Payments List</h3>
            <a href="{{ route('payment.form') }}" class="btn btn-primary btn-sm">New Payment</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>Amount (INR)</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr @if($p->deleted_at) class="table-danger" @endif>
                        <td>{{ $p->id }}</td>
                        <td>₹{{ $p->amount }}</td>
                        <td>{{ ucfirst($p->payment_method) }}</td>
                        <td>
                            @if($p->status==='success')
                                <span class="badge bg-success">Success</span>
                            @elseif($p->status==='pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>
                            @if($p->deleted_at)
                                <a href="{{ route('payments.restore', $p->id) }}" class="btn btn-sm btn-success">Restore</a>
                            @else
                                <a href="{{ route('payments.delete', $p->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No payments found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
8.2 form.blade.php
html

<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color:#f8f9fa; }
        .payment-card { max-width:450px; width:100%; padding:20px; border-radius:12px; }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow payment-card">
        <div class="card-header bg-primary text-white text-center"><h3>Razorpay Payment</h3></div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            <form action="{{ route('payment.process') }}" method="POST">@csrf
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (INR)</label>
                    <input type="number" name="amount" id="amount" class="form-control" min="1" placeholder="Enter amount" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Pay Now</button>
                    <a href="{{ route('payments.list') }}" class="btn btn-secondary btn-lg">View Payments</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
8.3 checkout.blade.php
html

<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Checkout</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<script>
var options = {
    "key": "{{ $key }}",
    "amount": "{{ $payment->amount*100 }}",
    "currency": "INR",
    "name": "Laravel App",
    "description": "Test Payment",
    "order_id": "{{ $order['id'] }}",
    "handler": function(response){
        var form = document.createElement('form');
        form.method='POST';
        form.action='{{ route("payment.success") }}';
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                         '<input type="hidden" name="payment_id" value="{{ $payment->id }}">';
        document.body.appendChild(form);
        form.submit();
    }
};
var rzp1 = new Razorpay(options);
rzp1.open();
</script>
</body>
</html>
⚙ 9. Environment Variables
Add Razorpay keys to .env:

ini

RAZORPAY_KEY=rzp_test_xxxxxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxx
🚀 10. Run Application
bash

php artisan serve
Open in browser:

bash

http://localhost:8000/payment

✅ You now have a fully functional Laravel 11 Payment Gateway Integration using Razorpay
