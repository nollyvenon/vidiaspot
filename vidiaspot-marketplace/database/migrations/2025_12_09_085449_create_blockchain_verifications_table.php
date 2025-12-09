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
        Schema::create('blockchain_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('verification_request_id')->unique();
            $table->string('transaction_hash')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected, expired
            $table->json('identity_data')->nullable(); // Encrypted personal data
            $table->string('document_type')->nullable(); // passport, driver_license, national_id
            $table->string('document_number')->nullable();
            $table->string('blockchain_network')->default('ethereum');
            $table->string('contract_address')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['verification_request_id']);
            $table->index(['transaction_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_verifications');
    }
};
