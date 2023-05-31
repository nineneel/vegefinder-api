<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Type extends Model
{
    use HasFactory;

    /**
     * guarded
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * type_group
     *
     * @return BelongsTo
     */
    public function type_group(): BelongsTo
    {
        return $this->belongsTo(TypeGroup::class);
    }

    /**
     * vegetables
     *
     * @return BelongsToMany
     */
    public function vegetables(): BelongsToMany
    {
        return $this->belongsToMany(Vegetable::class, 'vegetable_id', 'type_id')->using(VegetablesType::class);
    }
}
