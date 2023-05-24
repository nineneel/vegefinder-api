<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }
}
