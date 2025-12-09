<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preference_key',
        'preference_value',
    ];

    protected $casts = [
        'preference_value' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('preference_key', $key);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function getPreference($userId, $key, $default = null)
    {
        $preference = static::where('user_id', $userId)
            ->where('preference_key', $key)
            ->first();

        return $preference ? $preference->preference_value : $default;
    }

    public static function setPreference($userId, $key, $value)
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'preference_key' => $key],
            ['preference_value' => $value]
        );
    }
}
