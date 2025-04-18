<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceHistory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'balance',
        'amount',
        'type',
        'debit',
        'credit',
        'reference_id',
        'reference_type',
        'description',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'user_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'reference_id', 'id');
    }
}
