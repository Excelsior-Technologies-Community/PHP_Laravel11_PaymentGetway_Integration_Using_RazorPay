Laravel 11 Payment Gateway Integration (Razorpay)

By: Manasi Patel
Date: 2025
Laravel Version: 11

In this tutorial, we are going to build a Payment Management System using Laravel 11. Users can make payments using Razorpay, view active payments, and manage them with soft delete and restore.

We will cover:

Payment form using Razorpay

Save payments in database (pending → success)

Display payments list (only active)

Delete payments with popup confirmation

Restore deleted payments (optional)

Centered Bootstrap design

This tutorial is beginner-friendly and explained step by step. By the end, you will have a fully functional payment system.


Step 1: Install Laravel 11

Install a fresh Laravel 11 project:

composer create-project laravel/laravel laravel11-payment "^11.0"
cd laravel11-payment


Step 2: Configure Database

Open .env and update database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_app
DB_USERNAME=root
DB_PASSWORD=


Create the database:

CREATE DATABASE payment_app;


Step 3: Install Razorpay SDK
composer require razorpay/razorpay


Step 4: Create Payments Table Migration

php artisan make:migration create_payments_table --create=payments

Edit migration database/migrations/xxxx_create_payments_table.php:

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create 'payments' table
        Schema::create('payments', function (Blueprint $table) {

            $table->id(); // Primary key 'id', auto-increment

            $table->decimal('amount', 10, 2); 
            // Payment amount, max 10 digits, 2 decimal places

            $table->string('payment_method')->nullable(); 
            // Payment method (e.g., card, cash, online), nullable

            $table->string('status')->default('pending'); 
            // Payment status, default is 'pending'

            $table->unsignedBigInteger('created_by')->nullable(); 
            // ID of the user who created the payment, nullable

            $table->unsignedBigInteger('updated_by')->nullable(); 
            // ID of the user who last updated the payment, nullable

            $table->timestamps(); 
            // Adds 'created_at' and 'updated_at' columns automatically

            $table->softDeletes(); 
            // Adds 'deleted_at' column for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop 'payments' table if exists
        Schema::dropIfExists('payments');
    }
};

Run migration:

php artisan migrate



Step 5: Create Payment Model
php artisan make:model Payment

app/Models/Payment.php:

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes; 
    // HasFactory → allows factory usage for testing/seeding
    // SoftDeletes → allows soft deleting (adds deleted_at column)

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'amount',          // Payment amount
        'payment_method',  // Payment method like Razorpay, Stripe, etc.
        'status',          // Payment status: pending, success, failed
        'created_by',      // ID of user who created payment
        'updated_by',      // ID of user who updated payment
    ];
}



Step 6: Create Payment Controller

php artisan make:controller PaymentController


app/Http/Controllers/PaymentController.php:

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;             // For Stripe integration (optional)
use Stripe\PaymentIntent;      // For Stripe payment intent
use App\Models\Payment;        // Payment model
use Razorpay\Api\Api;           // Razorpay SDK

class PaymentController extends Controller
{
    /**
     * Show payment form
     */
    public function paymentForm()
    {
        return view('payment.form'); 
        // Returns the payment form view
    }

    /**
     * Process payment using Razorpay
     */
    public function processPayment(Request $request)
    {
        // Validate amount input
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Initialize Razorpay API
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create Razorpay order
        $order = $api->order->create([
            'receipt' => 'order_rcptid_'.rand(1000,9999), // Unique receipt ID
            'amount' => $request->amount * 100,           // Amount in paise
            'currency' => 'INR',
        ]);

        // Save payment record as pending
        $payment = Payment::create([
            'amount' => $request->amount,
            'payment_method' => 'razorpay',
            'status' => 'pending',
            'created_by' => 1, // Hardcoded, replace with Auth::id() in real app
            'updated_by' => 1,
        ]);

        // Redirect to Razorpay checkout view
        return view('payment.checkout', [
            'order' => $order,     // Razorpay order details
            'payment' => $payment, // Payment record
            'key' => env('RAZORPAY_KEY') // Public key for checkout
        ]);
    }

    /**
     * Handle successful payment callback
     */
    public function paymentSuccess(Request $request)
    {
        // Find the payment record by ID
        $payment = Payment::findOrFail($request->payment_id);

        // Update status to success
        $payment->update([
            'status' => 'success',
            'updated_by' => 1 // Hardcoded, replace with Auth::id() in real app
        ]);

        // Redirect to payment list with success message
        return redirect()->route('payments.list')->with('success','Payment successful!');
    }

    /**
     * List all payments
     */
    public function listPayments()
    {
        // Fetch all payments ordered by latest
        $payments = Payment::orderBy('id','desc')->get();

        // Pass payments to view
        return view('payment.list', compact('payments'));
    }

    /**
     * Soft delete a payment
     */
    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id); // Find payment
        $payment->delete();                  // Soft delete (deleted_at set)
        return redirect()->back()->with('success','Payment soft deleted!');
    }

    /**
     * Restore a soft-deleted payment
     */
    public function restorePayment($id)
    {
        $payment = Payment::withTrashed()->findOrFail($id); // Include trashed records
        $payment->restore();                                // Restore soft-deleted payment
        return redirect()->back()->with('success','Payment restored!');
    }
}



Step 7: Add Routes

Open routes/web.php and add the following resource route for CRUD operations:

routes/web.php:

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



Step 8: Create Blade Views

Create a folder resources/views/payment/ and create these files:

list.blade.php,
form.blade.php,
checkout.blade.php


1) resources/views/payment/list.blade.php (List of all payments)

<!DOCTYPE html>
<html>

<head>
    <title>Payments List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <!-- Card Header -->
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Payments List</h3>
                <!-- Link to new payment form -->
                <a href="{{ route('payment.form') }}" class="btn btn-primary btn-sm">New Payment</a>
            </div>

            <div class="card-body">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Payments Table -->
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
                            <!-- Highlight deleted payments in red -->
                            <tr @if($p->deleted_at) class="table-danger" @endif>
                                <td>{{ $p->id }}</td>
                                <td>₹{{ $p->amount }}</td>
                                <td>{{ ucfirst($p->payment_method) }}</td>
                                <td>
                                    <!-- Status Badge -->
                                    @if($p->status === 'success')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($p->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>

                                <td>
                                    <!-- Actions: Restore if deleted, Delete if active -->
                                    @if($p->deleted_at)
                                        <a href="{{ route('payments.restore', $p->id) }}"
                                            class="btn btn-sm btn-success">Restore</a>
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



2) resources/views/payment/form.blade.php (Payment Form)


<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .payment-card {
            max-width: 450px;   /* Card width */
            width: 100%;
            padding: 20px;      /* Padding inside card */
            border-radius: 12px; /* Rounded corners */
        }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <!-- Center card vertically and horizontally -->
    <div class="card shadow payment-card">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Razorpay Payment</h3>
        </div>
        <div class="card-body">

            <!-- Display success alert if session has success message -->
            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif
            <!-- Display error alert if session has error message -->
            @if(session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif

            <!-- Payment Form -->
            <form action="{{ route('payment.process') }}" method="POST">
                @csrf <!-- CSRF token for security -->

                <!-- Amount Input -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (INR)</label>
                    <input type="number" name="amount" id="amount" class="form-control" min="1" placeholder="Enter amount" required>
                </div>

                <!-- Submit and View Payments Buttons -->
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


3) resources/views/payment/checkout.blade.php (Razorpay Checkout)

<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Checkout</title>
    <!-- Razorpay Checkout JS -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<script>
// Razorpay options
var options = {
    "key": "{{ $key }}", // Razorpay public key
    "amount": "{{ $payment->amount*100 }}", // Amount in paise
    "currency": "INR",
    "name": "Laravel App",
    "description": "Test Payment",
    "order_id": "{{ $order['id'] }}", // Razorpay order ID

    // Handler after successful payment
    "handler": function (response){
        // Create a hidden form to submit payment ID to server
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("payment.success") }}';
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                         '<input type="hidden" name="payment_id" value="{{ $payment->id }}">';
        document.body.appendChild(form);
        form.submit(); // Submit form to process payment success
    }
};

// Open Razorpay checkout popup
var rzp1 = new Razorpay(options);
rzp1.open();
</script>
</body>
</html>




Step 9: Environment Variables

Add Razorpay test keys to .env:

RAZORPAY_KEY= rzp_test_xxxxxxxxxxxxx
RAZORPAY_SECRET= xxxxxxxxxxxxxxxx



Step 10: Run the Application
php artisan serve

Open browser:

http://localhost:8000/payment


✅ Congratulations! You now have a fully functional Laravel 11 payment gateway integration

