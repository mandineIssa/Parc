<?php

class Kernel
{
    protected $routeMiddleware = [
        // ...
        'super_admin' => \App\Http\Middleware\CheckSuperAdmin::class,
        'ensure.super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
    ];
}