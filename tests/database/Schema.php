<?php 

namespace Lumenpress\ORM\Tests\Database;

use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Capsule\Manager as Capsule;

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