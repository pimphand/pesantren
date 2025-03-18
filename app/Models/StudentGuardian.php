<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGuardian extends Model
{
    /** @use HasFactory<\Database\Factories\StudentGuardianFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $guarded = [];
}
