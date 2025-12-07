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
        Schema::create('featured_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ad owner
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null'); // Payment for feature
            $table->string('type')->default('premium'); // premium, highlighted, top, etc.
            $table->decimal('cost', 10, 2)->nullable(); // Cost of featuring
            $table->string('currency_code', 3)->default('NGN');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->json('settings')->nullable(); // Additional settings for the feature
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'expires_at']);
            $table->index('user_id');
            $table->index('ad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_ads');
    }
};
