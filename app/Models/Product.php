<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $guarded = [];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductCategory::class)->withTrashed();
    }

    public function scopeName($query, $name)
    {
        return $query->where('name', 'ilike', "%$name%");
    }

    public function scopeDescription($query, $name)
    {
        return $query->where('description', 'ilike', "%$name%");
    }
}
