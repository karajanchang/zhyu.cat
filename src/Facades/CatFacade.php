<?php

namespace Cat\Facades;

use Illuminate\Support\Facades\Facade;

class CatFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cat';
    }
}