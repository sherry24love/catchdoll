<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;

class Single extends Model {
	
	protected $table='cms_singlepage';
	
	
    protected $fillable = [
    		'title' , 'keyword' , 'description' , 'order' , 'cover' , 'short_intro' , 'content'
    ];

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }
    
}
