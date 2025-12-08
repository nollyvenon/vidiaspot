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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_location_id');
            $table->unsignedBigInteger('ad_id'); // Links to the ad/offer in the marketplace
            $table->string('sku')->nullable(); // Stock Keeping Unit
            $table->string('barcode')->nullable(); // UPC/EAN code
            $table->string('name'); // Item name
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // Product category
            $table->string('brand')->nullable(); // Brand name
            $table->integer('quantity_on_hand')->default(0); // Available stock
            $table->integer('quantity_reserved')->default(0); // Reserved for pending orders
            $table->integer('quantity_available')->default(0); // Available for sale (on_hand - reserved)
            $table->integer('minimum_stock_level')->default(0); // Min threshold for reordering
            $table->integer('maximum_stock_level')->nullable(); // Max inventory level
            $table->decimal('cost_price', 10, 2)->default(0.00); // Cost per unit
            $table->decimal('selling_price', 10, 2)->default(0.00); // Selling price per unit
            $table->string('currency')->default('NGN'); // Currency code
            $table->decimal('weight', 8, 2)->nullable(); // Weight in kg/lbs
            $table->json('dimensions')->nullable(); // {length, width, height, unit}
            $table->string('color')->nullable(); // Color variant
            $table->string('size')->nullable(); // Size variant
            $table->string('material')->nullable(); // Material composition
            $table->string('condition')->default('new'); // new, used, refurbished
            $table->string('serial_number')->nullable(); // For serializable items
            $table->string('batch_number')->nullable(); // For batched items
            $table->date('production_date')->nullable(); // Manufacturing date
            $table->date('expiry_date')->nullable(); // Expiry date for perishables
            $table->unsignedBigInteger('supplier_id')->nullable(); // Supplier who provided item
            $table->integer('reorder_point')->default(0); // Quantity at which to reorder
            $table->integer('reorder_quantity')->default(1); // Quantity to reorder
            $table->string('warehouse_position')->nullable(); // Location in warehouse
            $table->boolean('is_active')->default(true); // Is item active for sale
            $table->boolean('is_archived')->default(false); // Is item archived
            $table->json('tags')->nullable(); // Tags for better search
            $table->json('custom_attributes')->nullable(); // Custom attributes
            $table->json('seasonal_adjustments')->nullable(); // Seasonal pricing/sales adjustments
            $table->timestamps();

            // Indexes
            $table->index('inventory_location_id');
            $table->index('ad_id');
            $table->index('sku');
            $table->index('barcode');
            $table->index('category');
            $table->index('brand');
            $table->index('quantity_available');
            $table->index(['inventory_location_id', 'category']);
            $table->index(['ad_id', 'quantity_available']);
            $table->index(['sku', 'is_active']);
            $table->index('supplier_id');
            $table->index('is_active');
            $table->index('is_archived');
            $table->index(['is_active', 'is_archived']);

            // Foreign key constraints
            $table->foreign('inventory_location_id')->references('id')->on('inventory_locations')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
