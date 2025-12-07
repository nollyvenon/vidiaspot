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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key (e.g., site_name, contact_email, etc.)
            $table->text('value'); // Setting value
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('section')->default('general'); // general, payment, map, content, etc.
            $table->string('name')->nullable(); // Human-readable name for the setting
            $table->text('description')->nullable(); // Description of the setting
            $table->json('options')->nullable(); // Options for dropdowns, etc.
            $table->boolean('is_public')->default(false); // Whether this setting is public
            $table->boolean('is_active')->default(true); // Whether the setting is active
            $table->integer('order')->default(0); // Display order
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // User who last updated
            $table->timestamps();

            // Indexes for performance
            $table->index(['section', 'order']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
