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
        Schema::create('insurance_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('license_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->decimal('claim_settlement_ratio', 5, 2)->nullable(); // e.g., 96.50%
            $table->decimal('rating', 3, 2)->default(0.00); // e.g., 4.50 out of 5
            $table->boolean('is_active')->default(true);
            $table->json('categories')->nullable(); // ['life', 'health', 'motor', 'travel', 'home']
            $table->json('features')->nullable(); // ['cashless', '24/7 support', 'network hospitals']
            $table->json('coverage_areas')->nullable(); // ['Maharashtra', 'Delhi', 'Karnataka']
            $table->decimal('min_premium', 10, 2)->nullable();
            $table->decimal('max_premium', 15, 2)->nullable();
            $table->decimal('min_coverage', 10, 2)->nullable();
            $table->decimal('max_coverage', 15, 2)->nullable();
            $table->json('specializations')->nullable(); // ['critical illness', 'family floater', 'third party']
            $table->json('network_partners')->nullable(); // ['Apollo Hospitals', 'Fortis', 'Medanta']
            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
            $table->index('categories');
            $table->index('coverage_areas');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_providers');
    }
};
