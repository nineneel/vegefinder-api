<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Type extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function type_group(): BelongsTo
    {
        return $this->belongsTo(TypeGroup::class);
    }

    public function vegetables(): BelongsToMany
    {
        return $this->belongsToMany(Vegetable::class, 'vegetable_id', 'type_id')->using(VegetablesType::class);
    }
}
