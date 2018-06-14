<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model {
	
	use SoftDeletes ;
	
	protected $table='cms_notice';
	
	
    protected $fillable = [
    		'title' , 'content'
    ];

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }
    
}
