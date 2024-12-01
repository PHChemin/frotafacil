<?php

namespace Config;

use App\Middleware\ManagerAuthenticate;

class App
{
    public static array $middlewareAliases = [
        'auth' => \App\Middleware\Authenticate::class,
        'manager' => \App\Middleware\ManagerAuthenticate::class,
        'driver' => \App\Middleware\DriverAuthenticate::class
    ];
}
