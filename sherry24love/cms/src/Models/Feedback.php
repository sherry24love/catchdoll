<?php

namespace Sherrycin\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use App\User ;
class Feedback extends Model {
	
	protected $table='cms_feedback';
	
    protected $fillable = [
    	'user_id' , 'content'  , 'mobile' , 'status'
    ];

    public function __construct(array $attributes = []) {
    	
    	parent::__construct( $attributes );
    }
    
    
    public function user() {
    	return $this->belongsTo( User::class , 'user_id' , 'id' );
    }
}
