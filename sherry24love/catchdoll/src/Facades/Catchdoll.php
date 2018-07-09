<?php

namespace Sherrycin\Catchdoll\Facades;

use Illuminate\Support\Facades\Facade;

class Catchdoll extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sherrycin\Catchdoll\Catchdoll::class;
    }
}
