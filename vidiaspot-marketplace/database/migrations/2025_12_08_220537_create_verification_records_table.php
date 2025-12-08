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
        Schema::create('verification_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('verification_type'); // biometric, fingerprint, face_recognition, video, document
            $table->string('verification_subtype')->nullable(); // fingerprint_left_thumb, face_front, video_introduction, etc.
            $table->text('verification_data')->nullable(); // Encrypted verification data
            $table->json('verification_metadata')->nullable(); // Additional metadata like confidence score, device info
            $table->string('result')->default('pending'); // success, failed, pending, flagged
            $table->decimal('confidence_score', 5, 2)->default(0.00); // Confidence level of verification (0-100)
            $table->string('status')->default('pending'); // active, expired, revoked
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('verification_session_id')->nullable(); // Unique session for the verification
            $table->json('device_info')->nullable(); // Information about the device used
            $table->string('ip_address', 45)->nullable(); // IP address of the verifier
            $table->json('location_data')->nullable(); // GPS coordinates if available
            $table->string('file_path')->nullable(); // Path to uploaded verification file (for biometrics/videos)
            $table->string('hash_verification')->nullable(); // Hash of the verification data
            $table->text('notes')->nullable(); // Internal notes
            $table->unsignedBigInteger('verified_by_admin')->nullable(); // If verified by an admin
            $table->text('admin_notes')->nullable(); // Notes from admin verification
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('verification_type');
            $table->index('verification_subtype');
            $table->index('status');
            $table->index('result');
            $table->index(['user_id', 'verification_type']); // For checking user's verification status
            $table->index(['verification_type', 'status']); // For admin verification management
            $table->index('verified_at');
            $table->index('expires_at');
            $table->index('verification_session_id');
            $table->index('ip_address');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by_admin')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_records');
    }
};
