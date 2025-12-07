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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency_code', 3); // From currency code
            $table->string('to_currency_code', 3); // To currency code
            $table->decimal('rate', 15, 8); // Exchange rate (e.g., 1 USD = 1500 NGN)
            $table->timestamp('last_updated'); // When was this rate last updated
            $table->string('provider')->nullable(); // Provider of the exchange rate (e.g., XE, OpenExchange)
            $table->timestamps();

            // Indexes for performance
            $table->index(['from_currency_code', 'to_currency_code']);

            // Foreign keys (if needed)
            // Note: We're not adding foreign key constraints to avoid issues during seeding
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
