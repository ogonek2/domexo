<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // В конце web-стека → terminate() сработает ДО StartSession и
        // успеет сбросить «грязный» PDO до UPDATE sessions.
        $middleware->appendToGroup('web', \App\Http\Middleware\ReleaseMysqlConnection::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
