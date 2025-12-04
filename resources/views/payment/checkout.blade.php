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
