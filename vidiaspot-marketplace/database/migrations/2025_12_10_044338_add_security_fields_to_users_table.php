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
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_level')->default('unverified')->after('is_verified');
            $table->decimal('reputation_score', 5, 2)->default(0.00)->after('verification_level');
            $table->decimal('trade_completion_rate', 5, 2)->default(0.00)->after('reputation_score');
            $table->integer('total_trade_count')->default(0)->after('trade_completion_rate');
            $table->timestamp('last_trade_at')->nullable()->after('total_trade_count');
            $table->boolean('is_trusted_seller')->default(false)->after('last_trade_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_level',
                'reputation_score',
                'trade_completion_rate',
                'total_trade_count',
                'last_trade_at',
                'is_trusted_seller',
            ]);
        });
    }
};
