<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title> <!-- Page title -->

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Body background color */
        body {
            background-color: #f8f9fa; /* Light gray */
        }

        /* Payment card styling */
        .payment-card {
            max-width: 450px; /* Maximum width */
            width: 100%; /* Full width on smaller screens */
            padding: 20px; /* Padding inside the card */
            border-radius: 12px; /* Rounded corners */
        }
    </style>
</head>
<body>

<!-- Center the payment card vertically and horizontally -->
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow payment-card"> <!-- Card with shadow and custom styling -->

        <!-- Card Header -->
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Razorpay Payment</h3> <!-- Card title -->
        </div>

        <!-- Card Body -->
        <div class="card-body">

            <!-- Alerts for success or error messages -->
            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div> <!-- Success alert -->
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div> <!-- Error alert -->
            @endif

            <!-- Payment Form -->
            <form action="{{ route('payment.process') }}" method="POST">
                @csrf <!-- CSRF token for security -->

                <!-- Amount input field -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (INR)</label>
                    <input type="number" name="amount" id="amount" class="form-control" min="1" placeholder="Enter amount" required>
                </div>

                <!-- Submit and navigation buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Pay Now</button> <!-- Submit button -->
                    <a href="{{ route('payments.list') }}" class="btn btn-secondary btn-lg">View Payments</a> <!-- Link to view payments -->
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
