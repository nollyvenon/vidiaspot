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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('feature_key'); // 'cryptocurrency_payments', 'bnpl_klarna', 'bnpl_afterpay', 'mobile_money_mpese', 'mobile_money_mtn', etc.
            $table->string('feature_name'); // Human-readable name
            $table->string('feature_type'); // 'payment_method', 'service', 'integration'
            $table->boolean('is_enabled')->default(true);
            $table->json('available_countries')->nullable(); // Countries where this feature is available
            $table->json('configuration')->nullable(); // Feature-specific configuration
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique('feature_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
