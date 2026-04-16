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
