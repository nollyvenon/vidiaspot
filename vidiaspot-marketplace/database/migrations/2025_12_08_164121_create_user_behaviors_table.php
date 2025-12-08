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
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('behavior_type'); // 'view', 'click', 'search', 'purchase', 'like', etc.
            $table->string('target_type')->nullable(); // 'ad', 'category', 'vendor', etc.
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('metadata')->nullable(); // additional data like search terms, time spent, etc.
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['user_id', 'behavior_type']);
            $table->index(['target_type', 'target_id']);
            $table->index('occurred_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
};
