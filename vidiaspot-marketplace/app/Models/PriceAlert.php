<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Ad;

class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_id',
        'target_price',
        'current_price',
        'active',
        'last_triggered',
        'notification_sent_at'
    ];

    protected $casts = [
        'target_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'active' => 'boolean',
        'last_triggered' => 'datetime',
        'notification_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
