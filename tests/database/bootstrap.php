<?php 

use Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__.'/../../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

date_default_timezone_set(getenv('APP_TIMEZONE'));

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER')?:'mysql',
    'host'      => getenv('DB_HOST')?:'mysql',
    'database'  => getenv('DB_NAME')?:'wordpress',
    'username'  => getenv('DB_USER')?:'wordpress',
    'password'  => getenv('DB_PASSWORD')?:'wordpress',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => getenv('DB_PREFIX')?:'wp_testing_',
]);

// Set the event dispatcher used by Eloquent models... (optional)
// use Illuminate\Events\Dispatcher;
// use Illuminate\Container\Container;

// $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

require_once __DIR__ . '/migrations.php';
