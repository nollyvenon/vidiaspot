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
        Schema::create('p2p_crypto_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_type'); // bank_transfer, mobile_money, paypal, credit_card, etc.
            $table->string('payment_provider'); // specific provider name
            $table->string('name'); // display name for the payment method
            $table->json('payment_details'); // encrypted details like account number, etc.
            $table->string('account_number')->nullable(); // visible account number
            $table->string('account_name')->nullable(); // account holder name
            $table->string('bank_name')->nullable(); // for bank transfers
            $table->string('branch_code')->nullable(); // for certain banks
            $table->string('swift_code')->nullable(); // for international transfers
            $table->string('country_code', 3)->nullable(); // for international payments
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // how many times this method has been used
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['user_id', 'is_active']);
            $table->index(['payment_type', 'is_active']);
            $table->index(['is_verified', 'is_active']);
        });
        
        // Update the existing p2p_crypto_orders table to include payment_method_id
        Schema::table('p2p_crypto_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('payment_method');
            $table->foreign('payment_method_id')->references('id')->on('p2p_crypto_payment_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p2p_crypto_orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });
        
        Schema::dropIfExists('p2p_crypto_payment_methods');
    }
};