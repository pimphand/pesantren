<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    protected $fillable = [
        'key',
        'merchant_id',
        'request_data',
        'response_data',
        'status'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array'
    ];
}
