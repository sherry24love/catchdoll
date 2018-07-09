<?php
namespace Sherrycin\Mall\Models ;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model {
	
	protected $table = 'order_products';
	protected $perPage = 15 ;
	protected $primaryKey = 'id' ;

	protected $fillable = [
			'order_id' , 'product_id' , 'product_name' , 'price' , 'quantity' , 'snapshot'
	] ;
	
	public $timestamps = true ;
	
	public function product() {
		return $this->belongsTo( Product::class , 'product_id' , 'id' );
	}
}