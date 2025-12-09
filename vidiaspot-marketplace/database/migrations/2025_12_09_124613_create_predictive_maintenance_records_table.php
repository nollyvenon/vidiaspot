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
        Schema::create('predictive_maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('device_id'); // External device ID
            $table->foreignId('iot_device_id')->constrained('iot_devices')->onDelete('cascade');
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('maintenance_type')->default('routine');
            $table->timestamp('predicted_failure_date')->nullable();
            $table->decimal('confidence_level', 5, 2)->default(0.50);
            $table->tinyInteger('maintenance_priority')->default(5); // 1-10 scale
            $table->string('status')->default('pending'); // pending, scheduled, in_progress, completed, cancelled
            $table->json('symptoms')->nullable();
            $table->json('detected_anomalies')->nullable();
            $table->json('maintenance_suggestions')->nullable();
            $table->decimal('maintenance_cost_estimate', 10, 2)->nullable();
            $table->decimal('maintenance_duration_estimate', 8, 2)->nullable(); // in hours
            $table->json('recommended_parts')->nullable();
            $table->json('sensor_data')->nullable();
            $table->json('ai_analysis')->nullable();
            $table->timestamp('maintenance_schedule_date')->nullable();
            $table->timestamp('actual_maintenance_date')->nullable();
            $table->string('maintenance_performed_by')->nullable();
            $table->decimal('maintenance_cost_actual', 10, 2)->nullable();
            $table->boolean('resolved')->default(false);
            $table->text('feedback')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['iot_device_id', 'predicted_failure_date']);
            $table->index('maintenance_priority');
            $table->index('predicted_failure_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictive_maintenance_records');
    }
};
