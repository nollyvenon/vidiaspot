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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nigeria, Ghana, Kenya, etc.
            $table->string('code', 3)->unique(); // NG, GH, KE, etc.
            $table->string('phone_code', 10)->nullable(); // +234, +233, etc.
            $table->string('currency_code', 3); // NGN, GHS, KES, etc.
            $table->boolean('is_active')->default(true);
            $table->string('flag_icon', 50)->nullable(); // Emoji or flag image path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
