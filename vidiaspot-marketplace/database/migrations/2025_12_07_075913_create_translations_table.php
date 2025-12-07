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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 50); // Name of the table being translated
            $table->unsignedBigInteger('record_id'); // ID of the record being translated
            $table->string('column_name', 50); // Name of the column being translated
            $table->string('locale', 10); // Language code (en, fr, etc.)
            $table->text('value'); // Translated value
            $table->timestamps();

            // Create indexes for performance
            $table->index(['table_name', 'record_id', 'column_name', 'locale']);

            // Foreign key to languages table
            $table->foreign('locale')->references('code')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
