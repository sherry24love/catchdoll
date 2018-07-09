<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/7
 * Time: 11:45
 */

namespace Sherrycin\Video\Providers;


use Sherrycin\Video\Facades\Video ;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;


class VideoServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $commands = [

    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__.'/../../views', 'video');
        if ($this->app->runningInConsole() ) {
            $this->publishes([__DIR__.'/../../resources/lang' => resource_path('lang')], 'laravel-video-lang');
            $this->publishes([__DIR__.'/../../database/migrations' => database_path('migrations')], 'laravel-video-migrations');
            $this->publishes([__DIR__.'/../../config/video.php' => config_path('video.php')], 'laravel-video-config');

        }

        Video::registerAdminRoutes();
        Video::registerApiRoutes();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (is_null(config('auth.guards.admin'))) {
            $this->setupAuth();
        }

    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function setupAuth()
    {
        config([
            'auth.guards.admin.driver'    => 'session',
            'auth.guards.admin.provider'  => 'admin',
            'auth.providers.admin.driver' => 'eloquent',
            'auth.providers.admin.model'  => 'Encore\Admin\Auth\Database\Administrator',
        ]);
    }
}
