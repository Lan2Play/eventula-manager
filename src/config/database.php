<?php

use App\Libraries\Helpers;

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => Helpers::getEnvWithFallback('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix'   => '',
        ],

        'mysql' => [
           'driver' => 'mysql',
            'host' => Helpers::getEnvWithFallback('DB_HOST', 'database'),
            'port' => Helpers::getEnvWithFallback('DB_PORT', '3306'),
            'database' => Helpers::getEnvWithFallback('DB_DATABASE', 'eventula_manager_database'),
            'username' => Helpers::getEnvWithFallback('DB_USERNAME', 'eventula_manager'),
            'password' => Helpers::getEnvWithFallback('DB_PASSWORD', 'password'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => Helpers::getEnvWithFallback('DB_HOST', 'localhost'),
            'database' => Helpers::getEnvWithFallback('DB_DATABASE', 'forge'),
            'username' => Helpers::getEnvWithFallback('DB_USERNAME', 'forge'),
            'password' => Helpers::getEnvWithFallback('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => Helpers::getEnvWithFallback('DB_HOST', 'localhost'),
            'database' => Helpers::getEnvWithFallback('DB_DATABASE', 'forge'),
            'username' => Helpers::getEnvWithFallback('DB_USERNAME', 'forge'),
            'password' => Helpers::getEnvWithFallback('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host'     => Helpers::getEnvWithFallback('REDIS_HOST', 'localhost'),
            'password' => Helpers::getEnvWithFallback('REDIS_PASSWORD', null),
            'port'     => Helpers::getEnvWithFallback('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
