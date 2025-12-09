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
        Schema::create('smart_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('contract_address')->unique();
            $table->string('blockchain')->default('ethereum');
            $table->json('abi'); // Contract ABI (Application Binary Interface)
            $table->json('bytecode')->nullable(); // Contract bytecode (could be large)
            $table->string('contract_type')->default('standard'); // standard, marketplace, escrow, nft, etc.
            $table->string('status')->default('pending'); // pending, deployed, active, inactive, deprecated
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('version')->default('1.0.0');
            $table->integer('gas_limit')->default(500000);
            $table->decimal('gas_price', 10, 2)->default(20.00);
            $table->boolean('is_active')->default(true);
            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('last_interaction_at')->nullable();
            $table->integer('transaction_count')->default(0);
            $table->json('parameters')->nullable();
            $table->json('functions')->nullable(); // Extracted functions from ABI
            $table->json('events')->nullable(); // Extracted events from ABI
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['creator_id', 'status']);
            $table->index(['owner_id', 'is_active']);
            $table->index('blockchain');
            $table->index('contract_type');
            $table->index('contract_address');
        });

        Schema::create('smart_contract_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smart_contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_type')->default('function_call'); // function_call, purchase, escrow, etc.
            $table->string('function_name');
            $table->json('parameters')->nullable();
            $table->string('transaction_hash')->nullable()->unique();
            $table->string('blockchain')->default('ethereum');
            $table->bigInteger('block_number')->nullable();
            $table->string('from_address');
            $table->string('to_address');
            $table->decimal('value', 18, 8)->default(0); // Amount in Ether or other cryptocurrency
            $table->decimal('gas_used', 15, 2)->nullable();
            $table->decimal('gas_price', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, failed, reverted
            $table->text('error_message')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->integer('confirmation_blocks')->default(0);
            $table->json('events_logs')->nullable();
            $table->json('receipt')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['smart_contract_id', 'status']);
            $table->index(['user_id', 'transaction_type']);
            $table->index('transaction_hash');
            $table->index('status');
            $table->index('block_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_contract_transactions');
        Schema::dropIfExists('smart_contracts');
    }
};
