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
        Schema::create('market_sentiment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trading_pair_id'); // The trading pair sentiment applies to
            $table->string('source'); // twitter, reddit, news, telegram, custom
            $table->string('sentiment_type'); // bullish, bearish, neutral, mixed
            $table->decimal('sentiment_score', 5, 2); // Sentiment score from -100 to 100
            $table->integer('volume_score')->default(0); // Volume of mentions
            $table->json('sentiment_breakdown')->nullable(); // Breakdown of sentiment (bullish: 45, bearish: 30, neutral: 25)
            $table->string('intensity'); // low, medium, high, extreme
            $table->text('source_content')->nullable(); // The original content that was analyzed
            $table->string('source_url')->nullable(); // URL to the original source
            $table->timestamp('source_timestamp')->nullable(); // When the source was created
            $table->timestamp('analyzed_at'); // When the sentiment was analyzed
            $table->json('analysis_metadata')->nullable(); // Additional metadata about the analysis
            $table->timestamps();

            $table->index(['trading_pair_id', 'analyzed_at']);
            $table->index(['trading_pair_id', 'sentiment_score']);
            $table->index('sentiment_type');
            $table->index('sentiment_score');
            $table->index('analyzed_at');
            $table->index('source');
            
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
        
        Schema::create('social_sentiment_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trading_pair_id');
            $table->decimal('overall_sentiment', 5, 2); // Overall sentiment score
            $table->integer('total_mentions'); // Total mentions across all sources
            $table->json('sentiment_distribution'); // Distribution of sentiment types
            $table->json('source_breakdown'); // Sentiment by source (twitter: 0.6, reddit: 0.3, etc.)
            $table->timestamp('snapshot_time'); // When the snapshot was taken
            $table->timestamps();

            $table->index(['trading_pair_id', 'snapshot_time']);
            $table->index('snapshot_time');
            $table->index('overall_sentiment');
            
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
        
        Schema::create('news_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary');
            $table->text('content');
            $table->string('source');
            $table->string('category')->default('general'); // market, regulatory, technology, adoption, etc.
            $table->json('related_pairs')->nullable(); // Trading pairs related to this news
            $table->string('sentiment_impact')->default('neutral'); // bullish, bearish, neutral
            $table->string('reliability_score')->default('medium'); // low, medium, high
            $table->string('url');
            $table->timestamp('published_at');
            $table->timestamps();

            $table->index('published_at');
            $table->index('category');
            $table->index('sentiment_impact');
            $table->index('reliability_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_feeds');
        Schema::dropIfExists('social_sentiment_snapshots');
        Schema::dropIfExists('market_sentiment');
    }
};