<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model {
	
	use SoftDeletes ;
	
	protected $table='cms_blog';
	

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }
    
    public function user() {
    	return $this->belongsTo( \App\User::class , 'uid' , 'id' );
    }
    
    public function doctor() {
    	return $this->belongsTo( \App\Models\Doctor::class , 'uid' , 'uid' );
    }
    
    
    public function setImagesAttribute($pictures)
    {
    	if (is_array($pictures)) {
    		$this->attributes['images'] = json_encode($pictures);
    	}
    }
    
    public function getImagesAttribute($pictures)
    {
    	return json_decode($pictures, true);
    }
}
