<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model {
	
	protected $table='cms_adv';
	
	
    protected $fillable = [
    		'title' , 'keyword' , 'description' , 'order' , 'cover' , 'short_intro' , 'content'
    ];

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }
    
    
    public function advtarget() {
    	return $this->belongsTo( Advtarget::class , 'target_id' , 'id' );
    }
}
