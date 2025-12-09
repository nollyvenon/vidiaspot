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
        Schema::create('inventory_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id');
            $table->unsignedBigInteger('inventory_item_id')->nullable(); // Link to inventory item if using inventory system
            $table->integer('initial_quantity')->default(0);
            $table->integer('current_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('sold_quantity')->default(0);
            $table->integer('damaged_quantity')->default(0);
            $table->integer('lost_quantity')->default(0);
            $table->string('quantity_unit')->default('pieces'); // pieces, kg, liters, etc.
            $table->boolean('location_trackable')->default(false); // Whether location tracking is enabled
            $table->json('location_coordinates')->nullable(); // Current location coordinates
            $table->unsignedBigInteger('last_updated_by')->nullable(); // User who last updated
            $table->timestamp('last_updated_at')->nullable(); // When last updated
            $table->boolean('automatic_updates_enabled')->default(false); // Whether to sync with POS/system
            $table->boolean('sync_with_vendor_store')->default(false); // Sync with vendor store inventory
            $table->boolean('sync_with_ads')->default(false); // Sync with classified ads inventory
            $table->boolean('out_of_stock_notification_enabled')->default(true);
            $table->integer('low_stock_threshold')->default(5); // At what quantity to send low stock notification
            $table->integer('reorder_threshold')->default(10); // At what quantity to suggest reorder
            $table->integer('reorder_quantity')->default(20); // How much to reorder
            $table->timestamp('restock_date')->nullable(); // Expected restock date
            $table->json('inventory_history')->nullable(); // Track all movements: {date, action, quantity, reason, updated_by}
            $table->string('inventory_status')->default('in_stock'); // in_stock, low_stock, out_of_stock, discontinued
            $table->timestamp('last_scanned_at')->nullable(); // When item was last scanned
            $table->unsignedBigInteger('scanned_by')->nullable(); // User who last scanned
            $table->boolean('qr_code_enabled')->default(false); // If QR code tracking is available
            $table->string('qr_code_url')->nullable(); // URL to the QR code for this item
            $table->boolean('rfid_enabled')->default(false); // If RFID tracking is available
            $table->string('rfid_tag_id')->nullable(); // RFID tag ID
            $table->boolean('batch_tracking_enabled')->default(false);
            $table->string('batch_number')->nullable();
            $table->boolean('expiry_tracking_enabled')->default(false);
            $table->date('expiry_date')->nullable();
            $table->date('production_date')->nullable();
            $table->date('best_before_date')->nullable();
            $table->boolean('recall_tracking_enabled')->default(false);
            $table->json('recall_dates')->nullable(); // Dates when item was recalled
            $table->boolean('quality_checks_enabled')->default(false);
            $table->timestamp('last_quality_check')->nullable();
            $table->string('quality_status')->default('pending'); // passed, pending, failed
            $table->text('notes')->nullable(); // Additional notes
            $table->json('custom_fields')->nullable(); // For extended functionality
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('ad_id');
            $table->index('inventory_item_id');
            $table->index('inventory_status');
            $table->index('current_quantity');
            $table->index('location_trackable');
            $table->index(['user_id', 'ad_id']); // For user's ad inventory
            $table->index(['ad_id', 'inventory_status']); // For ad's inventory status
            $table->index(['current_quantity', 'inventory_status']); // For low/out of stock queries
            $table->index('last_updated_at');
            $table->index('last_scanned_at');
            $table->index('rfid_tag_id');
            $table->index('batch_number');
            $table->index('expiry_date');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('scanned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_tracking');
    }
};
