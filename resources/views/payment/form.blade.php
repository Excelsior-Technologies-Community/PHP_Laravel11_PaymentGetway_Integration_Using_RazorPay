<!DOCTYPE html>
<html>

<head>

    <title>Razorpay Payment</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .payment-card {
            width: 450px;
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }
    </style>

</head>

<body>

    <div class="card payment-card shadow-lg">

        <div class="card-header bg-dark text-white text-center">

            <h3 class="mb-0">

                Secure Payment Gateway

            </h3>

        </div>

        <div class="card-body p-4">

            @if(session('success'))

            <div class="alert alert-success">

                {{session('success')}}

            </div>

            @endif

            @if(session('error'))

            <div class="alert alert-danger">

                {{session('error')}}

            </div>

            @endif

            <form action="{{route('payment.process')}}" method="POST">

                @csrf

                <label class="mb-2">

                    Enter Amount (INR)

                </label>

                <input
                    type="number"
                    name="amount"
                    class="form-control mb-3"
                    min="1"
                    placeholder="Enter amount"
                    required>

                @error('amount')

                <small class="text-danger">

                    {{$message}}

                </small>

                @enderror


                <button
                    class="btn btn-success w-100">

                    Pay Securely

                </button>


                <a
                    href="{{route('payments.list')}}"
                    class="btn btn-secondary w-100 mt-3">

                    View Payments

                </a>

            </form>

        </div>

    </div>

</body>

</html>