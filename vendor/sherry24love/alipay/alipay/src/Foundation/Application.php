<?php
namespace EasyAlipay\Foundation ;



use Pimple\Container;

class Application extends Container {
	
	protected $providers = [
			ServiceProviders\PaymentServiceProvider::class ,
	] ;
	
	public function __construct( $config ) {
		parent::__construct( );
		$this['config'] = function () use ($config) {
			return new Config($config);
		};
		if ($this['config']['debug']) {
			error_reporting(E_ALL);
		}
		
		$this->registerProviders();
	}
	
	
	/**
	 * 注册服务
	 */
	public function registerProviders() {
		foreach ($this->providers as $provider) {
			$this->register(new $provider());
		}
	}
	
	/**
	 * Return all providers.
	 *
	 * @return array
	 */
	public function getProviders() {
		return $this->providers;
	}
	
	/**
	 * Magic get access.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function __get($id) {
		return $this->offsetGet($id);
	}
	
	/**
	 * Magic set access.
	 *
	 * @param string $id
	 * @param mixed  $value
	 */
	public function __set($id, $value) {
		$this->offsetSet($id, $value);
	}
	
	/**
	 * Magic call.
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function __call($method, $args) {
		if (is_callable([$this['fundamental.api'], $method])) {
			return call_user_func_array([$this['fundamental.api'], $method], $args);
		}
	
		throw new \Exception("Call to undefined method {$method}()");
	}
}