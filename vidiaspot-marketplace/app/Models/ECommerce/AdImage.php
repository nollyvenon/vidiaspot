<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'image_path',
        'image_url',
        'is_primary',
        'order',
    ];

    protected $casts = [
        'ad_id' => 'integer',
        'is_primary' => 'boolean',
        'order' => 'integer',
    ];

    // Relationship: AdImage belongs to an Ad
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
