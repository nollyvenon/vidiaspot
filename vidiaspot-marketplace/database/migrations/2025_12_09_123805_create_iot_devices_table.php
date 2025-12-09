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
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('device_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->string('device_type');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('status')->default('active');
            $table->string('connection_status')->default('disconnected');
            $table->timestamp('last_seen')->nullable();
            $table->string('location')->nullable();
            $table->json('specs')->nullable();
            $table->json('supported_protocols')->nullable();
            $table->string('firmware_version')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->boolean('is_registered')->default(false);
            $table->timestamp('registration_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'device_type']);
            $table->index('device_id');
            $table->index('is_connected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iot_devices');
    }
};
