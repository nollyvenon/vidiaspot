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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Unique configuration key
            $table->text('value');           // Configuration value (can be JSON string)
            $table->string('type')->default('string'); // Data type: string, integer, boolean, json
            $table->string('category')->nullable();    // Category for grouping configs
            $table->string('description')->nullable(); // Description for admin
            $table->boolean('is_active')->default(true); // Whether config is active
            $table->timestamp('expires_at')->nullable(); // Optional expiry for temporary configs
            $table->timestamps();

            $table->index(['key', 'is_active']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
