<?php

namespace Miracuthbert\Royalty\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Point extends Model
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
}
