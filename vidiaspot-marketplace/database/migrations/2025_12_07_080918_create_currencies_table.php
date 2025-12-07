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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, NGN, etc.
            $table->string('name'); // US Dollar, Euro, Naira, etc.
            $table->string('symbol'); // $, €, ₦, etc.
            $table->string('country')->nullable(); // Country where currency is used
            $table->integer('precision')->default(2); // Number of decimal places
            $table->string('format'); // How to format the currency (naira, dollar, etc.)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('minor_unit')->default(100); // Number of minor units in major unit (100 cents in dollar)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
