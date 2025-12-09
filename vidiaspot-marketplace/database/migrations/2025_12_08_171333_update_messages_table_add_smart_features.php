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
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('conversation_id')->nullable()->after('ad_id');
            $table->string('message_type')->default('text')->after('content'); // 'text', 'image', 'voice', 'video', 'file'
            $table->string('language')->default('en')->after('message_type'); // For translation purposes
            $table->text('translated_content')->nullable()->after('language'); // Store translated version
            $table->string('status')->default('sent')->after('translated_content'); // 'sent', 'delivered', 'read', 'error'
            $table->json('metadata')->nullable()->after('read_at'); // For storing additional data like file paths, voice transcription, etc.

            $table->index(['conversation_id', 'created_at']);

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropIndex(['conversation_id_created_at_index']);
            $table->dropColumn(['conversation_id', 'message_type', 'language', 'translated_content', 'status', 'metadata']);
        });
    }
};
