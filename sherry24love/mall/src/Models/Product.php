<?php
namespace Sherrycin\Mall\Models ;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	
	protected $table = 'products';
	protected $perPage = 15 ;
	protected $primaryKey = 'id' ;

	protected $fillable = [
			'name' , 'seller_id' , 'market_price' , 'price' , 'status' , 'cover' ,'album' ,
			'content' , 'quantity' , 'sort' , 'top' , 'created_at' , 'updated_at' ,
	] ;
	
	public $timestamps = true ;
	
	public function setAlbumAttribute( $v ) {
		if( isset( $v ) && is_array( $v ) ) {
			
		} else {
			$v = [] ;
		}
		$this->cover = json_encode( $v ) ;
	}
	
	public function getAlbumAttribute( $v ) {
		return json_decode( $this->cover , true ) ;
	}
	
	public function getCoverAttribute( $v ) {
		return $v ? \Storage::disk('public')->url( $v ) : '' ;
	}
}