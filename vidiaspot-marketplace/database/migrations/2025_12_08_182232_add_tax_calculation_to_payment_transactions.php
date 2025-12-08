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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('tax_region')->nullable()->after('currency'); // The region for tax calculation
            $table->decimal('tax_rate', 5, 2)->nullable()->after('tax_region'); // Tax rate percentage
            $table->decimal('tax_amount', 10, 2)->nullable()->after('tax_rate'); // Calculated tax amount
            $table->decimal('total_amount_with_tax', 10, 2)->nullable()->after('tax_amount'); // Total including tax
            $table->json('tax_breakdown')->nullable()->after('total_amount_with_tax'); // Detailed tax breakdown

            $table->index(['tax_region', 'created_at']);
            $table->index('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex(['payment_transactions_tax_region_created_at_index']);
            $table->dropIndex(['payment_transactions_tax_rate_index']);

            $table->dropColumn([
                'tax_region',
                'tax_rate',
                'tax_amount',
                'total_amount_with_tax',
                'tax_breakdown'
            ]);
        });
    }
};
