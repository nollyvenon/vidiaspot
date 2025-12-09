<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportSettings extends Model
{
    protected $table = 'product_import_settings';
    
    protected $fillable = [
        'import_days',
        'import_enabled',
        'import_interval_hours',
        'last_import_time',
        'import_source',
        'import_categories',
        'import_images',
        'import_location_filter',
        'import_price_range_min',
        'import_price_range_max',
        'import_duplicate_check',
    ];
    
    protected $casts = [
        'import_enabled' => 'boolean',
        'import_categories' => 'array',
        'import_images' => 'boolean',
        'last_import_time' => 'datetime',
        'import_price_range_min' => 'decimal:2',
        'import_price_range_max' => 'decimal:2',
    ];
    
    /**
     * Get the default settings
     */
    public static function getDefaultSettings()
    {
        return [
            'import_days' => 3,
            'import_enabled' => true,
            'import_interval_hours' => 24,
            'import_source' => 'jiji.ng',
            'import_categories' => null, // All categories
            'import_images' => true,
            'import_location_filter' => null, // All locations
            'import_price_range_min' => null,
            'import_price_range_max' => null,
            'import_duplicate_check' => true, // Check for duplicates by default
        ];
    }
    
    /**
     * Get current import settings or defaults
     */
    public static function getCurrentSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = new self();
            $settings->fill(self::getDefaultSettings());
            $settings->save();
        }
        
        return $settings;
    }
    
    /**
     * Check if it's time to import again
     */
    public function isTimeToImport()
    {
        if (!$this->import_enabled) {
            return false;
        }

        if (!$this->last_import_time) {
            return true;
        }

        $timeSinceLastImport = now()->diffInHours($this->last_import_time);
        return $timeSinceLastImport >= $this->import_interval_hours;
    }
}