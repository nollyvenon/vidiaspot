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
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_id')->nullable(); // For calls related to specific ads
            $table->unsignedBigInteger('initiator_user_id'); // User who initiated the call
            $table->unsignedBigInteger('recipient_user_id'); // User who received the call
            $table->string('room_id'); // Unique room ID for the video call
            $table->string('call_status')->default('pending'); // 'pending', 'ongoing', 'completed', 'declined', 'missed'
            $table->string('call_type')->default('video'); // 'video', 'audio'
            $table->timestamp('scheduled_at')->nullable(); // When the call is scheduled for
            $table->timestamp('started_at')->nullable(); // When the call actually started
            $table->timestamp('ended_at')->nullable(); // When the call ended
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->json('participants')->nullable(); // Store list of participants
            $table->json('settings')->nullable(); // Call settings like video quality, etc.
            $table->timestamps();

            $table->index(['initiator_user_id', 'created_at']);
            $table->index(['recipient_user_id', 'created_at']);
            $table->index(['ad_id', 'created_at']);
            $table->index('scheduled_at');

            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
            $table->foreign('initiator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_calls');
    }
};
