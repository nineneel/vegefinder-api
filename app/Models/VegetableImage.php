<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VegetableImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function vegetable(): BelongsTo
    {
        return $this->belongsTo(Vegetable::class);
    }
}
