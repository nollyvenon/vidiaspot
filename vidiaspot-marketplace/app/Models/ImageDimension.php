<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImageDimension extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'purpose',
        'width',
        'height',
        'maintain_aspect_ratio',
        'quality_setting',
        'format_preference',
        'description',
        'is_active',
        'sort_order',
        'allowed_extensions',
        'max_file_size_kb',
        'enable_cropping',
        'enable_upscaling',
        'crop_positions',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'maintain_aspect_ratio' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'allowed_extensions' => 'array',
        'max_file_size_kb' => 'integer',
        'enable_cropping' => 'boolean',
        'enable_upscaling' => 'boolean',
        'crop_positions' => 'array',
        'quality_setting' => 'string',
        'format_preference' => 'string',
        'purpose' => 'string',
        'name' => 'string',
    ];

    /**
     * Scope to get active image dimensions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get dimensions by purpose
     */
    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    /**
     * Scope to get dimensions by name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Check if this dimension allows a specific file extension
     */
    public function allowsExtension($extension): bool
    {
        $allowedExtensions = $this->allowed_extensions ?? ['jpg', 'jpeg', 'png', 'webp'];
        return in_array(strtolower($extension), array_map('strtolower', $allowedExtensions));
    }

    /**
     * Check if a file size is within the allowed limits
     */
    public function isFileSizeAllowed($fileSizeKB): bool
    {
        $maxSize = $this->max_file_size_kb ?? 5000; // Default 5MB
        return $fileSizeKB <= $maxSize;
    }

    /**
     * Get the aspect ratio of this dimension
     */
    public function getAspectRatio(): float
    {
        if ($this->height > 0) {
            return $this->width / $this->height;
        }
        return 1.0; // Square aspect ratio if height is 0
    }
}
