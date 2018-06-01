<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
     // Namespace for normal APIs.
    protected $apiNamespace = 'App\Http\Controllers\Api';
    // Namespace for APIs with V1.
     protected $apiV1namespace = 'App\Http\Controllers\Api\v1';
    // Namespace for APIs with V2.
    protected $apiV2namespace = 'App\Http\Controllers\Api\v2';
   

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->apiNamespace)
             ->group(base_path('routes/api.php'));
        
        //api version v1 routes
         Route::prefix('api/v1')
             ->middleware('api')
             ->namespace($this->apiV1namespace)
             ->group(base_path('routes/apiV1.php'));
    }
}
