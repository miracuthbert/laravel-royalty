<?php

namespace Miracuthbert\Royalty\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Miracuthbert\Royalty\Traits\CollectsPoints;

class User extends Authenticatable
{
    use CollectsPoints;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
