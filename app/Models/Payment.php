<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at',
        'expired_at',
        'canceled_at',
        'failed_at',
        'note',
        'verified_at',
        'verified_by',
        'type',
        'idempotency_key'
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id', 'uuid');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
