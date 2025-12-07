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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // google, facebook, twitter, etc
            $table->string('provider_user_id'); // ID from the provider
            $table->string('provider_username')->nullable(); // Username from the provider
            $table->string('email')->nullable(); // Email from the provider
            $table->string('avatar')->nullable(); // Avatar URL from the provider
            $table->text('access_token'); // Access token
            $table->text('refresh_token')->nullable(); // Refresh token
            $table->timestamp('expires_at')->nullable(); // Token expiration
            $table->timestamp('last_login_at')->nullable(); // Last login with this account
            $table->json('metadata')->nullable(); // Additional provider-specific data
            $table->timestamps();

            // Indexes for performance
            $table->index(['provider', 'provider_user_id']);
            $table->index('user_id');
            $table->index('email');
            $table->unique(['provider', 'provider_user_id']); // Each provider ID should be unique per provider
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
