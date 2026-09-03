<?php

namespace App\Providers;

use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use PDO;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        require_once app_path('Helpers/helpers.php');

        Event::listen(ConnectionEstablished::class, function (ConnectionEstablished $event): void {
            if ($event->connection->getDriverName() !== 'mysql') {
                return;
            }

            try {
                $pdo = $event->connection->getPdo();
                if ($pdo instanceof PDO) {
                    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                    $pdo->setAttribute(
                        PDO::ATTR_EMULATE_PREPARES,
                        filter_var(config('database.connections.mysql.options')[PDO::ATTR_EMULATE_PREPARES] ?? true, FILTER_VALIDATE_BOOLEAN)
                    );
                }
            } catch (Throwable) {
                //
            }
        });

        View::composer(
            ['includes.main.mega-menu', 'includes.main.nav'],
            fn ($view) => $view->with('megaMenuItems', get_mega_menu_data())
        );
    }
}
