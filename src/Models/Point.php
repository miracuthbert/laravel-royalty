<?php

namespace Miracuthbert\Royalty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Point extends Model implements PointContract
{
    use NodeTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'description',
        'points',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($point) {
            $key = $point->parent ? Str::slug($point->parent->name . ' ' . $point->name, '-') : $point->key;

            $point->key = $key;
        });
    }

    /**
     * Get users with this point.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('royalty.user.model'))
            ->withTimestamps();
    }
}
