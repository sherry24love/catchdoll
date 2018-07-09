<?php
namespace Sherrycin\Mall\Models ;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
	
	protected $table = 'orders';
	protected $perPage = 15 ;
	protected $primaryKey = 'id' ;

	protected $fillable = [
			'buyer_id' , 'seller_id' , 'goods_num' , 'total_amount' , 'status' , 'shipping_no' ,
			'shipping_at' , 'pay_status' , 'pay_at' , 'out_trade_no' , 'remark' , 'consignee' , 
			'mobile' , 'zipcode' , 'address' , 'created_at' , 'updated_at'
	] ;
	
	public $timestamps = true ;
	
	public function products() {
		return $this->hasMany( OrderProduct::class , 'order_id' , 'id' );
	}
}