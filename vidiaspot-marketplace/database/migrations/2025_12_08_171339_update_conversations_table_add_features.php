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
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('title')->nullable()->after('user2_id'); // Title of the conversation
            $table->text('description')->nullable()->after('title'); // Description of the conversation topic
            $table->boolean('is_active')->default(true)->after('description'); // Whether the conversation is active
            $table->json('participants_info')->nullable()->after('is_active'); // Store additional participant info
            $table->timestamp('last_message_at')->nullable()->after('participants_info'); // When the last message was sent

            $table->unique(['ad_id', 'user1_id', 'user2_id']); // Ensure one conversation per ad between two users
            $table->index(['user1_id', 'last_message_at']);
            $table->index(['user2_id', 'last_message_at']);
            $table->index(['ad_id', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['ad_id']);
            $table->dropUnique(['ad_id_user1_id_user2_id_unique']);
            $table->dropIndex(['conversations_user1_id_last_message_at_index']);
            $table->dropIndex(['conversations_user2_id_last_message_at_index']);
            $table->dropIndex(['conversations_ad_id_last_message_at_index']);
            $table->dropColumn(['title', 'description', 'is_active', 'participants_info', 'last_message_at']);
        });
    }
};
