<?php

namespace Sherrycin\Cms\Facades;

use Illuminate\Support\Facades\Facade;

class Cms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sherrycin\Cms\Cms::class;
    }
}
