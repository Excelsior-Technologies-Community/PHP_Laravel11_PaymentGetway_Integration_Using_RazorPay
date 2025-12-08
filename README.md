Laravel 11 Payment Gateway Integration (Razorpay)
---
By: Manasi Patel
Date: 2025
Laravel Version: 11

This project demonstrates a Payment Management System using Laravel 11 and Razorpay. Users can make payments, view active payments, and manage them with soft delete and restore functionality.

🚀 Features
---
Payment form using Razorpay

Save payments in database (pending → success)

Display payments list (only active)

Delete payments with popup confirmation

Restore deleted payments (optional)

Centered Bootstrap design

📦 Installation & Setup
---
1️⃣ Install Laravel 11 & Navigate to Project
```
composer create-project laravel/laravel laravel11-payment "^11.0"
cd laravel11-payment
```
2️⃣ Configure Database

Edit .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_app
DB_USERNAME=root
DB_PASSWORD=

```
Create database:

CREATE DATABASE payment_app;

3️⃣ Install Razorpay SDK
```
composer require razorpay/razorpay
```
4️⃣ Create Payments Table Migration
```
php artisan make:migration create_payments_table --create=payments
```

Edit migration file database/migrations/xxxx_create_payments_table.php:
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->decimal('amount', 10, 2); // Payment amount
            $table->string('payment_method')->nullable(); // e.g., Razorpay
            $table->string('status')->default('pending'); // pending, success, failed
            $table->unsignedBigInteger('created_by')->nullable(); // User ID
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at for soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
```

Run migration:
```
php artisan migrate
```
5️⃣ Create Payment Model
```
php artisan make:model Payment

```
app/Models/Payment.php:
```
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
```
6️⃣ Create Payment Controller
```
php artisan make:controller PaymentController
```

app/Http/Controllers/PaymentController.php:
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    // Show payment form
    public function paymentForm()
    {
        return view('payment.form');
    }

    // Process payment using Razorpay
    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create Razorpay order
        $order = $api->order->create([
            'receipt' => 'order_rcptid_'.rand(1000,9999),
            'amount' => $request->amount * 100, // Amount in paise
            'currency' => 'INR',
        ]);

        // Save payment record as pending
        $payment = Payment::create([
            'amount' => $request->amount,
            'payment_method' => 'razorpay',
            'status' => 'pending',
            'created_by' => 1, // Replace with Auth::id() in production
            'updated_by' => 1,
        ]);

        return view('payment.checkout', [
            'order' => $order,
            'payment' => $payment,
            'key' => env('RAZORPAY_KEY')
        ]);
    }

    // Handle successful payment
    public function paymentSuccess(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);

        $payment->update([
            'status' => 'success',
            'updated_by' => 1, // Replace with Auth::id()
        ]);

        return redirect()->route('payments.list')
                         ->with('success', 'Payment successful!');
    }

    // List all payments
    public function listPayments()
    {
        $payments = Payment::orderBy('id','desc')->get();
        return view('payment.list', compact('payments'));
    }

    // Soft delete payment
    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success', 'Payment soft deleted!');
    }

    // Restore soft-deleted payment
    public function restorePayment($id)
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $payment->restore();
        return redirect()->back()->with('success', 'Payment restored!');
    }
}
```
7️⃣ Add Routes

routes/web.php:
```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', fn() => view('welcome'));

// Payment routes
Route::get('payment', [PaymentController::class, 'paymentForm'])->name('payment.form');
Route::post('payment', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('payments', [PaymentController::class, 'listPayments'])->name('payments.list');
Route::get('payments/delete/{id}', [PaymentController::class, 'deletePayment'])->name('payments.delete');
Route::get('payments/restore/{id}', [PaymentController::class, 'restorePayment'])->name('payments.restore');
Route::post('payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
```
8️⃣ Create Blade Views
```
Folder: resources/views/payment/
```
1️⃣ list.blade.php (Payments List)
```
<!DOCTYPE html>
<html>
<head>
    <title>Payments List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Payments List</h3>
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
                                @if($p->status === 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($p->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>
                                @if($p->deleted_at)
                                    <a href="{{ route('payments.restore', $p->id) }}" class="btn btn-sm btn-success">Restore</a>
                                @else
                                    <a href="{{ route('payments.delete', $p->id) }}" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this payment?');">
                                        Delete
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
```
2️⃣ form.blade.php (Payment Form)
```
<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .payment-card { max-width: 450px; width: 100%; padding: 20px; border-radius: 12px; }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow payment-card">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Razorpay Payment</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif
            <form action="{{ route('payment.process') }}" method="POST">
                @csrf
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
```
3️⃣ checkout.blade.php (Razorpay Checkout)
```
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
    "handler": function (response){
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("payment.success") }}';
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
```
9️⃣ Add Razorpay Test Keys

Add to .env:
```
RAZORPAY_KEY=rzp_test_xxxxxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxxxx
```
🔟 Run the Application
```
php artisan serve

```
Open browser:
```
http://localhost:8000/payment
```
✅ Congratulations! You now have a fully functional Laravel 11 Payment Gateway Integration with Razorpay, including payment creation, listing, soft delete, and restore.
