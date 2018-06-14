<?php
use App\Admin\Extensions\Form\Filed\BMap ;
use App\Admin\Extensions\Form\Filed\Ueditor ;
use Encore\Admin\Admin ;
/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


Admin::js ( [
		'/laravel-u-editor/ueditor.config.js',
		'/laravel-u-editor/ueditor.all.min.js'
] );

Encore\Admin\Form::forget(['map', 'editor']);


//注册新的表单插件
Encore\Admin\Form::extend('bmap', BMap::class);
Encore\Admin\Form::extend('ueditor', Ueditor::class);
