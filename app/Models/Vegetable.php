<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vegetable extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'images' => 'array',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(VegetableImage::class);
    }

    public function user_saveds(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saveds')->withTimestamps();
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'vegetables_types');
    }
}
