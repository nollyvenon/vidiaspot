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
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('date_range'); // daily, weekly, monthly, quarterly
            $table->date('forecast_date');
            $table->integer('predicted_demand'); // Predicted number of units/items
            $table->integer('actual_demand')->nullable(); // Actual demand after the fact
            $table->decimal('confidence_level', 5, 2); // Confidence level percentage
            $table->json('forecast_data'); // Detailed forecast data (historical trends, seasonality, etc.)
            $table->json('factors'); // Factors affecting demand predictions
            $table->timestamps();

            // Indexes for performance
            $table->index(['category_id', 'forecast_date']);
            $table->index(['location_id', 'forecast_date']);
            $table->index('forecast_date');
            $table->unique(['category_id', 'location_id', 'forecast_date'], 'unique_category_location_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
    }
};
