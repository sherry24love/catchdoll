<?php
namespace EasyAlipay\Foundation\ServiceProviders ;

use EasyAlipay\AlipayTrade\AlipayTradeWap ;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PaymentServiceProvider implements ServiceProviderInterface {
	
	
	/**
	 * 注册使用
	 * @param Container $pimple
	 */
	public function register(Container $pimple) {
		$pimple['wap_payment'] = function ($pimple) {
			$config = array_merge(
					['app_id' => $pimple['config']['app_id']],
					$pimple['config']->get('payment', [])
					);
			return new AlipayTradeWap($config);
		};
	}
}