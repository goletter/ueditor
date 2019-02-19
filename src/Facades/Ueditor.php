<?php

namespace Yanthink\Ueditor\Facades;

use Illuminate\Support\Facades\Facade;

class Ueditor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ueditor';
    }
}
