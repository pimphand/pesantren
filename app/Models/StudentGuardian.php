<?php

namespace App\Models;

use Database\Factories\StudentGuardianFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGuardian extends Model
{
    /** @use HasFactory<StudentGuardianFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $guarded = [];
}
