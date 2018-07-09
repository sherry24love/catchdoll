<?php

namespace Sherrycin\Video\Facades;

use Illuminate\Support\Facades\Facade;

class Video extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sherrycin\Video\Video::class;
    }
}
