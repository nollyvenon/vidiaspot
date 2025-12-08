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
        Schema::create('negotiation_assistants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('message_thread_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->decimal('initial_offer', 10, 2);
            $table->decimal('counter_offer', 10, 2)->nullable();
            $table->decimal('accepted_price', 10, 2)->nullable();
            $table->decimal('recommended_price', 10, 2)->nullable();
            $table->string('status')->default('active'); // active, accepted, rejected, counter_provided
            $table->string('negotiation_stage')->default('initial'); // initial, counter, acceptance, final
            $table->json('negotiation_history'); // History of all offers and counter offers
            $table->json('recommendations'); // AI-powered recommendations for next steps
            $table->json('factors_considered'); // Factors considered in negotiation
            $table->json('sentiment_analysis'); // Sentiment of both parties
            $table->integer('offer_count')->default(0);
            $table->decimal('acceptance_probability', 5, 2)->nullable(); // Probability of offer acceptance
            $table->text('ai_suggestions')->nullable(); // AI-generated suggestions for both parties
            $table->text('negotiation_strategy')->nullable(); // Suggested negotiation strategy
            $table->timestamp('expires_at')->nullable(); // When this negotiation expires
            $table->timestamps();

            // Indexes for performance
            $table->index(['ad_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiation_assistants');
    }
};
