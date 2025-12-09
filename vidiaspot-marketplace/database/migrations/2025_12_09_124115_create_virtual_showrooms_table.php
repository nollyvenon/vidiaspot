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
        Schema::create('virtual_showrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('platform')->default('custom'); // custom, unity, unreal, etc.
            $table->string('url')->nullable();
            $table->text('embed_code')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('background_image_url')->nullable();
            $table->string('virtual_environment')->default('standard');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_visitors')->default(100);
            $table->integer('current_visitors')->default(0);
            $table->boolean('requires_reservation')->default(false);
            $table->decimal('reservation_fee', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('features')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'is_active']);
            $table->index('is_public');
            $table->index('platform');
            $table->index('slug');
        });

        Schema::create('virtual_showroom_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_showroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->json('position')->nullable(); // 3D position in the virtual space
            $table->json('rotation')->nullable(); // 3D rotation in the virtual space
            $table->json('scale')->nullable(); // 3D scale in the virtual space
            $table->integer('display_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['virtual_showroom_id', 'ad_id']);
            $table->index(['virtual_showroom_id', 'display_order']);
        });

        Schema::create('virtual_showroom_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_showroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('visit_time');
            $table->integer('duration')->default(0); // in minutes
            $table->string('status')->default('active'); // active, completed, left
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['virtual_showroom_id', 'user_id']);
            $table->index(['virtual_showroom_id', 'status']);
            $table->index('visit_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_showroom_visitors');
        Schema::dropIfExists('virtual_showroom_products');
        Schema::dropIfExists('virtual_showrooms');
    }
};
