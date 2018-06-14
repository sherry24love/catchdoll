<?php

namespace Sherrycin\Video\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Comment extends Model
{
    //
    
	protected $table = 'video_comments' ;

	protected $fillable = [
	    'content' , 'user_id' , 'status' , 'video_id'
    ] ;

	
	
	public function user() {
		return $this->belongsTo( User::class , 'user_id' , 'id' );
	}

	public function video() {
	    return $this->hasMany( Video::class , 'video_id' , 'id' );
    }

}
