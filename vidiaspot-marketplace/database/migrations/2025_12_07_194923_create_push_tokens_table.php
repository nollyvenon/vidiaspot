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
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token'); // Push notification token
            $table->string('device_type')->default('web'); // web, android, ios
            $table->string('browser')->nullable(); // Browser name for web push
            $table->string('platform')->nullable(); // Platform for mobile (Android, iOS)
            $table->string('os_version')->nullable(); // OS version
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Token expiration (may be used by services)
            $table->json('metadata')->nullable(); // Additional device information
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('token');
            $table->index('is_active');
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
    }
};
