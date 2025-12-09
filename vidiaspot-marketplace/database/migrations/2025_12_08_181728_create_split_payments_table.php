<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('split_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Who initiated the split payment
            $table->unsignedBigInteger('ad_id'); // Which ad this payment is for
            $table->unsignedBigInteger('payment_transaction_id'); // Reference to the main transaction
            $table->decimal('total_amount', 10, 2); // Total amount to be split
            $table->decimal('amount_paid', 10, 2); // Amount already collected
            $table->decimal('amount_remaining', 10, 2); // Amount still needed
            $table->string('status'); // 'active', 'completed', 'cancelled', 'expired'
            $table->string('title'); // Title for the split payment request
            $table->text('description')->nullable(); // Description of the reason
            $table->integer('participant_count'); // Number of expected participants
            $table->timestamp('expires_at')->nullable(); // When the split payment expires
            $table->json('participants')->nullable(); // Store participant details {user_id, amount, status}
            $table->json('payment_details')->nullable(); // Details of each payment
            $table->json('settings')->nullable(); // {notify_on_join, auto_approve_join, etc.}
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('ad_id');
            $table->index('payment_transaction_id');
            $table->index('expires_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_payments');
    }
};
