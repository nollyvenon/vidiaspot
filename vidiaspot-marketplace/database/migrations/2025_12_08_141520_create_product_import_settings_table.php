<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_import_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('import_days')->default(3); // Default to last 3 days
            $table->boolean('import_enabled')->default(true);
            $table->integer('import_interval_hours')->default(24); // Run daily by default
            $table->timestamp('last_import_time')->nullable();
            $table->string('import_source')->default('jiji.ng');
            $table->json('import_categories')->nullable(); // Specific categories to import
            $table->boolean('import_images')->default(true);
            $table->string('import_location_filter')->nullable(); // Location filter
            $table->decimal('import_price_range_min', 15, 2)->nullable();
            $table->decimal('import_price_range_max', 15, 2)->nullable();
            $table->json('import_filters')->nullable(); // Additional filters
            $table->timestamps();
        });

        // Insert default settings
        DB::table('product_import_settings')->insert([
            'import_days' => 3,
            'import_enabled' => true,
            'import_interval_hours' => 24,
            'import_source' => 'jiji.ng',
            'import_images' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_import_settings');
    }
};
