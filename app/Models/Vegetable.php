<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Vegetable extends Model
{
    use HasFactory;

    /**
     * guarded
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
    ];

    /**
     * hidden
     *
     * @var array
     */
    protected $hidden = ['pivot'];


    /**
     * user_saveds
     *
     * @return BelongsToMany
     */
    public function user_saveds(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saveds')->withTimestamps();
    }

    public function user_histories(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'histories')->withPivot('created_at', 'updated_at');
    }

    /**
     * types
     *
     * @return BelongsToMany
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'vegetables_types');
    }
}
