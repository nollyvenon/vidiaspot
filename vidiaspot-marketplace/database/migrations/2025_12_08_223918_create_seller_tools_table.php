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
        Schema::create('seller_tools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tool_type'); // 'bulk_editor', 'pricing_analyzer', 'repricing_tool', 'crm_integration', 'loyalty_program', 'cross_platform_sync', 'seasonal_planner'
            $table->string('name'); // Tool name
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Tool-specific settings
            $table->json('integration_config')->nullable(); // Integration configuration
            $table->json('usage_stats')->nullable(); // Usage statistics
            $table->timestamp('last_used_at')->nullable();
            $table->json('permissions')->nullable(); // User permissions for shared tools
            $table->string('access_level')->default('owner'); // owner, manager, employee
            $table->timestamp('trial_expires_at')->nullable(); // When trial expires
            $table->string('subscription_status')->default('inactive'); // inactive, active, trialing, expired
            $table->json('custom_fields')->nullable(); // Tool-specific custom fields
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('tool_type');
            $table->index('is_active');
            $table->index(['user_id', 'tool_type']); // For checking if user has specific tool
            $table->index(['tool_type', 'is_active']); // For admin tool management
            $table->index('last_used_at');
            $table->index(['subscription_status', 'trial_expires_at']); // For subscription management
            $table->index('access_level');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_tools');
    }
};
