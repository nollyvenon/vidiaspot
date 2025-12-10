<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    protected $fillable = [
        'query',
        'location',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'searched_at' => 'datetime',
    ];

    public $timestamps = false;
}
