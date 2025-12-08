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
        Schema::create('price_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id');
            $table->decimal('target_price', 15, 2);
            $table->decimal('current_price', 15, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamp('last_triggered')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamps();

            // Add foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');

            // Add indexes
            $table->index(['user_id', 'active']);
            $table->index(['ad_id']);
            $table->index(['target_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_alerts');
    }
};
