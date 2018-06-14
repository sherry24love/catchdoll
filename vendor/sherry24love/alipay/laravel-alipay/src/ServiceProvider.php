<?php
namespace Sherrycin\LaravelAlipay ;

use EasyAlipay\Foundation\Application as EasyAlipay;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider {
	
	
	public function boot() {
		
		$source = realpath(__DIR__.'/config.php');
		if ($this->app instanceof LaravelApplication) {
			if ($this->app->runningInConsole()) {
				$this->publishes([
						$source => config_path('alipay.php'),
				]);
			}
		
		}
		$this->mergeConfigFrom($source, 'alipay');
	}
	
	
	/**
	 * Register the provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(EasyAlipay::class, function ($app) {
			$easyalipay = new EasyAlipay(config('alipay'));
			if (config('alipay.use_laravel_cache')) {
				$easywechat->cache = new CacheBridge();
			}
			return $easyalipay;
		});
	
		$this->app->alias(EasyAlipay::class, 'alipay');
		$this->app->alias(EasyAlipay::class, 'easyalipay');
	}
	

	/**
	 * Register routes.
	 */
	protected function registerRoutes()
	{
		
	}
	
	
	/**
	 * Get Route attributes.
	 *
	 * @return array
	 */
	public function routeAttributes()
	{
		return array_merge($this->config('route.attributes', []), [
				'namespace' => '\\Sherrycin\\LaravelAlipay\\Controllers',
		]);
	}
	
	/**
	 * Get config value by key.
	 *
	 * @param string $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	private function config($key, $default = null)
	{
		return $this->app->make('config')->get("alipay.{$key}", $default);
	}
}