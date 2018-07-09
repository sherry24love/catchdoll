<?php

namespace Sherrycin\Mall;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/**
 * Class Shop.
 */
class Mall {

	
	/**
	 * 注册后台管理路邮
	 */
	public function registerAdminRoutes() {
		$attributes = [
				'prefix'        => config('mall.route.prefix'),
				'namespace'     => 'Sherrycin\Mall\Controllers',
				'middleware'    => ['web', 'admin'],
		];
		//注册路邮
		Route::group($attributes, function ($router) {
			$attributes = ['middleware' => 'admin.permission:allow,administrator'];

			/* @var \Illuminate\Routing\Router $router */
				
			/**
			 * 商品管理
			 */
			$router->resource('product', 'ProductController' , [
					'names' => [
							'index' => 'mall.product.index' ,
							'create' => 'mall.product.create' ,
							'store' => 'mall.product.store' ,
							'edit' => 'mall.product.edit' ,
							'update' => 'mall.product.update' ,
							'destroy' => 'mall.product.delete' ,
					] ,
					'middleware' => ['admin.permission:check,product'] ,
			]);
			
				
		});
	}
	
	
	/**
	 * 注册路邮到api
	 */
	public function registerApiRoutes() {
		$attributes = [
				'prefix'        => config('mall.apiroute.prefix'),
				'namespace'     => 'Sherrycin\Mall\Apis',
				'middleware'    => [ 'api' ],
		];
		//注册路邮
		Route::group($attributes, function ($router) {
	
			/**
			 * 资讯分类管理 平台
			 */
			$router->resource('product', 'ProductApi' , [
					'names' => [
							'index' => 'mall.product.api.index' ,
							'create' => 'mall.product.api.create' ,
							'store' => 'mall.product.api.store' ,
							'edit' => 'mall.product.api.edit' ,
							'update' => 'mall.product.api.update' ,
							'destroy' => 'mall.product.api.delete' ,
					]
			]);
	
		});
	}
	
	
}