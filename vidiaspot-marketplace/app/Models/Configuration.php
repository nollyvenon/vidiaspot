<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'description',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'json', // Allow flexible storage of configuration values
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get configuration value by key
     *
     * @param string $key
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $config = static::where('key', $key)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$config) {
            return $default;
        }

        // If it's a JSON type, return as decoded array/object
        if ($config->type === 'json') {
            return json_decode($config->value, true);
        }

        // If it's a boolean type
        if ($config->type === 'boolean') {
            return $config->value === 'true' || $config->value === true || $config->value === 1;
        }

        // If it's an integer type
        if ($config->type === 'integer') {
            return (int) $config->value;
        }

        // If it's a float/double type
        if ($config->type === 'float') {
            return (float) $config->value;
        }

        return $config->value;
    }

    /**
     * Set configuration value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string|null $category
     * @param string|null $description
     * @return Configuration
     */
    public static function setValue(string $key, $value, string $type = 'string', string $category = null, string $description = null)
    {
        $config = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $type === 'json' ? json_encode($value) : $value,
                'type' => $type,
                'category' => $category,
                'description' => $description,
                'is_active' => true,
            ]
        );

        return $config;
    }
}
