<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Checkout</title> <!-- Page title -->

    <!-- Razorpay Checkout JS library -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<script>
    // Razorpay payment options
    var options = {
        "key": "{{ $key }}", // Razorpay API key from server
        "amount": "{{ $payment->amount*100 }}", // Amount in paise (multiply by 100)
        "currency": "INR", // Currency
        "name": "Laravel App", // Name displayed on the checkout
        "description": "Test Payment", // Payment description
        "order_id": "{{ $order['id'] }}", // Order ID generated from server
        "handler": function (response){ // Callback function on successful payment
            // Create a form dynamically to submit payment details to server
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("payment.success") }}'; // Route to handle success

            // Hidden input fields with CSRF token and payment ID
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                             '<input type="hidden" name="payment_id" value="{{ $payment->id }}">';

            // Append the form to the body and submit
            document.body.appendChild(form);
            form.submit();
        }
    };

    // Initialize Razorpay checkout
    var rzp1 = new Razorpay(options);
    rzp1.open(); // Open the checkout modal
</script>

</body>
</html>
