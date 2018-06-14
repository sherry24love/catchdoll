<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model {
	
	use SoftDeletes ;
	
	protected $table='cms_posts';
	
	
    protected $fillable = [
    		'display' , 'title' , 'keyword' , 'description' , 'sort' , 'category_id' , 'cover' , 'content' ,'is_hot' , 'is_top' , 'is_recom' , 'is_pic'
    ];

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }
    
    public function category() {
    	return $this->belongsTo( Category::class , 'category_id' , 'id' );
    }
    
    
    public function org() {
    	return $this->belongsTo( \App\Models\Organization::class , 'org_id' , 'id' );
    }
}
