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
        Schema::table('p2p_crypto_trade_disputes', function (Blueprint $table) {
            $table->string('resolution')->nullable()->after('status');
            $table->json('evidence')->nullable()->after('resolution');
            $table->unsignedBigInteger('resolver_id')->nullable()->after('evidence');
            $table->decimal('refund_amount', 15, 8)->nullable()->after('resolver_id');

            $table->foreign('resolver_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['resolver_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p2p_crypto_trade_disputes', function (Blueprint $table) {
            $table->dropForeign(['resolver_id']);
            $table->dropIndex(['resolver_id']);
            $table->dropIndex(['status']);

            $table->dropColumn([
                'resolution',
                'evidence',
                'resolver_id',
                'refund_amount',
            ]);
        });
    }
};
