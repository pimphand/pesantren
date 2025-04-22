<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements LaratrustUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use HasRolesAndPermissions, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'photo',
        'phone',
        'uuid',
        'balance',
        'parent_id',
    ];

    // boot method
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'parent_id',
        'id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function balanceHistories(): HasMany
    {
        return $this->hasMany(BalanceHistory::class)->orderBy('created_at', 'desc');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function parentDetail(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'parent_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function merchant(): HasOne
    {
        return $this->hasOne(Merchant::class);
    }
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }
    public function scopeWithRole(\Illuminate\Database\Eloquent\Builder $query, string $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }
}
