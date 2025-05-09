<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Level extends Model
{
    /** @use HasFactory<\Database\Factories\LevelFactory> */
    use HasFactory;
    use HasUuids;
    
    protected $guarded = [];
    
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
}
