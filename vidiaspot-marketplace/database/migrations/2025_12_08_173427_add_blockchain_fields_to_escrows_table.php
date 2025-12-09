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
        Schema::table('escrows', function (Blueprint $table) {
            $table->string('blockchain_transaction_hash')->nullable()->after('notes');
            $table->string('blockchain_contract_address')->nullable()->after('blockchain_transaction_hash');
            $table->string('blockchain_status')->nullable()->after('blockchain_contract_address');
            $table->json('blockchain_verification_data')->nullable()->after('blockchain_status');

            $table->index(['blockchain_transaction_hash']);
            $table->index(['blockchain_contract_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escrows', function (Blueprint $table) {
            $table->dropIndex(['escrows_blockchain_transaction_hash_index']);
            $table->dropIndex(['escrows_blockchain_contract_address_index']);
            $table->dropColumn(['blockchain_transaction_hash', 'blockchain_contract_address', 'blockchain_status', 'blockchain_verification_data']);
        });
    }
};
