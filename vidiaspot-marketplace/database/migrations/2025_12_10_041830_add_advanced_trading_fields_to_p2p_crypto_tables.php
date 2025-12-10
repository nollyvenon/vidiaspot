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
        Schema::table('p2p_crypto_trading_orders', function (Blueprint $table) {
            $table->boolean('is_oco_group_member')->default(false)->after('metadata');
            $table->uuid('oco_group_id')->nullable()->after('is_oco_group_member');
            $table->boolean('is_grid_member')->default(false)->after('oco_group_id');
            $table->uuid('grid_group_id')->nullable()->after('is_grid_member');
            $table->boolean('is_grid_protection')->default(false)->after('grid_group_id');
            $table->boolean('post_only')->default(false)->after('is_grid_protection');
            $table->boolean('reduce_only')->default(false)->after('post_only');

            $table->index(['oco_group_id', 'user_id']);
            $table->index(['grid_group_id', 'user_id']);
            $table->index(['is_oco_group_member', 'user_id']);
            $table->index(['is_grid_member', 'user_id']);
        });

        Schema::table('p2p_crypto_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('payment_method');

            $table->foreign('payment_method_id')->references('id')->on('p2p_crypto_payment_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p2p_crypto_trading_orders', function (Blueprint $table) {
            $table->dropIndex(['oco_group_id', 'user_id']);
            $table->dropIndex(['grid_group_id', 'user_id']);
            $table->dropIndex(['is_oco_group_member', 'user_id']);
            $table->dropIndex(['is_grid_member', 'user_id']);

            $table->dropColumn([
                'is_oco_group_member',
                'oco_group_id',
                'is_grid_member',
                'grid_group_id',
                'is_grid_protection',
                'post_only',
                'reduce_only',
            ]);
        });

        Schema::table('p2p_crypto_orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });
    }
};
