<?php

namespace Sherrycin\Video\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Collect extends Model
{
    //
    
	protected $table = 'video_collects' ;

	protected $fillable = [
	    'user_id' , 'video_id'
    ] ;

	
	
	public function user() {
		return $this->belongsTo( User::class , 'user_id' , 'id' );
	}

	public function video() {
	    return $this->belongsTo( Video::class , 'video_id' , 'id' );
    }

}
