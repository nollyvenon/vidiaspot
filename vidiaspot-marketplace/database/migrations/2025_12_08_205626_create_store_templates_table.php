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
        Schema::create('store_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Unique key for the template (e.g., 'modern', 'premium')
            $table->string('name'); // Display name (e.g., 'Modern Theme')
            $table->text('description')->nullable(); // Description of the template
            $table->json('features')->nullable(); // Features included in the template
            $table->json('config')->nullable(); // Configuration options for the template
            $table->boolean('is_active')->default(true); // Whether the template is available for use
            $table->integer('sort_order')->default(0); // Order to display templates
            $table->timestamps();

            $table->index('key');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_templates');
    }
};
