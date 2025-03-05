<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Ruta a la que se redirige el usuario luego del login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Registra las vinculaciones de modelo, filtros de patrones, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configura los limitadores de tasa para la aplicación.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        //
    }
}
