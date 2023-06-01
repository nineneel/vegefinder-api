<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'thumbnail',
        'register_method',
        'avatar_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * canAccessFilament
     *
     * @return bool
     */
    public function canAccessFilament(): bool
    {
        return true;
    }

    /**
     * avatar
     *
     * @return BelongsTo
     */
    public function avatar(): BelongsTo
    {
        return $this->belongsTo(Avatar::class);
    }

    /**
     * vegetable_saveds
     *
     * @return BelongsToMany
     */
    public function vegetable_saveds(): BelongsToMany
    {
        return $this->belongsToMany(Vegetable::class, 'saveds')->withTimestamps();
    }

    public function vegetable_histories(): BelongsToMany
    {
        return $this->belongsToMany(Vegetable::class, 'histories')->withPivot('created_at', 'updated_at');
    }

    /**
     * roles
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    /**
     * isAdmin
     *
     * @return Attribute
     */
    protected function isAdmin(): Attribute
    {
        return Attribute::make(
            get: function () {
                foreach ($this->roles as $_role) {
                    if ($_role->name == 'admin') {
                        return true;
                    }
                }
                return false;
            }
        );
    }
}
