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
        Schema::create('custom_ad_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_id');
            $table->string('field_key'); // 'brand', 'orientation', 'manufacture_year', 'screen_size', 'color', etc.
            $table->string('field_label'); // 'Brand', 'Orientation', 'Manufacture Year', 'Screen Size', etc.
            $table->string('field_type'); // 'text', 'number', 'select', 'multiselect', 'checkbox', 'date', etc.
            $table->json('field_options')->nullable(); // For select/multiselect fields
            $table->text('field_value'); // Actual value for this field
            $table->json('field_config')->nullable(); // Configuration options like required, validation rules, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['ad_id', 'field_key']); // Each ad can only have one of each field key
            $table->index(['ad_id', 'field_key']);
            $table->index('field_key');
            $table->index('field_type');
            $table->index('sort_order');

            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_ad_fields');
    }
};
