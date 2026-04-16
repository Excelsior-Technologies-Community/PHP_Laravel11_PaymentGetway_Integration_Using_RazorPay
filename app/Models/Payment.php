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
