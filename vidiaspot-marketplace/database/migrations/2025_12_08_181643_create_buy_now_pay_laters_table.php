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
        Schema::create('buy_now_pay_laters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id');
            $table->unsignedBigInteger('payment_transaction_id'); // Reference to the payment transaction
            $table->string('provider'); // 'klarna', 'afterpay', 'paypal_credit', etc.
            $table->decimal('total_amount', 10, 2);
            $table->decimal('down_payment', 10, 2)->default(0);
            $table->integer('installment_count')->default(4);
            $table->decimal('installment_amount', 10, 2);
            $table->string('frequency'); // 'week', 'month'
            $table->string('status'); // 'pending_approval', 'approved', 'active', 'completed', 'failed', 'cancelled'
            $table->timestamp('first_payment_date')->nullable();
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamp('next_payment_date')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->json('provider_details')->nullable(); // Provider-specific details
            $table->json('payment_schedule')->nullable(); // Installment dates and amounts
            $table->json('checks')->nullable(); // Credit checks and approvals
            $table->decimal('apr_rate', 5, 2)->nullable(); // Annual percentage rate
            $table->text('agreement_url')->nullable(); // Link to the agreement
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('ad_id');
            $table->index('provider');
            $table->index('next_payment_date');

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
        Schema::dropIfExists('buy_now_pay_laters');
    }
};
