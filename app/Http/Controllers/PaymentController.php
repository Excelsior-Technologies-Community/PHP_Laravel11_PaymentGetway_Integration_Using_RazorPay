<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Payment;


use Razorpay\Api\Api;


class PaymentController extends Controller
{
    public function paymentForm()
    {
        return view('payment.form');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create Razorpay order
        $order = $api->order->create([
            'receipt' => 'order_rcptid_'.rand(1000,9999),
            'amount' => $request->amount * 100, // in paise
            'currency' => 'INR',
        ]);

        // Save payment as pending
        $payment = Payment::create([
            'amount' => $request->amount,
            'payment_method' => 'razorpay',
            'status' => 'pending',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // Redirect to Razorpay checkout view
        return view('payment.checkout', [
            'order' => $order,
            'payment' => $payment,
            'key' => env('RAZORPAY_KEY')
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);
        $payment->update([
            'status' => 'success',
            'updated_by' => 1
        ]);

        return redirect()->route('payments.list')->with('success','Payment successful!');
    }

    // List payments including trashed
    public function listPayments()
    {
        $payments = Payment::orderBy('id','asc')->get();

        return view('payment.list', compact('payments'));
    }

    // Soft delete
    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success','Payment soft deleted!');
    }

    // Restore soft deleted payment
    public function restorePayment($id)
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $payment->restore();
        return redirect()->back()->with('success','Payment restored!');
    }
}
