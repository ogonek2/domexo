<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Хостинг MySQL (mysql.tools / ProxySQL) иногда оставляет незакрытый
 * unbuffered-результат. Тогда финальный UPDATE sessions падает с HY000 2014.
 *
 * Middleware стоит в конце web-группы, поэтому terminate() вызывается
 * раньше StartSession и успевает отдать чистый PDO до записи сессии.
 */
class ReleaseMysqlConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        try {
            DB::disconnect();
        } catch (Throwable) {
            //
        }
    }
}
