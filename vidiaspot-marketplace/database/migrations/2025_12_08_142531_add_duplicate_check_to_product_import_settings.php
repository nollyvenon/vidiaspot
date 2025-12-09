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
        Schema::table('product_import_settings', function (Blueprint $table) {
            $table->boolean('import_duplicate_check')->default(true)->after('import_price_range_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_import_settings', function (Blueprint $table) {
            $table->dropColumn('import_duplicate_check');
        });
    }
};
