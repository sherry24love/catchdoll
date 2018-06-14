<?php

namespace Sherrycin\Catchdoll;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/**
 * Class Shop.
 */
class Catchdoll {

	
	/**
	 * 注册后台管理路邮
	 */
	public function registerAdminRoutes() {
		$attributes = [
				'prefix'        => config('catchdoll.route.prefix'),
				'namespace'     => 'Sherrycin\Catchdoll\Controllers',
				'middleware'    => ['web', 'admin'],
		];
		//注册路邮
		Route::group($attributes, function ($router) {
			$attributes = ['middleware' => 'admin.permission:allow,administrator'];

			/* @var \Illuminate\Routing\Router $router */
				
			/**
			 * 资讯分类管理 平台
			 */
			$router->get('catchdoll/agree/{id}' , 'CatchdollController@agree')->name('catchdoll.catchdoll.agree');
			$router->get('catchdoll/disagree/{id}' , 'CatchdollController@disagree')->name('catchdoll.catchdoll.disagree');
			$router->resource('catchdoll', 'CatchdollController' , [
					'names' => [
							'index' => 'catchdoll.catchdoll.index' ,
							'create' => 'catchdoll.catchdoll.create' ,
							'store' => 'catchdoll.catchdoll.store' ,
							'edit' => 'catchdoll.catchdoll.edit' ,
							'update' => 'catchdoll.catchdoll.update' ,
							'destroy' => 'catchdoll.catchdoll.delete' ,
					] ,
					'middleware' => ['admin.permission:check,catchdoll'] ,
			]);
			
				
		});
	}
	
	
	/**
	 * 注册路邮到api
	 */
	public function registerApiRoutes() {
		$attributes = [
				'prefix'        => config('catchdoll.apiroute.prefix'),
				'namespace'     => 'Sherrycin\Catchdoll\Apis',
				'middleware'    => [ 'api' ],
		];
		//注册路邮
		Route::group($attributes, function ($router) {
	
			/**
			 * 资讯分类管理 平台
			 */
			$router->get('catchdoll/map' , 'CatchdollApi@map') ;
			$router->post('catchdoll/upload' , 'CatchdollApi@upload') ;
			$router->post('catchdoll/{id}/favor' , 'CatchdollApi@favor');
			$router->resource('catchdoll', 'CatchdollApi' , [
					'names' => [
							'index' => 'catchdoll.api.index' ,
							'create' => 'catchdoll.api.create' ,
							'store' => 'catchdoll.api.store' ,
							'edit' => 'catchdoll.api.edit' ,
							'update' => 'catchdoll.api.update' ,
							'destroy' => 'catchdoll.api.delete' ,
					]
			]);
	
		});
	}
	
	
}