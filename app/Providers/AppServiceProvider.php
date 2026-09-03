<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use PDO;

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

        // Прості PDO-опції для проксі mysql.tools (без зайвої магії в runtime).
        if (config('database.default') === 'mysql' && extension_loaded('pdo_mysql')) {
            config([
                'database.connections.mysql.options' => array_filter([
                    ...(config('database.connections.mysql.options') ?? []),
                    PDO::ATTR_EMULATE_PREPARES => true,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                ], static fn ($value) => $value !== null),
            ]);
        }

        View::composer(
            'includes.main.mega-menu',
            fn ($view) => $view->with('megaMenuItems', get_mega_menu_data())
        );
    }
}
