<?php

namespace Sherrycin\Mall\Providers;


use Sherrycin\Mall\Facades\Mall ;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;


class MallServiceProvider extends ServiceProvider
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
		$this->loadViewsFrom(__DIR__.'/../../views', 'mall');
		if ($this->app->runningInConsole() ) {
			$this->publishes([__DIR__.'/../../resources/lang' => resource_path('lang')], 'laravel-mall-lang');
			$this->publishes([__DIR__.'/../../database/migrations' => database_path('migrations')], 'laravel-mall-migrations');
			$this->publishes([__DIR__.'/../../config/mall.php' => config_path('mall.php')], 'laravel-mall-config');
			
		}
		 
		Mall::registerAdminRoutes();
		Mall::registerApiRoutes();
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
