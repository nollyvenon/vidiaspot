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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedulings');
    }
};
