<?php
namespace Sherrycin\Catchdoll\Models ;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model {
	
	protected $table = 'machines';
	protected $perPage = 15 ;
	protected $primaryKey = 'id' ;

	protected $fillable = [
	    'landmark' , 'owner_id' , 'creator_id' , 'auth_id' , 'no' , 'cover' , 'address' , 'lat' , 'lon' , 'sort' , 'top' ,
        'score' , 'status' , 'lbs_id'
	] ;
	
	public $timestamps = true ;
	
	public function comments() {
		return $this->hasMany( MachineComment::class , 'machine_id' , 'id' );
	}

	public function collect() {
	    return $this->hasMany(MachineCollect::class , 'machine_id' , 'id' );
    }
	
	public function getCoverAttribute( $v ) {
		return $v ? \Storage::disk('public')->url( $v ) : '' ;
	}
}