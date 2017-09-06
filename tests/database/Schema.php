<?php

namespace Lumenpress\ORM\Tests\database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Facade;

class Schema extends Facade
{
    public static function connection($name)
    {
        return Capsule::schema($name);
    }

    protected static function getFacadeAccessor()
    {
        return Capsule::schema();
    }
}
