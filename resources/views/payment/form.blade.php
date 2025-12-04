<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .payment-card {
            max-width: 450px;
            width: 100%;
            padding: 20px;
            border-radius: 12px;
        }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow payment-card">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Razorpay Payment</h3>
        </div>
        <div class="card-body">

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif

            <!-- Payment Form -->
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
