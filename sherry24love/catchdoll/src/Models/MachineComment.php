<?php
namespace Sherrycin\Catchdoll\Models ;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MachineComment extends Model {
	protected $table = 'machine_comments';
	protected $perPage = 15 ;
	protected $primaryKey = 'id' ;
	
	protected $fillable = ['machine_id' , 'user_id' , 'score' , 'comment' ] ;
	public $timestamps = false ;
	
	public function user() {
		return $this->belongsTo( User::class , 'user_id');
	}
	
	
	public function machine() {
		return $this->belongsTo( Machine::class , 'machine_id' , 'id' );
	}
	
	
	
}