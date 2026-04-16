<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;             // For Stripe integration (optional)
use Stripe\PaymentIntent;      // For Stripe payment intent
use App\Models\Payment;        // Payment model
use Razorpay\Api\Api;           // Razorpay SDK
use Barryvdh\DomPDF\Facade\Pdf; // For PDF generation (optional)

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

    public function downloadInvoice($id)
{
    $payment = Payment::findOrFail($id);

    if ($payment->status !== 'success') {
        return redirect()->back()->with('error', 'Only successful payments have invoices.');
    }

    $pdf = Pdf::loadView('payment.invoice', compact('payment'));

    return $pdf->download('invoice-'.$payment->id.'.pdf');
}
}
