<?php
/**
 * Created by PhpStorm.
 * User: wangfan
 * Date: 2018/6/7
 * Time: 11:16
 */

namespace Sherrycin\Video ;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/**
 * Class Shop.
 */
class Video {


    /**
     * 注册后台管理路邮
     */
    public function registerAdminRoutes() {
        $attributes = [
            'prefix'        => config('video.route.prefix'),
            'namespace'     => 'Sherrycin\Video\Controllers',
            'middleware'    => ['web', 'admin'],
        ];
        //注册路邮
        Route::group($attributes, function ($router) {
            $attributes = ['middleware' => 'admin.permission:allow,administrator'];

            /* @var \Illuminate\Routing\Router $router */

            /**
             * 商品管理
             */
            $router->resource('video', 'VideoController' , [
                'names' => [
                    'index' => 'video.video.index' ,
                    'create' => 'video.video.create' ,
                    'store' => 'video.video.store' ,
                    'edit' => 'video.video.edit' ,
                    'update' => 'video.video.update' ,
                    'destroy' => 'video.video.delete' ,
                ] ,
                'middleware' => ['admin.permission:check,video'] ,
            ]);


        });
    }


    /**
     * 注册路邮到api
     */
    public function registerApiRoutes() {
        $attributes = [
            'prefix'        => config('video.apiroute.prefix'),
            'namespace'     => 'Sherrycin\Video\Apis',
            'middleware'    => [ 'api' ],
        ];
        //注册路邮
        Route::group($attributes, function ($router) {

            /**
             * 资讯分类管理 平台
             */
            $router->get('video/uploadconf' , 'VideoApi@conf');
            $router->resource('video', 'VideoApi' , [
                'names' => [
                    'index' => 'video.video.api.index' ,
                    'create' => 'video.video.api.create' ,
                    'store' => 'video.video.api.store' ,
                    'edit' => 'video.video.api.edit' ,
                    'update' => 'video.video.api.update' ,
                    'destroy' => 'video.video.api.delete' ,
                ]
            ]);


            /**
             * 评论处理
             */
            $router->post('video_comment/{id}' , 'CommentApi@store');
            $router->get('video_comment/{id}' , 'CommentApi@index');
            /**
            $router->resource('video_comment' , 'CommentApi' , [
                'only' => [
                    'index' , 'store' , 'destroy'
                ]
            ] );

             **/

            /**
             * 收藏管理
             */
            $router->post('video_collect/{id}' , 'CollectApi@store');
            $router->get('video_collect' , 'CollectApi@index');
            $router->delete('video_collect/{id}' , 'CollectApi@destroy') ;

        });
    }


}