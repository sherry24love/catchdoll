<?php

namespace App\Admin\Extensions\Form\Filed ;

use Encore\Admin\Form\Field;

class Ueditor extends Field
{
    protected static $js = [
    ];
    
    protected $view = 'admin.extensions.form.ueditor';
    
    public function __construct($column, $arguments = []) {
    	
    	parent::__construct($column, $arguments ) ;
    	$locale = str_replace('_', '-', strtolower(config('app.locale')));
    	$file = "/laravel-u-editor/lang/$locale/$locale.js";
    	$filePath = public_path() . $file;
    	
    	if (!\File::exists($filePath)) {
    		//Default is zh-cn
    		$file = "/laravel-u-editor/lang/zh-cn/zh-cn.js";
    	}
    	self::$js[] = $file ;
    }

    public function render()
    {
    	$this->script =<<< EOT
    	UE.delEditor('$this->column');
    var ue = UE.getEditor('$this->column');
		ue.ready(function() {
		ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');    
    });	
EOT;
        return parent::render();
    }
}
