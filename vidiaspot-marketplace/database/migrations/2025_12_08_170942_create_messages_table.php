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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('ad_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable(); // For grouping messages in a conversation
            $table->text('content');
            $table->string('message_type')->default('text'); // 'text', 'image', 'voice', 'video', 'file'
            $table->string('language')->default('en'); // For translation purposes
            $table->text('translated_content')->nullable(); // Store translated version
            $table->string('status')->default('sent'); // 'sent', 'delivered', 'read', 'error'
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data like file paths, voice transcription, etc.
            $table->timestamps();

            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'created_at']);
            $table->index(['ad_id', 'created_at']);
            $table->index(['conversation_id', 'created_at']);

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
