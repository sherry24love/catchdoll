<?php

namespace Sherrycin\Cms;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/**
 * Class Shop.
 */
class Cms {
	
	/**
	 * 注册后台管理路邮
	 */
	public function registerAdminRoutes() {
		$attributes = [
				'prefix'        => config('cms.route.prefix'),
				'namespace'     => 'Sherrycin\Cms\Controllers',
				'middleware'    => ['web', 'admin'],
		];
		//dd( $attributes );
		Route::group($attributes, function ($router) {
			$attributes = ['middleware' => 'admin.permission:allow,administrator'];
		
			/* @var \Illuminate\Routing\Router $router */
			
			/**
			 * 资讯分类管理 平台
			 */
			$router->resource('cms/category', 'CategoryController' , [
					'names' => [
							'index' => 'cms.category.index' ,
							'create' => 'cms.category.create' ,
							'store' => 'cms.category.store' ,
							'edit' => 'cms.category.edit' ,
							'update' => 'cms.category.update' ,
							'destroy' => 'cms.category.delete' ,
					] ,
					'middleware' => ['admin.permission:check,cms_category'] ,
			]);
			/**
			 * 资讯管理
			 */
			$router->put('cms/posts/restore/{id}' , 'PostsController@restore')
			->name( 'cms.posts.restore' )
			->middleware('admin.permission:check,cmsposts,hospitalposts,supper');
			$router->resource('cms/posts', 'PostsController' , [
					'names' => [
						'index' => 'cms.posts.index' ,
						'create' => 'cms.posts.create' ,
						'store' => 'cms.posts.store' ,
						'edit' => 'cms.posts.edit' ,
						'update' => 'cms.posts.update' ,
						'destroy' => 'cms.posts.delete' ,
					] ,
					'middleware' => [
						//平台菜单 ， 医院菜单
						'admin.permission:check,cms_adv'
					] ,
					
			]);
			
			/**
			 * 单页面管理
			 */
			$router->resource('cms/singlepage', 'SingleController' , [
					'names' => [
							'index' => 'cms.single.index' ,
							'create' => 'cms.single.create' ,
							'store' => 'cms.single.store' ,
							'edit' => 'cms.single.edit' ,
							'update' => 'cms.single.update' ,
							'destroy' => 'cms.single.delete' ,
					] ,
					'middleware' => [
							'admin.permission:check,cms_single'
					] ,
			]);
			
			/**
			 * 广告位管理
			 */
			$router->resource('cms/advtarget', 'AdvtargetController' , [
					'names' => [
							'index' => 'cms.adv.index' ,
							'create' => 'cms.adv.create' ,
							'store' => 'cms.adv.store' ,
							'edit' => 'cms.adv.edit' ,
							'update' => 'cms.adv.update' ,
							'destroy' => 'cms.adv.delete' ,
					] ,
					'middleware' => [
							'admin.permission:check,cms_advtarget'
					] ,
			]);
			
			/**
			 * 广告管理
			 */
			$router->resource('cms/adv', 'AdvertisementController', [
					'name' => [
							'index' => 'cms.advtarget.index' ,
							'create' => 'cms.advtarget.create' ,
							'store' => 'cms.advtarget.store' ,
							'edit' => 'cms.advtarget.edit' ,
							'update' => 'cms.advtarget.update' ,
							'destroy' => 'cms.advtarget.delete' ,
					] ,
					'middleware' => [
							'admin.permission:check,cms_adv'
					] ,
			]);
			
			/**
			 * 系统通知
			 */
			$router->put('cms/notice/restore/{id}' , 'NoticeController@restore')
			->name( 'cms.notice.restore' )
			->middleware('admin.permission:check,cms_notice,supper');
			$router->resource('cms/notice', 'NoticeController' , [
					'names' => [
							'index' => 'cms.notice.index' ,
							'create' => 'cms.notice.create' ,
							'store' => 'cms.notice.store' ,
							'edit' => 'cms.notice.edit' ,
							'update' => 'cms.notice.update' ,
							'destroy' => 'cms.notice.delete' ,
					] ,
					'middleware' => [
							//平台菜单 ， 医院菜单
							'admin.permission:check,cms_notice,supper'
					] ,
						
			]);
			
			/**
			 * 医生发表
			 */
			$router->put('cms/blog/restore/{id}' , 'BlogController@restore')
			->name( 'cms.blog.restore' )
			->middleware('admin.permission:check,cms_blog,supper');
			$router->resource('cms/blog', 'BlogController' , [
					'names' => [
							'index' => 'cms.blog.index' ,
							'create' => 'cms.blog.create' ,
							'store' => 'cms.blog.store' ,
							'edit' => 'cms.blog.edit' ,
							'update' => 'cms.blog.update' ,
							'destroy' => 'cms.blog.delete' ,
					] ,
					'middleware' => [
							//平台菜单 ， 医院菜单
							'admin.permission:check,cms_blog,supper'
					] ,
			
			]);
			
			
			/**
			 * 意见反馈
			 */
			/**
			 * 意见反馈
			 */
			$router->resource('cms/feedback', 'FeedbackController' , [
					'names' => [
							'index' => 'cms.feedback.index' ,
							'create' => 'cms.feedback.create' ,
							'store' => 'cms.feedback.store' ,
							'edit' => 'cms.feedback.edit' ,
							'update' => 'cms.feedback.update' ,
							'destroy' => 'cms.feedback.destroy' ,
					] ,
					'middleware' => ['admin.permission:check,cms_feedback']
			]);
			
		});
	}



    /**
     * 注册路邮到api
     */
    public function registerApiRoutes() {
        $attributes = [
            'prefix'        => config('cms.apiroute.prefix'),
            'namespace'     => 'Sherrycin\Cms\Apis',
            'middleware'    => [ 'api' ],
        ];
        //注册路邮
        Route::group($attributes, function ($router) {

            /**
             * 资讯分类管理 平台
             */
            $router->get('cms/adv' , 'AdvertisementApi@index') ;
        });
    }
}