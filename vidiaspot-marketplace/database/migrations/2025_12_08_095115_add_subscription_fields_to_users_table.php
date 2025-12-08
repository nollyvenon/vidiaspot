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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->onDelete('set null');
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();
            $table->string('subscription_status')->default('inactive'); // inactive, active, cancelled, expired
            $table->integer('ad_limit')->default(0); // Number of ads allowed per subscription tier
            $table->integer('featured_ads_limit')->default(0); // Number of featured ads allowed
            $table->boolean('has_priority_support')->default(false);
            $table->json('subscription_features')->nullable(); // Additional features based on subscription
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn([
                'subscription_id',
                'subscription_start_date',
                'subscription_end_date',
                'subscription_status',
                'ad_limit',
                'featured_ads_limit',
                'has_priority_support',
                'subscription_features'
            ]);
        });
    }
};