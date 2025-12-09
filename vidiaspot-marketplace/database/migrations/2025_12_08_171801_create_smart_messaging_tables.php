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
        // Create conversations table if it doesn't exist
        if (!Schema::hasTable('conversations')) {
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

        // Create video_calls table if it doesn't exist
        if (!Schema::hasTable('video_calls')) {
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

        // Create schedulings table if it doesn't exist
        if (!Schema::hasTable('schedulings')) {
            Schema::create('schedulings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ad_id')->nullable(); // Related ad
                $table->unsignedBigInteger('initiator_user_id'); // User who initiated the schedule request
                $table->unsignedBigInteger('recipient_user_id'); // User who received the request
                $table->string('title'); // Title of the meeting/pickup
                $table->text('description')->nullable(); // Description of the meeting/pickup
                $table->timestamp('scheduled_datetime'); // When the meeting/pickup is scheduled for
                $table->string('location'); // Location for the meeting/pickup
                $table->string('status')->default('pending'); // 'pending', 'confirmed', 'declined', 'completed', 'cancelled'
                $table->string('type')->default('pickup'); // 'pickup', 'meeting', 'inspection', 'test_drive', etc.
                $table->json('participants')->nullable(); // Store list of participants
                $table->json('preferences')->nullable(); // Store scheduling preferences
                $table->timestamp('confirmed_at')->nullable(); // When it was confirmed
                $table->timestamp('completed_at')->nullable(); // When it was completed
                $table->text('notes')->nullable(); // Additional notes
                $table->timestamps();

                $table->index(['initiator_user_id', 'scheduled_datetime']);
                $table->index(['recipient_user_id', 'scheduled_datetime']);
                $table->index(['ad_id', 'scheduled_datetime']);
                $table->index('status');
                $table->index('scheduled_datetime');

                $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
                $table->foreign('initiator_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Create escrows table if it doesn't exist
        if (!Schema::hasTable('escrows')) {
            Schema::create('escrows', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transaction_id'); // Related transaction
                $table->unsignedBigInteger('ad_id'); // Related ad
                $table->unsignedBigInteger('buyer_user_id'); // Buyer
                $table->unsignedBigInteger('seller_user_id'); // Seller
                $table->decimal('amount', 10, 2); // Amount held in escrow
                $table->string('currency')->default('NGN'); // Currency
                $table->string('status')->default('pending'); // 'pending', 'released', 'refunded', 'disputed', 'completed'
                $table->string('dispute_status')->nullable(); // 'none', 'buyer_disputed', 'seller_disputed', 'under_review', 'resolved'
                $table->timestamp('release_date')->nullable(); // When funds should be released
                $table->timestamp('dispute_resolved_at')->nullable(); // When dispute was resolved
                $table->json('dispute_details')->nullable(); // Details about the dispute
                $table->json('release_conditions')->nullable(); // Conditions for releasing funds
                $table->text('notes')->nullable(); // Additional notes
                $table->timestamps();

                $table->index(['transaction_id', 'status']);
                $table->index(['buyer_user_id', 'status']);
                $table->index(['seller_user_id', 'status']);
                $table->index(['ad_id', 'status']);
                $table->index('status');
                $table->index('dispute_status');

                $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
                $table->foreign('buyer_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('seller_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrows');
        Schema::dropIfExists('schedulings');
        Schema::dropIfExists('video_calls');
        Schema::dropIfExists('conversations');
    }
};
