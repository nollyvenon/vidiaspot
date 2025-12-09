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
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('feature_key')->unique(); // e.g., 'insurance_policy', 'online_store', 'pay_later'
            $table->string('name'); // User-friendly name
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->json('allowed_countries')->nullable(); // JSON array of allowed countries
            $table->json('allowed_states')->nullable(); // JSON array of allowed states
            $table->json('allowed_cities')->nullable(); // JSON array of allowed cities
            $table->json('config')->nullable(); // Additional config options
            $table->timestamp('starts_at')->nullable(); // Feature activation date
            $table->timestamp('expires_at')->nullable(); // Feature expiration date
            $table->timestamps();

            $table->index('feature_key');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
    }
};
