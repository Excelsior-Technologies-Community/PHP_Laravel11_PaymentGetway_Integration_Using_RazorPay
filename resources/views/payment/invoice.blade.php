<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $payment->id }}</title>
    <style>
        body { font-family: sans-serif; }
        .invoice-box { border: 1px solid #eee; padding: 30px; max-width: 800px; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; width: 100%; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h2>Payment Invoice</h2>
            <p>Laravel Razorpay Integration</p>
        </div>

        <table class="details">
            <tr>
                <td><strong>Invoice ID:</strong> #INV-{{ $payment->id }}</td>
                <td style="text-align: right;"><strong>Date:</strong> {{ $payment->created_at->format('d-m-Y') }}</td>
            </tr>
        </table>

        <table class="table">
            <thead style="background-color: #f4f4f4;">
                <tr>
                    <th>Description</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Razorpay Online Payment</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td><b style="color: green;">{{ strtoupper($payment->status) }}</b></td>
                    <td>₹{{ $payment->amount }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <h3>Grand Total: ₹{{ $payment->amount }}</h3>
        </div>
    </div>
</body>
</html>