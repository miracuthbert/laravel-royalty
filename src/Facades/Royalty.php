<?php

namespace Miracuthbert\Royalty\Facades;

use Illuminate\Support\Facades\Facade;

class Royalty extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'royalty';
    }
}
