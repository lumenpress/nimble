<?php

use Illuminate\Database\Capsule\Manager as Capsule;

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

foreach ([
    'APP_DEBUG' => true,
    'DB_HOST' => 'localhost',
    'DB_DATABASE' => 'wordpress',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',
    'DB_PREFIX' => 'wptests_',
    'APP_TIMEZONE' => 'UTC',
] as $key => $value) {
    if (! getenv($key)) {
        putenv("$key=$value");
    }
}

$packagePath = realpath(dirname(PHPUNIT_COMPOSER_INSTALL).'/lumenpress/testing');

system("php $packagePath/tests/includes/install.php");

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => env('DB_HOST', 'mysql'),
    'database'  => env('DB_DATABASE', 'wordpress'),
    'username'  => env('DB_USERNAME', 'wordpress'),
    'password'  => env('DB_PASSWORD', ''),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => env('DB_PREFIX', 'wptests_'),
    'timezone'  => env('DB_TIMEZONE', '+00:00'),
    'strict'    => env('DB_STRICT_MODE', false),
]);

// Set the event dispatcher used by Eloquent models... (optional)
// use Illuminate\Events\Dispatcher;
// use Illuminate\Container\Container;

// $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();
