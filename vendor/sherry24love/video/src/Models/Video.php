<?php

namespace Sherrycin\Video\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Sherrycin\Video\Models\Collect ;

class Video extends Model
{
    //
    
	protected $table = 'videos' ;

	protected $fillable = [ 'url' , 'size' , 'user_id' , 'title' , 'desc' , 'status' , 'viewed' , 'voted' , 'collected' , 'shared' , 'sort' , 'top' ] ;

	
	
	public function user() {
		return $this->belongsTo( User::class , 'user_id' , 'id' );
	}

	public function comment() {
	    return $this->hasMany( Comment::class , 'video_id' , 'id' );
    }

    public function collect() {
        return $this->hasMany( Collect::class , 'video_id' , 'id' );
    }


    public function getCoverAttribute( ) {
	    return "https://{$this->url}" ;
    }
}
