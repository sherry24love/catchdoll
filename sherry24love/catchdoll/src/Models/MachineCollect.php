<?php
namespace Sherrycin\Catchdoll\Models ;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MachineCollect extends Model {
	protected $table = 'machine_collects';
	protected $perPage = 15 ;
	protected $primaryKey = 'id' ;
	
	protected $fillable = ['machine_id' , 'user_id' ] ;
	public $timestamps = false ;
	
	public function user() {
		return $this->belongsTo( User::class , 'user_id');
	}
	
	
	public function machine() {
		return $this->belongsTo( Machine::class , 'machine_id' , 'id' );
	}
	
	
	
}