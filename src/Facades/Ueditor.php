<?php

namespace Goletter\Ueditor\Facades;

use Illuminate\Support\Facades\Facade;

class Ueditor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ueditor';
    }
}
