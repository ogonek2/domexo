<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

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

        View::composer(
            ['includes.main.mega-menu', 'includes.main.nav'],
            fn ($view) => $view->with('megaMenuItems', get_mega_menu_data())
        );
    }
}
