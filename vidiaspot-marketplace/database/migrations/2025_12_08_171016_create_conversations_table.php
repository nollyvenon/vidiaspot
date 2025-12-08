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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_id')->nullable(); // For conversations about specific ads
            $table->unsignedBigInteger('user1_id'); // First participant
            $table->unsignedBigInteger('user2_id'); // Second participant
            $table->string('title')->nullable(); // Title of the conversation
            $table->text('description')->nullable(); // Description of the conversation topic
            $table->boolean('is_active')->default(true); // Whether the conversation is active
            $table->json('participants_info')->nullable(); // Store additional participant info
            $table->timestamp('last_message_at')->nullable(); // When the last message was sent
            $table->timestamps();

            $table->unique(['ad_id', 'user1_id', 'user2_id']); // Ensure one conversation per ad between two users
            $table->index(['user1_id', 'last_message_at']);
            $table->index(['user2_id', 'last_message_at']);
            $table->index(['ad_id', 'last_message_at']);

            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
