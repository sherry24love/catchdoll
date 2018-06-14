<?php

namespace Sherrycin\Mall\Facades;

use Illuminate\Support\Facades\Facade;

class Mall extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \Sherrycin\Mall\Mall::class;
	}
}
