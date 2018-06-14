<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
] , function( $router ){
	$router->post('login' , 'AuthenticateApi@login');
	
	$router->post('session' , 'AuthenticateApi@session');

	$router->post('user/updatebase' , 'AuthenticateApi@updatebase') ;

	$router->post('user/setmobile' , 'AuthenticateApi@setMobile') ;
	
	$router->get('user' , 'AuthenticateApi@user');
});

