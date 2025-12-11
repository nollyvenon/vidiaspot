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
        Schema::table('ads', function (Blueprint $table) {
            $table->boolean('direct_from_farm')->default(false)->after('status');
            $table->string('farm_name')->nullable()->after('direct_from_farm');
            $table->boolean('is_organic')->default(false)->after('farm_name');
            $table->date('harvest_date')->nullable()->after('is_organic');
            $table->string('farm_location')->nullable()->after('harvest_date');
            $table->decimal('farm_latitude', 10, 8)->nullable()->after('farm_location');
            $table->decimal('farm_longitude', 11, 8)->nullable()->after('farm_latitude');
            $table->string('certification')->nullable()->after('farm_longitude');
            $table->string('harvest_season')->nullable()->after('certification');
            $table->decimal('farm_size', 8, 2)->nullable()->after('harvest_season'); // in acres/hectares
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn([
                'direct_from_farm',
                'farm_name',
                'is_organic',
                'harvest_date',
                'farm_location',
                'farm_latitude',
                'farm_longitude',
                'certification',
                'harvest_season',
                'farm_size'
            ]);
        });
    }
};
