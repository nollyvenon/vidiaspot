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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Premium, Pro, Business, etc.
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency_code', 3)->default('NGN');
            $table->string('billing_cycle'); // monthly, yearly, quarterly
            $table->integer('duration_days'); // How many days the subscription lasts
            $table->json('features')->nullable(); // Features included in this subscription
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Show as popular plan
            $table->integer('ad_limit')->default(0); // Number of ads allowed per month
            $table->integer('featured_ads_limit')->default(0); // Number of featured ads allowed
            $table->boolean('has_priority_support')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
