<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;

class Advtarget extends Model {
	
	protected $table='cms_adv_target';
	
	
    protected $fillable = [
    		'title' , 'keyword' , 'description' , 'order' , 'cover' , 'short_intro' , 'content'
    ];

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }

}
