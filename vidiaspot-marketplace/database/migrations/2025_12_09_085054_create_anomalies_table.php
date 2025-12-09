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
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};
