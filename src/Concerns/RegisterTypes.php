<?php

namespace Lumenpress\ORM\Concerns;

trait RegisterTypes
{
    public static function register($type, $className)
    {
        if (! class_exists($className)) {
            throw new \Exception("{$className} class doesn't exist.", 1);
        }
        static::$registeredTypes[$type] = $className;
    }

    public static function getClassNameByType($type, $default = null)
    {
        return array_get(static::$registeredTypes, $type, $default);
    }
}
