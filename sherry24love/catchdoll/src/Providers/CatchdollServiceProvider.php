<?php

namespace Sherrycin\Catchdoll\Providers;


use Sherrycin\Catchdoll\Facades\Catchdoll ;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;


class CatchdollServiceProvider extends ServiceProvider
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
    	$this->loadViewsFrom(__DIR__.'/../../views', 'catchdoll');
    	if ($this->app->runningInConsole() ) {
    		$this->publishes([__DIR__.'/../../resources/lang' => resource_path('lang')], 'laravel-catchdoll-lang');
    		$this->publishes([__DIR__.'/../../database/migrations' => database_path('migrations')], 'laravel-catchdoll-migrations');
    		$this->publishes([__DIR__.'/../../config/catchdoll.php' => config_path('catchdoll.php')], 'laravel-catchdoll-config');
    		
    		//$this->publishes([__DIR__.'/../../assets' => public_path('packages/admin')], 'laravel-admin');
    		 
    	}
    	
    	
    	
    	Catchdoll::registerAdminRoutes();
    	Catchdoll::registerApiRoutes();
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
        /**
    	$this->app->booting(function () {
    		$loader = AliasLoader::getInstance();
    	
    		$loader->alias('Cms', \Sherry\Cms\Facades\Cms::class);
    		if (is_null(config('auth.guards.admin'))) {
    			$this->setupAuth();
    		}
    	});
    	**/
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
